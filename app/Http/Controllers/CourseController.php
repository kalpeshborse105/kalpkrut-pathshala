<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Course, CourseCategory, Institute, FeeStructure, FeeInstallment, Subject};

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['category', 'subjects', 'batches'])->latest()->get();
        $categories = CourseCategory::all();
        return view('courses.index', compact('courses', 'categories'));
    }

    public function create()
    {
        $categories = CourseCategory::all();
        $institutes = Institute::all();
        return view('courses.create', compact('categories', 'institutes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institute_id' => 'required|exists:institutes,id',
            'category_id' => 'required|exists:course_categories,id',
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50|unique:courses,code',
            'duration_value' => 'required|integer|min:1',
            'duration_unit' => 'required|in:days,weeks,months,years',
            'delivery_mode' => 'required|in:offline,online,hybrid',
            'base_fee' => 'required|numeric|min:0',
            'gst_percentage' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $base = (float)$validated['base_fee'];
        $gst = round($base * ((float)$validated['gst_percentage'] / 100), 2);
        $validated['total_fee'] = $base + $gst;
        $validated['status'] = 'active';

        $course = Course::create($validated);

        // Auto create standard 3-installment plan
        $fs = FeeStructure::create([
            'institute_id' => $course->institute_id,
            'course_id' => $course->id,
            'name' => $course->name . ' - Default Installment Plan',
            'total_amount' => $course->total_fee,
            'installment_count' => 3,
            'is_active' => true,
        ]);

        FeeInstallment::create([
            'fee_structure_id' => $fs->id,
            'installment_number' => 1,
            'name' => '1st Installment (Down-payment)',
            'due_days_offset' => 0,
            'percentage' => 40.00,
            'amount' => round($course->total_fee * 0.40, 2),
        ]);
        FeeInstallment::create([
            'fee_structure_id' => $fs->id,
            'installment_number' => 2,
            'name' => '2nd Installment',
            'due_days_offset' => 30,
            'percentage' => 30.00,
            'amount' => round($course->total_fee * 0.30, 2),
        ]);
        FeeInstallment::create([
            'fee_structure_id' => $fs->id,
            'installment_number' => 3,
            'name' => '3rd Installment',
            'due_days_offset' => 60,
            'percentage' => 30.00,
            'amount' => round($course->total_fee * 0.30, 2),
        ]);

        return redirect()->route('courses.show', $course)->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load(['category', 'subjects', 'batches.classroom', 'feeStructures.installments']);
        return view('courses.show', compact('course'));
    }

    public function addSubject(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50',
            'total_hours' => 'required|integer|min:1',
            'is_practical' => 'boolean',
            'syllabus' => 'nullable|string',
        ]);

        $validated['course_id'] = $course->id;
        $validated['is_practical'] = $request->boolean('is_practical');

        Subject::create($validated);

        return back()->with('success', 'Subject added to course syllabus.');
    }
}

