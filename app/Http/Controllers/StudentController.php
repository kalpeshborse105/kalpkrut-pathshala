<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Student, Branch, Batch, Course};

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['branch', 'batches', 'latestAdmission.course'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('admission_no', 'like', "%{$s}%")
                  ->orWhere('roll_no', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('batch_id')) {
            $batchId = $request->batch_id;
            $query->whereHas('batches', function($q) use ($batchId) {
                $q->where('batches.id', $batchId);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->paginate(15)->withQueryString();
        $branches = Branch::all();
        $batches = Batch::where('status', 'active')->get();

        return view('students.index', compact('students', 'branches', 'batches'));
    }

    public function show(Student $student)
    {
        $student->load([
            'branch', 'user', 'batches.course', 'batches.mentor',
            'admissions.course', 'admissions.batch',
            'attendances.subject',
            'feeDues.payments',
            'payments.feeDue',
            'examAttempts.exam.subject',
            'assignmentSubmissions.assignment',
            'certificates.course'
        ]);

        $totalClasses = $student->attendances->count();
        $presentClasses = $student->attendances->where('status', 'present')->count();
        $attendancePercentage = $totalClasses > 0 ? round(($presentClasses / $totalClasses) * 100, 1) : 0;

        $totalFeeAssigned = $student->feeDues->sum('net_amount');
        $totalFeePaid = $student->payments->sum('amount');
        $totalFeeBalance = max(0, $totalFeeAssigned - $totalFeePaid);

        return view('students.show', compact('student', 'attendancePercentage', 'totalFeeAssigned', 'totalFeePaid', 'totalFeeBalance'));
    }

    public function edit(Student $student)
    {
        $branches = Branch::all();
        return view('students.edit', compact('student', 'branches'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:191',
            'current_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'father_name' => 'nullable|string|max:191',
            'father_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,completed,suspended,dropped,alumni',
        ]);

        $student->update($validated);

        if ($student->user) {
            $student->user->update([
                'name' => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
        }

        return redirect()->route('students.show', $student)->with('success', 'Student details updated successfully.');
    }
}

