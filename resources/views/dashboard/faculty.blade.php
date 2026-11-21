@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- WELCOME BANNER -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-brand-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/10 text-white font-extrabold text-2xl flex items-center justify-center border border-white/20 shadow-inner">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-brand-400 text-slate-950 mb-1">
                    Faculty Academic Console
                </span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Welcome, {{ auth()->user()->name }}</h1>
                <p class="text-xs text-indigo-200 mt-0.5">
                    {{ auth()->user()->designation ?? 'Senior Faculty Specialist' }} &bull; Kalpkrut Pathshala Faculty Board
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('attendance.index') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-clipboard-user"></i>
                <span>Mark Attendance</span>
            </a>
            <a href="{{ route('exams.create') }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Schedule Exam</span>
            </a>
        </div>
    </div>

    <!-- 4 FACULTY METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">My Active Cohorts</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $batches->count() }} Batches</div>
                <div class="text-[11px] text-slate-400 mt-1">Lead instructor assignment</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-users-rectangle"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Enrolled Candidates</span>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalStudentsUnderMe }} Students</div>
                <div class="text-[11px] text-slate-400 mt-1">Across assigned batches</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Today's Lectures</span>
                <div class="text-2xl font-bold text-brand-700 mt-1">{{ $todaysClasses->count() }} Slots</div>
                <div class="text-[11px] text-slate-400 mt-1 font-mono">{{ $todayDay }} schedule</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Assignments Published</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $myAssignments->count() }} Active</div>
                <div class="text-[11px] text-slate-400 mt-1">Homework tasks</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-tasks"></i>
            </div>
        </div>
    </div>

    <!-- TODAY'S SCHEDULE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Today's Teaching Schedule ({{ $todayDay }})</h2>
                <p class="text-xs text-slate-400">Classroom sessions and laboratory slots</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Time Slot</th>
                        <th class="px-4 py-3 font-semibold">Subject Module</th>
                        <th class="px-4 py-3 font-semibold">Target Batch</th>
                        <th class="px-4 py-3 font-semibold">Classroom</th>
                        <th class="px-4 py-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($todaysClasses as $tt)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-mono font-bold text-brand-700">
                            {{ date('h:i A', strtotime($tt->start_time)) }} - {{ date('h:i A', strtotime($tt->end_time)) }}
                        </td>
                        <td class="px-4 py-3 font-bold text-slate-900">{{ $tt->subject->name ?? 'Curriculum' }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $tt->batch->name ?? 'Batch' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $tt->classroom->room_name ?? 'Virtual Room' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('attendance.index', ['batch_id' => $tt->batch_id]) }}" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded font-semibold text-[11px]">
                                Mark Register
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No scheduled lectures for today ({{ $todayDay }}).</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MY ASSIGNED BATCHES -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">My Supervised Cohorts & Batches</h2>
            </div>
            <a href="{{ route('batches.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">Browse all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Batch Name & Code</th>
                        <th class="px-4 py-3 font-semibold">Course</th>
                        <th class="px-4 py-3 font-semibold">Classroom</th>
                        <th class="px-4 py-3 font-semibold">Enrolled Candidates</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($batches as $batch)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-bold text-slate-900">
                            {{ $batch->name }}
                            <div class="text-[11px] text-slate-400 font-mono">{{ $batch->code }}</div>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $batch->course->name ?? 'Course' }}</td>
                        <td class="px-4 py-3">{{ $batch->classroom->room_name ?? 'Virtual Studio' }}</td>
                        <td class="px-4 py-3 font-bold font-mono">{{ $batch->students->count() }} Students</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('attendance.index', ['batch_id' => $batch->id]) }}" class="px-2.5 py-1 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded font-semibold text-[11px]">
                                Attendance
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No cohorts assigned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection


