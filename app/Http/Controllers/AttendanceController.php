<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{StudentAttendance, Batch, Student, Branch};

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $selectedBatchId = $request->get('batch_id');
        $selectedDate = $request->get('attendance_date', now()->toDateString());

        $batches = Batch::where('status', 'active')->get();
        $batch = $selectedBatchId ? Batch::with('students')->find($selectedBatchId) : $batches->first();

        $attendances = [];
        if ($batch) {
            $attendances = StudentAttendance::where('batch_id', $batch->id)
                ->where('attendance_date', $selectedDate)
                ->get()
                ->keyBy('student_id');
        }

        return view('attendance.index', compact('batches', 'batch', 'selectedDate', 'attendances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'remarks' => 'nullable|array',
        ]);

        $batch = Batch::findOrFail($validated['batch_id']);
        $date = $validated['attendance_date'];

        foreach ($validated['attendance'] as $studentId => $status) {
            $remark = $validated['remarks'][$studentId] ?? null;

            StudentAttendance::updateOrCreate(
                [
                    'batch_id' => $batch->id,
                    'student_id' => $studentId,
                    'attendance_date' => $date,
                ],
                [
                    'branch_id' => $batch->branch_id,
                    'status' => $status,
                    'in_time' => $status === 'late' ? '08:45:00' : ($status === 'present' ? '08:30:00' : null),
                    'remarks' => $remark,
                    'marked_by' => auth()->id() ?? 1,
                ]
            );
        }

        return redirect()->route('attendance.index', [
            'batch_id' => $batch->id,
            'attendance_date' => $date,
        ])->with('success', 'Attendance marked successfully for ' . count($validated['attendance']) . ' students.');
    }
}

