@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('courses.index') }}" class="hover:text-slate-600">Courses</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-slate-700 font-mono">{{ $course->code }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                {{ $course->name }}
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                    {{ $course->category->name ?? 'General' }}
                </span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('batches.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
                <i class="fa-solid fa-plus"></i>
                <span>Launch New Batch</span>
            </a>
        </div>
    </div>

    <!-- 3 Quick Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Duration</span>
            <div class="text-xl font-bold text-slate-900 mt-1">{{ $course->duration_value }} {{ $course->duration_unit }}</div>
            <span class="text-xs text-brand-600 capitalize font-medium">{{ $course->delivery_mode }} delivery</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Course Tuition Fee</span>
            <div class="text-xl font-bold text-slate-900 mt-1">₹{{ number_format($course->total_fee, 2) }}</div>
            <span class="text-xs text-slate-400">Base: ₹{{ number_format($course->base_fee) }} + {{ $course->gst_percentage }}% GST</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Syllabus Subjects</span>
            <div class="text-xl font-bold text-slate-900 mt-1">{{ $course->subjects->count() }} Subjects</div>
            <span class="text-xs text-slate-400">Master Curriculum</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Batches</span>
            <div class="text-xl font-bold text-brand-700 mt-1">{{ $course->batches->count() }} Batches</div>
            <span class="text-xs text-emerald-600 font-medium">Currently in session</span>
        </div>
    </div>

    <!-- 2 Column Layout: Subjects Syllabus (Left) & Add Subject / Fee Structure (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2-Cols: Subjects Syllabus -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Curriculum Subjects & Practical Modules</h2>
                        <p class="text-xs text-slate-400">Subjects taught sequentially across the program</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($course->subjects as $subj)
                    <div class="p-4 hover:bg-slate-50/70 transition flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center shrink-0">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">{{ $subj->name }}</h3>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                    Code: {{ $subj->code }} &bull; Total: {{ $subj->total_hours }} Hours
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($subj->is_practical)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    Practical Lab
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                    Theory
                                </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-slate-400 text-xs italic">
                        No subjects added to curriculum yet. Use the form on the right to add.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Running Batches for this course -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900">Associated Course Batches</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Batch Name</th>
                                <th class="px-4 py-3 font-semibold">Timings</th>
                                <th class="px-4 py-3 font-semibold">Classroom</th>
                                <th class="px-4 py-3 font-semibold">Mode</th>
                                <th class="px-4 py-3 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($course->batches as $batch)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 font-semibold text-brand-700">{{ $batch->name }}</td>
                                <td class="px-4 py-3 font-mono text-slate-500">
                                    {{ date('h:i A', strtotime($batch->start_time)) }} - {{ date('h:i A', strtotime($batch->end_time)) }}
                                </td>
                                <td class="px-4 py-3">{{ $batch->classroom->room_name ?? 'Classroom' }}</td>
                                <td class="px-4 py-3 uppercase font-semibold text-slate-600">{{ $batch->delivery_mode }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('batches.show', $batch) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded text-[11px] font-semibold">
                                        Open
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right 1-Col: Add Subject Form & Fee Schedule -->
        <div class="space-y-6">

            <!-- Add Subject Form -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-brand-600"></i>
                    <span>Add Subject to Syllabus</span>
                </h2>

                <form action="{{ route('courses.subjects.store', $course) }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Subject Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. Relational Database Modeling" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Subject Code <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" required placeholder="e.g. RDBMS-204" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Total Curriculum Hours</label>
                        <input type="number" name="total_hours" value="40" required min="1" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_practical" value="1" id="is_practical" class="w-4 h-4 text-brand-600 rounded">
                        <label for="is_practical" class="font-medium text-slate-700 cursor-pointer">Hands-on Practical / Lab Component</label>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-semibold shadow-xs transition">
                        Add to Course Syllabus
                    </button>
                </form>
            </div>

            <!-- Default Fee Installment Plan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-3">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-emerald-600"></i>
                    <span>Default Installment Plan</span>
                </h2>
                @if($course->feeStructures->first())
                    <div class="space-y-2 text-xs">
                        @foreach($course->feeStructures->first()->installments as $inst)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <div>
                                <strong class="text-slate-800">{{ $inst->name }}</strong>
                                <span class="block text-[11px] text-slate-400">Due: {{ $inst->due_days_offset }} days after admission</span>
                            </div>
                            <div class="text-right">
                                <strong class="text-emerald-600 font-bold">₹{{ number_format($inst->amount, 2) }}</strong>
                                <span class="block text-[10px] text-slate-400">({{ $inst->percentage }}%)</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection

