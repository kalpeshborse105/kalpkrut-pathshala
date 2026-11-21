@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Courses & Academic Programs</h1>
            <p class="text-xs text-slate-500 mt-1">Master curriculum, durations, syllabus modules, and fee schedules.</p>
        </div>
        <a href="{{ route('courses.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Create New Course</span>
        </a>
    </div>

    <!-- Courses Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $c)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between hover:border-brand-500/50 transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-brand-50 text-brand-700 border border-brand-200">
                        {{ $c->category->name ?? 'General' }}
                    </span>
                    <span class="text-xs font-mono font-bold text-slate-400">{{ $c->code }}</span>
                </div>

                <h2 class="text-base font-bold text-slate-900 leading-snug">
                    <a href="{{ route('courses.show', $c) }}" class="hover:text-brand-600">
                        {{ $c->name }}
                    </a>
                </h2>

                <p class="text-xs text-slate-500 line-clamp-2">
                    {{ $c->description }}
                </p>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Duration</span>
                        <span class="font-semibold text-slate-800">{{ $c->duration_value }} {{ $c->duration_unit }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Delivery Mode</span>
                        <span class="font-semibold text-slate-800 capitalize">{{ $c->delivery_mode }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Syllabus Subjects</span>
                        <span class="font-bold text-slate-800">{{ $c->subjects->count() }} Subjects</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Active Batches</span>
                        <span class="font-bold text-brand-700">{{ $c->batches->count() }} Batches</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 block">Standard Fee (GST Incl.)</span>
                    <span class="text-base font-extrabold text-slate-900">₹{{ number_format($c->total_fee, 2) }}</span>
                </div>
                <a href="{{ route('courses.show', $c) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded-lg text-xs font-semibold transition">
                    Syllabus & Batches
                </a>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection

