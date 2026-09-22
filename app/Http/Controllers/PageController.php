<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __invoke(Request $request)
    {
        $categories = $this->getCategories();

        return view('home-video', compact('categories'));
    }

    public function video()
    {
        return redirect()->route('home');
    }

    private function getCategories()
    {
        $metaCategories = [
            'SIA Security' => ['icon' => 'shield', 'color' => 'text-blue-500'],
            'Security' => ['icon' => 'shield', 'color' => 'text-blue-500'], // Legacy alias

            'Accounting' => ['icon' => 'line-chart', 'color' => 'text-green-500'],
            'Cleaning' => ['icon' => 'line-chart', 'color' => 'text-green-500'], // Legacy alias (re-purposed)

            'Event Management' => ['icon' => 'calendar', 'color' => 'text-purple-500'],
            'Events' => ['icon' => 'calendar', 'color' => 'text-purple-500'], // Legacy alias

            'Hospitality Staff' => ['icon' => 'users', 'color' => 'text-orange-500'],
            'Hospitality' => ['icon' => 'users', 'color' => 'text-orange-500'], // Legacy alias

            'Customer Service' => ['icon' => 'headphones', 'color' => 'text-pink-500'],
            'Construction' => ['icon' => 'headphones', 'color' => 'text-pink-500'], // Legacy alias

            'Waiting Staff' => ['icon' => 'utensils', 'color' => 'text-yellow-500'],
        ];

        // Fetch all active categories (not Archived)
        $activeCategories = \App\Models\JobCategory::where('status', 'Active')
            ->withCount(['jobs' => function ($query) {
                $query->active();
            }])
            ->get();

        // Build the final array dynamically
        $categories = [];
        // Normalize meta keys for case-insensitive lookup
        $normalizedMeta = [];
        foreach($metaCategories as $key => $val) {
            $normalizedMeta[strtolower($key)] = $val;
        }

        foreach ($activeCategories as $cat) {
            $catNameLower = strtolower($cat->name);
            
            // Use metadata if found, otherwise default generic icon
            $meta = $normalizedMeta[$catNameLower] ?? ['icon' => 'briefcase', 'color' => 'text-slate-500'];

            $categories[] = [
                'name' => $cat->name,
                'jobs' => $cat->jobs_count . '+',
                'icon' => $cat->icon ?? $meta['icon'], // Prefer DB icon
                'color' => $meta['color']
            ];
        }

        return $categories;
    }
}
