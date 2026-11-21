@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Certificates & Digital Credentials</h1>
            <p class="text-xs text-slate-500 mt-1">Issue tamper-proof certificates with public cryptographic verification codes.</p>
        </div>
    </div>

    <!-- 2 Column Layout: Certificates List (Left) & Issue Certificate Form (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2-Cols: Issued Certificates -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Issued Course Certificates (10)</h2>
                        <p class="text-xs text-slate-400">Verifiable credentials issued upon complete course & fee clearance</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Certificate No</th>
                                <th class="px-4 py-3 font-semibold">Student Name</th>
                                <th class="px-4 py-3 font-semibold">Program</th>
                                <th class="px-4 py-3 font-semibold">Issue Date</th>
                                <th class="px-4 py-3 font-semibold">Grade</th>
                                <th class="px-4 py-3 font-semibold">Verification Code</th>
                                <th class="px-4 py-3 font-semibold text-right">Certificate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($certificates as $cert)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3 font-mono font-bold text-brand-700">{{ $cert->certificate_no }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $cert->student->full_name ?? 'Student' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $cert->student->admission_no ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $cert->course->name ?? 'Course' }}</td>
                                <td class="px-4 py-3 font-mono text-slate-500">{{ date('d M Y', strtotime($cert->issue_date)) }}</td>
                                <td class="px-4 py-3 font-bold text-indigo-700">{{ $cert->grade }}</td>
                                <td class="px-4 py-3 font-mono text-[11px]">
                                    <a href="{{ route('certificates.verify', $cert->verification_code) }}" target="_blank" class="text-emerald-600 hover:underline">
                                        {{ $cert->verification_code }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('certificates.show', $cert) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded font-semibold text-[11px]">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">No certificates issued yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $certificates->links() }}
                </div>
            </div>
        </div>

        <!-- Right 1-Col: Issue Certificate Form -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-award text-brand-600"></i>
                    <span>Issue Certificate</span>
                </h2>

                <form action="{{ route('certificates.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Select Graduating Student <span class="text-rose-500">*</span></label>
                        <select name="student_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            <option value="">-- Choose Candidate --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_no }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Completed Course <span class="text-rose-500">*</span></label>
                        <select name="course_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Certificate Award Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required value="Certificate of Professional Mastery & Graduation" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Awarded Grade / Standing <span class="text-rose-500">*</span></label>
                        <select name="grade" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold">
                            <option value="Distinction (A+)">Distinction (A+)</option>
                            <option value="First Class with Honours (A)">First Class with Honours (A)</option>
                            <option value="First Class (B)">First Class (B)</option>
                            <option value="Pass Class (C)">Pass Class (C)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Date of Issuance <span class="text-rose-500">*</span></label>
                        <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-semibold shadow-xs transition">
                        Generate & Digitally Sign Certificate
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection

