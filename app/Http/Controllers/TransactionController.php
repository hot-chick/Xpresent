<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->when($request->start_date, function ($query) use ($request) {
                $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->where('date', '<=', $request->end_date);
            })
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expenses = $transactions->where('type', 'expense')->sum('amount');

        return inertia('Transactions/Index', compact('transactions', 'income', 'expenses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
        ]);

        Transaction::create(array_merge($request->all(), ['user_id' => Auth::id()]));

        return redirect()->back();
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
        ]);

        $transaction->update($request->all());

        return redirect()->back();
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->back();
    }
}
