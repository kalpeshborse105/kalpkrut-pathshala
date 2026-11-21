<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification — Kalpkrut Pathshala</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="h-full flex items-center justify-center p-4 font-sans text-slate-800">

    <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        
        <!-- Header -->
        <div class="bg-slate-900 p-6 text-center text-white relative">
            <div class="w-12 h-12 mx-auto mb-2 rounded-xl bg-indigo-600 flex items-center justify-center text-xl shadow-md">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h1 class="text-lg font-bold">{{ $appInstituteName ?? 'Kalpkrut Pathshala' }}</h1>
            <p class="text-xs text-slate-400">Official Digital Credential & Certificate Verification Portal</p>
        </div>

        <div class="p-6 sm:p-8">
            @if($certificate)
                <!-- Verified Status Banner -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center mb-6">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md mb-2">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h2 class="text-base font-extrabold text-emerald-900">Valid & Authenticated Credential</h2>
                    <p class="text-xs text-emerald-700 mt-0.5">This certificate record has been verified against {{ $appInstituteName ?? 'Kalpkrut Pathshala' }} official records.</p>
                </div>

                <!-- Credential Details Table -->
                <div class="space-y-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div class="flex justify-between py-1 border-b border-slate-200">
                        <span class="text-slate-500">Student Name:</span>
                        <strong class="text-slate-900 text-sm">{{ $certificate->student->full_name }}</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200">
                        <span class="text-slate-500">Admission No:</span>
                        <strong class="font-mono text-slate-800">{{ $certificate->student->admission_no }}</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200">
                        <span class="text-slate-500">Course / Program:</span>
                        <strong class="text-brand-700">{{ $certificate->course->name }}</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200">
                        <span class="text-slate-500">Award Standing:</span>
                        <strong class="text-indigo-900">{{ $certificate->grade }}</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200">
                        <span class="text-slate-500">Certificate No:</span>
                        <strong class="font-mono text-slate-800">{{ $certificate->certificate_no }}</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200">
                        <span class="text-slate-500">Issue Date:</span>
                        <span class="font-mono text-slate-700">{{ date('d F Y', strtotime($certificate->issue_date)) }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Campus:</span>
                        <span class="font-medium text-slate-700">{{ $certificate->student->branch->name ?? 'Indiranagar Central' }}</span>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('certificates.show', $certificate) }}" class="inline-flex items-center gap-2 text-xs font-bold text-brand-600 hover:text-brand-800">
                        <span>View Certificate Document</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            @else
                <!-- Invalid Status -->
                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-center mb-4">
                    <div class="w-12 h-12 mx-auto rounded-full bg-rose-600 text-white flex items-center justify-center text-xl shadow-md mb-2">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                    <h2 class="text-base font-extrabold text-rose-900">Invalid Verification Code</h2>
                    <p class="text-xs text-rose-700 mt-1">No certificate found matching the code: <code class="font-bold">{{ $code }}</code></p>
                </div>
            @endif

            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-slate-600">
                    &larr; Return to Institute Portal
                </a>
            </div>
        </div>

    </div>

</body>
</html>


