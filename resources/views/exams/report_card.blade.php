@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('exams.show', $exam) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Exam</span>
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Print Scorecard</span>
            </button>
        </div>
    </div>

    <!-- PRINTABLE SCORECARD -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-lg p-8 sm:p-10 space-y-8 text-xs font-sans text-slate-800 relative overflow-hidden" id="printable-scorecard">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b-2 border-slate-900 gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-700 text-white flex items-center justify-center text-2xl font-bold shadow-md">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ $appInstituteName ?? 'Kalpkrut Pathshala' }}</h1>
                    <div class="text-[11px] text-slate-500 font-medium">Department of Examinations & Academic Evaluation</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">Autonomous Assessment Division &bull; Central Campus</div>
                </div>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-slate-900 text-white text-[11px] font-bold uppercase tracking-wider rounded-md">
                    Official Student Scorecard
                </span>
                <div class="text-base font-extrabold text-brand-700 font-mono mt-1.5">{{ $exam->exam_code }}</div>
                <div class="text-[11px] text-slate-500 font-mono">Issued: {{ date('d F Y') }}</div>
            </div>
        </div>

        <!-- Student & Exam Details -->
        <div class="grid grid-cols-2 gap-6 bg-slate-50 p-5 rounded-xl border border-slate-100">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Candidate Particulars</span>
                <div class="text-sm font-bold text-slate-900">{{ $student->full_name }}</div>
                <div class="text-[11px] text-slate-600">Admission No: <strong class="font-mono">{{ $student->admission_no }}</strong></div>
                <div class="text-[11px] text-slate-600">Roll No: <strong class="font-mono">{{ $student->roll_no }}</strong></div>
                <div class="text-[11px] text-slate-600">Campus: <span>{{ $student->branch->name ?? 'Indiranagar' }}</span></div>
            </div>

            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Assessment Specification</span>
                <div class="text-sm font-bold text-brand-700">{{ $exam->title }}</div>
                <div class="text-[11px] text-slate-600">Program: <strong>{{ $exam->course->name ?? 'Course' }}</strong></div>
                <div class="text-[11px] text-slate-600">Subject: <strong>{{ $exam->subject->name ?? 'Course Modules' }}</strong></div>
                <div class="text-[11px] text-slate-600">Conducted On: <span class="font-mono">{{ date('d M Y', strtotime($exam->start_time)) }}</span></div>
            </div>
        </div>

        <!-- Marks Breakdown Table -->
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider">
                    <th class="py-2 font-bold">Assessment Component</th>
                    <th class="py-2 font-bold text-center">Maximum Marks</th>
                    <th class="py-2 font-bold text-center">Passing Cutoff</th>
                    <th class="py-2 font-bold text-center">Marks Obtained</th>
                    <th class="py-2 font-bold text-right">Result</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
                <tr>
                    <td class="py-4 font-semibold text-slate-900">
                        {{ $exam->title }}
                        <span class="block text-[10px] font-normal text-slate-400">{{ $exam->exam_type }} examination paper</span>
                    </td>
                    <td class="py-4 text-center font-bold font-mono">{{ $exam->total_marks }}</td>
                    <td class="py-4 text-center font-mono text-slate-600">{{ $exam->passing_marks }}</td>
                    <td class="py-4 text-center font-bold text-brand-700 text-base font-mono">{{ $attempt->marks_obtained }}</td>
                    <td class="py-4 text-right">
                        <span class="px-2.5 py-0.5 rounded-full font-bold uppercase text-[10px] {{ $attempt->result === 'pass' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $attempt->result }}
                        </span>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-900">
                    <td colspan="3" class="py-3 text-right font-bold uppercase text-slate-600">Overall Score Percentage:</td>
                    <td class="py-3 text-center font-black text-lg text-brand-700 font-mono">{{ $attempt->percentage }}%</td>
                    <td class="py-3 text-right font-black text-lg text-indigo-700">Grade: {{ $attempt->grade }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Evaluator Feedback -->
        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1">
            <span class="font-bold text-slate-700">Faculty & Evaluator Feedback:</span>
            <p class="text-slate-600 italic">"{{ $attempt->faculty_remarks ?? 'Candidate displayed strong practical command of concepts.' }}"</p>
        </div>

        <!-- Grading Scale Legend -->
        <div class="p-3 bg-slate-50 rounded-lg text-[10px] text-slate-500 flex flex-wrap items-center justify-between gap-2 border border-slate-200">
            <span><strong>A+</strong>: 85% & above (Distinction)</span>
            <span><strong>A</strong>: 75% to 84% (Excellent)</span>
            <span><strong>B</strong>: 60% to 74% (First Class)</span>
            <span><strong>C</strong>: 40% to 59% (Pass)</span>
            <span><strong>F</strong>: Below 40% (Fail)</span>
        </div>

        <!-- Signatures -->
        <div class="pt-8 flex items-end justify-between border-t border-slate-100">
            <div class="text-center">
                <div class="w-32 border-b border-slate-400 mb-1"></div>
                <span class="font-bold text-slate-700 text-[11px] block">Course Mentor</span>
                <span class="text-[10px] text-slate-400 uppercase">Faculty Evaluator</span>
            </div>

            <div class="text-center">
                <div class="w-32 border-b border-slate-400 mb-1"></div>
                <span class="font-bold text-slate-700 text-[11px] block">Dr. Rajesh Sharma</span>
                <span class="text-[10px] text-slate-400 uppercase">Controller of Examinations</span>
            </div>
        </div>

    </div>

</div>

<style>
@media print {
    .no-print, header, aside, footer { display: none !important; }
    body { background: white !important; }
    #printable-scorecard { border: none !important; box-shadow: none !important; padding: 0 !important; }
}
</style>
@endsection


