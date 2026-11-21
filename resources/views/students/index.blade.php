@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Student Directory</h1>
            <p class="text-xs text-slate-500 mt-1">Directory of all 100 enrolled students across active programs and batches.</p>
        </div>
        <a href="{{ route('admissions.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
            <i class="fa-solid fa-user-plus"></i>
            <span>Admit New Student</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('students.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, roll no, admission no, phone..."
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

            <div>
                <select name="branch_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="">All Campuses</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="batch_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="">All Batches</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2 rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('students.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Student Name & Roll</th>
                        <th class="px-4 py-3 font-semibold">Admission No</th>
                        <th class="px-4 py-3 font-semibold">Contact Info</th>
                        <th class="px-4 py-3 font-semibold">Campus</th>
                        <th class="px-4 py-3 font-semibold">Course Program</th>
                        <th class="px-4 py-3 font-semibold">Active Batch</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($students as $st)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-50 text-brand-700 font-bold flex items-center justify-center shrink-0 border border-brand-200">
                                    {{ substr($st->first_name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('students.show', $st) }}" class="font-bold text-slate-900 hover:text-brand-600">
                                        {{ $st->full_name }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 font-mono">Roll: {{ $st->roll_no ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-mono font-semibold text-slate-800">
                            {{ $st->admission_no }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-mono text-slate-900">{{ $st->phone }}</div>
                            <div class="text-[11px] text-slate-400">{{ $st->email }}</div>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $st->branch->name ?? 'Campus' }}</td>
                        <td class="px-4 py-3 font-medium text-brand-700">
                            {{ $st->latestAdmission->course->name ?? 'Course' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($st->batches->first())
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">
                                    {{ $st->batches->first()->name }}
                                </span>
                            @else
                                <span class="text-slate-400 text-[11px] italic">No batch</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                {{ $st->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('students.show', $st) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded-lg font-semibold transition text-[11px]">
                                <span>360° Profile</span>
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                            No student records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $students->links() }}
        </div>
    </div>

</div>
@endsection

