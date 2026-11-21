@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Create Class Batch</h1>
            <p class="text-xs text-slate-500 mt-1">Schedule new batch cohort with room allocation and faculty mentor.</p>
        </div>
        <a href="{{ route('batches.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Batches</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('batches.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Campus / Branch <span class="text-rose-500">*</span></label>
                    <select name="branch_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Course Program <span class="text-rose-500">*</span></label>
                    <select name="course_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Batch Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Full Stack Web Development - Weekend Morning Cohort" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Batch Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. B-WEB-WKND-01" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Delivery Mode <span class="text-rose-500">*</span></label>
                    <select name="delivery_mode" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="offline">Offline In-Person</option>
                        <option value="hybrid">Hybrid</option>
                        <option value="online">Online Virtual</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Start Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Max Student Capacity <span class="text-rose-500">*</span></label>
                    <input type="number" name="capacity" value="35" required min="5" max="200" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Daily Start Time <span class="text-rose-500">*</span></label>
                    <input type="time" name="start_time" value="08:30" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Daily End Time <span class="text-rose-500">*</span></label>
                    <input type="time" name="end_time" value="10:30" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Classroom Physical Room</label>
                    <select name="classroom_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="">-- Virtual / None --</option>
                        @foreach($classrooms as $rm)
                            <option value="{{ $rm->id }}">{{ $rm->room_name }} (Cap: {{ $rm->seating_capacity }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Primary Faculty Mentor</label>
                    <select name="primary_mentor_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="">-- Select Mentor --</option>
                        @foreach($mentors as $m)
                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->designation }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('batches.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
                    Create & Activate Batch Cohort
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

