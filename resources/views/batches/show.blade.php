@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('batches.index') }}" class="hover:text-slate-600">Batches</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-slate-700 font-mono">{{ $batch->code }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                {{ $batch->name }}
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 uppercase">
                    {{ $batch->status }}
                </span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.index', ['batch_id' => $batch->id]) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md shadow-emerald-600/30 transition">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>Mark Attendance</span>
            </a>
        </div>
    </div>

    <!-- 4 Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Course Program</span>
            <div class="text-base font-bold text-slate-900 mt-1">{{ $batch->course->name }}</div>
            <span class="text-xs text-brand-600 font-medium">{{ $batch->branch->name }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Class Timings & Room</span>
            <div class="text-base font-bold text-slate-900 mt-1">
                {{ date('h:i A', strtotime($batch->start_time)) }} - {{ date('h:i A', strtotime($batch->end_time)) }}
            </div>
            <span class="text-xs text-slate-500">{{ $batch->classroom->room_name ?? 'Virtual Online Hub' }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Faculty Mentor</span>
            <div class="text-base font-bold text-slate-900 mt-1">{{ $batch->mentor->name ?? 'Lead Faculty' }}</div>
            <span class="text-xs text-slate-400 font-mono">{{ $batch->mentor->phone ?? '' }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Cohort Capacity</span>
            <div class="text-base font-bold text-slate-900 mt-1">
                {{ $batch->students->count() }} / {{ $batch->capacity }} Enrolled
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1.5">
                <div class="bg-brand-600 h-full" style="width: {{ min(100, ($batch->students->count() / max(1, $batch->capacity)) * 100) }}%"></div>
            </div>
        </div>
    </div>

    <!-- 2 Column Layout: Timetable (Left) & Enroll Student Form (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2-Cols: Timetable Schedule -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900">Weekly Lecture Timetable</h2>
                    <p class="text-xs text-slate-400">Scheduled classroom days, subjects and lecturers</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Day of Week</th>
                                <th class="px-4 py-3 font-semibold">Subject</th>
                                <th class="px-4 py-3 font-semibold">Instructor</th>
                                <th class="px-4 py-3 font-semibold">Time Slot</th>
                                <th class="px-4 py-3 font-semibold">Classroom</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($batch->timetables as $tt)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $tt->day_of_week }}</td>
                                <td class="px-4 py-3 font-semibold text-brand-700">{{ $tt->subject->name ?? 'Subject' }}</td>
                                <td class="px-4 py-3">{{ $tt->faculty->name ?? 'Faculty' }}</td>
                                <td class="px-4 py-3 font-mono text-slate-600">
                                    {{ date('h:i A', strtotime($tt->start_time)) }} - {{ date('h:i A', strtotime($tt->end_time)) }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $tt->room_or_link ?? 'Lab Hall' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No timetable slots scheduled yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Enrolled Students Roster -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Enrolled Students Roster</h2>
                        <p class="text-xs text-slate-400">Total of {{ $batch->students->count() }} active candidates in this cohort</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Roll No</th>
                                <th class="px-4 py-3 font-semibold">Student Name</th>
                                <th class="px-4 py-3 font-semibold">Admission No</th>
                                <th class="px-4 py-3 font-semibold">Contact</th>
                                <th class="px-4 py-3 font-semibold">Enrolled Date</th>
                                <th class="px-4 py-3 font-semibold text-right">Profile</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($batch->students as $st)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $st->roll_no }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $st->full_name }}</td>
                                <td class="px-4 py-3 font-mono text-brand-700">{{ $st->admission_no }}</td>
                                <td class="px-4 py-3 font-mono text-slate-500">{{ $st->phone }}</td>
                                <td class="px-4 py-3 font-mono text-slate-500">{{ date('d M Y', strtotime($st->pivot->enrolled_at)) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('students.show', $st) }}" class="text-brand-600 hover:text-brand-800 font-semibold text-[11px]">
                                        View Profile
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No students currently enrolled in this batch.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right 1-Col: Quick Enroll Form -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-brand-600"></i>
                    <span>Enroll Student to Batch</span>
                </h2>
                <p class="text-xs text-slate-500 mb-4">Attach an existing registered student into this cohort.</p>

                <form action="{{ route('batches.enroll', $batch) }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Select Candidate Student</label>
                        <select name="student_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            <option value="">-- Choose Candidate --</option>
                            @foreach($allStudents as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->full_name }} ({{ $candidate->admission_no }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-semibold shadow-xs transition">
                        Attach to Batch Cohort
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection

