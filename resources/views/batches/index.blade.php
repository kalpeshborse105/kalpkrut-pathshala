@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Batches & Scheduling Management</h1>
            <p class="text-xs text-slate-500 mt-1">Manage active class cohorts, schedules, faculty allocations, and student rosters.</p>
        </div>
        <a href="{{ route('batches.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Create New Batch</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('batches.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <select name="course_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
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
                <select name="status" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2 rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('batches.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Batches Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Batch Name & Code</th>
                        <th class="px-4 py-3 font-semibold">Program</th>
                        <th class="px-4 py-3 font-semibold">Campus & Room</th>
                        <th class="px-4 py-3 font-semibold">Primary Mentor</th>
                        <th class="px-4 py-3 font-semibold">Timings</th>
                        <th class="px-4 py-3 font-semibold">Enrolled / Cap</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($batches as $batch)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3">
                            <a href="{{ route('batches.show', $batch) }}" class="font-bold text-slate-900 hover:text-brand-600">
                                {{ $batch->name }}
                            </a>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $batch->code }}</div>
                        </td>
                        <td class="px-4 py-3 font-medium text-brand-700">{{ $batch->course->name ?? 'Course' }}</td>
                        <td class="px-4 py-3">
                            <div class="text-slate-800 font-medium">{{ $batch->classroom->room_name ?? 'Classroom' }}</div>
                            <span class="text-[10px] text-slate-500 font-medium">{{ $batch->branch->name ?? 'Campus' }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $batch->mentor->name ?? 'Faculty Member' }}</td>
                        <td class="px-4 py-3 font-mono text-slate-600">
                            {{ date('h:i A', strtotime($batch->start_time)) }} - {{ date('h:i A', strtotime($batch->end_time)) }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">{{ $batch->students->count() }} / {{ $batch->capacity }}</div>
                            <div class="w-20 bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1">
                                <div class="bg-brand-600 h-full" style="width: {{ min(100, ($batch->students->count() / max(1, $batch->capacity)) * 100) }}%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                {{ $batch->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('batches.show', $batch) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded-lg font-semibold transition text-[11px]">
                                <span>Roster</span>
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                            No batches found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $batches->links() }}
        </div>
    </div>

</div>
@endsection

