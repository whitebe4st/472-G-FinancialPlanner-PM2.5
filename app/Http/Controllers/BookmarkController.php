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

    public function getBookmarks()
    {
        try {
            $bookmarks = BookmarkedTransaction::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($bookmarks);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookmarks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific bookmark for editing
     */
    public function show($id)
    {
        try {
            $bookmark = BookmarkedTransaction::where('user_id', Auth::id())
                ->where('bookmark_id', $id)
                ->first();

            if (!$bookmark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bookmark not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'bookmark' => $bookmark
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookmark: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a bookmarked transaction
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|in:expense,income',
            'category' => 'required|string|max:50',
        ]);

        try {
            $bookmark = BookmarkedTransaction::where('user_id', Auth::id())
                ->where('bookmark_id', $id)
                ->first();

            if (!$bookmark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bookmark not found'
                ], 404);
            }

            $bookmark->update([
                'description' => $request->description,
                'amount' => $request->amount,
                'type' => $request->type,
                'category' => $request->category,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bookmark updated successfully',
                'bookmark' => $bookmark
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bookmark: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a single bookmarked transaction
     */
    public function destroy($id)
    {
        try {
            $bookmark = BookmarkedTransaction::where('user_id', Auth::id())
                ->where('bookmark_id', $id)
                ->first();

            if (!$bookmark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bookmark not found'
                ], 404);
            }

            $bookmark->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bookmark deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bookmark: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete multiple bookmarked transactions
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'numeric'
        ]);

        try {
            $deletedCount = BookmarkedTransaction::where('user_id', Auth::id())
                ->whereIn('bookmark_id', $request->ids)
                ->delete();

            if ($deletedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No bookmarks found to delete'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' bookmarks deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bookmarks: ' . $e->getMessage()
            ], 500);
        }
    }
} 