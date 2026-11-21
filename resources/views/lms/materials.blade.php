@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Study Materials & Digital Content Repository</h1>
            <p class="text-xs text-slate-500 mt-1">Lecture slides, PDF notes, recorded video links, and curriculum resources.</p>
        </div>
    </div>

    <!-- 2 Column Layout: Materials Grid (Left) & Upload Form (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2-Cols: Materials Cards/List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Digital Resource Repository (30 Items)</h2>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($materials as $mat)
                    <div class="p-4 hover:bg-slate-50/70 transition flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-lg shrink-0">
                                @if($mat->material_type === 'video_link')
                                    <i class="fa-brands fa-youtube text-rose-600"></i>
                                @elseif($mat->material_type === 'pdf')
                                    <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                @else
                                    <i class="fa-solid fa-file-lines text-indigo-500"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">{{ $mat->title }}</h3>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    <strong class="text-brand-700">{{ $mat->course->name ?? 'Course' }}</strong>
                                    &bull; <span>{{ $mat->subject->name ?? 'Curriculum' }}</span>
                                </div>
                                <p class="text-slate-500 text-[11px] mt-0.5">{{ $mat->description }}</p>
                            </div>
                        </div>

                        <div>
                            @if($mat->external_url)
                                <a href="{{ $mat->external_url }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded-lg font-semibold inline-flex items-center gap-1.5 transition text-[11px]">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    <span>Open Link</span>
                                </a>
                            @else
                                <span class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg font-semibold inline-flex items-center gap-1.5 text-[11px]">
                                    <i class="fa-solid fa-download text-[10px]"></i>
                                    <span>Handout PDF</span>
                                </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-slate-400 text-xs italic">No study materials found.</div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $materials->links() }}
                </div>
            </div>
        </div>

        <!-- Right 1-Col: Upload Form -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-brand-600"></i>
                    <span>Upload Study Material</span>
                </h2>

                <form action="{{ route('lms.materials.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Target Course <span class="text-rose-500">*</span></label>
                        <select name="course_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Resource Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required placeholder="e.g. Complete Revision Handout: Chapter 4" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Material Content Type <span class="text-rose-500">*</span></label>
                        <select name="material_type" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            <option value="pdf">PDF E-Book / Handout</option>
                            <option value="video_link">Recorded Lecture Video URL</option>
                            <option value="notes">Lecture Notes / Code Snippets</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">External Resource URL (Optional)</label>
                        <input type="url" name="external_url" placeholder="https://youtube.com/... or Google Drive URL" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Content Summary</label>
                        <textarea name="description" rows="2" placeholder="Key topics covered, formulas, revision points..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-semibold shadow-xs transition">
                        Save to Course Library
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection

