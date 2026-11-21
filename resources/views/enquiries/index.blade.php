@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Enquiries & Lead Management CRM</h1>
            <p class="text-xs text-slate-500 mt-1">Track prospective student inquiries, counsellor follow-ups, and conversion funnel.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('enquiries.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
                <i class="fa-solid fa-plus"></i>
                <span>Register New Lead</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('enquiries.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search student name, phone, enquiry no..."
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

            <div>
                <select name="status" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="counselling_scheduled" {{ request('status') == 'counselling_scheduled' ? 'selected' : '' }}>Counselling Scheduled</option>
                    <option value="demo_attended" {{ request('status') == 'demo_attended' ? 'selected' : '' }}>Demo Attended</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted (Admitted)</option>
                    <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                </select>
            </div>

            <div>
                <select name="course_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2 rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('enquiries.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Enquiries Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Enquiry No</th>
                        <th class="px-4 py-3 font-semibold">Student / Contact</th>
                        <th class="px-4 py-3 font-semibold">Interested Course</th>
                        <th class="px-4 py-3 font-semibold">Lead Source</th>
                        <th class="px-4 py-3 font-semibold">Assigned Counsellor</th>
                        <th class="px-4 py-3 font-semibold">Status & Priority</th>
                        <th class="px-4 py-3 font-semibold">Next Follow-up</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($enquiries as $enq)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3 font-mono font-semibold text-slate-900">
                            {{ $enq->enquiry_no }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $enq->student_name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $enq->phone }}</div>
                        </td>
                        <td class="px-4 py-3 font-medium">
                            {{ $enq->course->name ?? 'General Inquiry' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-[11px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                                <i class="fa-solid fa-tag text-[9px] text-slate-400"></i>
                                {{ $enq->source->name ?? 'Direct' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            {{ $enq->assignedTo->name ?? 'Unassigned' }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badgeClass = match($enq->status) {
                                    'converted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'counselling_scheduled' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'demo_attended' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'dropped' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-amber-50 text-amber-700 border-amber-200'
                                };
                            @endphp
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeClass }} capitalize">
                                    {{ str_replace('_', ' ', $enq->status) }}
                                </span>
                                @if($enq->priority === 'urgent' || $enq->priority === 'high')
                                    <span class="text-[9px] font-extrabold text-rose-600 bg-rose-50 px-1 py-0.2 rounded border border-rose-200 uppercase">
                                        {{ $enq->priority }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px]">
                            @if($enq->next_follow_up_date)
                                <span class="{{ strtotime($enq->next_follow_up_date) < time() ? 'text-amber-600 font-bold' : 'text-slate-600' }}">
                                    {{ date('d M Y', strtotime($enq->next_follow_up_date)) }}
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('enquiries.show', $enq) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded-lg font-semibold transition text-[11px]">
                                <span>Details</span>
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                            No enquiry records found matching current criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $enquiries->links() }}
        </div>
    </div>

</div>
@endsection

