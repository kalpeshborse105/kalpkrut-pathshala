@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Assignments & Homework Projects</h1>
            <p class="text-xs text-slate-500 mt-1">Manage practical lab homework, milestone submissions and student grading.</p>
        </div>
    </div>

    <!-- 2 Column Layout: Assignments List (Left) & Create Assignment Form (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2-Cols: Assignments Table -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Active Course Assignments (20)</h2>
                        <p class="text-xs text-slate-400">Published practical tasks and deadlines</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Assignment Title</th>
                                <th class="px-4 py-3 font-semibold">Batch Cohort</th>
                                <th class="px-4 py-3 font-semibold">Subject</th>
                                <th class="px-4 py-3 font-semibold">Max Marks</th>
                                <th class="px-4 py-3 font-semibold">Due Deadline</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($assignments as $asg)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $asg->title }}</div>
                                    <p class="text-[11px] text-slate-400 line-clamp-1">{{ $asg->description }}</p>
                                </td>
                                <td class="px-4 py-3 font-medium text-brand-700">{{ $asg->batch->name ?? 'Batch' }}</td>
                                <td class="px-4 py-3">{{ $asg->subject->name ?? 'Subject' }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $asg->max_marks }} Marks</td>
                                <td class="px-4 py-3 font-mono text-[11px] text-slate-500">
                                    {{ date('d M Y, h:i A', strtotime($asg->due_datetime)) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                        {{ $asg->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No assignments created yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $assignments->links() }}
                </div>
            </div>
        </div>

        <!-- Right 1-Col: Publish Assignment Form -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-brand-600"></i>
                    <span>Publish New Assignment</span>
                </h2>

                <form action="{{ route('lms.assignments.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Target Batch <span class="text-rose-500">*</span></label>
                        <select name="batch_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            @foreach($batches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Subject Module <span class="text-rose-500">*</span></label>
                        <select name="subject_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Assignment Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required placeholder="e.g. Build RESTful API with Authentication" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Maximum Marks <span class="text-rose-500">*</span></label>
                        <input type="number" name="max_marks" value="50" required min="5" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Submission Due Date & Time <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="due_datetime" value="{{ date('Y-m-d\TH:i', strtotime('+7 days 23:59')) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Task Instructions & Deliverables</label>
                        <textarea name="description" rows="3" placeholder="Provide GitHub repository link and README documentation..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-semibold shadow-xs transition">
                        Publish to Student Portal
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection

