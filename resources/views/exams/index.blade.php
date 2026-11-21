@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Examinations & Assessments</h1>
            <p class="text-xs text-slate-500 mt-1">Manage assessment scheduling, question bank, marks entry and grading scorecards.</p>
        </div>
        <a href="{{ route('exams.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Schedule Examination</span>
        </a>
    </div>

    <!-- Exams Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Exam Code</th>
                        <th class="px-4 py-3 font-semibold">Assessment Title</th>
                        <th class="px-4 py-3 font-semibold">Course & Batch</th>
                        <th class="px-4 py-3 font-semibold">Subject</th>
                        <th class="px-4 py-3 font-semibold">Date & Time</th>
                        <th class="px-4 py-3 font-semibold">Marks Scheme</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($exams as $exam)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $exam->exam_code }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('exams.show', $exam) }}" class="font-bold text-slate-900 hover:text-brand-600">
                                {{ $exam->title }}
                            </a>
                            <span class="text-[10px] uppercase font-semibold text-indigo-600 bg-indigo-50 px-1.5 py-0.2 rounded ml-1">{{ $exam->exam_type }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $exam->course->name ?? 'Course' }}</div>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $exam->batch->name ?? 'All Batches' }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-700">{{ $exam->subject->name ?? 'Comprehensive' }}</td>
                        <td class="px-4 py-3 font-mono text-[11px] text-slate-500">
                            {{ date('d M Y, h:i A', strtotime($exam->start_time)) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-slate-900">{{ $exam->total_marks }} Marks</span>
                            <span class="text-[10px] text-slate-400 block">Pass: {{ $exam->passing_marks }} Marks</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                {{ str_replace('_', ' ', $exam->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('exams.show', $exam) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded-lg font-semibold transition text-[11px]">
                                <span>Marks Entry</span>
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                            No examinations scheduled yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $exams->links() }}
        </div>
    </div>

</div>
@endsection

