@extends('layouts.app')

@section('content')
<div class="space-y-4" x-data="{
    collectModal: false,
    selectedDue: {
        id: '',
        student_name: '',
        admission_no: '',
        title: '',
        balance: 0,
        installment_no: 1
    },
    paymentDate: '{{ date('Y-m-d') }}',
    amount: 0,
    paymentMethod: 'upi',
    reference: '',
    remarks: '',

    openCollect(due) {
        this.selectedDue = due;
        this.amount = due.balance;
        this.reference = '';
        this.remarks = 'Collected against ' + due.title;
        this.collectModal = true;
    }
}">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-brand-600"></i>
                <span>Fee Management & Installment Dues</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Track scheduled course installments, outstanding receivables, and instant collections.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('fees.payments') }}" class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                <i class="fa-solid fa-receipt text-slate-500"></i>
                <span>Receipts Register</span>
            </a>
            <a href="{{ route('fees.collect') }}" class="inline-flex items-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-xs shadow-brand-600/30 transition">
                <i class="fa-solid fa-cash-register"></i>
                <span>Full Cashier Desk</span>
            </a>
        </div>
    </div>

    <!-- 3 Compact Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Contract Dues</span>
                <div class="text-lg font-bold text-slate-900 mt-0.5">₹{{ number_format($totalDuesAmount, 2) }}</div>
                <span class="text-[10px] text-slate-400">Total invoiced installments</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm">
                <i class="fa-solid fa-file-contract"></i>
            </div>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Fee Collected</span>
                <div class="text-lg font-bold text-emerald-600 mt-0.5">₹{{ number_format($totalCollected, 2) }}</div>
                <span class="text-[10px] text-emerald-600 font-medium">Settled cashier payments</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Outstanding Balance</span>
                <div class="text-lg font-bold text-rose-600 mt-0.5">₹{{ number_format($totalOutstanding, 2) }}</div>
                <span class="text-[10px] text-rose-500 font-medium">Pending receivable</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>
    </div>

    <!-- Compact Filter Bar -->
    <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('fees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search candidate, phone, admission..."
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
            </div>

            <div>
                <select name="status" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                    <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <div>
                <select name="branch_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    <option value="">All Campuses</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-1.5 rounded-lg transition">
                    Filter
                </button>
                <a href="{{ route('fees.index') }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs transition" title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Dues Table (Compact ERP Density) -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-3 py-2 font-semibold">Student / Roll</th>
                        <th class="px-3 py-2 font-semibold">Installment Title</th>
                        <th class="px-3 py-2 font-semibold">Due Date</th>
                        <th class="px-3 py-2 font-semibold">Net Due</th>
                        <th class="px-3 py-2 font-semibold">Paid Amount</th>
                        <th class="px-3 py-2 font-semibold">Balance</th>
                        <th class="px-3 py-2 font-semibold">Status</th>
                        <th class="px-3 py-2 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($dues as $due)
                    <tr class="hover:bg-indigo-50/20 transition-colors">
                        <td class="px-3 py-2">
                            <div class="font-bold text-slate-900">{{ $due->student->full_name ?? 'Candidate' }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $due->student->admission_no ?? '' }}</div>
                        </td>
                        <td class="px-3 py-2">
                            <div class="font-semibold text-slate-800">{{ $due->title }}</div>
                            <span class="text-[10px] text-slate-400">Installment #{{ $due->installment_no }}</span>
                        </td>
                        <td class="px-3 py-2 font-mono text-[11px] text-slate-600">
                            {{ date('d M Y', strtotime($due->due_date)) }}
                        </td>
                        <td class="px-3 py-2 font-bold text-slate-900">₹{{ number_format($due->net_amount, 2) }}</td>
                        <td class="px-3 py-2 font-bold text-emerald-600">₹{{ number_format($due->paid_amount, 2) }}</td>
                        <td class="px-3 py-2 font-bold {{ $due->balance_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                            ₹{{ number_format($due->balance_amount, 2) }}
                        </td>
                        <td class="px-3 py-2">
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
                        <td class="px-3 py-2 text-right">
                            @if($due->status !== 'paid')
                                <button type="button" 
                                        @click="openCollect({
                                            id: {{ $due->id }},
                                            student_name: '{{ addslashes($due->student->full_name ?? 'Student') }}',
                                            admission_no: '{{ $due->student->admission_no ?? '' }}',
                                            title: '{{ addslashes($due->title) }}',
                                            balance: {{ $due->balance_amount }},
                                            installment_no: {{ $due->installment_no }}
                                        })" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold transition text-[11px] shadow-xs">
                                    <i class="fa-solid fa-cash-register text-[10px]"></i>
                                    <span>Collect</span>
                                </button>
                            @else
                                @if($due->payments->first())
                                    <a href="{{ route('fees.receipt', $due->payments->first()) }}" class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold transition text-[11px]">
                                        <i class="fa-solid fa-receipt text-[10px]"></i>
                                        <span>Receipt</span>
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-slate-400">
                            No fee records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-slate-100 bg-slate-50/50">
            {{ $dues->links() }}
        </div>
    </div>

    <!-- QUICK CASHIER COLLECTION MODAL -->
    <div x-cloak x-show="collectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="collectModal = false" class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="px-5 py-3.5 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-cash-register text-brand-400"></i>
                    <h3 class="text-sm font-bold">Quick Fee Collection</h3>
                </div>
                <button type="button" @click="collectModal = false" class="text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Student & Due Details Pill -->
            <div class="bg-indigo-50/70 border-b border-indigo-100 px-5 py-3 text-xs flex items-center justify-between">
                <div>
                    <div class="font-bold text-slate-900" x-text="selectedDue.student_name"></div>
                    <div class="text-[11px] text-slate-500 font-mono" x-text="selectedDue.admission_no"></div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-indigo-700 font-semibold block" x-text="selectedDue.title"></span>
                    <span class="font-bold text-rose-600 text-sm">₹<span x-text="parseFloat(selectedDue.balance).toLocaleString('en-IN', {minimumFractionDigits: 2})"></span></span>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('fees.pay') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <input type="hidden" name="student_fee_due_id" :value="selectedDue.id">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Collection Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="payment_date" x-model="paymentDate" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Amount Being Collected (₹) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1.5 text-slate-400 font-bold">₹</span>
                            <input type="number" step="0.01" min="1" :max="selectedDue.balance" name="amount" x-model.number="amount" required class="w-full pl-6 pr-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-bold text-slate-900 text-sm focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Payment Method <span class="text-rose-500">*</span></label>
                        <select name="payment_method" x-model="paymentMethod" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                            <option value="upi">UPI / QR (PhonePe / GPay)</option>
                            <option value="cash">Cash Settlement</option>
                            <option value="card">Card POS Terminal</option>
                            <option value="net_banking">Net Banking / NEFT</option>
                            <option value="cheque">Bank Cheque / DD</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Transaction Ref / Cheque No</label>
                        <input type="text" name="transaction_reference" x-model="reference" placeholder="e.g. UPI Ref / Cheque No" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Cashier Remarks</label>
                    <input type="text" name="remarks" x-model="remarks" placeholder="Notes for official receipt..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="collectModal = false" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold shadow-xs shadow-brand-600/30 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Confirm & Issue Receipt</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
