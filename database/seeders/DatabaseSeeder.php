<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\{
    Company, Institute, Branch, AcademicSession, Role, Permission, User,
    EnquirySource, Enquiry, EnquiryFollowup, CounsellingSession, DemoClass,
    CourseCategory, Course, Subject, Classroom, Batch, BatchStudent, Timetable,
    Student, StudentDocument, Admission, StudentAttendance, StaffAttendance,
    FeeStructure, FeeInstallment, StudentFeeDue, Payment, Expense,
    Exam, ExamQuestion, StudentExamAttempt, Assignment, AssignmentSubmission,
    StudyMaterial, Certificate, PlacementRecord, SystemSetting, ActivityLog, Announcement
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions
        $roles = [
            ['name' => 'Super Administrator', 'slug' => 'super_admin', 'description' => 'Unrestricted global system access', 'is_system' => true],
            ['name' => 'Branch Manager', 'slug' => 'branch_manager', 'description' => 'Branch operations head', 'is_system' => true],
            ['name' => 'Academic Counsellor', 'slug' => 'counsellor', 'description' => 'Lead handling, counselling & admissions', 'is_system' => true],
            ['name' => 'Accountant / Cashier', 'slug' => 'accountant', 'description' => 'Fee collection, invoicing & expenses', 'is_system' => true],
            ['name' => 'Faculty / Instructor', 'slug' => 'faculty', 'description' => 'Teaching, timetable, attendance, exams', 'is_system' => true],
            ['name' => 'Student', 'slug' => 'student', 'description' => 'Student portal user', 'is_system' => true],
            ['name' => 'Parent / Guardian', 'slug' => 'parent', 'description' => 'Parent portal user', 'is_system' => true],
        ];

        $roleModels = [];
        foreach ($roles as $r) {
            $roleModels[$r['slug']] = Role::create($r);
        }

        $modules = ['leads', 'admissions', 'students', 'courses', 'batches', 'timetable', 'attendance', 'fees', 'exams', 'lms', 'hr', 'expenses', 'reports', 'settings'];
        foreach ($modules as $mod) {
            foreach (['view', 'create', 'edit', 'delete'] as $act) {
                $p = Permission::create([
                    'name' => ucfirst($act) . ' ' . ucfirst($mod),
                    'slug' => "{$mod}.{$act}",
                    'module' => $mod,
                ]);
                $roleModels['super_admin']->permissions()->attach($p->id);
            }
        }

        // 2. Company & Institutes
        $company = Company::create([
            'name' => 'Kalpkrut Infotech Pvt Ltd',
            'code' => 'KKI-CORP',
            'pan_number' => 'AABCK5678G',
            'gstin' => '29AABCK1234G1Z5',
            'email' => 'corporate@kalpkrutinfotech.com',
            'phone' => '+91 79000 00001',
            'website' => 'https://kalpkrutinfotech.com',
            'address' => '301, Kalpkrut Bhavan, Prahladnagar',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'pincode' => '380015',
            'status' => 'active',
        ]);

        $inst1 = Institute::create([
            'company_id' => $company->id,
            'name' => 'Kalpkrut Pathshala',
            'code' => 'KPS-MAIN',
            'tagline' => 'Nurturing Knowledge, Building Futures',
            'email' => 'info@kalpkrutpathshala.com',
            'phone' => '+91 80 4123 4567',
            'status' => 'active',
        ]);

        $inst2 = Institute::create([
            'company_id' => $company->id,
            'name' => 'Kalpkrut Junior Science & Test Prep',
            'code' => 'KJUNIOR',
            'tagline' => 'Excellence in School Boards, JEE & NEET Foundation',
            'email' => 'prep@kalpkrutpathshala.com',
            'phone' => '+91 80 4123 4568',
            'status' => 'active',
        ]);

        // 3. Branches (3 branches)
        $branchCentral = Branch::create([
            'institute_id' => $inst1->id,
            'name' => 'Main Campus - Prahladnagar',
            'code' => 'AMD-CENTRAL',
            'email' => 'central@kalpkrutpathshala.com',
            'phone' => '+91 98450 11001',
            'address' => '301, Prahladnagar Garden, Satellite Road',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'pincode' => '380015',
            'is_main' => true,
            'status' => 'active',
        ]);

        $branchNorth = Branch::create([
            'institute_id' => $inst1->id,
            'name' => 'North Hub - Hebbal',
            'code' => 'BLR-NORTH',
            'email' => 'north@kalpkrutpathshala.com',
            'phone' => '+91 98450 11002',
            'address' => 'Bellary Road, Near Esteem Mall, Hebbal',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'pincode' => '560024',
            'is_main' => false,
            'status' => 'active',
        ]);

        $branchDigital = Branch::create([
            'institute_id' => $inst1->id,
            'name' => 'Digital & Hybrid Learning Hub',
            'code' => 'BLR-DIGITAL',
            'email' => 'online@kalpkrutpathshala.com',
            'phone' => '+91 98450 11003',
            'address' => 'Cloud Hub, kalpkrut-pathshala Media Center, MG Road',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'pincode' => '380015',
            'is_main' => false,
            'status' => 'active',
        ]);

        $branches = [$branchCentral, $branchNorth, $branchDigital];

        // Academic Sessions
        $sessionCurrent = AcademicSession::create([
            'institute_id' => $inst1->id,
            'name' => '2026-2027 Academic Year',
            'code' => 'AY-2026-27',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
            'status' => 'active',
        ]);

        // 4. Staff Users: Super Admin, Branch Managers, Counsellors, Accountants, Faculty
        $adminUser = User::create([
            'company_id' => $company->id,
            'branch_id' => $branchCentral->id,
            'role_id' => $roleModels['super_admin']->id,
            'name' => 'Director Rajesh Sharma',
            'email' => 'admin@coaching.com',
            'phone' => '+91 98888 77771',
            'user_type' => 'super_admin',
            'designation' => 'Managing Director & Principal',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        // Branch Managers
        User::create([
            'company_id' => $company->id,
            'branch_id' => $branchCentral->id,
            'role_id' => $roleModels['branch_manager']->id,
            'name' => 'Arun Kumar Verma',
            'email' => 'manager.central@kalpkrutinfotech.com',
            'phone' => '+91 98888 77772',
            'user_type' => 'branch_manager',
            'designation' => 'Branch Head - Central',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'company_id' => $company->id,
            'branch_id' => $branchNorth->id,
            'role_id' => $roleModels['branch_manager']->id,
            'name' => 'Meenakshi Sundaram',
            'email' => 'manager.north@kalpkrutinfotech.com',
            'phone' => '+91 98888 77773',
            'user_type' => 'branch_manager',
            'designation' => 'Branch Head - North',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        // 5 Counsellors
        $counsellors = [];
        $counsellorNames = [
            'Priya Nair', 'Vikram Malhotra', 'Sneha Kulkarni', 'Ananya Sengupta', 'Karthik Raman'
        ];
        foreach ($counsellorNames as $idx => $name) {
            $br = $branches[$idx % 3];
            $counsellors[] = User::create([
                'company_id' => $company->id,
                'branch_id' => $br->id,
                'role_id' => $roleModels['counsellor']->id,
                'name' => $name,
                'email' => 'counsellor' . ($idx + 1) . '@kalpkrutinfotech.com',
                'phone' => '+91 97777 ' . (20000 + $idx),
                'user_type' => 'counsellor',
                'designation' => 'Senior Academic Counsellor',
                'status' => 'active',
                'password' => Hash::make('password123'),
            ]);
        }

        // Accountants
        $accountant = User::create([
            'company_id' => $company->id,
            'branch_id' => $branchCentral->id,
            'role_id' => $roleModels['accountant']->id,
            'name' => 'Suresh Bhatt',
            'email' => 'accounts@kalpkrutinfotech.com',
            'phone' => '+91 96666 11111',
            'user_type' => 'accountant',
            'designation' => 'Chief Accounts Officer',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        // 15 Faculty Members
        $facultyMembers = [];
        $facultyData = [
            ['Dr. Alok Nath', 'Python & Machine Learning Specialist', 'alok.nath@kalpkrutinfotech.com'],
            ['Sunita Deshmukh', 'Senior Laravel & PHP Architect', 'sunita.d@kalpkrutinfotech.com'],
            ['Rohan Banerjee', 'Flutter & Mobile Development Lead', 'rohan.b@kalpkrutinfotech.com'],
            ['Kavita Krishnan', 'Digital Marketing & Growth Consultant', 'kavita.k@kalpkrutinfotech.com'],
            ['Manish Tiwari', 'AI & Deep Learning Mentor', 'manish.t@kalpkrutinfotech.com'],
            ['Deepika Padukolli', 'Computer Applications & Office Lead', 'deepika.p@kalpkrutinfotech.com'],
            ['Robert D\'Souza', 'Corporate English & Soft Skills Trainer', 'robert.d@kalpkrutinfotech.com'],
            ['Prof. R. C. Mukherjee', 'JEE Advanced Physics Master Faculty', 'rc.mukherjee@kalpkrutinfotech.com'],
            ['Dr. Smita Rao', 'NEET Botany & Zoology Specialist', 'smita.rao@kalpkrutinfotech.com'],
            ['Harish Chandra', 'Senior Mathematics HOD', 'harish.c@kalpkrutinfotech.com'],
            ['Geetha Nambiar', 'Class 10 CBSE Science & Math Faculty', 'geetha.n@kalpkrutinfotech.com'],
            ['CA Vineet Saxena', 'Chartered Accountant & Tally Specialist', 'vineet.s@kalpkrutinfotech.com'],
            ['Nikhil Joshi', 'Full Stack MERN & Cloud Faculty', 'nikhil.j@kalpkrutinfotech.com'],
            ['Tanvi Agarwal', 'UI/UX & Frontend Development Trainer', 'tanvi.a@kalpkrutinfotech.com'],
            ['Pradeep Hegde', 'Quantitative Aptitude & Reasoning Coach', 'pradeep.h@kalpkrutinfotech.com'],
        ];

        foreach ($facultyData as $idx => $f) {
            $br = $branches[$idx % 3];
            $facultyMembers[] = User::create([
                'company_id' => $company->id,
                'branch_id' => $br->id,
                'role_id' => $roleModels['faculty']->id,
                'name' => $f[0],
                'email' => $f[2],
                'phone' => '+91 95555 ' . (30000 + $idx),
                'user_type' => 'faculty',
                'designation' => $f[1],
                'status' => 'active',
                'password' => Hash::make('password123'),
            ]);
        }

        // 5. Classrooms (10 classrooms)
        $classrooms = [];
        $roomConfigs = [
            ['Lab 101 - High Performance Coding Lab', 35, '1st Floor', true, true, true],
            ['Lab 102 - Web Dev & Design Studio', 30, '1st Floor', true, true, false],
            ['Lecture Hall A - Harvard Theatre Style', 60, '2nd Floor', true, true, true],
            ['Lecture Hall B - Advanced Tech Suite', 50, '2nd Floor', true, true, true],
            ['Seminar Room C - Digital Boardroom', 25, '3rd Floor', true, true, true],
            ['Classroom 201 - North Tech Lab', 40, 'Ground Floor', true, true, false],
            ['Classroom 202 - North Foundation Hall', 45, '1st Floor', true, true, true],
            ['Classroom 203 - North Language Lab', 30, '1st Floor', true, true, true],
            ['Digital Studio 1 - Virtual Broadcast Suite', 500, 'MG Road Studio', true, true, true],
            ['Digital Studio 2 - Live Webinar Room', 500, 'MG Road Studio', true, true, true],
        ];

        foreach ($roomConfigs as $idx => $cfg) {
            $br = ($idx < 5) ? $branchCentral : (($idx < 8) ? $branchNorth : $branchDigital);
            $classrooms[] = Classroom::create([
                'branch_id' => $br->id,
                'room_number' => 'R-' . (101 + $idx),
                'room_name' => $cfg[0],
                'seating_capacity' => $cfg[1],
                'floor' => $cfg[2],
                'has_projector' => $cfg[3],
                'has_air_conditioner' => $cfg[4],
                'has_smart_board' => $cfg[5],
                'status' => 'active',
            ]);
        }

        // 6. Course Categories & 10 Courses (Strictly matching Section 45)
        $catTech = CourseCategory::create(['institute_id' => $inst1->id, 'name' => 'Information Technology & Software', 'slug' => 'it-software']);
        $catMarketing = CourseCategory::create(['institute_id' => $inst1->id, 'name' => 'Digital Business & Marketing', 'slug' => 'marketing']);
        $catLanguage = CourseCategory::create(['institute_id' => $inst1->id, 'name' => 'Languages & Corporate Skills', 'slug' => 'languages']);
        $catSchool = CourseCategory::create(['institute_id' => $inst2->id, 'name' => 'School & Board Academics', 'slug' => 'school-boards']);
        $catTestPrep = CourseCategory::create(['institute_id' => $inst2->id, 'name' => 'Competitive Entrance Coaching', 'slug' => 'test-prep']);
        $catFinance = CourseCategory::create(['institute_id' => $inst1->id, 'name' => 'Finance & Professional Accounting', 'slug' => 'accounting-finance']);

        $coursesData = [
            ['Digital Marketing', 'DM-PRO', 4, 'months', 'hybrid', 35000, $catMarketing->id, 'Comprehensive SEO, SEM, Social Media Marketing, Meta Ads, GA4, Email Marketing, Content Strategies.'],
            ['Web Development', 'WEB-DEV-101', 6, 'months', 'hybrid', 55000, $catTech->id, 'Full-stack development with modern HTML5, CSS3, Tailwind, JavaScript, PHP 8, Laravel and Vue.js.'],
            ['Flutter App Development', 'FLUTTER-PRO', 5, 'months', 'hybrid', 48000, $catTech->id, 'Cross-platform native iOS and Android application development using Dart, Flutter, State Management & Firebase.'],
            ['Artificial Intelligence', 'AI-ML-ENG', 8, 'months', 'hybrid', 75000, $catTech->id, 'Advanced AI & Machine Learning with Python, TensorFlow, PyTorch, Scikit-Learn, LLMs & Prompt Engineering.'],
            ['Python', 'PY-DATA', 4, 'months', 'hybrid', 32000, $catTech->id, 'Core Python programming, OOP, data analysis with Pandas & NumPy, automation scripts, and Web Scraping.'],
            ['Computer Fundamentals', 'COMP-FUND', 3, 'months', 'offline', 18000, $catTech->id, 'Operating systems, MS Office Suite (Excel, Word, PowerPoint), internet security, cloud basics, and typing.'],
            ['Spoken English', 'ENG-FLUENT', 3, 'months', 'offline', 15000, $catLanguage->id, 'Spoken English fluency, public speaking, business communication, email etiquette, and personality development.'],
            ['School Tuition', 'TUITION-CBSE10', 10, 'months', 'offline', 42000, $catSchool->id, 'Class 10 CBSE complete syllabus for Mathematics, Science (Physics, Chem, Bio) with board exam test series.'],
            ['Competitive Examination Coaching', 'JEE-NEET-PREP', 12, 'months', 'hybrid', 95000, $catTestPrep->id, 'Intensive coaching for IIT-JEE Main/Advanced and NEET Medical entrance with daily practice papers (DPP).'],
            ['Accounting', 'ACC-TALLY-GST', 4, 'months', 'offline', 26000, $catFinance->id, 'Tally Prime, GST filing, TDS compliance, Income Tax basics, Payroll management, and computerized accounting.'],
        ];

        $courses = [];
        foreach ($coursesData as $c) {
            $base = $c[5];
            $gst = round($base * 0.18, 2);
            $total = $base + $gst;

            $course = Course::create([
                'institute_id' => $inst1->id,
                'category_id' => $c[6],
                'name' => $c[0],
                'code' => $c[1],
                'duration_value' => $c[2],
                'duration_unit' => $c[3],
                'delivery_mode' => $c[4],
                'base_fee' => $base,
                'gst_percentage' => 18.00,
                'total_fee' => $total,
                'description' => $c[7],
                'status' => 'active',
            ]);

            // Create fee structure for course (downpayment + 2 installments)
            $fs = FeeStructure::create([
                'institute_id' => $inst1->id,
                'course_id' => $course->id,
                'name' => $course->name . ' - Standard 3-Part Installment Plan',
                'total_amount' => $total,
                'installment_count' => 3,
                'description' => 'Installment 1: 40% on Admission, Installment 2: 30% at 30 days, Installment 3: 30% at 60 days',
                'is_active' => true,
            ]);

            FeeInstallment::create([
                'fee_structure_id' => $fs->id,
                'installment_number' => 1,
                'name' => '1st Installment (Admission Down-Payment)',
                'due_days_offset' => 0,
                'percentage' => 40.00,
                'amount' => round($total * 0.40, 2),
            ]);

            FeeInstallment::create([
                'fee_structure_id' => $fs->id,
                'installment_number' => 2,
                'name' => '2nd Installment (Mid-term Dues)',
                'due_days_offset' => 30,
                'percentage' => 30.00,
                'amount' => round($total * 0.30, 2),
            ]);

            FeeInstallment::create([
                'fee_structure_id' => $fs->id,
                'installment_number' => 3,
                'name' => '3rd Installment (Final Clearance)',
                'due_days_offset' => 60,
                'percentage' => 30.00,
                'amount' => round($total * 0.30, 2),
            ]);

            $courses[] = $course;
        }

        // 7. 30 Subjects across the 10 Courses
        $subjectsData = [
            // Digital Marketing (3)
            [$courses[0]->id, 'Search Engine Optimization (SEO)', 'SEO-101', 30, true],
            [$courses[0]->id, 'Performance Marketing & Google Ads', 'PPC-102', 35, true],
            [$courses[0]->id, 'Social Media & Brand Building', 'SMM-103', 25, false],

            // Web Development (3)
            [$courses[1]->id, 'Modern Frontend (HTML5, Tailwind, Vue)', 'FED-201', 50, true],
            [$courses[1]->id, 'Backend Engineering with PHP 8 & Laravel', 'BED-202', 60, true],
            [$courses[1]->id, 'Relational Databases & MySQL Architecture', 'DB-203', 40, true],

            // Flutter App Development (3)
            [$courses[2]->id, 'Dart Programming Language Mastery', 'DART-301', 35, true],
            [$courses[2]->id, 'Flutter UI Architecture & State Management', 'FLT-302', 55, true],
            [$courses[2]->id, 'REST APIs, Local SQLite & Firebase Integration', 'FLT-303', 45, true],

            // Artificial Intelligence (3)
            [$courses[3]->id, 'Statistical Foundations for Machine Learning', 'AIML-401', 45, false],
            [$courses[3]->id, 'Supervised & Unsupervised ML Algorithms', 'AIML-402', 60, true],
            [$courses[3]->id, 'Deep Neural Networks, LLMs & GenAI', 'AIML-403', 70, true],

            // Python (3)
            [$courses[4]->id, 'Python Core Syntax, Data Structures & OOP', 'PY-501', 40, true],
            [$courses[4]->id, 'Data Analysis with Pandas, NumPy & Matplotlib', 'PY-502', 45, true],
            [$courses[4]->id, 'Automated Web Scraping & REST Clients', 'PY-503', 35, true],

            // Computer Fundamentals (3)
            [$courses[5]->id, 'Computer Hardware, OS & Networking Basics', 'CF-601', 25, false],
            [$courses[5]->id, 'Advanced Microsoft Excel & Data Spreadsheets', 'CF-602', 35, true],
            [$courses[5]->id, 'Office Presentation, Documentation & Cloud', 'CF-603', 25, true],

            // Spoken English (3)
            [$courses[6]->id, 'Grammar, Phonetics & Pronunciation Clinic', 'ENG-701', 30, false],
            [$courses[6]->id, 'Conversational English & Extempore Speech', 'ENG-702', 35, true],
            [$courses[6]->id, 'Corporate Email, Interview & Group Discussions', 'ENG-703', 30, true],

            // School Tuition (3)
            [$courses[7]->id, 'CBSE Class 10 Pure & Applied Mathematics', 'SCH-801', 60, false],
            [$courses[7]->id, 'CBSE Class 10 Physics & Chemistry Concepts', 'SCH-802', 60, true],
            [$courses[7]->id, 'CBSE Class 10 Biology & Environmental Sciences', 'SCH-803', 40, true],

            // Competitive Examination Coaching (3)
            [$courses[8]->id, 'Advanced Mechanics & Electromagnetism for JEE', 'CMP-901', 80, true],
            [$courses[8]->id, 'Inorganic & Organic Chemistry Reaction Mechanisms', 'CMP-902', 80, true],
            [$courses[8]->id, 'Calculus, Vectors & Coordinate Geometry for JEE', 'CMP-903', 80, true],

            // Accounting (3)
            [$courses[9]->id, 'Double Entry Bookkeeping & Financial Statements', 'ACC-1001', 35, false],
            [$courses[9]->id, 'Tally Prime Computerized Accounting & Inventory', 'ACC-1002', 45, true],
            [$courses[9]->id, 'GST Rules, E-Way Bill & Income Tax Returns', 'ACC-1003', 40, true],
        ];

        $subjects = [];
        foreach ($subjectsData as $s) {
            $subjects[] = Subject::create([
                'course_id' => $s[0],
                'name' => $s[1],
                'code' => $s[2],
                'total_hours' => $s[3],
                'is_practical' => $s[4],
            ]);
        }

        // 8. 20 Batches across branches
        $batches = [];
        for ($b = 1; $b <= 20; $b++) {
            $course = $courses[($b - 1) % count($courses)];
            $branch = $branches[($b - 1) % count($branches)];
            $classroom = $classrooms[($b - 1) % count($classrooms)];
            $faculty = $facultyMembers[($b - 1) % count($facultyMembers)];

            $isMorning = ($b % 2 == 1);
            $startTime = $isMorning ? '08:30:00' : '17:30:00';
            $endTime = $isMorning ? '10:30:00' : '19:30:00';

            $batch = Batch::create([
                'branch_id' => $branch->id,
                'course_id' => $course->id,
                'academic_session_id' => $sessionCurrent->id,
                'name' => $course->name . ' - Batch ' . ($b <= 10 ? 'Alpha' : 'Beta') . " ({$b})",
                'code' => 'B-' . strtoupper(Str::slug(substr($course->name, 0, 6))) . '-' . sprintf('%02d', $b),
                'start_date' => date('Y-m-d', strtotime('-' . (30 - $b) . ' days')),
                'end_date' => date('Y-m-d', strtotime('+' . (150 + $b * 5) . ' days')),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'capacity' => 35,
                'delivery_mode' => $b % 3 == 0 ? 'online' : ($b % 2 == 0 ? 'hybrid' : 'offline'),
                'classroom_id' => $classroom->id,
                'primary_mentor_id' => $faculty->id,
                'status' => 'active',
            ]);

            // Assign faculty to batch
            $batch->faculty()->attach($faculty->id, ['subject_id' => $subjects[($b - 1) % count($subjects)]->id]);

            // Timetable entries for batch
            foreach (['Monday', 'Wednesday', 'Friday'] as $day) {
                Timetable::create([
                    'branch_id' => $branch->id,
                    'batch_id' => $batch->id,
                    'subject_id' => $subjects[($b - 1) % count($subjects)]->id,
                    'faculty_id' => $faculty->id,
                    'classroom_id' => $classroom->id,
                    'day_of_week' => $day,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'room_or_link' => $classroom->room_name,
                    'status' => 'active',
                ]);
            }

            $batches[] = $batch;
        }

        // 9. Enquiry Sources
        $sourceNames = ['Google Ads', 'Walk-in Inquiry', 'Website Online Form', 'Student Referral', 'Instagram / Meta Ads', 'Newspaper / Flyer'];
        $enquirySources = [];
        foreach ($sourceNames as $src) {
            $enquirySources[] = EnquirySource::create(['name' => $src, 'is_active' => true]);
        }

        // 10. 100 Enquiries and Follow-ups
        $firstNames = ['Aarav', 'Vivaan', 'Aditya', 'Vihaan', 'Arjun', 'Sai', 'Reyansh', 'Ayaan', 'Krishna', 'Ishaan', 'Shaurya', 'Atharva', 'Advik', 'Pranav', 'Kabir', 'Ananya', 'Diya', 'Gauri', 'Aadhya', 'Pari', 'Saanvi', 'Ananya', 'Navya', 'Myra', 'Ira', 'Avani', 'Riya', 'Siya', 'Shanaya', 'Kavya'];
        $lastNames = ['Patel', 'Sharma', 'Gupta', 'Verma', 'Singh', 'Rao', 'Nair', 'Iyer', 'Reddy', 'Mehta', 'Kulkarni', 'Joshi', 'Chopra', 'Banerjee', 'Das', 'Chatterjee', 'Mishra', 'Pandey', 'Bhat', 'Deshmukh'];

        $enquiries = [];
        for ($i = 1; $i <= 100; $i++) {
            $fn = $firstNames[($i - 1) % count($firstNames)];
            $ln = $lastNames[($i * 3) % count($lastNames)];
            $branch = $branches[$i % count($branches)];
            $course = $courses[$i % count($courses)];
            $counsellor = $counsellors[$i % count($counsellors)];
            $source = $enquirySources[$i % count($enquirySources)];

            // First 50 will be converted to admissions, rest will be in lead stages
            $status = $i <= 50 ? 'converted' : ($i % 4 == 0 ? 'counselling_scheduled' : ($i % 3 == 0 ? 'demo_attended' : ($i % 5 == 0 ? 'contacted' : 'new')));

            $enquiry = Enquiry::create([
                'branch_id' => $branch->id,
                'academic_session_id' => $sessionCurrent->id,
                'enquiry_no' => 'ENQ-' . date('Y') . '-' . sprintf('%04d', $i),
                'student_name' => "{$fn} {$ln}",
                'father_or_guardian_name' => "Manoj {$ln}",
                'email' => strtolower("{$fn}.{$ln}{$i}@gmail.com"),
                'phone' => '+91 98' . sprintf('%08d', 10000000 + $i * 73),
                'alternate_phone' => '+91 97' . sprintf('%08d', 20000000 + $i * 51),
                'address' => "#{$i}, Sector " . (($i % 10) + 1) . ", Green Glen Layout",
                'city' => 'Ahmedabad',
                'previous_school_or_college' => 'National Public School / Christ University',
                'previous_percentage_or_grade' => (75 + ($i % 23)) . '%',
                'interested_course_id' => $course->id,
                'enquiry_source_id' => $source->id,
                'assigned_to' => $counsellor->id,
                'status' => $status,
                'priority' => $i % 3 == 0 ? 'high' : ($i % 2 == 0 ? 'medium' : 'low'),
                'enquiry_date' => date('Y-m-d', strtotime('-' . (60 - ($i % 40)) . ' days')),
                'next_follow_up_date' => date('Y-m-d', strtotime('+' . ($i % 7) . ' days')),
                'remarks' => "Inquired about {$course->name}. Interested in weekend / morning batch.",
            ]);

            // Follow-up history
            EnquiryFollowup::create([
                'enquiry_id' => $enquiry->id,
                'user_id' => $counsellor->id,
                'followup_date' => date('Y-m-d', strtotime($enquiry->enquiry_date . ' +2 days')),
                'contact_method' => $i % 2 == 0 ? 'call' : 'whatsapp',
                'response_status' => $status === 'converted' ? 'positive' : 'interested',
                'conversation_summary' => "Discussed syllabus, placements and fee installment scheme with candidate.",
                'next_followup_date' => date('Y-m-d', strtotime('+3 days')),
            ]);

            $enquiries[] = $enquiry;
        }

        // 11. 100 Students (50 from converted leads, 50 direct admissions)
        $students = [];
        $admissions = [];
        for ($s = 1; $s <= 100; $s++) {
            $enq = $enquiries[($s - 1) % count($enquiries)];
            $fn = $firstNames[($s * 2) % count($firstNames)];
            $ln = $lastNames[($s * 5) % count($lastNames)];
            $branch = $branches[($s - 1) % count($branches)];
            $course = $courses[($s - 1) % count($courses)];
            $batch = $batches[($s - 1) % count($batches)];

            // Create student user account
            $studentUser = User::create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'role_id' => $roleModels['student']->id,
                'name' => "{$fn} {$ln}",
                'email' => strtolower("student{$s}.{$fn}@apexcoach.test"),
                'phone' => '+91 99' . sprintf('%08d', 11000000 + $s * 67),
                'user_type' => 'student',
                'status' => 'active',
                'password' => Hash::make('password123'),
            ]);

            $student = Student::create([
                'branch_id' => $branch->id,
                'user_id' => $studentUser->id,
                'admission_no' => 'ADM-2026-' . sprintf('%04d', $s),
                'roll_no' => 'R26-' . sprintf('%03d', $s),
                'first_name' => $fn,
                'last_name' => $ln,
                'date_of_birth' => date('Y-m-d', strtotime('-' . (18 + ($s % 6)) . ' years')),
                'gender' => $s % 2 == 0 ? 'female' : 'male',
                'blood_group' => ['A+', 'B+', 'O+', 'AB+'][$s % 4],
                'email' => $studentUser->email,
                'phone' => $studentUser->phone,
                'alternate_phone' => '+91 98' . sprintf('%08d', 30000000 + $s * 81),
                'current_address' => "Flat {$s}, Block B, Brigade Meadows",
                'permanent_address' => "House No. " . (100 + $s) . ", Civil Lines",
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '560076',
                'father_name' => "Ramesh {$ln}",
                'father_phone' => '+91 98' . sprintf('%08d', 40000000 + $s * 55),
                'father_occupation' => 'Senior Executive / Business Owner',
                'mother_name' => "Sunita {$ln}",
                'mother_phone' => '+91 98' . sprintf('%08d', 50000000 + $s * 55),
                'guardian_name' => "Ramesh {$ln}",
                'guardian_phone' => '+91 98' . sprintf('%08d', 40000000 + $s * 55),
                'guardian_relation' => 'Father',
                'previous_school' => 'Delhi Public School / St. Joseph\'s',
                'previous_qualification' => 'Class 12 / Graduate B.Sc / B.Tech',
                'status' => 'active',
            ]);

            // Enroll student into batch
            $batch->students()->attach($student->id, [
                'roll_no' => $student->roll_no,
                'enrolled_at' => date('Y-m-d', strtotime('-' . (30 - ($s % 20)) . ' days')),
                'status' => 'active',
            ]);

            // Create Admission record
            $agreedFee = $course->total_fee;
            $discount = ($s % 5 == 0) ? 5000 : 0;
            $finalFee = $agreedFee - $discount;

            $admission = Admission::create([
                'branch_id' => $branch->id,
                'academic_session_id' => $sessionCurrent->id,
                'student_id' => $student->id,
                'course_id' => $course->id,
                'batch_id' => $batch->id,
                'enquiry_id' => $s <= 50 ? $enq->id : null,
                'admission_no' => $student->admission_no,
                'admission_date' => date('Y-m-d', strtotime('-' . (30 - ($s % 20)) . ' days')),
                'total_course_fee' => $agreedFee,
                'discount_amount' => $discount,
                'discount_reason' => $discount > 0 ? 'Merit Scholarship / Early Bird Waiver' : null,
                'final_agreed_fee' => $finalFee,
                'fee_plan' => 'installments',
                'installments_count' => 3,
                'status' => 'confirmed',
                'admitted_by' => $adminUser->id,
            ]);

            $students[] = $student;
            $admissions[] = $admission;
        }

        // 12. 50 Fee Invoices (Student Fee Dues) & 50 Fee Payments
        for ($p = 1; $p <= 50; $p++) {
            $student = $students[$p - 1];
            $admission = $admissions[$p - 1];
            $amountDue = round($admission->final_agreed_fee * 0.40, 2); // Down-payment installment

            $due = StudentFeeDue::create([
                'branch_id' => $student->branch_id,
                'student_id' => $student->id,
                'admission_id' => $admission->id,
                'installment_no' => 1,
                'title' => 'Admission Down-Payment & Registration Fee',
                'due_date' => $admission->admission_date,
                'base_amount' => $amountDue,
                'fine_amount' => 0,
                'discount_amount' => 0,
                'net_amount' => $amountDue,
                'paid_amount' => $amountDue,
                'balance_amount' => 0,
                'status' => 'paid',
            ]);

            // Corresponding Payment Receipt
            $methods = ['upi', 'card', 'net_banking', 'cash'];
            Payment::create([
                'branch_id' => $student->branch_id,
                'student_id' => $student->id,
                'student_fee_due_id' => $due->id,
                'receipt_no' => 'RCP-2026-' . sprintf('%05d', $p),
                'payment_date' => $admission->admission_date,
                'amount' => $amountDue,
                'payment_method' => $methods[$p % 4],
                'transaction_reference' => 'TXN' . strtoupper(Str::random(10)),
                'received_by' => $accountant->id,
                'remarks' => 'Admission first installment settled successfully.',
            ]);

            // Installment 2 (Upcoming due for first 25 students, unpaid)
            if ($p <= 25) {
                $inst2Due = round($admission->final_agreed_fee * 0.30, 2);
                StudentFeeDue::create([
                    'branch_id' => $student->branch_id,
                    'student_id' => $student->id,
                    'admission_id' => $admission->id,
                    'installment_no' => 2,
                    'title' => '2nd Quarter Installment Dues',
                    'due_date' => date('Y-m-d', strtotime($admission->admission_date . ' +30 days')),
                    'base_amount' => $inst2Due,
                    'fine_amount' => 0,
                    'discount_amount' => 0,
                    'net_amount' => $inst2Due,
                    'paid_amount' => 0,
                    'balance_amount' => $inst2Due,
                    'status' => 'unpaid',
                ]);
            }
        }

        // 13. 100 Attendance Records
        for ($att = 1; $att <= 100; $att++) {
            $student = $students[$att - 1];
            $batch = $batches[($att - 1) % count($batches)];
            $subj = $subjects[($att - 1) % count($subjects)];
            $status = ($att % 10 == 0) ? 'absent' : (($att % 15 == 0) ? 'late' : 'present');

            StudentAttendance::create([
                'branch_id' => $student->branch_id,
                'batch_id' => $batch->id,
                'student_id' => $student->id,
                'subject_id' => $subj->id,
                'attendance_date' => date('Y-m-d', strtotime('-' . ($att % 5) . ' days')),
                'status' => $status,
                'in_time' => $status === 'late' ? '08:45:00' : '08:25:00',
                'out_time' => '10:30:00',
                'remarks' => $status === 'absent' ? 'Parent informed via SMS' : 'Attended actively',
                'marked_by' => $facultyMembers[0]->id,
            ]);
        }

        // 14. 10 Examinations & 100 Exam Marks
        for ($e = 1; $e <= 10; $e++) {
            $course = $courses[$e - 1];
            $batch = $batches[$e - 1];
            $subj = $subjects[$e - 1];

            $exam = Exam::create([
                'branch_id' => $batch->branch_id,
                'course_id' => $course->id,
                'batch_id' => $batch->id,
                'subject_id' => $subj->id,
                'title' => "{$course->name} Mid-Term Assessment #{$e}",
                'exam_code' => 'EXAM-' . sprintf('%03d', $e),
                'exam_type' => $e % 2 == 0 ? 'online' : 'offline',
                'start_time' => date('Y-m-d 10:00:00', strtotime('-' . (10 - $e) . ' days')),
                'end_time' => date('Y-m-d 11:30:00', strtotime('-' . (10 - $e) . ' days')),
                'duration_minutes' => 90,
                'total_marks' => 100,
                'passing_marks' => 40,
                'instructions' => 'Calculators and mobile devices strictly prohibited. Answer all questions.',
                'status' => 'results_published',
                'created_by' => $facultyMembers[0]->id,
            ]);

            // Add Questions
            for ($q = 1; $q <= 4; $q++) {
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_text' => "Sample theoretical/practical question {$q} for {$subj->name}: Explain core architectural principles.",
                    'question_type' => 'mcq',
                    'option_a' => 'Option Alpha - Principle A',
                    'option_b' => 'Option Beta - Principle B',
                    'option_c' => 'Option Gamma - Principle C',
                    'option_d' => 'Option Delta - Principle D',
                    'correct_answer' => 'A',
                    'marks' => 25.00,
                    'explanation' => 'Principle A provides optimal modularity and tenant isolation.',
                ]);
            }

            // Record 10 marks per exam (10 exams * 10 = 100 exam marks)
            for ($m = 1; $m <= 10; $m++) {
                $student = $students[($e - 1) * 10 + ($m - 1)];
                $score = 45 + (($m * 5) % 52); // range 45 to 95
                $pct = $score;
                $grade = $score >= 85 ? 'A+' : ($score >= 75 ? 'A' : ($score >= 60 ? 'B' : 'C'));

                StudentExamAttempt::create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'started_at' => $exam->start_time,
                    'submitted_at' => $exam->end_time,
                    'answers' => ['1' => 'A', '2' => 'B', '3' => 'A', '4' => 'A'],
                    'marks_obtained' => $score,
                    'percentage' => $pct,
                    'grade' => $grade,
                    'status' => 'evaluated',
                    'result' => $score >= 40 ? 'pass' : 'fail',
                    'faculty_remarks' => 'Good analytical clarity in answers.',
                ]);
            }
        }

        // 15. 20 Assignments
        for ($a = 1; $a <= 20; $a++) {
            $batch = $batches[($a - 1) % count($batches)];
            $subj = $subjects[($a - 1) % count($subjects)];
            $faculty = $facultyMembers[($a - 1) % count($facultyMembers)];

            Assignment::create([
                'branch_id' => $batch->branch_id,
                'batch_id' => $batch->id,
                'subject_id' => $subj->id,
                'faculty_id' => $faculty->id,
                'title' => "Hands-on Practical Assignment #{$a} on {$subj->name}",
                'description' => "Complete the laboratory exercise on {$subj->name}. Submit GitHub repository URL or documented PDF.",
                'due_datetime' => date('Y-m-d 23:59:59', strtotime('+' . ($a * 2) . ' days')),
                'max_marks' => 50,
                'status' => 'active',
            ]);
        }

        // 16. 30 Study Materials
        for ($sm = 1; $sm <= 30; $sm++) {
            $course = $courses[($sm - 1) % count($courses)];
            $subj = $subjects[($sm - 1) % count($subjects)];

            StudyMaterial::create([
                'branch_id' => $branches[$sm % count($branches)]->id,
                'course_id' => $course->id,
                'subject_id' => $subj->id,
                'uploaded_by' => $facultyMembers[0]->id,
                'title' => "Comprehensive Module Handout & Lecture Slides #{$sm} - {$subj->name}",
                'material_type' => $sm % 3 == 0 ? 'video_link' : ($sm % 2 == 0 ? 'notes' : 'pdf'),
                'file_path' => 'materials/sample_handout_' . $sm . '.pdf',
                'external_url' => $sm % 3 == 0 ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                'description' => "Key revision points, formulas, and real-world code templates for {$subj->name}.",
                'is_public_preview' => $sm % 5 == 0,
            ]);
        }

        // 17. 10 Certificates
        for ($c = 1; $c <= 10; $c++) {
            $student = $students[$c - 1];
            $course = $courses[$c - 1];

            Certificate::create([
                'branch_id' => $student->branch_id,
                'student_id' => $student->id,
                'course_id' => $course->id,
                'certificate_no' => 'CERT-2026-' . sprintf('%04d', $c),
                'title' => "Certificate of Professional Mastery in {$course->name}",
                'issue_date' => date('Y-m-d', strtotime('-' . ($c * 3) . ' days')),
                'grade' => 'Distinction (A+)',
                'verification_code' => 'VERIF-' . strtoupper(Str::random(12)),
                'status' => 'issued',
                'issued_by' => $adminUser->id,
            ]);
        }

        // 18. 10 Expenses
        $expenseCategories = [
            ['High Speed Leased Line Internet', 12500, 'Marketing & Infra'],
            ['Electricity & UPS Power Backup', 18400, 'Utilities'],
            ['Classroom Projector Replacement Bulb', 4500, 'Hardware & Maintenance'],
            ['Study Material Printing & Spiral Binding', 15800, 'Printing & Stationery'],
            ['Digital Facebook & Instagram Ads Campaign', 25000, 'Marketing'],
            ['Water Dispenser & Pantry Supplies', 3200, 'Office Operations'],
            ['Annual Software Licensing & AWS Hosting', 35000, 'IT & Cloud'],
            ['Faculty Guest Lecture Honorarium', 10000, 'Academics'],
            ['Office Sanitization & Deep Cleaning', 6000, 'Maintenance'],
            ['Google Workspace Enterprise Email Renewals', 8500, 'Software Subscriptions'],
        ];

        foreach ($expenseCategories as $idx => $exp) {
            $br = $branches[$idx % count($branches)];
            Expense::create([
                'branch_id' => $br->id,
                'voucher_no' => 'EXP-2026-' . sprintf('%04d', $idx + 1),
                'category' => $exp[2],
                'title' => $exp[0],
                'amount' => $exp[1],
                'expense_date' => date('Y-m-d', strtotime('-' . ($idx * 3) . ' days')),
                'payment_method' => $idx % 2 == 0 ? 'bank_transfer' : 'upi',
                'paid_to' => 'Vendor ' . ($idx + 1) . ' Solutions',
                'reference_no' => 'REF-' . (50000 + $idx),
                'approved_by' => $adminUser->id,
                'remarks' => 'Approved under monthly branch operating budget.',
            ]);
        }

        // 19. Announcements
        Announcement::create([
            'branch_id' => $branchCentral->id,
            'title' => 'Upcoming Hackathon & Code Fest 2026 Announced!',
            'message' => 'Kalpkrut Pathshala is hosting the 48-Hour Full-Stack & AI Hackathon on the last weekend of this month. Cash prizes up to Rs. 1,00,000 for top winning prototypes.',
            'target_audience' => 'all',
            'publish_date' => date('Y-m-d'),
            'expire_date' => date('Y-m-d', strtotime('+30 days')),
            'is_pinned' => true,
            'created_by' => $adminUser->id,
        ]);

        Announcement::create([
            'branch_id' => null, // all branches
            'title' => 'Fee Payment Reminder for Term 2 Dues',
            'message' => 'Students whose 2nd installment is due this month are advised to clear dues through the student portal or cashier desk before the 25th to avoid late fee penalties.',
            'target_audience' => 'students',
            'publish_date' => date('Y-m-d', strtotime('-2 days')),
            'expire_date' => date('Y-m-d', strtotime('+20 days')),
            'is_pinned' => false,
            'created_by' => $adminUser->id,
        ]);

        // 20. System Settings
        $settings = [
            'institute_name' => 'Kalpkrut Pathshala',
            'currency_symbol' => '₹',
            'currency_code' => 'INR',
            'tax_name' => 'GST',
            'default_tax_rate' => '18',
            'sms_gateway_enabled' => '1',
            'whatsapp_alerts_enabled' => '1',
            'email_alerts_enabled' => '1',
            'academic_session_current' => $sessionCurrent->name,
            'attendance_threshold_percentage' => '75',
        ];

        foreach ($settings as $k => $v) {
            SystemSetting::create(['key' => $k, 'value' => $v, 'group' => 'general']);
        }
    }
}


