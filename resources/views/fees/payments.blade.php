@extends('layouts.app')

@section('content')
<div class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-receipt text-indigo-600"></i>
                <span>Payment Receipts Register</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Audit log of all settled tuition collections, UPI/Bank transactions, and cashier receipts.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('fees.index') }}" class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Fee Dues</span>
            </a>
            <a href="{{ route('fees.collect') }}" class="inline-flex items-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-xs shadow-brand-600/30 transition">
                <i class="fa-solid fa-cash-register"></i>
                <span>Collect Payment</span>
            </a>
        </div>
    </div>

    <!-- Receipts Table (Compact) -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-3 py-2 font-semibold">Receipt No</th>
                        <th class="px-3 py-2 font-semibold">Date</th>
                        <th class="px-3 py-2 font-semibold">Student Name & Roll</th>
                        <th class="px-3 py-2 font-semibold">Amount Paid</th>
                        <th class="px-3 py-2 font-semibold">Payment Mode</th>
                        <th class="px-3 py-2 font-semibold">Transaction Reference</th>
                        <th class="px-3 py-2 font-semibold">Cashier / Staff</th>
                        <th class="px-3 py-2 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($payments as $pay)
                    <tr class="hover:bg-indigo-50/20 transition-colors">
                        <td class="px-3 py-2 font-mono font-bold text-brand-700">
                            {{ $pay->receipt_no }}
                        </td>
                        <td class="px-3 py-2 font-mono text-slate-500 text-[11px]">
                            {{ date('d M Y', strtotime($pay->payment_date)) }}
                        </td>
                        <td class="px-3 py-2">
                            <div class="font-bold text-slate-900">{{ $pay->student->full_name ?? 'Student' }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $pay->student->admission_no ?? '' }}</div>
                        </td>
                        <td class="px-3 py-2 font-bold text-emerald-600">
                            ₹{{ number_format($pay->amount, 2) }}
                        </td>
                        <td class="px-3 py-2">
                            <span class="uppercase font-semibold text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded">
                                {{ $pay->payment_method }}
                            </span>
                        </td>
                        <td class="px-3 py-2 font-mono text-[11px] text-slate-600">
                            {{ $pay->transaction_reference ?? 'CASH' }}
                        </td>
                        <td class="px-3 py-2 text-slate-500">
                            {{ $pay->receiver->name ?? 'Accounts Officer' }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('fees.receipt', $pay) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold text-[11px] transition">
                                <i class="fa-solid fa-print text-[10px]"></i>
                                <span>Receipt</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-slate-400">
                            No payment receipts on file.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-slate-100 bg-slate-50/50">
            {{ $payments->links() }}
        </div>
    </div>

</div>
@endsection
