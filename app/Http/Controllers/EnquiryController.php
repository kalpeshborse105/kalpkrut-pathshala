<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Enquiry, EnquirySource, EnquiryFollowup, Course, Branch, User, AcademicSession};

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::with(['course', 'source', 'assignedTo', 'branch'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('student_name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('enquiry_no', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('course_id')) {
            $query->where('interested_course_id', $request->course_id);
        }

        $enquiries = $query->paginate(15)->withQueryString();
        $courses = Course::where('status', 'active')->get();
        $branches = Branch::all();
        $sources = EnquirySource::where('is_active', true)->get();

        return view('enquiries.index', compact('enquiries', 'courses', 'branches', 'sources'));
    }

    public function create()
    {
        $courses = Course::where('status', 'active')->get();
        $branches = Branch::all();
        $sources = EnquirySource::where('is_active', true)->get();
        $counsellors = User::whereIn('user_type', ['counsellor', 'branch_manager', 'super_admin'])->get();

        return view('enquiries.create', compact('courses', 'branches', 'sources', 'counsellors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'student_name' => 'required|string|max:191',
            'father_or_guardian_name' => 'nullable|string|max:191',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:191',
            'interested_course_id' => 'required|exists:courses,id',
            'enquiry_source_id' => 'nullable|exists:enquiry_sources,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'enquiry_date' => 'required|date',
            'next_follow_up_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'previous_school_or_college' => 'nullable|string|max:191',
        ]);

        $count = Enquiry::count() + 1;
        $validated['enquiry_no'] = 'ENQ-' . date('Y') . '-' . sprintf('%04d', $count);
        $session = AcademicSession::where('is_current', true)->first();
        $validated['academic_session_id'] = $session?->id;
        $validated['status'] = 'new';

        $enquiry = Enquiry::create($validated);

        if ($request->filled('initial_notes')) {
            EnquiryFollowup::create([
                'enquiry_id' => $enquiry->id,
                'user_id' => auth()->id() ?? 1,
                'followup_date' => $enquiry->enquiry_date,
                'contact_method' => 'in_person',
                'response_status' => 'interested',
                'conversation_summary' => $request->initial_notes,
                'next_followup_date' => $enquiry->next_follow_up_date,
            ]);
        }

        return redirect()->route('enquiries.show', $enquiry)->with('success', 'Enquiry registered successfully with Ref: ' . $enquiry->enquiry_no);
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load(['course', 'source', 'assignedTo', 'branch', 'followups.user', 'admission']);
        $counsellors = User::whereIn('user_type', ['counsellor', 'branch_manager', 'super_admin'])->get();

        return view('enquiries.show', compact('enquiry', 'counsellors'));
    }

    public function addFollowup(Request $request, Enquiry $enquiry)
    {
        $validated = $request->validate([
            'followup_date' => 'required|date',
            'contact_method' => 'required|in:call,whatsapp,email,in_person,sms',
            'response_status' => 'required|in:positive,interested,neutral,call_back_later,not_interested,wrong_number',
            'conversation_summary' => 'required|string',
            'next_followup_date' => 'nullable|date',
            'update_status' => 'nullable|in:new,contacted,counselling_scheduled,demo_attended,admission_offered,converted,dropped,closed',
        ]);

        EnquiryFollowup::create([
            'enquiry_id' => $enquiry->id,
            'user_id' => auth()->id() ?? 1,
            'followup_date' => $validated['followup_date'],
            'contact_method' => $validated['contact_method'],
            'response_status' => $validated['response_status'],
            'conversation_summary' => $validated['conversation_summary'],
            'next_followup_date' => $validated['next_followup_date'],
        ]);

        $enquiry->next_follow_up_date = $validated['next_followup_date'];
        if (!empty($validated['update_status'])) {
            $enquiry->status = $validated['update_status'];
        }
        $enquiry->save();

        return back()->with('success', 'Follow-up interaction logged successfully.');
    }
}

