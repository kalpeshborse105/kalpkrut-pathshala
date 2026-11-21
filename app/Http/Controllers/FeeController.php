<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{StudentFeeDue, Payment, Student, Branch};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentFeeDue::with(['student.latestAdmission.course', 'branch', 'payments'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('student', function($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('admission_no', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $dues = $query->paginate(15)->withQueryString();
        $branches = Branch::all();

        // Totals
        $totalDuesAmount = StudentFeeDue::sum('net_amount');
        $totalCollected = Payment::sum('amount');
        $totalOutstanding = max(0, $totalDuesAmount - $totalCollected);

        return view('fees.index', compact('dues', 'branches', 'totalDuesAmount', 'totalCollected', 'totalOutstanding'));
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['student', 'feeDue', 'branch', 'receiver'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('receipt_no', 'like', "%{$s}%")
                  ->orWhere('transaction_reference', 'like', "%{$s}%")
                  ->orWhereHas('student', function($sq) use ($s) {
                      $sq->where('first_name', 'like', "%{$s}%")
                         ->orWhere('last_name', 'like', "%{$s}%")
                         ->orWhere('admission_no', 'like', "%{$s}%");
                  });
            });
        }

        $payments = $query->paginate(15)->withQueryString();
        return view('fees.payments', compact('payments'));
    }

    public function collect(Request $request)
    {
        $due = null;
        if ($request->filled('due_id')) {
            $due = StudentFeeDue::with('student')->findOrFail($request->due_id);
        }
        $students = Student::where('status', 'active')->get();
        return view('fees.collect', compact('due', 'students'));
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'student_fee_due_id' => 'required|exists:student_fee_dues,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,upi,card,net_banking,cheque,dd',
            'transaction_reference' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        return DB::transaction(function() use ($validated) {
            $due = StudentFeeDue::with('student')->findOrFail($validated['student_fee_due_id']);
            $payAmount = (float)$validated['amount'];

            $receiptCount = Payment::count() + 1;
            $receiptNo = 'RCP-' . date('Y') . '-' . sprintf('%05d', $receiptCount);

            $payment = Payment::create([
                'branch_id' => $due->branch_id,
                'student_id' => $due->student_id,
                'student_fee_due_id' => $due->id,
                'receipt_no' => $receiptNo,
                'payment_date' => $validated['payment_date'],
                'amount' => $payAmount,
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? 'TXN' . strtoupper(Str::random(8)),
                'received_by' => auth()->id() ?? 1,
                'remarks' => $validated['remarks'],
            ]);

            // Update due
            $newPaid = $due->paid_amount + $payAmount;
            $newBalance = max(0, $due->net_amount - $newPaid);
            $newStatus = ($newBalance <= 0) ? 'paid' : 'partially_paid';

            $due->update([
                'paid_amount' => $newPaid,
                'balance_amount' => $newBalance,
                'status' => $newStatus,
            ]);

            return redirect()->route('fees.receipt', $payment)->with('success', "Payment of ₹" . number_format($payAmount, 2) . " received. Receipt No: {$receiptNo}");
        });
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['student.latestAdmission.course', 'student.branch.institute.company', 'feeDue', 'receiver']);
        return view('fees.receipt', compact('payment'));
    }
}

