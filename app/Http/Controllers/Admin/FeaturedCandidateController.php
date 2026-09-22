<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Http\Request;

class FeaturedCandidateController extends Controller
{
    /**
     * Display Featured Candidates Management Page.
     */
    public function index(Request $request)
    {
        $query = Applicant::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('sub_category', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter')) {
            if ($request->filter === 'featured') {
                $query->where('is_featured', true);
            } elseif ($request->filter === 'standard') {
                $query->where('is_featured', false);
            }
        }

        $totalFeaturedCount = Applicant::where('is_featured', true)->count();
        $totalCandidatesCount = Applicant::count();

        // Sort featured first, then latest
        $applicants = $query->orderByDesc('is_featured')->latest()->paginate(12)->withQueryString();

        return view('admin.featured-candidates', compact(
            'applicants', 
            'totalFeaturedCount', 
            'totalCandidatesCount'
        ));
    }

    /**
     * Toggle featured status for a single candidate.
     */
    public function toggle(Request $request, $id)
    {
        $applicant = Applicant::findOrFail($id);
        $applicant->is_featured = !$applicant->is_featured;
        $applicant->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_featured' => (bool)$applicant->is_featured,
                'message' => $applicant->is_featured 
                    ? "{$applicant->name} is now marked as a Featured Candidate."
                    : "{$applicant->name} has been removed from Featured Candidates."
            ]);
        }

        $msg = $applicant->is_featured 
            ? "{$applicant->name} marked as Featured Candidate." 
            : "{$applicant->name} removed from Featured Candidates.";

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Bulk update featured candidates selection.
     */
    public function bulkUpdate(Request $request)
    {
        $featuredIds = $request->input('featured_ids', []);

        // Un-feature all candidates not in the selected list
        Applicant::whereNotIn('id', $featuredIds)->update(['is_featured' => false]);

        // Feature all candidates in the selected list
        if (!empty($featuredIds)) {
            Applicant::whereIn('id', $featuredIds)->update(['is_featured' => true]);
        }

        return redirect()->back()->with('success', 'Featured candidates updated successfully (' . count($featuredIds) . ' selected).');
    }
}
