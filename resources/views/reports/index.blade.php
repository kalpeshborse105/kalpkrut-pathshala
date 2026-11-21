@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Executive Reports & Business Analytics</h1>
            <p class="text-xs text-slate-500 mt-1">Cross-branch operational intelligence, financial P&L, and student acquisition reports.</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition">
            <i class="fa-solid fa-print"></i>
            <span>Export Analytics Report</span>
        </button>
    </div>

    <!-- 3 Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Gross Collections</span>
            <div class="text-2xl font-bold text-emerald-600 mt-1">₹{{ number_format($totalCollected, 2) }}</div>
            <span class="text-xs text-slate-400">Total verified payment settlements</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Operating Expenses</span>
            <div class="text-2xl font-bold text-rose-600 mt-1">₹{{ number_format($totalExpenses, 2) }}</div>
            <span class="text-xs text-slate-400">Branch administrative vouchers</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Net Operating Surplus (EBITDA)</span>
            <div class="text-2xl font-bold text-brand-700 mt-1">₹{{ number_format($netIncome, 2) }}</div>
            <span class="text-xs text-emerald-600 font-bold">Surplus Margin Positive</span>
        </div>
    </div>

    <!-- Charts / Breakdown Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Payment Channels Distribution -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 mb-1">Fee Collection Channels</h2>
            <p class="text-xs text-slate-400 mb-4">Breakdown by cash, UPI, card and net banking</p>
            <div class="space-y-3 text-xs">
                @foreach($paymentMethods as $mode => $amt)
                <div>
                    <div class="flex justify-between font-semibold mb-1">
                        <span class="uppercase text-slate-700">{{ $mode }}</span>
                        <span class="text-slate-900 font-bold">₹{{ number_format($amt, 2) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-brand-600 h-full" style="width: {{ $totalCollected > 0 ? ($amt / $totalCollected) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Marketing Leads Sources Distribution -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 mb-1">Lead Acquisition Channels</h2>
            <p class="text-xs text-slate-400 mb-4">Inquiries attributed across marketing sources</p>
            <div class="space-y-3 text-xs">
                @foreach($leadsBySource as $src => $count)
                <div>
                    <div class="flex justify-between font-semibold mb-1">
                        <span class="text-slate-700">{{ $src }}</span>
                        <span class="text-slate-900 font-bold">{{ $count }} Leads ({{ $totalLeads > 0 ? round(($count / $totalLeads) * 100) : 0 }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full" style="width: {{ $totalLeads > 0 ? ($count / $totalLeads) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Branch Performance & Cohort Utilization -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900">Campus Revenue & Admission Volume</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Campus Name</th>
                        <th class="px-4 py-3 font-semibold">Code</th>
                        <th class="px-4 py-3 font-semibold">Location</th>
                        <th class="px-4 py-3 font-semibold">Contracted Revenue Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($branchRevenue as $br)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-bold text-slate-900">{{ $br->name }}</td>
                        <td class="px-4 py-3 font-mono text-slate-500">{{ $br->code }}</td>
                        <td class="px-4 py-3">{{ $br->city }}, {{ $br->state }}</td>
                        <td class="px-4 py-3 font-bold text-emerald-600 text-sm">₹{{ number_format($br->admissions_sum_final_agreed_fee ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

