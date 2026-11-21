@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'overview' }">

    <!-- Header Banner -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-500 text-white font-extrabold text-2xl flex items-center justify-center shadow-lg shadow-brand-500/20 border-2 border-white">
                {{ substr($student->first_name, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $student->full_name }}</h1>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 uppercase">
                        {{ $student->status }}
                    </span>
                </div>
                <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                    <span>Admission No: <strong class="text-slate-800 font-mono">{{ $student->admission_no }}</strong></span>
                    <span>Roll No: <strong class="text-slate-800 font-mono">{{ $student->roll_no ?? 'N/A' }}</strong></span>
                    <span>Campus: <strong class="text-slate-800">{{ $student->branch->name ?? 'Indiranagar' }}</strong></span>
                </div>
            </div>
        </div>

        <!-- 3 Quick KPI Chips -->
        <div class="flex items-center gap-4 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6 text-xs">
            <div>
                <span class="text-slate-400 block text-[11px]">Attendance Rate</span>
                <span class="text-base font-bold {{ $attendancePercentage >= 75 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $attendancePercentage }}%
                </span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Total Fee Paid</span>
                <span class="text-base font-bold text-emerald-600">₹{{ number_format($totalFeePaid, 0) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Pending Balance</span>
                <span class="text-base font-bold {{ $totalFeeBalance > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                    ₹{{ number_format($totalFeeBalance, 0) }}
                </span>
            </div>
        </div>
    </div>

    <!-- TAB NAVIGATION BAR -->
    <div class="flex items-center gap-2 border-b border-slate-200 text-xs font-semibold overflow-x-auto">
        <button @click="activeTab = 'overview'" 
                :class="activeTab === 'overview' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-3 transition flex items-center gap-2">
            <i class="fa-solid fa-id-card"></i>
            <span>Overview & Personal</span>
        </button>

        <button @click="activeTab = 'batches'" 
                :class="activeTab === 'batches' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-3 transition flex items-center gap-2">
            <i class="fa-solid fa-users-rectangle"></i>
            <span>Batches & Classes ({{ $student->batches->count() }})</span>
        </button>

        <button @click="activeTab = 'attendance'" 
                :class="activeTab === 'attendance' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-3 transition flex items-center gap-2">
            <i class="fa-solid fa-clipboard-user"></i>
            <span>Attendance Log ({{ $student->attendances->count() }})</span>
        </button>

        <button @click="activeTab = 'finance'" 
                :class="activeTab === 'finance' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-3 transition flex items-center gap-2">
            <i class="fa-solid fa-receipt"></i>
            <span>Fee Ledger & Receipts ({{ $student->payments->count() }})</span>
        </button>

        <button @click="activeTab = 'exams'" 
                :class="activeTab === 'exams' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-3 transition flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Exams & Marks ({{ $student->examAttempts->count() }})</span>
        </button>

        <button @click="activeTab = 'certificates'" 
                :class="activeTab === 'certificates' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-3 transition flex items-center gap-2">
            <i class="fa-solid fa-certificate"></i>
            <span>Certificates ({{ $student->certificates->count() }})</span>
        </button>
    </div>

    <!-- TAB 1: OVERVIEW & PERSONAL -->
    <div x-show="activeTab === 'overview'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-user text-brand-600"></i>
                <span>Personal & Contact Information</span>
            </h2>
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">Date of Birth</span>
                    <span class="font-semibold text-slate-800">{{ $student->date_of_birth ? date('d M Y', strtotime($student->date_of_birth)) : 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Gender & Blood Group</span>
                    <span class="font-semibold text-slate-800 capitalize">{{ $student->gender }} &bull; {{ $student->blood_group ?? 'O+' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Mobile Number</span>
                    <span class="font-semibold text-slate-800 font-mono">{{ $student->phone }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Email Address</span>
                    <span class="font-semibold text-slate-800">{{ $student->email }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400 block text-[11px]">Current Address</span>
                    <span class="font-medium text-slate-700">{{ $student->current_address }}, {{ $student->city }}, {{ $student->state }} {{ $student->pincode }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-users text-brand-600"></i>
                <span>Parents & Guardian Profile</span>
            </h2>
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">Father's Name</span>
                    <span class="font-semibold text-slate-800">{{ $student->father_name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Father's Contact</span>
                    <span class="font-semibold text-slate-800 font-mono">{{ $student->father_phone ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Father's Occupation</span>
                    <span class="font-medium text-slate-700">{{ $student->father_occupation ?? 'Business / Service' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Guardian Name</span>
                    <span class="font-medium text-slate-700">{{ $student->guardian_name ?? $student->father_name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: ENROLLED BATCHES -->
    <div x-show="activeTab === 'batches'" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900">Enrolled Program Batches</h2>
            <p class="text-xs text-slate-400">Classrooms, schedule timings and assigned academic mentors</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Batch Name & Code</th>
                        <th class="px-4 py-3 font-semibold">Course</th>
                        <th class="px-4 py-3 font-semibold">Classroom</th>
                        <th class="px-4 py-3 font-semibold">Faculty Mentor</th>
                        <th class="px-4 py-3 font-semibold">Class Timings</th>
                        <th class="px-4 py-3 font-semibold">Enrolled On</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($student->batches as $batch)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-semibold text-brand-700">
                            {{ $batch->name }}
                            <div class="text-[11px] text-slate-400 font-mono">{{ $batch->code }}</div>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $batch->course->name ?? 'Course' }}</td>
                        <td class="px-4 py-3">{{ $batch->classroom->room_name ?? 'Classroom' }}</td>
                        <td class="px-4 py-3">{{ $batch->mentor->name ?? 'Mentor' }}</td>
                        <td class="px-4 py-3 font-mono text-slate-600">
                            {{ date('h:i A', strtotime($batch->start_time)) }} - {{ date('h:i A', strtotime($batch->end_time)) }}
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-500">{{ date('d M Y', strtotime($batch->pivot->enrolled_at)) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No batch assigned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: ATTENDANCE LOG -->
    <div x-show="activeTab === 'attendance'" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Attendance Log & Biometric Records</h2>
                <p class="text-xs text-slate-400">Overall Attendance Score: <strong class="{{ $attendancePercentage >= 75 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $attendancePercentage }}%</strong></p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Date</th>
                        <th class="px-4 py-3 font-semibold">Subject</th>
                        <th class="px-4 py-3 font-semibold">Attendance Status</th>
                        <th class="px-4 py-3 font-semibold">Check-in Time</th>
                        <th class="px-4 py-3 font-semibold">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($student->attendances as $att)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-mono text-slate-900">{{ date('d M Y', strtotime($att->attendance_date)) }}</td>
                        <td class="px-4 py-3 font-medium">{{ $att->subject->name ?? 'General Session' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $attClass = match($att->status) {
                                    'present' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'late' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-rose-50 text-rose-700 border-rose-200'
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $attClass }} uppercase">
                                {{ $att->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-500">{{ $att->in_time ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500 italic">{{ $att->remarks ?? 'Regular session' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No attendance records on file.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 4: FEE LEDGER -->
    <div x-show="activeTab === 'finance'" class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Student Fee Installments Schedule</h2>
                    <p class="text-xs text-slate-400">Total Agreed Course Fee: ₹{{ number_format($totalFeeAssigned, 2) }}</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Installment #</th>
                            <th class="px-4 py-3 font-semibold">Title</th>
                            <th class="px-4 py-3 font-semibold">Due Date</th>
                            <th class="px-4 py-3 font-semibold">Due Amount</th>
                            <th class="px-4 py-3 font-semibold">Paid Amount</th>
                            <th class="px-4 py-3 font-semibold">Balance</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($student->feeDues as $due)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-bold text-slate-900">#{{ $due->installment_no }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $due->title }}</td>
                            <td class="px-4 py-3 font-mono text-slate-500">{{ date('d M Y', strtotime($due->due_date)) }}</td>
                            <td class="px-4 py-3 font-bold">₹{{ number_format($due->net_amount, 2) }}</td>
                            <td class="px-4 py-3 font-bold text-emerald-600">₹{{ number_format($due->paid_amount, 2) }}</td>
                            <td class="px-4 py-3 font-bold text-rose-600">₹{{ number_format($due->balance_amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $due->status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $due->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($due->status !== 'paid')
                                    <a href="{{ route('fees.collect', ['due_id' => $due->id]) }}" class="px-2.5 py-1 bg-brand-600 hover:bg-brand-700 text-white rounded text-[11px] font-semibold">
                                        Collect
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Receipts Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">Issued Official Payment Receipts</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Receipt No</th>
                            <th class="px-4 py-3 font-semibold">Date</th>
                            <th class="px-4 py-3 font-semibold">Amount</th>
                            <th class="px-4 py-3 font-semibold">Method & Reference</th>
                            <th class="px-4 py-3 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($student->payments as $pay)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-mono font-bold text-brand-700">{{ $pay->receipt_no }}</td>
                            <td class="px-4 py-3 font-mono text-slate-500">{{ date('d M Y', strtotime($pay->payment_date)) }}</td>
                            <td class="px-4 py-3 font-bold text-emerald-600">₹{{ number_format($pay->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="uppercase font-semibold text-slate-600">{{ $pay->payment_method }}</span>
                                <span class="text-[11px] text-slate-400 font-mono ml-1">({{ $pay->transaction_reference }})</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('fees.receipt', $pay) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-semibold text-[11px]">
                                    Print Receipt
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No payment receipts yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 5: EXAMS & RESULTS -->
    <div x-show="activeTab === 'exams'" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900">Examination Scorecards & Assessment Results</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Exam Title</th>
                        <th class="px-4 py-3 font-semibold">Subject</th>
                        <th class="px-4 py-3 font-semibold">Score / Max</th>
                        <th class="px-4 py-3 font-semibold">Percentage</th>
                        <th class="px-4 py-3 font-semibold">Grade</th>
                        <th class="px-4 py-3 font-semibold">Result</th>
                        <th class="px-4 py-3 font-semibold text-right">Report Card</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($student->examAttempts as $att)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $att->exam->title ?? 'Exam' }}</td>
                        <td class="px-4 py-3 font-medium">{{ $att->exam->subject->name ?? 'Subject' }}</td>
                        <td class="px-4 py-3 font-bold text-slate-900">{{ $att->marks_obtained }} / {{ $att->exam->total_marks ?? 100 }}</td>
                        <td class="px-4 py-3 font-bold text-brand-600">{{ $att->percentage }}%</td>
                        <td class="px-4 py-3 font-extrabold text-indigo-700">{{ $att->grade }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $att->result === 'pass' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $att->result }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('exams.report-card', [$att->exam, $student]) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded font-semibold text-[11px]">
                                View Scorecard
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">No exam attempts recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 6: CERTIFICATES -->
    <div x-show="activeTab === 'certificates'" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900">Issued Course Certificates & Credentials</h2>
        </div>
        <div class="p-6">
            @forelse($student->certificates as $cert)
            <div class="p-5 rounded-2xl border border-slate-200 bg-gradient-to-r from-slate-50 to-indigo-50/30 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shadow-xs">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">{{ $cert->title }}</h3>
                        <div class="text-xs text-slate-500 mt-0.5">
                            Certificate No: <strong class="font-mono text-slate-800">{{ $cert->certificate_no }}</strong> &bull; Grade: <strong>{{ $cert->grade }}</strong>
                        </div>
                        <div class="text-[11px] text-brand-600 font-mono mt-0.5">
                            Verification Code: {{ $cert->verification_code }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('certificates.show', $cert) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-xs">
                    View Certificate
                </a>
            </div>
            @empty
            <div class="text-center py-8 text-slate-400 text-xs italic">
                No certificate issued to this student yet. (Issued upon 100% course clearance).
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

