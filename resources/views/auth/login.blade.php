<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — {{ $appInstituteName ?? 'Kalpkrut Pathshala ERP' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        html { font-size: 14px; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-slate-900">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-gradient-to-br from-indigo-700 via-indigo-800 to-slate-900 p-6 text-center text-white relative">
            <div class="w-12 h-12 mx-auto mb-2 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center shadow-md border border-white/20">
                <i class="fa-solid fa-graduation-cap text-2xl text-indigo-300"></i>
            </div>
            <h1 class="text-xl font-bold tracking-tight">{{ $appInstituteName ?? 'Kalpkrut Pathshala ERP' }}</h1>
            <p class="text-xs text-indigo-200 mt-0.5">{{ $systemSettings['institute_tagline'] ?? 'Coaching Institute & Learning Management System' }}</p>
        </div>

        <div class="p-6">
            <!-- Flash Errors -->
            @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl p-3">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 bg-sky-50 border border-sky-200 text-sky-800 text-xs rounded-xl p-3">
                    {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Email / Admission No / Roll No
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-user absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                        <input type="text" name="email" value="{{ old('email', 'admin@coaching.com') }}" required 
                               placeholder="e.g. admin@coaching.com or student1.aditya@apexcoach.test"
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-600 focus:bg-white focus:outline-none transition">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Password</label>
                        <span class="text-[11px] text-indigo-600 font-medium">Demo: password123</span>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                        <input type="password" name="password" value="password123" required 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-600 focus:bg-white focus:outline-none transition">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <span class="text-xs text-slate-600">Remember session</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30 transition duration-150">
                    Sign In to Portal
                </button>
            </form>

            <!-- Quick Demo One-Click Sign-in -->
            <div class="mt-5 pt-4 border-t border-slate-100">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 text-center mb-2.5">
                    Fast Demo One-Click Sign-In
                </div>

                <!-- Featured Student Portal Button -->
                <a href="{{ route('quick-login', 'student') }}" class="mb-2 p-2.5 rounded-xl border border-emerald-300 bg-emerald-50/70 hover:bg-emerald-100/80 flex items-center justify-between text-slate-800 transition shadow-xs group">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm shadow-xs">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-emerald-900 group-hover:text-emerald-950">Student Academic Portal</div>
                            <div class="text-[10px] text-emerald-700">Aditya Rao &bull; Dues, Classes & Scorecards</div>
                        </div>
                    </div>
                    <span class="text-[10px] bg-white border border-emerald-200 text-emerald-700 px-2 py-0.5 rounded-full font-bold">1-Click</span>
                </a>

                <!-- Staff Role Buttons Grid -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('quick-login', 'admin') }}" class="p-2 rounded-lg border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-slate-700 transition">
                        <i class="fa-solid fa-shield-halved text-indigo-600 text-sm"></i>
                        <div class="truncate">
                            <div class="font-semibold text-xs leading-tight">Super Admin</div>
                            <div class="text-[10px] text-slate-400">Full Operations</div>
                        </div>
                    </a>

                    <a href="{{ route('quick-login', 'counsellor') }}" class="p-2 rounded-lg border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-slate-700 transition">
                        <i class="fa-solid fa-headset text-indigo-600 text-sm"></i>
                        <div class="truncate">
                            <div class="font-semibold text-xs leading-tight">Counsellor</div>
                            <div class="text-[10px] text-slate-400">Leads CRM</div>
                        </div>
                    </a>

                    <a href="{{ route('quick-login', 'accountant') }}" class="p-2 rounded-lg border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-slate-700 transition">
                        <i class="fa-solid fa-calculator text-indigo-600 text-sm"></i>
                        <div class="truncate">
                            <div class="font-semibold text-xs leading-tight">Accountant</div>
                            <div class="text-[10px] text-slate-400">Fee Cashier</div>
                        </div>
                    </a>

                    <a href="{{ route('quick-login', 'faculty') }}" class="p-2 rounded-lg border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-slate-700 transition">
                        <i class="fa-solid fa-chalkboard-user text-indigo-600 text-sm"></i>
                        <div class="truncate">
                            <div class="font-semibold text-xs leading-tight">Faculty</div>
                            <div class="text-[10px] text-slate-400">Classes & Marks</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

