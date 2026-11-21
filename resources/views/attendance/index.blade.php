@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{
    markAll(status) {
        document.querySelectorAll('input[type=radio][value=' + status + ']').forEach(r => r.checked = true);
    }
}">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Daily Student Attendance Register</h1>
            <p class="text-xs text-slate-500 mt-1">Record and monitor attendance, late check-ins, and absences for batch sessions.</p>
        </div>
    </div>

    <!-- Batch & Date Selector Form -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Select Class Batch</label>
                <select name="batch_id" onchange="this.form.submit()" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none font-medium">
                    @foreach($batches as $b)
                        <option value="{{ $b->id }}" {{ $batch && $batch->id == $b->id ? 'selected' : '' }}>
                            {{ $b->name }} ({{ $b->classroom->room_name ?? 'Room' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Attendance Date</label>
                <input type="date" name="attendance_date" value="{{ $selectedDate }}" onchange="this.form.submit()" 
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2.5 rounded-xl transition">
                    Load Attendance Register
                </button>
            </div>
        </form>
    </div>

    @if($batch)
    <!-- Main Attendance Register Form -->
    <form action="{{ route('attendance.store') }}" method="POST">
        @csrf
        <input type="hidden" name="batch_id" value="{{ $batch->id }}">
        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Header bar with Quick Action Buttons -->
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">
                        {{ $batch->name }} &bull; <span class="font-mono text-brand-700">{{ date('l, d M Y', strtotime($selectedDate)) }}</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Total registered candidates in cohort: {{ $batch->students->count() }}</p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">Quick Mark:</span>
                    <button type="button" @click="markAll('present')" class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold transition">
                        All Present
                    </button>
                    <button type="button" @click="markAll('absent')" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition">
                        All Absent
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Roll No</th>
                            <th class="px-4 py-3 font-semibold">Candidate Student</th>
                            <th class="px-4 py-3 font-semibold text-center">Attendance Status</th>
                            <th class="px-4 py-3 font-semibold">Remarks / Late Arrival Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($batch->students as $student)
                        @php
                            $currentStatus = isset($attendances[$student->id]) ? $attendances[$student->id]->status : 'present';
                            $currentRemark = isset($attendances[$student->id]) ? $attendances[$student->id]->remarks : '';
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $student->roll_no }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $student->full_name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $student->admission_no }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-3">
                                    <label class="flex items-center gap-1.5 cursor-pointer bg-emerald-50/50 hover:bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="present" {{ $currentStatus === 'present' ? 'checked' : '' }} class="w-3.5 h-3.5 text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-xs font-bold text-emerald-700">Present</span>
                                    </label>

                                    <label class="flex items-center gap-1.5 cursor-pointer bg-amber-50/50 hover:bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }} class="w-3.5 h-3.5 text-amber-600 focus:ring-amber-500">
                                        <span class="text-xs font-bold text-amber-700">Late</span>
                                    </label>

                                    <label class="flex items-center gap-1.5 cursor-pointer bg-rose-50/50 hover:bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }} class="w-3.5 h-3.5 text-rose-600 focus:ring-rose-500">
                                        <span class="text-xs font-bold text-rose-700">Absent</span>
                                    </label>

                                    <label class="flex items-center gap-1.5 cursor-pointer bg-slate-100 hover:bg-slate-200/70 px-2.5 py-1 rounded-lg border border-slate-200">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="excused" {{ $currentStatus === 'excused' ? 'checked' : '' }} class="w-3.5 h-3.5 text-slate-600 focus:ring-slate-500">
                                        <span class="text-xs font-bold text-slate-700">Excused</span>
                                    </label>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="remarks[{{ $student->id }}]" value="{{ $currentRemark }}" placeholder="Optional notes (traffic, doctor appointment...)" 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 text-xs focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                No students enrolled in this batch yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($batch->students->count() > 0)
            <div class="p-5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <span class="text-xs text-slate-500">Updates will be logged in student biometric audit logs.</span>
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
                    Save Daily Attendance Register
                </button>
            </div>
            @endif
        </div>
    </form>
    @endif

</div>
@endsection

