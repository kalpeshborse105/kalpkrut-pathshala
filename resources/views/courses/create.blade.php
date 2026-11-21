@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Create Academic Course</h1>
            <p class="text-xs text-slate-500 mt-1">Define new program curriculum, durations and standard fee structure.</p>
        </div>
        <a href="{{ route('courses.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Courses</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('courses.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Institute Brand <span class="text-rose-500">*</span></label>
                    <select name="institute_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        @foreach($institutes as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Course Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Master in Cloud Computing & DevOps" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Course Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. CLOUD-DEV-301" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Delivery Mode <span class="text-rose-500">*</span></label>
                    <select name="delivery_mode" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="hybrid">Hybrid (Classroom + Online)</option>
                        <option value="offline">100% In-Person Classroom</option>
                        <option value="online">100% Virtual Online</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Duration Value <span class="text-rose-500">*</span></label>
                    <input type="number" name="duration_value" value="6" required min="1" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Duration Unit <span class="text-rose-500">*</span></label>
                    <select name="duration_unit" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="months" selected>Months</option>
                        <option value="weeks">Weeks</option>
                        <option value="days">Days</option>
                        <option value="years">Years</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Base Course Tuition Fee (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" name="base_fee" value="50000" required min="0" step="100" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none font-bold text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">GST Rate Percentage (%) <span class="text-rose-500">*</span></label>
                    <input type="number" name="gst_percentage" value="18.00" required step="0.5" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Course Description & Overview</label>
                    <textarea name="description" rows="3" placeholder="Overview of outcomes, tools taught, industry capstone project..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none"></textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('courses.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
                    Save Course & Initialize Installment Plan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

