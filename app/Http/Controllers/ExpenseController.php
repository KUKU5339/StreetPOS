<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $userId = auth()->id();

        // Get expenses for the selected date
        $expenses = Expense::where('user_id', $userId)
            ->whereDate('expense_date', $date)
            ->latest()
            ->get();

        // Calculate totals
        $totalExpenses = $expenses->sum('amount');

        // Get sales for the same date
        $totalRevenue = Sale::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->sum('total');

        // Calculate profit
        $profit = $totalRevenue - $totalExpenses;

        // Get expense categories for the dropdown
        $categories = [
            'Ingredients',
            'Cooking Supplies',
            'Gas/Charcoal',
            'Transportation',
            'Packaging',
            'Utilities',
            'Other'
        ];

        return view('expenses.index', compact(
            'expenses',
            'totalExpenses',
            'totalRevenue',
            'profit',
            'date',
            'categories'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date'
        ]);

        Expense::create([
            'user_id' => auth()->id(),
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date
        ]);

        return redirect()->route('expenses.index', ['date' => $request->expense_date, 't' => time()])->with('success', 'Expense added successfully!');
    }

    public function destroy(Expense $expense)
    {
        // Check if expense belongs to current user
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }

        $expenseDate = $expense->expense_date;
        $expense->delete();
        return redirect()->route('expenses.index', ['date' => $expenseDate, 't' => time()])->with('success', 'Expense deleted!');
    }
}
