@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Admissions & Enrolments</h1>
            <p class="text-xs text-slate-500 mt-1">Official register of confirmed student admissions and academic contracts.</p>
        </div>
        <a href="{{ route('admissions.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>New Student Admission</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admissions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search admission no, student, phone..."
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

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

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2 rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('admissions.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Admissions Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Admission No</th>
                        <th class="px-4 py-3 font-semibold">Enrolled Student</th>
                        <th class="px-4 py-3 font-semibold">Course & Batch</th>
                        <th class="px-4 py-3 font-semibold">Campus</th>
                        <th class="px-4 py-3 font-semibold">Agreed Fee</th>
                        <th class="px-4 py-3 font-semibold">Admission Date</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($admissions as $adm)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3 font-mono font-semibold text-brand-700">
                            {{ $adm->admission_no }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $adm->student->full_name ?? 'Student' }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $adm->student->phone ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $adm->course->name ?? 'Course' }}</div>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $adm->batch->name ?? 'Allocated Batch' }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $adm->branch->name ?? 'Campus' }}</td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">₹{{ number_format($adm->final_agreed_fee, 2) }}</div>
                            @if($adm->discount_amount > 0)
                                <span class="text-[10px] text-emerald-600 font-medium">Waived: ₹{{ number_format($adm->discount_amount) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px] text-slate-500">
                            {{ date('d M Y', strtotime($adm->admission_date)) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                {{ $adm->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admissions.show', $adm) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded-lg font-semibold transition text-[11px]">
                                <span>Overview</span>
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                            No admission records found matching current query.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $admissions->links() }}
        </div>
    </div>

</div>
@endsection

