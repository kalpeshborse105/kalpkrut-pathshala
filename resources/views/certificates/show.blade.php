@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('certificates.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Certificates</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('certificates.verify', $certificate->verification_code) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl text-xs font-semibold transition">
                <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                <span>Public Verification URL</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Print Certificate</span>
            </button>
        </div>
    </div>

    <!-- PRESTIGIOUS ORNAMENTAL CERTIFICATE CARD -->
    <div class="bg-[#fcfbf9] rounded-2xl border-8 border-double border-amber-600/60 shadow-2xl p-10 sm:p-14 text-center font-serif text-slate-800 relative overflow-hidden" id="printable-cert">

        <!-- Background Watermark -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03]">
            <i class="fa-solid fa-graduation-cap text-[320px]"></i>
        </div>

        <!-- Corner Ornaments -->
        <div class="absolute top-3 left-3 text-amber-600/50 text-2xl"><i class="fa-solid fa-certificate"></i></div>
        <div class="absolute top-3 right-3 text-amber-600/50 text-2xl"><i class="fa-solid fa-certificate"></i></div>
        <div class="absolute bottom-3 left-3 text-amber-600/50 text-2xl"><i class="fa-solid fa-certificate"></i></div>
        <div class="absolute bottom-3 right-3 text-amber-600/50 text-2xl"><i class="fa-solid fa-certificate"></i></div>

        <!-- Institute Header -->
        <div class="space-y-2 mb-8">
            <div class="w-16 h-16 mx-auto rounded-full bg-indigo-900 text-amber-400 flex items-center justify-center text-3xl shadow-lg border-2 border-amber-400">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h1 class="text-3xl font-black tracking-wider uppercase text-indigo-950 font-sans">
                {{ $appInstituteName ?? 'Kalpkrut Pathshala' }}
            </h1>
            <p class="text-xs uppercase tracking-widest text-amber-700 font-sans font-bold">
                Institute of Advanced Technology & Applied Sciences
            </p>
        </div>

        <!-- Certificate Title -->
        <div class="my-6">
            <div class="text-xs uppercase tracking-[0.25em] text-slate-500 font-sans font-semibold">This is to certify that</div>
            <div class="text-3xl font-extrabold text-slate-900 my-3 font-sans underline decoration-amber-500/50 decoration-2 underline-offset-8">
                {{ $certificate->student->full_name }}
            </div>
            <div class="text-xs font-mono text-slate-500 font-sans">
                Admission No: {{ $certificate->student->admission_no }} &bull; Roll No: {{ $certificate->student->roll_no }}
            </div>
        </div>

        <!-- Body -->
        <div class="max-w-2xl mx-auto space-y-3 my-6 font-sans text-sm text-slate-700">
            <p>has successfully completed the comprehensive professional curriculum in</p>
            <div class="text-xl font-bold text-indigo-900 font-serif">
                {{ $certificate->course->name }}
            </div>
            <p class="text-xs text-slate-600">
                and has fulfilled all rigorous examination, coursework, and laboratory requirements with distinction.
            </p>
            <div class="inline-block px-4 py-1.5 rounded-full bg-amber-50 border border-amber-300 text-amber-800 font-bold text-xs font-sans tracking-wide">
                Graduation Standing: {{ $certificate->grade }}
            </div>
        </div>

        <!-- Footer / Signatures -->
        <div class="pt-10 mt-10 border-t border-slate-200 grid grid-cols-3 gap-6 items-end font-sans text-xs">
            <!-- Left: Date & Cert No -->
            <div class="text-left space-y-1">
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Issue Date</div>
                <div class="font-bold text-slate-800 font-mono">{{ date('d F Y', strtotime($certificate->issue_date)) }}</div>
                <div class="text-[10px] text-slate-400 font-mono">{{ $certificate->certificate_no }}</div>
            </div>

            <!-- Center: Verification Badge -->
            <div class="text-center space-y-1">
                <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 border border-emerald-300 text-emerald-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Digitally Verified</div>
                <div class="text-[9px] font-mono text-slate-500">{{ $certificate->verification_code }}</div>
            </div>

            <!-- Right: Signatures -->
            <div class="text-right space-y-1">
                <div class="w-32 ml-auto border-b border-slate-400 mb-1"></div>
                <div class="font-bold text-slate-900 text-xs">Managing Director</div>
                <div class="text-[10px] text-slate-400 uppercase font-sans">Academic Council</div>
            </div>
        </div>

    </div>

</div>

<style>
@media print {
    .no-print, header, aside, footer { display: none !important; }
    body { background: white !important; }
    #printable-cert { border-width: 4px !important; box-shadow: none !important; }
}
</style>
@endsection


