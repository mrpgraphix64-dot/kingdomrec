<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GalleryItem;

class GalleryController extends Controller
{
    /**
     * Show the public gallery page.
     */
    public function index(Request $request)
    {
        $query = GalleryItem::orderBy('sort_order', 'asc');

        // Optional filtering by type (image/video)
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Limit results to 24 items per page for fast initial loading
        $items = $query->paginate(24);

        // For AJAX "Load More" calls, return JSON data
        if ($request->ajax()) {
            return response()->json([
                'data' => $items->items(),
                'next_page_url' => $items->nextPageUrl(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
            ]);
        }

        return view('gallery', compact('items'));
    }
}
