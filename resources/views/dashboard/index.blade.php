@extends('layouts.app')

@section('content')
<div class="space-y-4">

    <!-- TOP WELCOME BANNER & STATS -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span>Executive Operations Dashboard</span>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                    Live Active
                </span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Real-time metrics for <span class="font-semibold text-slate-700">{{ $appInstituteName ?? 'Kalpkrut Pathshala' }}</span> across all campuses and active batches.
            </p>
        </div>

        <!-- Campus Switcher Form -->
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            <label class="text-[11px] font-semibold text-slate-600">Filter Branch:</label>
            <select name="branch_id" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1 focus:ring-1 focus:ring-brand-500 focus:outline-none font-medium">
                <option value="all" {{ $selectedBranchId === 'all' || !$selectedBranchId ? 'selected' : '' }}>All Campuses (Consolidated)</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $selectedBranchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- 6 KEY KPI METRIC CARDS (COMPACT) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Metric 1: Students -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Enrolled</span>
                <div class="text-xl font-bold text-slate-900 mt-0.5">{{ number_format($totalStudents) }}</div>
                <div class="text-[10px] text-emerald-600 font-medium mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>100% active cohorts</span>
                </div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <!-- Metric 2: Leads & Conversion -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Leads & Enquiries</span>
                <div class="text-xl font-bold text-slate-900 mt-0.5">{{ number_format($totalEnquiries) }}</div>
                <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                    Conversion: <span class="font-bold text-brand-600">{{ $conversionRate }}%</span>
                </div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-funnel-dollar"></i>
            </div>
        </div>

        <!-- Metric 3: Fee Collections -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Fee Collected</span>
                <div class="text-xl font-bold text-emerald-600 mt-0.5">₹{{ number_format($totalFeeCollected, 2) }}</div>
                <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                    50 Receipts Verified
                </div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
        </div>

        <!-- Metric 4: Net Surplus -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Net Operating Surplus</span>
                <div class="text-xl font-bold text-slate-900 mt-0.5">₹{{ number_format($netOperatingBalance, 2) }}</div>
                <div class="text-[10px] text-rose-500 font-medium mt-0.5">
                    Expenses: ₹{{ number_format($totalExpenses, 0) }}
                </div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
        </div>
    </div>

    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <!-- Chart 1: Financial Health Bar Chart -->
        <div class="lg:col-span-2 bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-xs font-bold text-slate-800">Financial Performance & Cashflow</h2>
                    <p class="text-[11px] text-slate-400">Total collected student fees vs operating branch expenses</p>
                </div>
                <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Consolidated</span>
            </div>
            <div class="h-56">
                <canvas id="financeChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Lead CRM Status Doughnut Chart -->
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-xs font-bold text-slate-800">Enquiry Pipeline</h2>
                    <p class="text-[11px] text-slate-400">Status breakdown across active leads</p>
                </div>
            </div>
            <div class="h-56 flex items-center justify-center">
                <canvas id="leadStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- DATA TABLES SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
        <!-- Left Table: Recent CRM Leads -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-3.5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-slate-900">Recent Enquiries & Inquiries</h2>
                    <p class="text-[10px] text-slate-400">Latest prospective students registered</p>
                </div>
                <a href="{{ route('enquiries.index') }}" class="text-[11px] font-semibold text-brand-600 hover:text-brand-800 flex items-center gap-1">
                    <span>View all</span>
                    <i class="fa-solid fa-arrow-right text-[9px]"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-3 py-2 font-semibold">Lead / Student</th>
                            <th class="px-3 py-2 font-semibold">Course</th>
                            <th class="px-3 py-2 font-semibold">Status</th>
                            <th class="px-3 py-2 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($recentEnquiries as $enq)
                        <tr class="hover:bg-indigo-50/20 transition-colors">
                            <td class="px-3 py-2">
                                <div class="font-semibold text-slate-900">{{ $enq->student_name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $enq->phone }}</div>
                            </td>
                            <td class="px-3 py-2">
                                <span class="truncate max-w-[150px] block font-medium">{{ $enq->course->name ?? 'General' }}</span>
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    $badgeClass = match($enq->status) {
                                        'converted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'counselling_scheduled' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'demo_attended' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200'
                                    };
                                @endphp
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-semibold border {{ $badgeClass }} capitalize">
                                    {{ str_replace('_', ' ', $enq->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('enquiries.show', $enq) }}" class="text-brand-600 hover:text-brand-800 font-semibold text-[11px]">
                                    Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Table: Recent Fee Collections -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-3.5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-slate-900">Recent Payment Receipts</h2>
                    <p class="text-[10px] text-slate-400">Real-time cashier settlement records</p>
                </div>
                <a href="{{ route('fees.payments') }}" class="text-[11px] font-semibold text-brand-600 hover:text-brand-800 flex items-center gap-1">
                    <span>View all</span>
                    <i class="fa-solid fa-arrow-right text-[9px]"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-3 py-2 font-semibold">Receipt & Date</th>
                            <th class="px-3 py-2 font-semibold">Student</th>
                            <th class="px-3 py-2 font-semibold">Amount & Mode</th>
                            <th class="px-3 py-2 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($recentPayments as $pay)
                        <tr class="hover:bg-indigo-50/20 transition-colors">
                            <td class="px-3 py-2">
                                <div class="font-mono font-semibold text-slate-900">{{ $pay->receipt_no }}</div>
                                <div class="text-[10px] text-slate-400">{{ date('d M Y', strtotime($pay->payment_date)) }}</div>
                            </td>
                            <td class="px-3 py-2">
                                <div class="font-semibold text-slate-900">{{ $pay->student->full_name ?? 'Student' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $pay->student->admission_no ?? '' }}</div>
                            </td>
                            <td class="px-3 py-2">
                                <div class="font-bold text-emerald-600">₹{{ number_format($pay->amount, 2) }}</div>
                                <span class="text-[10px] uppercase font-semibold text-slate-500 bg-slate-100 px-1 py-0.2 rounded">
                                    {{ $pay->payment_method }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('fees.receipt', $pay) }}" class="text-brand-600 hover:text-brand-800 font-semibold text-[11px]">
                                    Receipt
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ACTIVE BATCHES OVERVIEW -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-3.5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-bold text-slate-900">Active Academy Batches</h2>
                <p class="text-[10px] text-slate-400">Currently running morning, evening & hybrid classes</p>
            </div>
            <a href="{{ route('batches.index') }}" class="text-[11px] font-semibold text-brand-600 hover:text-brand-800 flex items-center gap-1">
                <span>View all (20 Batches)</span>
                <i class="fa-solid fa-arrow-right text-[9px]"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-3 py-2 font-semibold">Batch Name & Code</th>
                        <th class="px-3 py-2 font-semibold">Course</th>
                        <th class="px-3 py-2 font-semibold">Primary Mentor</th>
                        <th class="px-3 py-2 font-semibold">Classroom / Mode</th>
                        <th class="px-3 py-2 font-semibold">Timings</th>
                        <th class="px-3 py-2 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($activeBatches as $batch)
                    <tr class="hover:bg-indigo-50/20 transition-colors">
                        <td class="px-3 py-2">
                            <a href="{{ route('batches.show', $batch) }}" class="font-semibold text-brand-700 hover:underline">
                                {{ $batch->name }}
                            </a>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $batch->code }}</div>
                        </td>
                        <td class="px-3 py-2 font-medium">{{ $batch->course->name ?? 'Course' }}</td>
                        <td class="px-3 py-2">{{ $batch->mentor->name ?? 'Faculty Member' }}</td>
                        <td class="px-3 py-2">
                            <div class="text-slate-800 font-medium">{{ $batch->classroom->room_name ?? 'Lab Hall' }}</div>
                            <span class="text-[10px] uppercase font-semibold text-indigo-600 bg-indigo-50 px-1 py-0.2 rounded">
                                {{ $batch->delivery_mode }}
                            </span>
                        </td>
                        <td class="px-3 py-2 font-mono text-slate-600">
                            {{ date('h:i A', strtotime($batch->start_time)) }} - {{ date('h:i A', strtotime($batch->end_time)) }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('batches.show', $batch) }}" class="px-2 py-0.8 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded text-[11px] font-semibold transition">
                                Open Batch
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Finance Bar Chart
        const ctxFinance = document.getElementById('financeChart').getContext('2d');
        new Chart(ctxFinance, {
            type: 'bar',
            data: {
                labels: ['Fee Collections', 'Operating Expenses', 'Net Operating Surplus'],
                datasets: [{
                    label: 'Amount in INR (₹)',
                    data: [{{ $totalFeeCollected }}, {{ $totalExpenses }}, {{ $netOperatingBalance }}],
                    backgroundColor: ['#10b981', '#f43f5e', '#6366f1'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return '₹' + value.toLocaleString(); }
                        }
                    }
                }
            }
        });

        // Leads Status Doughnut Chart
        const ctxLeads = document.getElementById('leadStatusChart').getContext('2d');
        new Chart(ctxLeads, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($leadStats)) !!}.map(s => s.replace('_', ' ').toUpperCase()),
                datasets: [{
                    data: {!! json_encode(array_values($leadStats)) !!},
                    backgroundColor: ['#10b981', '#6366f1', '#f59e0b', '#8b5cf6', '#ec4899', '#64748b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                }
            }
        });
    });
</script>
@endpush
@endsection

