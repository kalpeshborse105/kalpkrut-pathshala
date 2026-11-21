@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <div class="flex items-center justify-between bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-cash-register text-emerald-600"></i>
                <span>Cashier Payment Collection Desk</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Receive installment payment, record transaction mode, and issue official GST receipt.</p>
        </div>
        <a href="{{ route('fees.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Fee Dues</span>
        </a>
    </div>

    @if($due)
    <div class="bg-indigo-50/80 border border-indigo-200 text-indigo-900 p-3.5 rounded-xl flex items-center justify-between text-xs">
        <div>
            <span class="font-bold">Collecting For:</span> {{ $due->student->full_name }} ({{ $due->student->admission_no }})
            <div class="text-[11px] text-indigo-700 mt-0.5">
                {{ $due->title }} &bull; Outstanding Balance: <strong class="text-rose-600 font-bold">₹{{ number_format($due->balance_amount, 2) }}</strong>
            </div>
        </div>
        <span class="text-[10px] uppercase font-bold text-indigo-700 bg-white px-2 py-0.5 rounded shadow-xs">Due #{{ $due->installment_no }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-5">
        <form action="{{ route('fees.pay') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            @if($due)
                <input type="hidden" name="student_fee_due_id" value="{{ $due->id }}">
            @else
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Select Pending Due Record <span class="text-rose-500">*</span></label>
                    <select name="student_fee_due_id" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                        @foreach(\App\Models\StudentFeeDue::where('status', '!=', 'paid')->with('student')->get() as $d)
                            <option value="{{ $d->id }}">
                                {{ $d->student->full_name ?? 'Candidate' }} — {{ $d->title }} (Due: ₹{{ number_format($d->balance_amount) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Collection Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Amount Being Settled (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" value="{{ $due ? $due->balance_amount : 10000 }}" required min="1" step="0.5" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 font-bold text-slate-900 text-sm focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Payment Method <span class="text-rose-500">*</span></label>
                    <select name="payment_method" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                        <option value="upi">UPI / QR (PhonePe / GPay / Paytm)</option>
                        <option value="cash">Cash Collection</option>
                        <option value="card">Card POS Terminal</option>
                        <option value="net_banking">Net Banking / IMPS / NEFT</option>
                        <option value="cheque">Bank Cheque / DD</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Transaction ID / Bank Reference</label>
                    <input type="text" name="transaction_reference" placeholder="e.g. UPI Ref / Cheque No / Card Auth" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Cashier Notes / Receipt Remarks</label>
                <textarea name="remarks" rows="2" placeholder="Fee received against installment schedule. Settled by student/parent..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('fees.index') }}" class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold shadow-xs shadow-brand-600/30 transition">
                    Confirm Receipt & Settle Dues
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
