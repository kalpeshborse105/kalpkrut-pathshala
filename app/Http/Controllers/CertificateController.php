<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Certificate, Student, Course};
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with(['student', 'course', 'branch'])->latest()->paginate(15);
        $students = Student::where('status', 'active')->get();
        $courses = Course::where('status', 'active')->get();
        return view('certificates.index', compact('certificates', 'students', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:191',
            'grade' => 'required|string|max:50',
            'issue_date' => 'required|date',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $count = Certificate::count() + 1;

        $cert = Certificate::create([
            'branch_id' => $student->branch_id,
            'student_id' => $student->id,
            'course_id' => $validated['course_id'],
            'certificate_no' => 'CERT-' . date('Y') . '-' . sprintf('%04d', $count),
            'title' => $validated['title'],
            'grade' => $validated['grade'],
            'issue_date' => $validated['issue_date'],
            'verification_code' => 'VERIF-' . strtoupper(Str::random(12)),
            'status' => 'issued',
            'issued_by' => auth()->id() ?? 1,
        ]);

        return redirect()->route('certificates.show', $cert)->with('success', 'Certificate issued successfully.');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load(['student', 'course', 'branch.institute.company']);
        return view('certificates.show', compact('certificate'));
    }

    public function verify($code)
    {
        $certificate = Certificate::with(['student', 'course', 'branch.institute.company'])
            ->where('verification_code', $code)
            ->first();

        return view('certificates.verify', compact('certificate', 'code'));
    }
}

