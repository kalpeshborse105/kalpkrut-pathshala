@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- WELCOME BANNER -->
    <div class="bg-gradient-to-r from-indigo-900 via-purple-950 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/10 text-white font-extrabold text-2xl flex items-center justify-center border border-white/20 shadow-inner">
                <i class="fa-solid fa-headset text-purple-400"></i>
            </div>
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-400 text-slate-950 mb-1">
                    Counselling & CRM Desk
                </span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Welcome, {{ auth()->user()->name }}</h1>
                <p class="text-xs text-purple-200 mt-0.5">
                    {{ auth()->user()->designation ?? 'Senior Academic Counsellor' }} &bull; Kalpkrut Pathshala Student Acquisition
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('enquiries.create') }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>Register New Lead</span>
            </a>
            <a href="{{ route('admissions.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Enrol Admission</span>
            </a>
        </div>
    </div>

    <!-- 4 COUNSELLOR METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">My Assigned Leads</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $totalAssignedLeads }} Leads</div>
                <div class="text-[11px] text-slate-400 mt-1">In my counselling queue</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-funnel-dollar"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Follow-ups Due Today</span>
                <div class="text-2xl font-bold text-rose-600 mt-1">{{ $followupsDueToday }} Urgent</div>
                <div class="text-[11px] text-rose-500 font-semibold mt-1">Scheduled calls/visits</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-phone-volume"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Converted Admissions</span>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $convertedCount }} Students</div>
                <div class="text-[11px] text-slate-400 mt-1">Enrolled candidates</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Conversion Velocity</span>
                <div class="text-2xl font-bold text-brand-700 mt-1">{{ $conversionRate }}%</div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-1">High conversion rate</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- PIPELINE STATUS BREAKDOWN CHIPS -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <h2 class="text-sm font-bold text-slate-900 mb-3">My Inquiries Pipeline Distribution</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach(['new', 'contacted', 'counselling_scheduled', 'demo_attended', 'converted', 'dropped'] as $st)
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-center">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ str_replace('_', ' ', $st) }}</span>
                <span class="text-lg font-bold text-slate-900 font-mono">{{ $leadPipeline[$st] ?? 0 }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- MY RECENT ASSIGNED LEADS -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">My Priority Inquiries Queue</h2>
                <p class="text-xs text-slate-400">Prospective students awaiting counselling or follow-up</p>
            </div>
            <a href="{{ route('enquiries.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">Browse all leads</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Enquiry No</th>
                        <th class="px-4 py-3 font-semibold">Candidate Student</th>
                        <th class="px-4 py-3 font-semibold">Interested Course</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Next Follow-up</th>
                        <th class="px-4 py-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($recentLeads as $enq)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $enq->enquiry_no }}</td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">{{ $enq->student_name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $enq->phone }}</div>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $enq->course->name ?? 'General' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 uppercase">
                                {{ str_replace('_', ' ', $enq->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-amber-600 font-bold">
                            {{ $enq->next_follow_up_date ? date('d M Y', strtotime($enq->next_follow_up_date)) : 'Today' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('enquiries.show', $enq) }}" class="px-2.5 py-1 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded font-semibold text-[11px]">
                                Follow-up
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No leads currently assigned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection


