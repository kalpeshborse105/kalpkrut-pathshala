@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Conversion CTA -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('enquiries.index') }}" class="hover:text-slate-600">Enquiries</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-slate-700 font-mono">{{ $enquiry->enquiry_no }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                {{ $enquiry->student_name }}
                @php
                    $badgeClass = match($enquiry->status) {
                        'converted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'counselling_scheduled' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'demo_attended' => 'bg-purple-50 text-purple-700 border-purple-200',
                        'dropped' => 'bg-rose-50 text-rose-700 border-rose-200',
                        default => 'bg-amber-50 text-amber-700 border-amber-200'
                    };
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }} capitalize">
                    {{ str_replace('_', ' ', $enquiry->status) }}
                </span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            @if($enquiry->status !== 'converted')
                <a href="{{ route('admissions.create', ['enquiry_id' => $enquiry->id]) }}" 
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md shadow-emerald-600/30 transition">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Convert to Admission</span>
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-xs px-3 py-1.5 rounded-xl font-bold">
                    <i class="fa-solid fa-circle-check"></i>
                    Enrolled into Admission
                </span>
            @endif
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Lead Profile Card -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-lg">
                        {{ substr($enquiry->student_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-slate-900">{{ $enquiry->student_name }}</div>
                        <div class="text-xs text-slate-400 font-mono">{{ $enquiry->enquiry_no }}</div>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Primary Phone</span>
                        <a href="tel:{{ $enquiry->phone }}" class="font-semibold text-slate-800 hover:text-brand-600 flex items-center gap-1.5 mt-0.5">
                            <i class="fa-solid fa-phone text-emerald-600 text-[10px]"></i>
                            <span>{{ $enquiry->phone }}</span>
                        </a>
                    </div>

                    @if($enquiry->email)
                    <div>
                        <span class="text-slate-400 block text-[11px]">Email Address</span>
                        <a href="mailto:{{ $enquiry->email }}" class="font-semibold text-slate-800 hover:text-brand-600 flex items-center gap-1.5 mt-0.5">
                            <i class="fa-regular fa-envelope text-slate-400 text-[10px]"></i>
                            <span>{{ $enquiry->email }}</span>
                        </a>
                    </div>
                    @endif

                    @if($enquiry->father_or_guardian_name)
                    <div>
                        <span class="text-slate-400 block text-[11px]">Father / Guardian</span>
                        <span class="font-medium text-slate-700">{{ $enquiry->father_or_guardian_name }}</span>
                    </div>
                    @endif

                    <div>
                        <span class="text-slate-400 block text-[11px]">Campus / Branch</span>
                        <span class="font-semibold text-slate-900">{{ $enquiry->branch->name ?? 'Indiranagar' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px]">Interested Course</span>
                        <span class="font-bold text-brand-700">{{ $enquiry->course->name ?? 'Course' }}</span>
                        <span class="text-slate-500 block text-[10px]">Total Standard Fee: ₹{{ number_format($enquiry->course->total_fee ?? 0) }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px]">Lead Marketing Source</span>
                        <span class="inline-flex items-center gap-1 text-[11px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md mt-0.5">
                            <i class="fa-solid fa-bullhorn text-[9px] text-slate-400"></i>
                            {{ $enquiry->source->name ?? 'Direct Inquirer' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px]">Assigned Counsellor</span>
                        <span class="font-semibold text-slate-800">{{ $enquiry->assignedTo->name ?? 'Unassigned' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px]">Next Follow-up Reminder</span>
                        <span class="font-mono font-bold text-amber-600">
                            {{ $enquiry->next_follow_up_date ? date('l, d M Y', strtotime($enquiry->next_follow_up_date)) : 'None Scheduled' }}
                        </span>
                    </div>

                    @if($enquiry->remarks)
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-slate-400 block text-[11px]">Inquirer Remarks</span>
                        <p class="text-slate-600 italic bg-slate-50 p-2.5 rounded-lg border border-slate-100 mt-1">
                            "{{ $enquiry->remarks }}"
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right 2-Columns: Follow-up Timeline & New Interaction Form -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Log New Interaction Box -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-headset text-brand-600"></i>
                    <span>Log Follow-up Interaction</span>
                </h2>

                <form action="{{ route('enquiries.followup', $enquiry) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Interaction Date</label>
                            <input type="date" name="followup_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Contact Method</label>
                            <select name="contact_method" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                                <option value="call">Phone Call</option>
                                <option value="whatsapp">WhatsApp Chat</option>
                                <option value="in_person">In-Person Campus Visit</option>
                                <option value="email">Email</option>
                                <option value="sms">SMS Text</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Lead Response</label>
                            <select name="response_status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                                <option value="interested">Interested</option>
                                <option value="positive">Very Positive / Ready to Enroll</option>
                                <option value="call_back_later">Call Back Later</option>
                                <option value="neutral">Neutral / Comparing Options</option>
                                <option value="not_interested">Not Interested / Dropped</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Update Lead Status (Optional)</label>
                            <select name="update_status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                                <option value="">-- Keep Current Status --</option>
                                <option value="contacted">Contacted</option>
                                <option value="counselling_scheduled">Counselling Scheduled</option>
                                <option value="demo_attended">Demo Attended</option>
                                <option value="admission_offered">Admission Offered</option>
                                <option value="dropped">Dropped / Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Next Follow-up Due Date</label>
                            <input type="date" name="next_followup_date" value="{{ date('Y-m-d', strtotime('+3 days')) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                        </div>
                    </div>

                    <div class="text-xs">
                        <label class="block font-semibold text-slate-700 mb-1">Conversation Summary & Agreed Action Points</label>
                        <textarea name="conversation_summary" rows="2" required placeholder="Discussed batch timings, placement statistics, agreed to attend demo class on Saturday..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                            Save Interaction Record
                        </button>
                    </div>
                </form>
            </div>

            <!-- Follow-up Timeline -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center justify-between">
                    <span>Interaction History & Timeline</span>
                    <span class="text-xs font-normal text-slate-400">{{ $enquiry->followups->count() }} activities</span>
                </h2>

                <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse($enquiry->followups as $f)
                    <div class="relative">
                        <div class="absolute -left-6 top-1.5 w-3.5 h-3.5 rounded-full bg-brand-600 border-2 border-white ring-2 ring-brand-200"></div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800 capitalize">{{ $f->contact_method }}</span>
                                    <span class="text-[10px] uppercase font-semibold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                        {{ str_replace('_', ' ', $f->response_status) }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono">{{ date('d M Y', strtotime($f->followup_date)) }}</span>
                            </div>
                            <p class="text-slate-600 leading-relaxed">{{ $f->conversation_summary }}</p>
                            <div class="text-[10px] text-slate-400 pt-1 flex items-center gap-2">
                                <span>Logged by: <strong class="text-slate-600">{{ $f->user->name ?? 'Staff' }}</strong></span>
                                @if($f->next_followup_date)
                                    &bull; <span>Next follow-up: <strong class="text-amber-600 font-mono">{{ date('d M Y', strtotime($f->next_followup_date)) }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-xs text-slate-400 italic">No previous follow-up records recorded yet.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

