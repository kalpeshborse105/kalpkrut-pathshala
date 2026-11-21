<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Student, Enquiry, Admission, Payment, Expense, Batch, StudentAttendance, StudentExamAttempt, Course, Branch
};
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Financial Overview
        $totalCollected = Payment::sum('amount');
        $totalExpenses = Expense::sum('amount');
        $netIncome = $totalCollected - $totalExpenses;

        // Payment by method
        $paymentMethods = Payment::select('payment_method', DB::raw('sum(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        // 2. Admission & Lead Conversion
        $totalLeads = Enquiry::count();
        $convertedLeads = Enquiry::where('status', 'converted')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        $leadsBySource = Enquiry::join('enquiry_sources', 'enquiries.enquiry_source_id', '=', 'enquiry_sources.id')
            ->select('enquiry_sources.name', DB::raw('count(enquiries.id) as total'))
            ->groupBy('enquiry_sources.name')
            ->pluck('total', 'enquiry_sources.name')
            ->toArray();

        // 3. Batch Performance
        $batchStats = Batch::withCount(['students', 'attendances'])
            ->with('course')
            ->take(10)
            ->get();

        // 4. Branch revenue comparison
        $branchRevenue = Branch::withSum('admissions', 'final_agreed_fee')->get();

        return view('reports.index', compact(
            'totalCollected', 'totalExpenses', 'netIncome', 'paymentMethods',
            'totalLeads', 'convertedLeads', 'conversionRate', 'leadsBySource',
            'batchStats', 'branchRevenue'
        ));
    }
}

