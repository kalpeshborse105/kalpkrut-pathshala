<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Student, Enquiry, Admission, Batch, Course, Payment, Expense, StudentAttendance, Exam, Branch, Timetable, Assignment, StudentFeeDue, Certificate
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. STUDENT PORTAL DASHBOARD
        if ($user->isStudent()) {
            $student = $user->student 
                ?? Student::where('user_id', $user->id)->first() 
                ?? Student::where('email', $user->email)->first() 
                ?? Student::first();
            
            if ($student) {
                $student->load([
                    'batches.course', 'batches.classroom', 'batches.mentor',
                    'attendances.subject', 'feeDues.payments', 'payments',
                    'examAttempts.exam.subject', 'assignmentSubmissions.assignment', 'certificates'
                ]);

                $totalClasses = $student->attendances->count();
                $presentClasses = $student->attendances->where('status', 'present')->count();
                $attendancePct = $totalClasses > 0 ? round(($presentClasses / $totalClasses) * 100, 1) : 0;

                $totalFeeAssigned = $student->feeDues->sum('net_amount');
                $totalFeePaid = $student->payments->sum('amount');
                $pendingFeeBalance = max(0, $totalFeeAssigned - $totalFeePaid);

                $batchIds = $student->batches->pluck('id');
                $upcomingAssignments = Assignment::whereIn('batch_id', $batchIds)
                    ->where('status', 'active')
                    ->latest()
                    ->take(5)
                    ->get();

                $recentExamAttempts = $student->examAttempts->take(5);

                return view('dashboard.student', compact(
                    'student', 'attendancePct', 'totalFeeAssigned', 'totalFeePaid',
                    'pendingFeeBalance', 'upcomingAssignments', 'recentExamAttempts'
                ));
            }
        }

        // 2. FACULTY PORTAL DASHBOARD
        if ($user->isFaculty()) {
            $batches = Batch::where('primary_mentor_id', $user->id)
                ->orWhereHas('faculty', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->with(['course', 'classroom', 'students'])
                ->get();

            $batchIds = $batches->pluck('id');
            $totalStudentsUnderMe = Student::whereHas('batches', function($q) use ($batchIds) {
                $q->whereIn('batches.id', $batchIds);
            })->count();

            $todayDay = date('l');
            $todaysClasses = Timetable::where('faculty_id', $user->id)
                ->where('day_of_week', $todayDay)
                ->with(['batch', 'subject', 'classroom'])
                ->orderBy('start_time')
                ->get();

            $myAssignments = Assignment::where('faculty_id', $user->id)
                ->with('batch')
                ->latest()
                ->take(5)
                ->get();

            $myExams = Exam::whereIn('batch_id', $batchIds)
                ->with(['course', 'batch'])
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.faculty', compact(
                'batches', 'totalStudentsUnderMe', 'todaysClasses', 'myAssignments', 'myExams', 'todayDay'
            ));
        }

        // 3. ACCOUNTANT / CASHIER DASHBOARD
        if ($user->isAccountant()) {
            $totalCollected = Payment::sum('amount');
            $todayCollected = Payment::whereDate('payment_date', now()->toDateString())->sum('amount');
            $totalDues = StudentFeeDue::sum('net_amount');
            $totalOutstanding = max(0, $totalDues - $totalCollected);
            $totalExpenses = Expense::sum('amount');
            $netBalance = $totalCollected - $totalExpenses;

            $recentPayments = Payment::with(['student', 'feeDue'])
                ->latest()
                ->take(8)
                ->get();

            $unpaidDues = StudentFeeDue::where('status', '!=', 'paid')
                ->with(['student', 'admission.course'])
                ->latest()
                ->take(8)
                ->get();

            $paymentMethods = Payment::select('payment_method', DB::raw('sum(amount) as total'))
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method')
                ->toArray();

            return view('dashboard.accountant', compact(
                'totalCollected', 'todayCollected', 'totalOutstanding', 'totalExpenses',
                'netBalance', 'recentPayments', 'unpaidDues', 'paymentMethods'
            ));
        }

        // 4. COUNSELLOR DASHBOARD
        if ($user->isCounsellor()) {
            $myLeadsQuery = Enquiry::where('assigned_to', $user->id);
            $totalAssignedLeads = $myLeadsQuery->count();
            
            $convertedCount = Enquiry::where('assigned_to', $user->id)->where('status', 'converted')->count();
            $conversionRate = $totalAssignedLeads > 0 ? round(($convertedCount / $totalAssignedLeads) * 100, 1) : 0;

            $followupsDueToday = Enquiry::where('assigned_to', $user->id)
                ->whereDate('next_follow_up_date', '<=', now()->toDateString())
                ->where('status', '!=', 'converted')
                ->where('status', '!=', 'dropped')
                ->count();

            $recentLeads = Enquiry::where('assigned_to', $user->id)
                ->with(['course', 'source'])
                ->latest()
                ->take(8)
                ->get();

            $leadPipeline = Enquiry::where('assigned_to', $user->id)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            return view('dashboard.counsellor', compact(
                'totalAssignedLeads', 'convertedCount', 'conversionRate', 'followupsDueToday',
                'recentLeads', 'leadPipeline'
            ));
        }

        // 5. SUPER ADMIN & BRANCH MANAGER (Executive Operations Dashboard)
        $selectedBranchId = $request->get('branch_id', session('active_branch_id'));
        if ($selectedBranchId) {
            session(['active_branch_id' => $selectedBranchId]);
        }

        $branchQuery = function($query) use ($selectedBranchId) {
            if ($selectedBranchId && $selectedBranchId !== 'all') {
                $query->where('branch_id', $selectedBranchId);
            }
        };

        $totalStudents = Student::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)->count();
        $totalEnquiries = Enquiry::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)->count();
        $totalBatches = Batch::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)->where('status', 'active')->count();
        
        $totalFeeCollected = Payment::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)->sum('amount');
        $totalExpenses = Expense::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)->sum('amount');
        $netOperatingBalance = $totalFeeCollected - $totalExpenses;

        $newLeadsThisMonth = Enquiry::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $convertedLeads = Enquiry::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->where('status', 'converted')
            ->count();

        $conversionRate = $totalEnquiries > 0 ? round(($convertedLeads / $totalEnquiries) * 100, 1) : 0;

        $recentEnquiries = Enquiry::with(['course', 'source', 'assignedTo'])
            ->when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['student', 'feeDue'])
            ->when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->latest()
            ->take(5)
            ->get();

        $activeBatches = Batch::with(['course', 'mentor', 'classroom'])
            ->when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->where('status', 'active')
            ->take(5)
            ->get();

        $leadStats = Enquiry::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $branches = Branch::all();

        return view('dashboard.index', compact(
            'totalStudents', 'totalEnquiries', 'totalBatches', 'totalFeeCollected',
            'totalExpenses', 'netOperatingBalance', 'newLeadsThisMonth', 'conversionRate',
            'recentEnquiries', 'recentPayments', 'activeBatches', 'leadStats', 'branches',
            'selectedBranchId'
        ));
    }
}
