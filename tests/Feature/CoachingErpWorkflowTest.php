<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{
    User, Student, Enquiry, Admission, Batch, Course, StudentFeeDue, Payment,
    Exam, Certificate, Expense, Classroom, Branch, AcademicSession
};

class CoachingErpWorkflowTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'admin@coaching.com')->first();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@coaching.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_dashboard_renders_for_authenticated_admin(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Kalpkrut Pathshala');
        $response->assertSee('Executive Operations Dashboard');
    }

    public function test_enquiries_index_and_creation(): void
    {
        $response = $this->actingAs($this->admin)->get('/enquiries');
        $response->assertStatus(200);
        $response->assertSee('Enquiries');

        $course = Course::first();
        $postResponse = $this->actingAs($this->admin)->post('/enquiries', [
            'branch_id' => 1,
            'student_name' => 'Kunal Kashyap',
            'phone' => '+91 99000 88000',
            'email' => 'kunal.test@example.com',
            'interested_course_id' => $course->id,
            'priority' => 'high',
            'enquiry_date' => date('Y-m-d'),
            'remarks' => 'Test Lead Inquirer',
        ]);

        $this->assertDatabaseHas('enquiries', [
            'student_name' => 'Kunal Kashyap',
            'phone' => '+91 99000 88000',
        ]);
    }

    public function test_admission_creation_with_installments_and_downpayment(): void
    {
        $course = Course::first();
        $batch = Batch::first();
        $branch = Branch::first();

        $uniq = uniqid();
        $postData = [
            'branch_id' => $branch->id,
            'course_id' => $course->id,
            'batch_id' => $batch->id,
            'first_name' => 'Pooja',
            'last_name' => 'Menon',
            'gender' => 'female',
            'date_of_birth' => '2004-08-20',
            'phone' => '+91 99' . rand(10000000, 99999999),
            'email' => 'pooja.' . $uniq . '@example.com',
            'father_name' => 'Narayana Menon',
            'father_phone' => '+91 98' . rand(10000000, 99999999),
            'current_address' => 'Indiranagar 2nd Stage',
            'city' => 'Ahmedabad',
            'total_course_fee' => $course->total_fee,
            'discount_amount' => 5000,
            'discount_reason' => 'Academic Merit',
            'fee_plan' => 'installments',
            'installments_count' => 3,
            'collected_downpayment' => 15000,
            'payment_method' => 'upi',
        ];

        $response = $this->actingAs($this->admin)->post('/admissions', $postData);
        $response->assertRedirect();

        $this->assertDatabaseHas('students', [
            'first_name' => 'Pooja',
            'last_name' => 'Menon',
            'email' => $postData['email'],
        ]);

        $student = Student::where('email', $postData['email'])->first();
        $this->assertNotNull($student);

        // Assert admission created
        $this->assertDatabaseHas('admissions', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'discount_amount' => 5000,
        ]);

        // Assert 3 installments generated
        $this->assertEquals(3, StudentFeeDue::where('student_id', $student->id)->count());

        // Assert payment recorded for downpayment
        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'amount' => 15000,
        ]);
    }

    public function test_admission_creation_with_custom_modified_installment_fees(): void
    {
        $course = Course::first();
        $batch = Batch::first();
        $branch = Branch::first();

        $uniq = uniqid();
        $postData = [
            'branch_id' => $branch->id,
            'course_id' => $course->id,
            'batch_id' => $batch->id,
            'first_name' => 'Rahul',
            'last_name' => 'Sharma',
            'gender' => 'male',
            'date_of_birth' => '2003-05-10',
            'phone' => '+91 91' . rand(10000000, 99999999),
            'email' => 'rahul.' . $uniq . '@example.com',
            'father_name' => 'Ramesh Sharma',
            'father_phone' => '+91 97' . rand(10000000, 99999999),
            'current_address' => 'Koramangala 4th Block',
            'city' => 'Ahmedabad',
            'total_course_fee' => 40000,
            'discount_amount' => 0,
            'fee_plan' => 'installments',
            'installments_count' => 3,
            'custom_installments' => [
                [
                    'installment_no' => 1,
                    'title' => 'Admission Down-payment',
                    'due_date' => now()->toDateString(),
                    'amount' => 18000,
                ],
                [
                    'installment_no' => 2,
                    'title' => 'Installment 2 (Mid Term)',
                    'due_date' => now()->addDays(30)->toDateString(),
                    'amount' => 15000,
                ],
                [
                    'installment_no' => 3,
                    'title' => 'Installment 3 (Final Clearance)',
                    'due_date' => now()->addDays(60)->toDateString(),
                    'amount' => 12000,
                ],
            ],
            'collected_downpayment' => 18000,
            'payment_method' => 'upi',
        ];

        $response = $this->actingAs($this->admin)->post('/admissions', $postData);
        $response->assertRedirect();

        $student = Student::where('email', $postData['email'])->first();
        $this->assertNotNull($student);

        // Sum of installments is 18000 + 15000 + 12000 = 45000 (higher than base 40000)
        $admission = Admission::where('student_id', $student->id)->first();
        $this->assertEquals(45000, $admission->final_agreed_fee);
        $this->assertEquals(3, $admission->installments_count);

        $dues = StudentFeeDue::where('student_id', $student->id)->orderBy('installment_no')->get();
        $this->assertCount(3, $dues);
        $this->assertEquals(18000, $dues[0]->net_amount);
        $this->assertEquals('paid', $dues[0]->status);
        $this->assertEquals(15000, $dues[1]->net_amount);
        $this->assertEquals('unpaid', $dues[1]->status);
        $this->assertEquals(12000, $dues[2]->net_amount);
        $this->assertEquals('unpaid', $dues[2]->status);

        // Downpayment was fully recorded
        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'amount' => 18000,
            'student_fee_due_id' => $dues[0]->id,
        ]);
    }

    public function test_student_directory_and_profile(): void
    {
        $student = Student::first();
        $response = $this->actingAs($this->admin)->get('/students/' . $student->id);
        $response->assertStatus(200);
        $response->assertSee($student->full_name);
        $response->assertSee($student->admission_no);
    }

    public function test_attendance_marking(): void
    {
        $batch = Batch::first();
        $student = $batch->students->first();

        $response = $this->actingAs($this->admin)->post('/attendance', [
            'batch_id' => $batch->id,
            'attendance_date' => date('Y-m-d'),
            'attendance' => [
                $student->id => 'present',
            ],
            'remarks' => [
                $student->id => 'Punctual attendance',
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_attendances', [
            'batch_id' => $batch->id,
            'student_id' => $student->id,
            'attendance_date' => date('Y-m-d'),
            'status' => 'present',
        ]);
    }

    public function test_courses_and_batches(): void
    {
        $response = $this->actingAs($this->admin)->get('/courses');
        $response->assertStatus(200);
        $response->assertSee('Web Development');

        $batchResponse = $this->actingAs($this->admin)->get('/batches');
        $batchResponse->assertStatus(200);
    }

    public function test_fee_dues_and_payment_receipt(): void
    {
        $due = StudentFeeDue::where('status', '!=', 'paid')->first() ?? StudentFeeDue::first();
        $paymentResponse = $this->actingAs($this->admin)->post('/fees/pay', [
            'student_fee_due_id' => $due->id,
            'amount' => 5000,
            'payment_method' => 'upi',
            'payment_date' => date('Y-m-d'),
            'transaction_reference' => 'UPI99887766',
            'remarks' => 'Automated test fee settlement',
        ]);

        $this->assertDatabaseHas('payments', [
            'student_fee_due_id' => $due->id,
            'transaction_reference' => 'UPI99887766',
        ]);

        $payment = Payment::where('transaction_reference', 'UPI99887766')->first();
        $receiptResponse = $this->actingAs($this->admin)->get('/fees/receipt/' . $payment->id);
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee($payment->receipt_no);
    }

    public function test_exam_report_card_view(): void
    {
        $exam = Exam::first();
        $student = $exam->batch->students->first();

        $response = $this->actingAs($this->admin)->get("/exams/{$exam->id}/report-card/{$student->id}");
        $response->assertStatus(200);
        $response->assertSee('Official Student Scorecard');
    }

    public function test_public_certificate_verification(): void
    {
        $cert = Certificate::first();
        $response = $this->get('/verify-certificate/' . $cert->verification_code);
        $response->assertStatus(200);
        $response->assertSee('Valid & Authenticated Credential', false);
        $response->assertSee($cert->certificate_no);
    }

    public function test_expense_logging(): void
    {
        $branch = Branch::first();
        $response = $this->actingAs($this->admin)->post('/expenses', [
            'branch_id' => $branch->id,
            'category' => 'Utilities',
            'title' => 'Emergency Generator Fuel',
            'amount' => 4500,
            'expense_date' => date('Y-m-d'),
            'payment_method' => 'cash',
            'paid_to' => 'City Fuel Station',
            'remarks' => 'Power backup fuel voucher',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expenses', [
            'title' => 'Emergency Generator Fuel',
            'amount' => 4500,
        ]);
    }

    public function test_student_dashboard_isolation(): void
    {
        $studentUser = User::where('user_type', 'student')->first();
        $this->assertNotNull($studentUser);

        $response = $this->actingAs($studentUser)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Student Academic Portal', false);
        $response->assertSee('MY ACADEMICS', false);
        $response->assertDontSee('STUDENT ACQUISITION', false);
        $response->assertDontSee('FINANCES & CASHIER', false);
        $response->assertDontSee('MANAGEMENT & ANALYTICS', false);
    }

    public function test_faculty_dashboard_isolation(): void
    {
        $facultyUser = User::where('user_type', 'faculty')->first();
        $this->assertNotNull($facultyUser);

        $response = $this->actingAs($facultyUser)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Faculty Academic Console', false);
        $response->assertSee('ACADEMICS & BATCHES', false);
        $response->assertDontSee('STUDENT ACQUISITION', false);
        $response->assertDontSee('FINANCES & CASHIER', false);
        $response->assertDontSee('MANAGEMENT & ANALYTICS', false);
    }

    public function test_accountant_dashboard_isolation(): void
    {
        $accountantUser = User::where('user_type', 'accountant')->first();
        $this->assertNotNull($accountantUser);

        $response = $this->actingAs($accountantUser)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Financial Accounts Console', false);
        $response->assertSee('FINANCES & CASHIER', false);
        $response->assertDontSee('STUDENT ACQUISITION', false);
        $response->assertDontSee('EVALUATION & LMS', false);
    }

    public function test_counsellor_dashboard_isolation(): void
    {
        $counsellorUser = User::where('user_type', 'counsellor')->first();
        $this->assertNotNull($counsellorUser);

        $response = $this->actingAs($counsellorUser)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Counselling & CRM Desk', false);
        $response->assertSee('STUDENT ACQUISITION', false);
        $response->assertDontSee('FINANCES & CASHIER', false);
        $response->assertDontSee('EVALUATION & LMS', false);
    }

    public function test_settings_view_and_institute_branding_update(): void
    {
        $response = $this->actingAs($this->admin)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('Institute Profile & Branding', false);
        $response->assertSee('Institute Identity & Official Details', false);

        // Update Institute Name and Tagline
        $updateResponse = $this->actingAs($this->admin)->post('/settings/general', [
            'institute_name' => 'Apex Super Coaching Academy',
            'institute_tagline' => 'Pioneering Future Leaders in Science & Tech',
            'institute_code' => 'APEX-SCA',
            'institute_email' => 'contact@apexsca.edu',
            'currency_symbol' => '₹',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        // Verify Database updates
        $this->assertEquals('Apex Super Coaching Academy', \App\Models\SystemSetting::where('key', 'institute_name')->value('value'));
        $this->assertEquals('Apex Super Coaching Academy', \App\Models\Institute::first()->name);

        // Verify Dashboard and Login render updated Institute Name
        $dashResponse = $this->actingAs($this->admin)->get('/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Apex Super Coaching Academy', false);

        \Illuminate\Support\Facades\Auth::logout();
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Apex Super Coaching Academy', false);

        // Restore original name
        $this->actingAs($this->admin)->post('/settings/general', [
            'institute_name' => 'Kalpkrut Pathshala',
        ]);
    }
}

