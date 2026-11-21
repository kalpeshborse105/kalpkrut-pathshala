<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Assignment, AssignmentSubmission, StudyMaterial, Course, Subject, Batch};

class LmsController extends Controller
{
    public function assignments(Request $request)
    {
        $assignments = Assignment::with(['batch.course', 'subject', 'faculty', 'submissions'])
            ->latest()
            ->paginate(15);
        $batches = Batch::where('status', 'active')->get();
        $subjects = Subject::all();

        return view('lms.assignments', compact('assignments', 'batches', 'subjects'));
    }

    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'due_datetime' => 'required|date',
            'max_marks' => 'required|numeric|min:1',
        ]);

        $batch = Batch::findOrFail($validated['batch_id']);
        $validated['branch_id'] = $batch->branch_id;
        $validated['faculty_id'] = auth()->id() ?? 1;
        $validated['status'] = 'active';

        Assignment::create($validated);

        return back()->with('success', 'Assignment published successfully for batch: ' . $batch->name);
    }

    public function materials(Request $request)
    {
        $query = StudyMaterial::with(['course', 'subject'])->latest();

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $materials = $query->paginate(15)->withQueryString();
        $courses = Course::where('status', 'active')->get();

        return view('lms.materials', compact('materials', 'courses'));
    }

    public function storeMaterial(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'title' => 'required|string|max:191',
            'material_type' => 'required|in:pdf,video_link,document,audio,notes',
            'external_url' => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $validated['branch_id'] = 1;
        $validated['uploaded_by'] = auth()->id() ?? 1;

        StudyMaterial::create($validated);

        return back()->with('success', 'Study material uploaded successfully.');
    }
}

