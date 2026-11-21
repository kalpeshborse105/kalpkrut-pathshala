@extends('layouts.app')

@section('content')
<div class="space-y-4" x-data="{ tab: 'institute' }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-sliders text-brand-600"></i>
                <span>Institute Settings & Administration</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Customize institute brand name, contact credentials, academic parameters, campuses and RBAC permissions.</p>
        </div>
    </div>

    <!-- TABS BAR -->
    <div class="flex items-center gap-2 border-b border-slate-200 text-xs font-semibold overflow-x-auto">
        <button @click="tab = 'institute'" 
                :class="tab === 'institute' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-2.5 transition flex items-center gap-1.5 shrink-0">
            <i class="fa-solid fa-building-columns"></i>
            <span>Institute Profile & Branding</span>
        </button>

        <button @click="tab = 'system'" 
                :class="tab === 'system' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-2.5 transition flex items-center gap-1.5 shrink-0">
            <i class="fa-solid fa-gear"></i>
            <span>Master System Parameters</span>
        </button>

        <button @click="tab = 'branches'" 
                :class="tab === 'branches' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-2.5 transition flex items-center gap-1.5 shrink-0">
            <i class="fa-solid fa-code-branch"></i>
            <span>Campuses & Branches ({{ $branches->count() }})</span>
        </button>

        <button @click="tab = 'users'" 
                :class="tab === 'users' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-2.5 transition flex items-center gap-1.5 shrink-0">
            <i class="fa-solid fa-users-gear"></i>
            <span>Staff Users & Roles ({{ $users->total() }})</span>
        </button>
    </div>

    <!-- TAB 1: INSTITUTE PROFILE & BRANDING -->
    <div x-show="tab === 'institute'" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Edit Institute Details Form -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200/80 shadow-xs p-5">
            <div class="border-b border-slate-100 pb-3 mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-brand-600"></i>
                        <span>Institute Identity & Official Details</span>
                    </h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Changes saved here immediately update the Sidebar, Login page, Fee Receipts, and Report Cards.</p>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Live Active
                </span>
            </div>

            <form action="{{ route('settings.general.update') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">
                            Institute Official Display Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="institute_name" required 
                               value="{{ $settings['institute_name'] ?? $institute->name ?? 'Kalpkrut Pathshala' }}" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 font-bold text-slate-900 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                        <p class="text-[10px] text-slate-400 mt-1">Displayed on the top header, sidebar brand, student portals, and invoices.</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">
                            Tagline / Motto
                        </label>
                        <input type="text" name="institute_tagline" 
                               value="{{ $settings['institute_tagline'] ?? $institute->tagline ?? 'Empowering Careers in Technology, Management & Modern Skills' }}" 
                               placeholder="e.g. Empowering Excellence in Competitive Exams & Career Skills"
                               class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Institute Code / Acronym
                        </label>
                        <input type="text" name="institute_code" 
                               value="{{ $settings['institute_code'] ?? $institute->code ?? 'KPATHSHALA' }}" 
                               placeholder="e.g. KPATHSHALA or APEX"
                               class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 font-mono uppercase text-brand-700 font-bold focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Official Email Address
                        </label>
                        <input type="email" name="institute_email" 
                               value="{{ $settings['institute_email'] ?? $institute->email ?? 'info@kalpkrutpathshala.com' }}" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Contact Helpline / Phone
                        </label>
                        <input type="text" name="institute_phone" 
                               value="{{ $settings['institute_phone'] ?? $institute->phone ?? '+91 80 4123 4567' }}" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Official Website URL
                        </label>
                        <input type="text" name="institute_website" 
                               value="{{ $settings['institute_website'] ?? $company->website ?? 'https://kalpkrutinfotech.com' }}" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">
                            Registered Office Address
                        </label>
                        <input type="text" name="institute_address" 
                               value="{{ $settings['institute_address'] ?? $company->address ?? '100 Feet Road, HAL 2nd Stage, Indiranagar' }}" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">City & State</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="institute_city" 
                                   value="{{ $settings['institute_city'] ?? $company->city ?? 'Ahmedabad' }}" 
                                   placeholder="City"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                            <input type="text" name="institute_state" 
                                   value="{{ $settings['institute_state'] ?? $company->state ?? 'Gujarat' }}" 
                                   placeholder="State"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Pincode / Postal Code</label>
                        <input type="text" name="institute_pincode" 
                               value="{{ $settings['institute_pincode'] ?? $company->pincode ?? '380009' }}" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 font-mono text-slate-700 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Registration / Affiliation No.</label>
                        <input type="text" name="registration_no" 
                               value="{{ $settings['registration_no'] ?? $company->registration_no ?? 'ED-REG-2024-9981' }}" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 font-mono text-slate-700 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">GSTIN / Tax ID</label>
                        <input type="text" name="gstin" 
                               value="{{ $settings['gstin'] ?? $company->gstin ?? '29AAAAA0000A1Z5' }}" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 font-mono uppercase text-slate-700 focus:bg-white focus:ring-1 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">Settings sync across database models & system cache.</span>
                    <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-bold shadow-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Institute Profile</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Live Preview Card -->
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-4">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-eye text-brand-600"></i>
                    <span>Live Branding Preview</span>
                </div>

                <!-- Sidebar Preview -->
                <div class="bg-slate-900 rounded-lg p-3 text-white border border-slate-800 shadow-inner mb-3">
                    <div class="text-[9px] uppercase tracking-wider text-slate-400 mb-1.5 font-bold">Sidebar Brand Header</div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white text-xs shrink-0">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-xs truncate">{{ $appInstituteName ?? 'Kalpkrut Pathshala' }}</div>
                            <div class="text-[9px] text-slate-400 truncate">{{ $settings['institute_tagline'] ?? $institute->tagline ?? 'Coaching Institute Platform' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Receipt Header Preview -->
                <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 mb-3">
                    <div class="text-[9px] uppercase tracking-wider text-slate-400 mb-1.5 font-bold">Official Receipt Header</div>
                    <div class="border-b border-slate-200 pb-2">
                        <div class="font-black text-xs text-slate-900 uppercase tracking-tight">{{ $appInstituteName ?? 'Kalpkrut Pathshala' }}</div>
                        <div class="text-[10px] text-slate-500 font-medium">{{ $settings['institute_tagline'] ?? $institute->tagline ?? 'Premier Coaching & Learning Centre' }}</div>
                        <div class="text-[9px] text-slate-400 mt-1 font-mono">
                            {{ $settings['institute_phone'] ?? $institute->phone ?? '+91 80 4123 4567' }} &bull; {{ $settings['institute_email'] ?? $institute->email ?? 'info@kalpkrutpathshala.com' }}
                        </div>
                    </div>
                    <div class="text-[9px] text-slate-500 mt-1 flex justify-between">
                        <span>GSTIN: {{ $settings['gstin'] ?? $company->gstin ?? '29AAAAA0000A1Z5' }}</span>
                        <span>Ref: {{ $settings['institute_code'] ?? $institute->code ?? 'KPATHSHALA' }}</span>
                    </div>
                </div>

                <!-- Quick Help -->
                <div class="bg-brand-50/60 rounded-lg p-2.5 border border-brand-100 text-[11px] text-brand-900 leading-relaxed">
                    <i class="fa-solid fa-circle-info text-brand-600 mr-1"></i>
                    <strong>Unified Central Management:</strong> The institute name automatically reflects on all student dashboards, staff portals, official receipts, certificates, and reports.
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: MASTER SYSTEM PARAMETERS -->
    <div x-show="tab === 'system'" class="max-w-3xl bg-white rounded-xl border border-slate-200/80 shadow-xs p-5 sm:p-6">
        <h2 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
            <i class="fa-solid fa-sliders text-brand-600"></i>
            <span>Master System Parameters & Financial Settings</span>
        </h2>
        <p class="text-xs text-slate-500 mb-4">Set default billing currency, tax rates, academic session year, and attendance thresholds.</p>

        <form action="{{ route('settings.general.update') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '₹' }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 font-bold text-slate-800">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Currency ISO Code</label>
                    <input type="text" name="currency_code" value="{{ $settings['currency_code'] ?? 'INR' }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 font-bold font-mono text-slate-800">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Academic Session Period</label>
                    <input type="text" name="academic_session_current" value="{{ $settings['academic_session_current'] ?? '2026-2027 Academic Year' }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-slate-800">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">GST / Tax Name</label>
                    <input type="text" name="tax_name" value="{{ $settings['tax_name'] ?? 'GST' }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Default GST Tax Rate (%)</label>
                    <input type="number" name="default_tax_rate" value="{{ $settings['default_tax_rate'] ?? '18' }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Minimum Attendance Threshold (%)</label>
                    <input type="number" name="attendance_threshold_percentage" value="{{ $settings['attendance_threshold_percentage'] ?? '75' }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <!-- Notification Gateways -->
            <div class="pt-4 border-t border-slate-100">
                <div class="font-bold text-xs text-slate-800 mb-2">Automated Notifications & Channels</div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-lg cursor-pointer">
                        <input type="hidden" name="email_alerts_enabled" value="0">
                        <input type="checkbox" name="email_alerts_enabled" value="1" {{ ($settings['email_alerts_enabled'] ?? '1') == '1' ? 'checked' : '' }} class="rounded text-brand-600 focus:ring-brand-500">
                        <span class="font-semibold text-slate-700">Email Alerts</span>
                    </label>

                    <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-lg cursor-pointer">
                        <input type="hidden" name="sms_gateway_enabled" value="0">
                        <input type="checkbox" name="sms_gateway_enabled" value="1" {{ ($settings['sms_gateway_enabled'] ?? '1') == '1' ? 'checked' : '' }} class="rounded text-brand-600 focus:ring-brand-500">
                        <span class="font-semibold text-slate-700">SMS Gateway</span>
                    </label>

                    <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-lg cursor-pointer">
                        <input type="hidden" name="whatsapp_alerts_enabled" value="0">
                        <input type="checkbox" name="whatsapp_alerts_enabled" value="1" {{ ($settings['whatsapp_alerts_enabled'] ?? '1') == '1' ? 'checked' : '' }} class="rounded text-brand-600 focus:ring-brand-500">
                        <span class="font-semibold text-slate-700">WhatsApp Alerts</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-bold shadow-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Master Parameters</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 3: PHYSICAL CAMPUSES & BRANCHES -->
    <div x-show="tab === 'branches'" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Configured Physical Campuses & Locations</h2>
                <span class="text-xs text-slate-400">{{ $branches->count() }} registered branches</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-3.5 py-2.5 font-semibold">Campus Name</th>
                            <th class="px-3.5 py-2.5 font-semibold">Code</th>
                            <th class="px-3.5 py-2.5 font-semibold">Contact Info</th>
                            <th class="px-3.5 py-2.5 font-semibold">Location</th>
                            <th class="px-3.5 py-2.5 font-semibold">Type</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($branches as $b)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-3.5 py-2.5 font-bold text-slate-900">{{ $b->name }}</td>
                            <td class="px-3.5 py-2.5 font-mono font-bold text-brand-700">{{ $b->code }}</td>
                            <td class="px-3.5 py-2.5">
                                <div>{{ $b->phone }}</div>
                                <div class="text-[11px] text-slate-400">{{ $b->email }}</div>
                            </td>
                            <td class="px-3.5 py-2.5 text-slate-600">{{ $b->city }}, {{ $b->state }}</td>
                            <td class="px-3.5 py-2.5">
                                @if($b->is_main)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Headquarters</span>
                                @else
                                    <span class="text-slate-400 text-[10px]">Campus Branch</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Branch Form -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-brand-600"></i>
                <span>Add Physical Campus</span>
            </h2>

            <form action="{{ route('settings.branches.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <input type="hidden" name="institute_id" value="{{ $institute->id ?? 1 }}">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Campus Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. South Campus - Koramangala" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Branch Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="e.g. BLR-SOUTH" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Contact Phone</label>
                    <input type="text" name="phone" placeholder="+91 98765 00000" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">City</label>
                    <input type="text" name="city" value="Ahmedabad" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                </div>

                <button type="submit" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-bold shadow-xs transition">
                    Register Campus
                </button>
            </form>
        </div>
    </div>

    <!-- TAB 4: STAFF USERS -->
    <div x-show="tab === 'users'" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">User Accounts & Roles</h2>
                <span class="text-xs text-slate-400">Total: {{ $users->total() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-3.5 py-2.5 font-semibold">User Name</th>
                            <th class="px-3.5 py-2.5 font-semibold">Email</th>
                            <th class="px-3.5 py-2.5 font-semibold">Role</th>
                            <th class="px-3.5 py-2.5 font-semibold">Campus</th>
                            <th class="px-3.5 py-2.5 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($users as $u)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-3.5 py-2.5">
                                <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $u->designation ?? $u->user_type }}</div>
                            </td>
                            <td class="px-3.5 py-2.5 font-mono">{{ $u->email }}</td>
                            <td class="px-3.5 py-2.5 font-semibold text-brand-700 capitalize">
                                {{ $u->role->name ?? str_replace('_', ' ', $u->user_type) }}
                            </td>
                            <td class="px-3.5 py-2.5">{{ $u->branch->name ?? 'Main Campus' }}</td>
                            <td class="px-3.5 py-2.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 uppercase">
                                    {{ $u->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Add Staff User Form -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-user-shield text-brand-600"></i>
                <span>Create Staff User</span>
            </h2>

            <form action="{{ route('settings.users.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Ramesh Chandra" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="ramesh@institute.com" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Role Permission <span class="text-rose-500">*</span></label>
                    <select name="role_id" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Assigned Campus <span class="text-rose-500">*</span></label>
                    <select name="branch_id" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Official Designation</label>
                    <input type="text" name="designation" placeholder="e.g. Academic Counsellor" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Account Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required value="password123" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 font-mono">
                </div>

                <button type="submit" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-bold shadow-xs transition">
                    Create User Account
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

