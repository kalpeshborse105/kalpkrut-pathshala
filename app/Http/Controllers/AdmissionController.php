<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Admission, Student, User, Course, Batch, Branch, AcademicSession, Enquiry, StudentFeeDue, Payment, Role
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Admission::with(['student', 'course', 'batch', 'branch'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('admission_no', 'like', "%{$s}%")
                  ->orWhereHas('student', function($sq) use ($s) {
                      $sq->where('first_name', 'like', "%{$s}%")
                         ->orWhere('last_name', 'like', "%{$s}%")
                         ->orWhere('phone', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $admissions = $query->paginate(15)->withQueryString();
        $branches = Branch::all();
        $courses = Course::where('status', 'active')->get();

        return view('admissions.index', compact('admissions', 'branches', 'courses'));
    }

    public function create(Request $request)
    {
        $enquiry = null;
        if ($request->filled('enquiry_id')) {
            $enquiry = Enquiry::with('course')->find($request->enquiry_id);
        }

        $branches = Branch::all();
        $courses = Course::with('feeStructures')->where('status', 'active')->get();
        $batches = Batch::where('status', 'active')->get();

        return view('admissions.create', compact('enquiry', 'branches', 'courses', 'batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'required|exists:batches,id',
            'enquiry_id' => 'nullable|exists:enquiries,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'email' => 'required|email|max:191',
            'phone' => 'required|string|max:20',
            'father_name' => 'nullable|string|max:191',
            'father_phone' => 'nullable|string|max:20',
            'current_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'total_course_fee' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string|max:191',
            'fee_plan' => 'required|in:full_upfront,installments',
            'installments_count' => 'required|integer|min:1|max:24',
            'collected_downpayment' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,upi,card,net_banking,cheque',
            'custom_installments' => 'nullable|array',
            'custom_installments.*.installment_no' => 'nullable|integer',
            'custom_installments.*.title' => 'nullable|string|max:191',
            'custom_installments.*.due_date' => 'nullable|date',
            'custom_installments.*.amount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function() use ($validated, $request) {
            $course = Course::findOrFail($validated['course_id']);
            $batch = Batch::findOrFail($validated['batch_id']);
            $branch = Branch::findOrFail($validated['branch_id']);
            $currentSession = AcademicSession::where('is_current', true)->first() ?? AcademicSession::first();

            $studentCount = Student::count() + 1;
            $admNo = 'ADM-' . date('Y') . '-' . sprintf('%04d', $studentCount);
            $rollNo = 'R' . date('y') . '-' . sprintf('%03d', $studentCount);

            // 1. Create User account for student
            $studentRole = Role::where('slug', 'student')->first();
            $user = User::create([
                'company_id' => $branch->institute->company_id ?? 1,
                'branch_id' => $branch->id,
                'role_id' => $studentRole?->id,
                'name' => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'user_type' => 'student',
                'status' => 'active',
                'password' => Hash::make('password123'),
            ]);

            // 2. Create Student
            $student = Student::create([
                'branch_id' => $branch->id,
                'user_id' => $user->id,
                'admission_no' => $admNo,
                'roll_no' => $rollNo,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'father_name' => $validated['father_name'],
                'father_phone' => $validated['father_phone'],
                'current_address' => $validated['current_address'],
                'city' => $validated['city'] ?? 'Ahmedabad',
                'status' => 'active',
            ]);

            // 3. Attach student to batch
            $batch->students()->attach($student->id, [
                'roll_no' => $rollNo,
                'enrolled_at' => now()->toDateString(),
                'status' => 'active',
            ]);

            // 4. Create Admission
            $discount = (float)($validated['discount_amount'] ?? 0);
            $hasCustomInstallments = !empty($request->custom_installments) && is_array($request->custom_installments) && count($request->custom_installments) > 0;

            if ($hasCustomInstallments && $validated['fee_plan'] === 'installments') {
                $customTotal = (float)collect($request->custom_installments)->sum('amount');
                $finalFee = $customTotal;
                $installmentsCount = count($request->custom_installments);
                $totalCourseFee = max((float)$validated['total_course_fee'], $finalFee + $discount);
            } else {
                $finalFee = max(0, (float)$validated['total_course_fee'] - $discount);
                $installmentsCount = (int)$validated['installments_count'];
                $totalCourseFee = (float)$validated['total_course_fee'];
            }

            $admission = Admission::create([
                'branch_id' => $branch->id,
                'academic_session_id' => $currentSession->id,
                'student_id' => $student->id,
                'course_id' => $course->id,
                'batch_id' => $batch->id,
                'enquiry_id' => $validated['enquiry_id'] ?? null,
                'admission_no' => $admNo,
                'admission_date' => now()->toDateString(),
                'total_course_fee' => $totalCourseFee,
                'discount_amount' => $discount,
                'discount_reason' => $validated['discount_reason'] ?? null,
                'final_agreed_fee' => $finalFee,
                'fee_plan' => $validated['fee_plan'],
                'installments_count' => $installmentsCount,
                'status' => 'confirmed',
                'admitted_by' => auth()->id() ?? 1,
            ]);

            // 5. Generate Installments Dues
            $downPaymentRemaining = !empty($validated['collected_downpayment']) ? (float)$validated['collected_downpayment'] : 0;

            if ($hasCustomInstallments && $validated['fee_plan'] === 'installments') {
                $idx = 1;
                foreach ($request->custom_installments as $item) {
                    $installmentAmount = (float)($item['amount'] ?? 0);
                    $dueDate = !empty($item['due_date']) ? $item['due_date'] : now()->addDays(($idx - 1) * 30)->toDateString();
                    $title = !empty($item['title']) ? $item['title'] : ($idx === 1 ? 'Admission Down-payment' : "Installment {$idx} Dues");

                    $paidThisDue = 0;
                    if ($downPaymentRemaining > 0 && $installmentAmount > 0) {
                        $paidThisDue = min($downPaymentRemaining, $installmentAmount);
                        $downPaymentRemaining -= $paidThisDue;
                    }

                    $isPaid = ($paidThisDue >= $installmentAmount && $installmentAmount > 0);
                    $isPartial = ($paidThisDue > 0 && $paidThisDue < $installmentAmount);

                    $due = StudentFeeDue::create([
                        'branch_id' => $branch->id,
                        'student_id' => $student->id,
                        'admission_id' => $admission->id,
                        'installment_no' => $idx,
                        'title' => $title,
                        'due_date' => $dueDate,
                        'base_amount' => $installmentAmount,
                        'fine_amount' => 0,
                        'discount_amount' => 0,
                        'net_amount' => $installmentAmount,
                        'paid_amount' => $paidThisDue,
                        'balance_amount' => max(0, $installmentAmount - $paidThisDue),
                        'status' => $isPaid ? 'paid' : ($isPartial ? 'partially_paid' : 'unpaid'),
                    ]);

                    if ($paidThisDue > 0) {
                        $receiptCount = Payment::count() + 1;
                        Payment::create([
                            'branch_id' => $branch->id,
                            'student_id' => $student->id,
                            'student_fee_due_id' => $due->id,
                            'receipt_no' => 'RCP-' . date('Y') . '-' . sprintf('%05d', $receiptCount),
                            'payment_date' => now()->toDateString(),
                            'amount' => $paidThisDue,
                            'payment_method' => $validated['payment_method'] ?? 'cash',
                            'transaction_reference' => 'TXN' . strtoupper(Str::random(8)),
                            'received_by' => auth()->id() ?? 1,
                            'remarks' => $idx === 1 ? 'Collected upon admission enrolment (Down-payment).' : "Collected upon admission enrolment for {$title}.",
                        ]);
                    }
                    $idx++;
                }
            } else {
                $count = $installmentsCount;
                $installmentAmount = round($finalFee / $count, 2);

                for ($i = 1; $i <= $count; $i++) {
                    $dueDate = now()->addDays(($i - 1) * 30)->toDateString();
                    $isDownPayment = ($i === 1);
                    $collectedNow = ($isDownPayment && !empty($validated['collected_downpayment'])) ? (float)$validated['collected_downpayment'] : 0;
                    $isPaid = ($collectedNow >= $installmentAmount);
                    $isPartial = ($collectedNow > 0 && $collectedNow < $installmentAmount);

                    $due = StudentFeeDue::create([
                        'branch_id' => $branch->id,
                        'student_id' => $student->id,
                        'admission_id' => $admission->id,
                        'installment_no' => $i,
                        'title' => $i === 1 ? 'Admission Down-payment' : "Installment {$i} Dues",
                        'due_date' => $dueDate,
                        'base_amount' => $installmentAmount,
                        'fine_amount' => 0,
                        'discount_amount' => 0,
                        'net_amount' => $installmentAmount,
                        'paid_amount' => $collectedNow,
                        'balance_amount' => max(0, $installmentAmount - $collectedNow),
                        'status' => $isPaid ? 'paid' : ($isPartial ? 'partially_paid' : 'unpaid'),
                    ]);

                    if ($collectedNow > 0) {
                        $receiptCount = Payment::count() + 1;
                        Payment::create([
                            'branch_id' => $branch->id,
                            'student_id' => $student->id,
                            'student_fee_due_id' => $due->id,
                            'receipt_no' => 'RCP-' . date('Y') . '-' . sprintf('%05d', $receiptCount),
                            'payment_date' => now()->toDateString(),
                            'amount' => $collectedNow,
                            'payment_method' => $validated['payment_method'] ?? 'cash',
                            'transaction_reference' => 'TXN' . strtoupper(Str::random(8)),
                            'received_by' => auth()->id() ?? 1,
                            'remarks' => 'Collected upon admission enrolment.',
                        ]);
                    }
                }
            }

            // 6. Update enquiry if converted
            if (!empty($validated['enquiry_id'])) {
                Enquiry::where('id', $validated['enquiry_id'])->update(['status' => 'converted']);
            }

            return redirect()->route('admissions.show', $admission)->with('success', "Admission successfully processed! Student Admission No: {$admNo}");
        });
    }

    public function show(Admission $admission)
    {
        $admission->load(['student.user', 'course', 'batch.mentor', 'branch', 'feeDues.payments']);
        return view('admissions.show', compact('admission'));
    }
}


