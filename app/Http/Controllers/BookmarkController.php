<?php

namespace App\Http\Controllers;

use App\Models\BookmarkedTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $query = BookmarkedTransaction::where('user_id', Auth::id());
        
        // Apply filter if present
        if ($request->filter && $request->filter !== 'all') {
            $query->where('type', $request->filter);
        }
        
        $bookmarkedTransactions = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('html.bookmark-table', compact('bookmarkedTransactions'))->render(),
                'pagination' => $bookmarkedTransactions->links()->toHtml()
            ]);
        }
        
        return view('html.bookmark', compact('bookmarkedTransactions'));
    }
} 