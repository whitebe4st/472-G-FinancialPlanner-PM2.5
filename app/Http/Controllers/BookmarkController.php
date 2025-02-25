<?php

namespace App\Http\Controllers;

use App\Models\BookmarkedTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function index()
    {
        $bookmarkedTransactions = BookmarkedTransaction::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('html.bookmark', compact('bookmarkedTransactions'));
    }
} 