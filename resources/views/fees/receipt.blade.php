@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('fees.payments') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Receipts</span>
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Print Official Receipt</span>
            </button>
        </div>
    </div>

    <!-- PRINTABLE RECEIPT CARD -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-lg p-8 sm:p-10 space-y-8 text-xs font-sans text-slate-800 relative overflow-hidden" id="printable-receipt">

        <!-- Top Header / Letterhead -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b-2 border-slate-900 gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-brand-600 text-white flex items-center justify-center text-2xl font-bold shadow-md">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ $appInstituteName ?? 'Kalpkrut Pathshala' }}</h1>
                    <div class="text-[11px] text-slate-500 font-medium">{{ $systemSettings['institute_tagline'] ?? $appInstitute?->tagline ?? 'Premier Institute for Tech, Management & Competitive Coaching' }}</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">GSTIN: {{ $systemSettings['gstin'] ?? $appCompany?->gstin ?? '29AABCK1234G1Z5' }} &bull; PAN: {{ $appCompany?->pan_number ?? 'AABCK1234G' }}</div>
                </div>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-slate-900 text-white text-[11px] font-bold uppercase tracking-wider rounded-md">
                    Official Fee Receipt
                </span>
                <div class="text-base font-extrabold text-brand-700 font-mono mt-1.5">{{ $payment->receipt_no }}</div>
                <div class="text-[11px] text-slate-500 font-mono">Date: {{ date('d F Y', strtotime($payment->payment_date)) }}</div>
            </div>
        </div>

        <!-- Student & Campus Information -->
        <div class="grid grid-cols-2 gap-6 bg-slate-50 p-5 rounded-xl border border-slate-100">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Candidate Particulars</span>
                <div class="text-sm font-bold text-slate-900">{{ $payment->student->full_name }}</div>
                <div class="text-[11px] text-slate-600">Admission No: <strong class="font-mono">{{ $payment->student->admission_no }}</strong></div>
                <div class="text-[11px] text-slate-600">Roll No: <strong class="font-mono">{{ $payment->student->roll_no }}</strong></div>
                <div class="text-[11px] text-slate-600">Phone: <span class="font-mono">{{ $payment->student->phone }}</span></div>
            </div>

            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Academic Details</span>
                <div class="text-sm font-bold text-brand-700">{{ $payment->student->latestAdmission->course->name ?? 'Course' }}</div>
                <div class="text-[11px] text-slate-600">Campus: <strong>{{ $payment->student->branch->name ?? 'Indiranagar' }}</strong></div>
                <div class="text-[11px] text-slate-600">Payment Mode: <strong class="uppercase">{{ $payment->payment_method }}</strong></div>
                <div class="text-[11px] text-slate-600">Reference: <span class="font-mono">{{ $payment->transaction_reference ?? 'DIRECT-CASH' }}</span></div>
            </div>
        </div>

        <!-- Items Breakdown Table -->
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider">
                    <th class="py-2 font-bold">#</th>
                    <th class="py-2 font-bold">Fee Description</th>
                    <th class="py-2 font-bold text-center">Installment Period</th>
                    <th class="py-2 font-bold text-right">Amount (INR)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="py-4 font-mono">1</td>
                    <td class="py-4 font-semibold text-slate-900">
                        {{ $payment->feeDue->title ?? 'Tuition Fee Installment Settlement' }}
                        <span class="block text-[10px] font-normal text-slate-400">Course tuition inclusive of 18% GST (CGST 9% + SGST 9%)</span>
                    </td>
                    <td class="py-4 text-center font-mono">Period #{{ $payment->feeDue->installment_no ?? 1 }}</td>
                    <td class="py-4 text-right font-bold text-slate-900 text-sm font-mono">₹{{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-900">
                    <td colspan="3" class="py-3 text-right font-bold uppercase text-slate-600">Total Net Settled Amount:</td>
                    <td class="py-3 text-right font-black text-lg text-emerald-600 font-mono">₹{{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Amount In Words -->
        <div class="p-3 bg-slate-50 rounded-lg border border-slate-200/80 text-[11px]">
            <span class="font-bold text-slate-600">Amount in Words:</span>
            <span class="italic font-medium text-slate-800 ml-1">Indian Rupees {{ number_format($payment->amount, 2) }} Only</span>
        </div>

        <!-- Signatures & Disclaimer -->
        <div class="pt-8 flex items-end justify-between border-t border-slate-100">
            <div class="text-[10px] text-slate-400 space-y-1">
                <p>&bull; This is a computer generated official tax receipt.</p>
                <p>&bull; Fees once paid are non-refundable as per institute terms & conditions.</p>
                <p>&bull; Retain this receipt for certificate & examination clearance.</p>
            </div>

            <div class="text-center">
                <div class="w-32 border-b border-slate-400 mb-1"></div>
                <span class="font-bold text-slate-700 text-[11px] block">{{ $payment->receiver->name ?? 'Authorized Cashier' }}</span>
                <span class="text-[10px] text-slate-400 uppercase">Accounts Department</span>
            </div>
        </div>

    </div>

</div>

<style>
@media print {
    .no-print, header, aside, footer { display: none !important; }
    body { background: white !important; }
    #printable-receipt { border: none !important; box-shadow: none !important; padding: 0 !important; }
}
</style>
@endsection


