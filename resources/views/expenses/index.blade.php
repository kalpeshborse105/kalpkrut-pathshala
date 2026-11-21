@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Operating Expenses & Vouchers</h1>
            <p class="text-xs text-slate-500 mt-1">Track branch utility bills, faculty honorariums, internet, and stationery costs.</p>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 block font-medium">Total Operating Outflow</span>
            <span class="text-xl font-black text-rose-600">₹{{ number_format($totalExpenses, 2) }}</span>
        </div>
    </div>

    <!-- 2 Column Layout: Expenses List (Left) & Log Expense Form (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2-Cols: Expenses Table -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Branch Expense Vouchers (10)</h2>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Voucher No</th>
                                <th class="px-4 py-3 font-semibold">Date</th>
                                <th class="px-4 py-3 font-semibold">Expense Title & Category</th>
                                <th class="px-4 py-3 font-semibold">Paid To (Vendor)</th>
                                <th class="px-4 py-3 font-semibold">Amount</th>
                                <th class="px-4 py-3 font-semibold">Mode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($expenses as $exp)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $exp->voucher_no }}</td>
                                <td class="px-4 py-3 font-mono text-slate-500">{{ date('d M Y', strtotime($exp->expense_date)) }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $exp->title }}</div>
                                    <span class="text-[10px] text-slate-400 font-medium">{{ $exp->category }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $exp->paid_to }}</td>
                                <td class="px-4 py-3 font-bold text-rose-600 font-mono text-sm">
                                    ₹{{ number_format($exp->amount, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="uppercase font-semibold text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                                        {{ str_replace('_', ' ', $exp->payment_method) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No expense vouchers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>

        <!-- Right 1-Col: Log Expense Form -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-brand-600"></i>
                    <span>Log Expense Voucher</span>
                </h2>

                <form action="{{ route('expenses.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Campus / Branch <span class="text-rose-500">*</span></label>
                        <select name="branch_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Expense Category <span class="text-rose-500">*</span></label>
                        <select name="category" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            <option value="Utilities">Electricity & Utilities</option>
                            <option value="Rent">Campus Lease / Rent</option>
                            <option value="Internet & IT">Leased Line Internet & Cloud</option>
                            <option value="Marketing">Marketing & Advertising Ads</option>
                            <option value="Printing & Stationery">Printing & Stationery Handouts</option>
                            <option value="Office Operations">Pantry & Daily Operations</option>
                            <option value="Faculty Honorarium">Faculty Guest Lecture</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Expense Title / Item <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required placeholder="e.g. Office UPS Battery Replacement" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Amount (₹) <span class="text-rose-500">*</span></label>
                            <input type="number" name="amount" required min="1" step="0.5" placeholder="5000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-rose-600">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Expense Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Payment Method <span class="text-rose-500">*</span></label>
                        <select name="payment_method" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            <option value="upi">UPI / QR Code</option>
                            <option value="bank_transfer">Bank Transfer / NEFT</option>
                            <option value="cash">Petty Cash</option>
                            <option value="cheque">Bank Cheque</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Paid To (Vendor Name) <span class="text-rose-500">*</span></label>
                        <input type="text" name="paid_to" required placeholder="e.g. Power Electricals Ltd" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Voucher Remarks</label>
                        <textarea name="remarks" rows="2" placeholder="Authorized under monthly operations budget..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold shadow-xs transition">
                        Log Operating Expense
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection

