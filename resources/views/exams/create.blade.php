@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Schedule New Examination</h1>
            <p class="text-xs text-slate-500 mt-1">Configure offline written paper, lab test, or CBT online quiz.</p>
        </div>
        <a href="{{ route('exams.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Exams</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('exams.store') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Campus / Branch <span class="text-rose-500">*</span></label>
                    <select name="branch_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Course Program <span class="text-rose-500">*</span></label>
                    <select name="course_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Target Batch <span class="text-rose-500">*</span></label>
                    <select name="batch_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Assessment Subject</label>
                    <select name="subject_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                        <option value="">-- Comprehensive Course Exam --</option>
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}">{{ $subj->name }} ({{ $subj->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1.5">Examination Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Mid-Term Practical Evaluation on Laravel & MySQL" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-medium">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Assessment Delivery Mode <span class="text-rose-500">*</span></label>
                    <select name="exam_type" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                        <option value="offline">Offline Written / Laboratory Paper</option>
                        <option value="online">Online CBT Quiz (MCQ)</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Duration (Minutes) <span class="text-rose-500">*</span></label>
                    <input type="number" name="duration_minutes" value="90" required min="10" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Start Date & Time <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="start_time" value="{{ date('Y-m-d\TH:i', strtotime('+2 days 10:00')) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">End Date & Time <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="end_time" value="{{ date('Y-m-d\TH:i', strtotime('+2 days 11:30')) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Maximum Marks Scheme <span class="text-rose-500">*</span></label>
                    <input type="number" name="total_marks" value="100" required min="10" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-900">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Minimum Passing Cut-off Marks <span class="text-rose-500">*</span></label>
                    <input type="number" name="passing_marks" value="40" required min="1" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-rose-600">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1.5">Exam Rules & Candidate Instructions</label>
                    <textarea name="instructions" rows="2" placeholder="Strictly no electronic devices. Bring admit slip and ID card..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2"></textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('exams.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-semibold shadow-md shadow-brand-600/30 transition">
                    Schedule Examination
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

