<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Expense, Branch};

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['branch', 'approver'])->latest();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $expenses = $query->paginate(15)->withQueryString();
        $branches = Branch::all();
        $totalExpenses = Expense::sum('amount');

        return view('expenses.index', compact('expenses', 'branches', 'totalExpenses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:191',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,upi,bank_transfer,cheque',
            'paid_to' => 'required|string|max:191',
            'remarks' => 'nullable|string',
        ]);

        $count = Expense::count() + 1;
        $validated['voucher_no'] = 'EXP-' . date('Y') . '-' . sprintf('%04d', $count);
        $validated['approved_by'] = auth()->id() ?? 1;

        Expense::create($validated);

        return back()->with('success', 'Expense voucher created successfully.');
    }
}

