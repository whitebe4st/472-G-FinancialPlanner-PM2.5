<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
} 