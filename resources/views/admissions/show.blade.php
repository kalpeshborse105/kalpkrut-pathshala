@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('admissions.index') }}" class="hover:text-slate-600">Admissions</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-slate-700 font-mono">{{ $admission->admission_no }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                Admission Confirmation: {{ $admission->student->full_name }}
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 uppercase">
                    {{ $admission->status }}
                </span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('students.show', $admission->student) }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-semibold transition">
                <i class="fa-solid fa-user"></i>
                <span>Open 360° Student Profile</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Print Admission Slip</span>
            </button>
        </div>
    </div>

    <!-- 3 Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Candidate & Roll</span>
            <div class="text-base font-bold text-slate-900 mt-1">{{ $admission->student->full_name }}</div>
            <div class="text-xs text-slate-500 font-mono mt-0.5">Roll No: <strong>{{ $admission->student->roll_no }}</strong></div>
            <div class="text-xs text-slate-500 mt-0.5">{{ $admission->student->phone }} &bull; {{ $admission->student->email }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Enrolled Course & Batch</span>
            <div class="text-base font-bold text-brand-700 mt-1">{{ $admission->course->name }}</div>
            <div class="text-xs text-slate-600 mt-0.5 font-medium">{{ $admission->batch->name ?? 'Assigned Batch' }}</div>
            <div class="text-xs text-slate-400 mt-0.5">Classroom: {{ $admission->batch->classroom->room_name ?? 'Lecture Hall' }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Agreed Financial Contract</span>
            <div class="text-xl font-bold text-slate-900 mt-1">₹{{ number_format($admission->final_agreed_fee, 2) }}</div>
            <div class="text-xs text-slate-500 mt-0.5">
                Installments: <span class="font-semibold">{{ $admission->installments_count }} Period(s)</span>
                @if($admission->discount_amount > 0)
                    &bull; <span class="text-emerald-600 font-medium">Scholarship: ₹{{ number_format($admission->discount_amount) }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Installments Breakdown & Dues Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Payment Installments Schedule & Status</h2>
                <p class="text-xs text-slate-400">Tracking down-payment, due dates, fines, and settled receipts</p>
            </div>
            <span class="text-xs bg-brand-50 text-brand-700 font-bold px-3 py-1 rounded-full">
                {{ $admission->feeDues->count() }} Installment Periods
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Installment #</th>
                        <th class="px-4 py-3 font-semibold">Title</th>
                        <th class="px-4 py-3 font-semibold">Due Date</th>
                        <th class="px-4 py-3 font-semibold">Installment Amount</th>
                        <th class="px-4 py-3 font-semibold">Paid Amount</th>
                        <th class="px-4 py-3 font-semibold">Outstanding Balance</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($admission->feeDues as $due)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900">#{{ $due->installment_no }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $due->title }}</td>
                        <td class="px-4 py-3 font-mono text-[11px] text-slate-500">
                            {{ date('d M Y', strtotime($due->due_date)) }}
                        </td>
                        <td class="px-4 py-3 font-bold text-slate-900">₹{{ number_format($due->net_amount, 2) }}</td>
                        <td class="px-4 py-3 font-bold text-emerald-600">₹{{ number_format($due->paid_amount, 2) }}</td>
                        <td class="px-4 py-3 font-bold {{ $due->balance_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                            ₹{{ number_format($due->balance_amount, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badgeClass = match($due->status) {
                                    'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'partially_paid' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-rose-50 text-rose-700 border-rose-200'
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeClass }} uppercase">
                                {{ str_replace('_', ' ', $due->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($due->status !== 'paid')
                                <a href="{{ route('fees.collect', ['due_id' => $due->id]) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold transition text-[11px]">
                                    <i class="fa-solid fa-cash-register text-[10px]"></i>
                                    <span>Collect</span>
                                </a>
                            @else
                                @if($due->payments->first())
                                    <a href="{{ route('fees.receipt', $due->payments->first()) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold transition text-[11px]">
                                        <i class="fa-solid fa-receipt text-[10px]"></i>
                                        <span>Receipt</span>
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

