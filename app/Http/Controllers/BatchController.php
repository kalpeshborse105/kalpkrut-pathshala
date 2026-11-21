<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Batch, Course, Branch, Classroom, User, AcademicSession, Student};

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $query = Batch::with(['course', 'branch', 'classroom', 'mentor', 'students'])->latest();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $batches = $query->paginate(15)->withQueryString();
        $branches = Branch::all();
        $courses = Course::where('status', 'active')->get();

        return view('batches.index', compact('batches', 'branches', 'courses'));
    }

    public function create()
    {
        $branches = Branch::all();
        $courses = Course::where('status', 'active')->get();
        $classrooms = Classroom::where('status', 'active')->get();
        $mentors = User::whereIn('user_type', ['faculty', 'branch_manager'])->get();
        return view('batches.create', compact('branches', 'courses', 'classrooms', 'mentors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50|unique:batches,code',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'capacity' => 'required|integer|min:1',
            'delivery_mode' => 'required|in:offline,online,hybrid',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'primary_mentor_id' => 'nullable|exists:users,id',
        ]);

        $session = AcademicSession::where('is_current', true)->first() ?? AcademicSession::first();
        $validated['academic_session_id'] = $session->id;
        $validated['status'] = 'active';

        $batch = Batch::create($validated);

        if (!empty($validated['primary_mentor_id'])) {
            $batch->faculty()->attach($validated['primary_mentor_id']);
        }

        return redirect()->route('batches.show', $batch)->with('success', 'Batch created successfully.');
    }

    public function show(Batch $batch)
    {
        $batch->load([
            'course.subjects', 'branch', 'classroom', 'mentor',
            'students', 'faculty', 'timetables.subject', 'timetables.faculty', 'exams'
        ]);

        $allStudents = Student::where('status', 'active')
            ->whereDoesntHave('batches', function($q) use ($batch) {
                $q->where('batches.id', $batch->id);
            })
            ->get();

        return view('batches.show', compact('batch', 'allStudents'));
    }

    public function enrollStudent(Request $request, Batch $batch)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($request->student_id);

        $batch->students()->syncWithoutDetaching([
            $student->id => [
                'roll_no' => $student->roll_no,
                'enrolled_at' => now()->toDateString(),
                'status' => 'active',
            ]
        ]);

        return back()->with('success', "Student {$student->full_name} enrolled into batch.");
    }
}

