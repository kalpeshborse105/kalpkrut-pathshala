@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('exams.index') }}" class="hover:text-slate-600">Examinations</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-slate-700 font-mono">{{ $exam->exam_code }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                {{ $exam->title }}
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 uppercase">
                    {{ str_replace('_', ' ', $exam->status) }}
                </span>
            </h1>
        </div>
    </div>

    <!-- 4 Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Course & Batch</span>
            <div class="text-sm font-bold text-slate-900 mt-1">{{ $exam->course->name }}</div>
            <span class="text-xs text-brand-700 font-medium">{{ $exam->batch->name ?? 'All Batches' }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Scheduled Timing</span>
            <div class="text-sm font-bold text-slate-900 mt-1 font-mono">
                {{ date('d M Y, h:i A', strtotime($exam->start_time)) }}
            </div>
            <span class="text-xs text-slate-500">Duration: {{ $exam->duration_minutes }} Mins</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Marks & Pass Scheme</span>
            <div class="text-xl font-bold text-slate-900 mt-1">{{ $exam->total_marks }} Total Marks</div>
            <span class="text-xs text-rose-600 font-medium">Cutoff: {{ $exam->passing_marks }} Marks</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Evaluated Attempts</span>
            <div class="text-xl font-bold text-brand-700 mt-1">{{ $exam->attempts->count() }} Candidates</div>
            <span class="text-xs text-emerald-600 font-medium">Results published</span>
        </div>
    </div>

    <!-- Question Bank Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Curated Question Paper / Question Bank</h2>
                <p class="text-xs text-slate-400">Total of {{ $exam->questions->count() }} questions assigned</p>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($exam->questions as $q)
            <div class="p-5 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-brand-700">Question #{{ $loop->iteration }}</span>
                    <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">{{ $q->marks }} Marks</span>
                </div>
                <p class="font-semibold text-slate-900">{{ $q->question_text }}</p>
                <div class="grid grid-cols-2 gap-2 text-slate-600 pt-1">
                    <div class="p-2 rounded bg-slate-50 border border-slate-100">A) {{ $q->option_a }}</div>
                    <div class="p-2 rounded bg-slate-50 border border-slate-100">B) {{ $q->option_b }}</div>
                    <div class="p-2 rounded bg-slate-50 border border-slate-100">C) {{ $q->option_c }}</div>
                    <div class="p-2 rounded bg-slate-50 border border-slate-100">D) {{ $q->option_d }}</div>
                </div>
                <div class="text-[11px] text-emerald-700 font-medium pt-1">
                    Correct Answer: <strong>Option {{ $q->correct_answer }}</strong> &bull; {{ $q->explanation }}
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs italic">
                No question items in question bank. Written physical answer sheets used.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Marks Entry & Evaluation Form -->
    <form action="{{ route('exams.marks.store', $exam) }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Student Evaluation & Scorecard Ledger</h2>
                    <p class="text-xs text-slate-400">Enter marks obtained by cohort candidates out of {{ $exam->total_marks }}</p>
                </div>
                <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                    Save Marks & Publish Results
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Roll No</th>
                            <th class="px-4 py-3 font-semibold">Student Name</th>
                            <th class="px-4 py-3 font-semibold">Marks Obtained (Max: {{ $exam->total_marks }})</th>
                            <th class="px-4 py-3 font-semibold">Current Score %</th>
                            <th class="px-4 py-3 font-semibold">Grade</th>
                            <th class="px-4 py-3 font-semibold">Faculty Remark</th>
                            <th class="px-4 py-3 font-semibold text-right">Report Card</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @php
                            $attemptsMap = $exam->attempts->keyBy('student_id');
                        @endphp
                        @forelse($exam->batch->students as $st)
                        @php
                            $attempt = $attemptsMap[$st->id] ?? null;
                            $score = $attempt ? $attempt->marks_obtained : '';
                            $pct = $attempt ? $attempt->percentage : '';
                            $grade = $attempt ? $attempt->grade : '';
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $st->roll_no }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $st->full_name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $st->admission_no }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="marks[{{ $st->id }}]" value="{{ $score }}" step="0.5" min="0" max="{{ $exam->total_marks }}" placeholder="Score" 
                                       class="w-28 bg-slate-50 border border-slate-300 rounded-lg px-3 py-1 font-bold text-slate-900 text-xs">
                            </td>
                            <td class="px-4 py-3 font-bold text-brand-600 font-mono">
                                {{ $pct ? $pct . '%' : '—' }}
                            </td>
                            <td class="px-4 py-3 font-extrabold text-indigo-700">
                                {{ $grade ?: '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="remarks[{{ $st->id }}]" value="{{ $attempt->faculty_remarks ?? '' }}" placeholder="Remarks..." 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 text-xs">
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($attempt)
                                <a href="{{ route('exams.report-card', [$exam, $st]) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded font-semibold text-[11px]">
                                    Scorecard
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">
                                No students found in this batch.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($exam->batch->students->count() > 0)
            <div class="p-5 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
                    Save Marks & Publish Results
                </button>
            </div>
            @endif
        </div>
    </form>

</div>
@endsection

