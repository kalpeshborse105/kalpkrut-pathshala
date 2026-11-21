@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    courseFee: {{ $enquiry && $enquiry->course ? $enquiry->course->total_fee : ($courses->first()->total_fee ?? 0) }},
    discount: 0,
    feePlan: 'installments',
    installmentsCount: 3,
    collectedDownpayment: '',
    installments: [],

    init() {
        this.generateDefaultInstallments();
    },

    get netBaseFee() {
        return Math.max(0, this.courseFee - this.discount);
    },

    get totalInstallmentFee() {
        if (this.feePlan === 'full_upfront') {
            return this.netBaseFee;
        }
        return this.installments.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
    },

    get feeDifference() {
        return Math.round((this.totalInstallmentFee - this.netBaseFee) * 100) / 100;
    },

    updateCourse(e) {
        const opt = e.target.selectedOptions[0];
        if (opt) {
            this.courseFee = parseFloat(opt.dataset.fee || 0);
            this.generateDefaultInstallments();
        }
    },

    onDiscountChange() {
        this.generateDefaultInstallments();
    },

    onPlanChange() {
        if (this.feePlan === 'full_upfront') {
            this.installmentsCount = 1;
        } else if (this.installmentsCount == 1) {
            this.installmentsCount = 3;
        }
        this.generateDefaultInstallments();
    },

    onInstallmentsCountChange() {
        this.generateDefaultInstallments();
    },

    generateDefaultInstallments() {
        const count = parseInt(this.installmentsCount) || 1;
        const total = this.netBaseFee;
        const split = count > 0 ? Math.floor((total / count) * 100) / 100 : 0;
        const today = new Date();

        const list = [];
        let runningSum = 0;
        for (let i = 1; i <= count; i++) {
            const d = new Date();
            d.setDate(today.getDate() + (i - 1) * 30);
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            const dueDate = `${yyyy}-${mm}-${dd}`;

            let title = i === 1 ? 'Installment 1 (Down Payment)' : `Installment ${i} Dues`;
            if (count === 1) {
                title = 'Full Upfront Course Fee';
            }

            let amt = (i === count) ? Math.max(0, Math.round((total - runningSum) * 100) / 100) : split;
            runningSum += amt;

            list.push({
                installment_no: i,
                title: title,
                due_date: dueDate,
                amount: amt,
            });
        }
        this.installments = list;
    },

    addInstallment() {
        const nextNo = this.installments.length + 1;
        const d = new Date();
        d.setDate(new Date().getDate() + (nextNo - 1) * 30);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');

        this.installments.push({
            installment_no: nextNo,
            title: `Installment ${nextNo} Dues`,
            due_date: `${yyyy}-${mm}-${dd}`,
            amount: 0,
        });
        this.installmentsCount = this.installments.length;
    },

    removeInstallment(index) {
        if (this.installments.length <= 1) return;
        this.installments.splice(index, 1);
        this.installments.forEach((item, idx) => {
            item.installment_no = idx + 1;
            if (idx === 0) {
                item.title = 'Installment 1 (Down Payment)';
            } else {
                item.title = `Installment ${idx + 1} Dues`;
            }
        });
        this.installmentsCount = this.installments.length;
    },

    resetToEqualSplit() {
        this.generateDefaultInstallments();
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Student Admission & Enrollment</h1>
            <p class="text-xs text-slate-500 mt-1">Issue official student registration, allocate batch and generate fee schedule.</p>
        </div>
        <a href="{{ route('admissions.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Admissions</span>
        </a>
    </div>

    @if($enquiry)
    <div class="bg-indigo-50 border border-indigo-200 text-indigo-900 p-4 rounded-2xl flex items-center justify-between text-xs">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-indigo-600 text-base"></i>
            <div>
                <span class="font-bold">Converting Lead:</span> {{ $enquiry->student_name }} ({{ $enquiry->enquiry_no }})
                &bull; Interested in: <strong class="underline">{{ $enquiry->course->name ?? 'Course' }}</strong>
            </div>
        </div>
        <span class="text-[10px] uppercase font-bold text-indigo-700 bg-white px-2 py-0.5 rounded shadow-xs">CRM Converted</span>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('admissions.store') }}" method="POST" class="space-y-6">
            @csrf
            @if($enquiry)
                <input type="hidden" name="enquiry_id" value="{{ $enquiry->id }}">
            @endif

            <!-- Organization & Academic Master Allocation -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-6 border-b border-slate-100">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Campus / Branch <span class="text-rose-500">*</span></label>
                    <select name="branch_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $enquiry && $enquiry->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Course Program <span class="text-rose-500">*</span></label>
                    <select name="course_id" required @change="updateCourse($event)" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" data-fee="{{ $c->total_fee }}" {{ $enquiry && $enquiry->interested_course_id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} (₹{{ number_format($c->total_fee) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Allocated Class Batch <span class="text-rose-500">*</span></label>
                    <select name="batch_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">
                                {{ $batch->name }} ({{ $batch->delivery_mode }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Student Demographics -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Student Demographics</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" value="{{ $enquiry ? explode(' ', $enquiry->student_name)[0] : old('first_name') }}" required placeholder="First name" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Last Name</label>
                        <input type="text" name="last_name" value="{{ $enquiry && strpos($enquiry->student_name, ' ') !== false ? substr($enquiry->student_name, strpos($enquiry->student_name, ' ') + 1) : old('last_name') }}" placeholder="Last name" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Gender <span class="text-rose-500">*</span></label>
                        <select name="gender" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="2005-06-15" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Student Mobile <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="{{ $enquiry ? $enquiry->phone : old('phone') }}" required placeholder="+91 98765 43210" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Student Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ $enquiry ? $enquiry->email : old('email') }}" required placeholder="student@example.com" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Parent Details -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Guardian Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Father / Guardian Name</label>
                        <input type="text" name="father_name" value="{{ $enquiry ? $enquiry->father_or_guardian_name : old('father_name') }}" placeholder="Guardian name" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Father / Guardian Phone</label>
                        <input type="text" name="father_phone" placeholder="+91 98765 00000" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Residential City</label>
                        <input type="text" name="city" value="Ahmedabad" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Financial Calculation & Fee Schedule Setup -->
            <div class="pt-4 border-t border-slate-100 bg-slate-50/70 p-5 rounded-2xl border border-slate-200/60 space-y-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-brand-700 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-calculator"></i>
                        <span>Fee Structure & Payment Schedule Setup</span>
                    </h3>
                    <span class="text-[11px] text-slate-500">Live dynamic installment & fee calculator</span>
                </div>

                <!-- Parameters Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Base Course Fee (incl. GST)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">₹</span>
                            <input type="number" name="total_course_fee" x-model.number="courseFee" @input="generateDefaultInstallments()" required class="w-full pl-7 pr-3 py-2 text-xs bg-white border border-slate-300 rounded-xl font-bold text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Scholarship / Fee Waiver</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">₹</span>
                            <input type="number" name="discount_amount" x-model.number="discount" @input="onDiscountChange()" min="0" class="w-full pl-7 pr-3 py-2 text-xs bg-white border border-slate-300 rounded-xl font-semibold text-emerald-600 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Payment Plan</label>
                        <select name="fee_plan" x-model="feePlan" @change="onPlanChange()" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            <option value="installments">Flexible Installments</option>
                            <option value="full_upfront">100% Upfront Lump-Sum</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Number of Installments</label>
                        <select name="installments_count" x-model.number="installmentsCount" @change="onInstallmentsCountChange()" :disabled="feePlan === 'full_upfront'" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:outline-none disabled:bg-slate-100 disabled:text-slate-400">
                            <option value="1">1 (Single Payment)</option>
                            <option value="2">2 Installments (50/50)</option>
                            <option value="3">3 Installments (Standard)</option>
                            <option value="4">4 Quarterly Installments</option>
                            <option value="6">6 Monthly Installments</option>
                            <option value="8">8 Installments</option>
                            <option value="12">12 Installments</option>
                        </select>
                    </div>
                </div>

                <!-- INSTALLMENT-WISE FORM & FEE MODIFICATION TABLE -->
                <div x-show="feePlan === 'installments'" class="space-y-3 bg-white p-4 sm:p-5 rounded-2xl border border-indigo-100 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-indigo-600"></i>
                                <span>Installment-Wise Breakdown & Fee Customization</span>
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Modify individual installment amounts and scheduled due dates. Higher installment totals or custom milestone payments will be saved directly into the fee ledger.
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="resetToEqualSplit()" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition flex items-center gap-1.5">
                                <i class="fa-solid fa-rotate-left"></i>
                                <span>Reset Equal Split</span>
                            </button>
                            <button type="button" @click="addInstallment()" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-semibold transition flex items-center gap-1.5">
                                <i class="fa-solid fa-plus"></i>
                                <span>Add Installment</span>
                            </button>
                        </div>
                    </div>

                    <!-- Installments Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200/80">
                                <tr>
                                    <th class="py-2.5 px-3 w-16">Seq</th>
                                    <th class="py-2.5 px-3 min-w-[180px]">Installment Title / Milestone</th>
                                    <th class="py-2.5 px-3 min-w-[150px]">Scheduled Due Date</th>
                                    <th class="py-2.5 px-3 min-w-[160px]">Installment Amount (₹)</th>
                                    <th class="py-2.5 px-3 text-right w-24">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(inst, index) in installments" :key="index">
                                    <tr class="hover:bg-indigo-50/30 transition-colors">
                                        <td class="py-2.5 px-3">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold text-[10px]" x-text="'#' + (index + 1)"></span>
                                            <input type="hidden" :name="'custom_installments[' + index + '][installment_no]'" :value="inst.installment_no">
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <input type="text" :name="'custom_installments[' + index + '][title]'" x-model="inst.title" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <input type="date" :name="'custom_installments[' + index + '][due_date]'" x-model="inst.due_date" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1.5 text-slate-400 font-bold text-xs">₹</span>
                                                <input type="number" step="0.01" min="0" :name="'custom_installments[' + index + '][amount]'" x-model.number="inst.amount" required class="w-full pl-6 pr-2.5 py-1.5 text-xs font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-3 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <template x-if="index === 0">
                                                    <button type="button" @click="collectedDownpayment = inst.amount" title="Set this amount as down-payment" class="px-2 py-1 text-[10px] font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded transition">
                                                        Set Downpayment
                                                    </button>
                                                </template>
                                                <template x-if="installments.length > 1">
                                                    <button type="button" @click="removeInstallment(index)" title="Remove installment" class="p-1 text-slate-400 hover:text-rose-600 transition">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Live Summary Pill & Calculation Cards -->
                <div class="p-4 bg-white rounded-xl border border-slate-200 space-y-3 text-xs">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-slate-600">
                        <div>
                            <span class="text-[11px] text-slate-400 block">Base Course Fee</span>
                            <strong class="text-sm text-slate-800">₹<span x-text="courseFee.toLocaleString()"></span></strong>
                        </div>
                        <div>
                            <span class="text-[11px] text-slate-400 block">Scholarship Waiver</span>
                            <strong class="text-sm text-emerald-600">-₹<span x-text="(discount || 0).toLocaleString()"></span></strong>
                        </div>
                        <div>
                            <span class="text-[11px] text-slate-400 block">Base Net Fee</span>
                            <strong class="text-sm text-slate-800">₹<span x-text="netBaseFee.toLocaleString()"></span></strong>
                        </div>
                        <div>
                            <span class="text-[11px] text-brand-600 font-semibold block">Total of All Installments</span>
                            <strong class="text-base text-brand-700">₹<span x-text="totalInstallmentFee.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></strong>
                        </div>
                    </div>

                    <!-- Installment Surcharge / Variance Callout -->
                    <div x-show="feePlan === 'installments'" class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-500 font-medium">Installment Plan Total:</span>
                            <strong class="text-slate-900 font-mono text-sm">₹<span x-text="totalInstallmentFee.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></strong>
                            <span class="text-slate-400 text-[11px]">(across <span x-text="installments.length"></span> periods)</span>
                        </div>

                        <div>
                            <template x-if="feeDifference > 0">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-900 rounded-lg text-xs font-bold">
                                    <i class="fa-solid fa-arrow-trend-up text-amber-600"></i>
                                    <span>+₹<span x-text="feeDifference.toLocaleString('en-IN', {minimumFractionDigits: 2})"></span> Installment Surcharge / Custom Increase</span>
                                </span>
                            </template>
                            <template x-if="feeDifference < 0">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-lg text-xs font-bold">
                                    <i class="fa-solid fa-arrow-trend-down text-emerald-600"></i>
                                    <span>-₹<span x-text="Math.abs(feeDifference).toLocaleString('en-IN', {minimumFractionDigits: 2})"></span> Custom Adjustment</span>
                                </span>
                            </template>
                            <template x-if="feeDifference === 0">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-semibold">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                    <span>Matches standard equal split</span>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Instant Downpayment Settlement at Desk -->
                <div class="pt-3 border-t border-slate-200/80">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Collect Down-Payment Now (Optional)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">₹</span>
                                <input type="number" name="collected_downpayment" x-model.number="collectedDownpayment" placeholder="e.g. 15000" class="w-full pl-7 pr-3 py-2 text-xs bg-white border border-slate-300 rounded-xl font-bold text-emerald-600 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            </div>
                            <span class="text-[10px] text-slate-400 mt-1 block">
                                <template x-if="collectedDownpayment > 0">
                                    <span class="text-emerald-700 font-semibold">
                                        <i class="fa-solid fa-receipt mr-1"></i>
                                        ₹<span x-text="(parseFloat(collectedDownpayment) || 0).toLocaleString()"></span> will be paid immediately for 1st installment with an official receipt.
                                    </span>
                                </template>
                                <template x-if="!collectedDownpayment || collectedDownpayment <= 0">
                                    <span>Leave blank if full amount or 1st installment will be paid later.</span>
                                </template>
                            </span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Payment Settlement Method</label>
                            <select name="payment_method" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                                <option value="upi">UPI / QR Code (PhonePe, GPay, Paytm)</option>
                                <option value="cash">Cash Settlement</option>
                                <option value="card">Credit / Debit Card Terminal</option>
                                <option value="net_banking">Direct Bank IMPS / NEFT</option>
                                <option value="cheque">Bank Cheque / DD</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admissions.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
                    Confirm Admission & Issue Enrolment
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

