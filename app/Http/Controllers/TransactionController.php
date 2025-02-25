<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|in:expense,income',
            'category' => 'required|string|max:50',
            'transaction_date' => 'required|date',
        ]);

        try {
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'description' => $request->description,
                'amount' => $request->amount,
                'type' => $request->type,
                'category' => $request->category,
                'transaction_date' => $request->transaction_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction added successfully',
                'transaction' => $transaction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCategories()
    {
        // Get unique categories for the logged-in user
        $categories = Transaction::where('user_id', Auth::id())
            ->select('category')
            ->distinct()
            ->pluck('category');

        return response()->json($categories);
    }

    public function getTransactions(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id());

        // Apply type filter
        if ($request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Apply time filter
        switch ($request->time) {
            case 'today':
                $query->whereDate('transaction_date', Carbon::today());
                break;
            case 'weekly':
                $query->whereBetween('transaction_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereYear('transaction_date', Carbon::now()->year)
                      ->whereMonth('transaction_date', Carbon::now()->month);
                break;
            case 'yearly':
                $query->whereYear('transaction_date', Carbon::now()->year);
                break;
        }

        // Apply sorting
        $sortField = $request->sort === 'amount' ? 'amount' : 'transaction_date';
        $sortOrder = $request->order === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortField, $sortOrder);

        // Return paginated results
        return $query->paginate(10);
    }
} 