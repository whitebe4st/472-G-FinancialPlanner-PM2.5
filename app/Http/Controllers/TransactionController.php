<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\BookmarkedTransaction;

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
    
        // ✅ ระบุให้แน่ใจว่า `transaction_id` ถูกดึงมา
        $transactions = $query->paginate(10, [
            'transaction_id', // 🔥 ต้องแน่ใจว่าเอาค่ามานะ
            'description',
            'amount',
            'type',
            'category',
            'transaction_date'
        ]);
    
        // ✅ Debug ดูว่ามี `transaction_id` ใน JSON หรือไม่
        logger()->info('🔍 Transactions Response:', $transactions->toArray());
    
        return response()->json($transactions);
    }
    
    

    public function bookmarkTransaction(Request $request)
    {
        try {
            if ($request->action === 'unbookmark') {
                // Delete the bookmark
                BookmarkedTransaction::where('user_id', Auth::id())
                    ->where('description', $request->description)
                    ->where('amount', $request->amount)
                    ->where('type', $request->type)
                    ->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Transaction unbookmarked successfully'
                ]);
            } else {
                // Check if bookmark already exists
                $exists = BookmarkedTransaction::where('user_id', Auth::id())
                    ->where('description', $request->description)
                    ->where('amount', $request->amount)
                    ->where('type', $request->type)
                    ->exists();

                if (!$exists) {
                    $bookmarked = BookmarkedTransaction::create([
                        'user_id' => Auth::id(),
                        'description' => $request->description,
                        'default_date' => $request->date,
                        'amount' => $request->amount,
                        'type' => $request->type,
                        'category' => $request->category,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Transaction bookmarked successfully'
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction already bookmarked'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bookmark: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addFromBookmarks(Request $request)
    {
        $request->validate([
            'bookmark_ids' => 'required|array',
            'bookmark_ids.*' => 'exists:bookmarked_transactions,bookmark_id',
            'transaction_date' => 'required|date'
        ]);

        try {
            $bookmarks = BookmarkedTransaction::whereIn('bookmark_id', $request->bookmark_ids)
                ->where('user_id', Auth::id())
                ->get();

            foreach ($bookmarks as $bookmark) {
                Transaction::create([
                    'user_id' => Auth::id(),
                    'description' => $bookmark->description,
                    'amount' => $bookmark->amount,
                    'type' => $bookmark->type,
                    'category' => $bookmark->category,
                    'transaction_date' => $request->transaction_date,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transactions added successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add transactions: ' . $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        // ✅ ค้นหา Transaction ตาม ID และต้องเป็นของผู้ใช้ที่ล็อกอินอยู่
        $transaction = Transaction::where('user_id', Auth::id())->find($id);
    
        // ✅ Log ข้อมูลเพื่อ Debug
        if (!$transaction) {
            logger()->warning("Transaction ID {$id} not found for user " . Auth::id());
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }
    
        logger()->info("Transaction Loaded:", $transaction->toArray());
    
        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }
    

    // ✅ ฟังก์ชันอัปเดต Transaction
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|in:expense,income',
            'category' => 'required|string|max:50',
            'transaction_date' => 'required|date',
        ]);

        $transaction = Transaction::where('user_id', Auth::id())->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        try {
            $transaction->update([
                'description' => $request->description,
                'amount' => $request->amount,
                'type' => $request->type,
                'category' => $request->category,
                'transaction_date' => $request->transaction_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully',
                'transaction' => $transaction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
{
    $transactions = Transaction::where('user_id', Auth::id())
        ->orderBy('transaction_date', 'desc')
        ->get();

    logger()->info('Transactions Loaded:', $transactions->toArray()); // ✅ Debug Log

    return view('html.transaction', compact('transactions'));
}



} 

