@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- WELCOME BANNER -->
    <div class="bg-gradient-to-r from-emerald-900 via-slate-900 to-indigo-950 rounded-2xl p-6 sm:p-8 text-white shadow-lg flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/10 text-white font-extrabold text-2xl flex items-center justify-center border border-white/20 shadow-inner">
                <i class="fa-solid fa-calculator text-emerald-400"></i>
            </div>
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-400 text-slate-950 mb-1">
                    Financial Accounts Console
                </span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Welcome, {{ auth()->user()->name }}</h1>
                <p class="text-xs text-emerald-200 mt-0.5">
                    Chief Accounts & Cashier Desk &bull; Kalpkrut Pathshala Central Campus
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('fees.collect') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-cash-register"></i>
                <span>Collect Fee Payment</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-rose-600/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-receipt"></i>
                <span>Log Expense</span>
            </a>
        </div>
    </div>

    <!-- 4 FINANCIAL METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gross Fee Collections</span>
                <div class="text-2xl font-bold text-emerald-600 mt-1">₹{{ number_format($totalCollected, 2) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Total revenue collected</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Today's Inflow</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">₹{{ number_format($todayCollected, 2) }}</div>
                <div class="text-[11px] text-slate-400 mt-1 font-mono">Cash drawer active</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Receivables</span>
                <div class="text-2xl font-bold text-rose-600 mt-1">₹{{ number_format($totalOutstanding, 2) }}</div>
                <div class="text-[11px] text-rose-500 font-semibold mt-1">Unsettled installments</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Operating Expenses</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">₹{{ number_format($totalExpenses, 2) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Administrative vouchers</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
    </div>

    <!-- 2 COLUMNS: RECENT SETTLEMENTS & UNPAID DUES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Recent Settlements (Receipts) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Recent Payment Receipts</h2>
                    <p class="text-xs text-slate-400">Settled receipts at cashier desk</p>
                </div>
                <a href="{{ route('fees.payments') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">View register</a>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($recentPayments as $pay)
                <div class="p-4 hover:bg-slate-50/70 transition flex items-center justify-between">
                    <div>
                        <div class="font-bold text-brand-700 font-mono">{{ $pay->receipt_no }}</div>
                        <div class="font-semibold text-slate-900 mt-0.5">{{ $pay->student->full_name ?? 'Student' }}</div>
                        <div class="text-[10px] text-slate-400 font-mono">{{ date('d M Y', strtotime($pay->payment_date)) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-emerald-600 text-sm">₹{{ number_format($pay->amount, 2) }}</div>
                        <span class="text-[10px] font-semibold uppercase bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded">
                            {{ $pay->payment_method }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400 italic">No recent payments recorded.</div>
                @endforelse
            </div>
        </div>

        <!-- Pending Unpaid Dues -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Pending Installment Dues</h2>
                    <p class="text-xs text-slate-400">Installments awaiting collection</p>
                </div>
                <a href="{{ route('fees.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">View all dues</a>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($unpaidDues as $due)
                <div class="p-4 hover:bg-slate-50/70 transition flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-900">{{ $due->student->full_name ?? 'Candidate' }}</div>
                        <div class="text-[11px] text-slate-500">{{ $due->title }}</div>
                        <div class="text-[10px] text-slate-400 font-mono">Due Date: {{ date('d M Y', strtotime($due->due_date)) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-rose-600 text-sm">₹{{ number_format($due->balance_amount, 2) }}</div>
                        <a href="{{ route('fees.collect', ['due_id' => $due->id]) }}" class="px-2.5 py-1 bg-brand-600 hover:bg-brand-700 text-white rounded text-[10px] font-semibold inline-block mt-1">
                            Collect
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400 italic">No pending dues found!</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection


