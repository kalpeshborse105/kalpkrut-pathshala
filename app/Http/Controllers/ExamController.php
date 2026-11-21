<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Exam, ExamQuestion, StudentExamAttempt, Course, Batch, Subject, Branch, Student};

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['course', 'batch', 'subject', 'branch'])->latest();

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        $exams = $query->paginate(15)->withQueryString();
        $courses = Course::where('status', 'active')->get();
        $batches = Batch::where('status', 'active')->get();

        return view('exams.index', compact('exams', 'courses', 'batches'));
    }

    public function create()
    {
        $courses = Course::where('status', 'active')->get();
        $batches = Batch::where('status', 'active')->get();
        $subjects = Subject::all();
        $branches = Branch::all();
        return view('exams.create', compact('courses', 'batches', 'subjects', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'required|exists:batches,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'title' => 'required|string|max:191',
            'exam_type' => 'required|in:online,offline,hybrid',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:1',
            'passing_marks' => 'required|numeric|min:1',
            'instructions' => 'nullable|string',
        ]);

        $examCount = Exam::count() + 1;
        $validated['exam_code'] = 'EXAM-' . sprintf('%03d', $examCount);
        $validated['status'] = 'published';
        $validated['created_by'] = auth()->id() ?? 1;

        $exam = Exam::create($validated);

        return redirect()->route('exams.show', $exam)->with('success', 'Exam scheduled successfully with Code: ' . $exam->exam_code);
    }

    public function show(Exam $exam)
    {
        $exam->load(['course', 'batch.students', 'subject', 'branch', 'questions', 'attempts.student']);
        return view('exams.show', compact('exam'));
    }

    public function saveMarks(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'marks' => 'required|array',
            'remarks' => 'nullable|array',
        ]);

        foreach ($validated['marks'] as $studentId => $mark) {
            $score = (float)$mark;
            $pct = $exam->total_marks > 0 ? round(($score / $exam->total_marks) * 100, 2) : 0;
            $grade = $pct >= 85 ? 'A+' : ($pct >= 75 ? 'A' : ($pct >= 60 ? 'B' : ($pct >= 50 ? 'C' : 'F')));
            $result = $score >= $exam->passing_marks ? 'pass' : 'fail';

            StudentExamAttempt::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'student_id' => $studentId,
                ],
                [
                    'marks_obtained' => $score,
                    'percentage' => $pct,
                    'grade' => $grade,
                    'status' => 'evaluated',
                    'result' => $result,
                    'faculty_remarks' => $validated['remarks'][$studentId] ?? null,
                    'submitted_at' => now(),
                ]
            );
        }

        $exam->update(['status' => 'results_published']);

        return back()->with('success', 'Marks evaluated and results published successfully.');
    }

    public function reportCard(Exam $exam, Student $student)
    {
        $attempt = StudentExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $exam->load(['course', 'subject', 'branch.institute.company']);
        return view('exams.report_card', compact('exam', 'student', 'attempt'));
    }
}

