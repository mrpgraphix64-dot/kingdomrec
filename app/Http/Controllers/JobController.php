<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        // Mock Data ported from React JobListing.tsx
        $jobs = \App\Models\JobPost::with('category')
            ->active()
            ->latest()
            ->get()
            ->map(function ($job) {
                // Determine standardized salary string
                $salaryDisplay = $job->salary;
                if (!str_contains($salaryDisplay, '/') && !str_contains(strtolower($salaryDisplay), 'negotiable')) {
                    // Logic to guess if it's likely annual based on 'k' suffix or magnitude, OR just default to nothing
                    // But user wants "monthly weekly or whatsoever".
                    // If it has 'k' (e.g. £45k), it's annual.
                    if (str_contains(strtolower($salaryDisplay), 'k')) {
                        $salaryDisplay .= ' / Year';
                    } elseif (preg_match('/^\£?\d+$/', $salaryDisplay)) { 
                         // If just a number like "45000", assume Year? Or leave raw?
                         // Best to leave raw if unsure, but 'k' is a strong signal.
                    }
                }
                // Fix old "hr" to "Hour" if needed or leave as is.

                return [
                    'id' => $job->id,
                    'title' => $job->sub_category,
                    'company' => $job->company_name ?? 'Kingdom Recruitments',
                    'salaryRange' => $salaryDisplay, // e.g. "£45k - £55k / Year"
                    'salaryMax' => (int) preg_replace('/[^0-9]/', '', explode('-', $job->salary)[1] ?? $job->salary), // Extract max for sorting
                    'location' => $job->location,
                    'type' => $job->type ?? 'Contract', // Default to Contract if null
                    'posted' => $job->created_at->diffForHumans(),
                    'description' => $job->description ?? '',
                    'tags' => array_filter([$job->type, $job->dept]), // Derive tags (filter nulls)
                    'sectors' => [$job->dept], // Map dept to sectors
                    'isNew' => $job->created_at->diffInDays(now()) < 7,
                    'isFeatured' => $job->status === 'Featured',
                    'deadline' => $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('d M Y') : null,
                    'categoryIcon' => ($job->category && $job->category->icon) ? asset('media/' . $job->category->icon) : null
                ];
            });

        $categories = \App\Models\JobCategory::where('status', 'Active')->pluck('name');

        return view('jobs.index', compact('jobs', 'categories'));
    }

    public function show($id)
    {
        $jobPost = \App\Models\JobPost::with('category')->findOrFail($id);
        
        $salaryDisplay = $jobPost->salary;
        if (!str_contains($salaryDisplay, '/') && !str_contains(strtolower($salaryDisplay), 'negotiable')) {
            if (str_contains(strtolower($salaryDisplay), 'k')) {
                $salaryDisplay .= ' / Year';
            }
        }

        $job = [
            'id' => $jobPost->id,
            'title' => $jobPost->sub_category,
            'company' => $jobPost->company_name ?? 'Kingdom Recruitments',
            'salaryRange' => $salaryDisplay,
            'location' => $jobPost->location,
            'type' => $jobPost->type ?? 'Contract',
            'posted' => $jobPost->created_at->diffForHumans(),
            'description' => $jobPost->description ?? '',
            'tags' => array_filter([$jobPost->type, $jobPost->dept]),
            'sectors' => [$jobPost->dept],
            'isNew' => $jobPost->created_at->diffInDays(now()) < 7,
            'isFeatured' => $jobPost->status === 'Featured',
            'deadline' => $jobPost->deadline ? \Carbon\Carbon::parse($jobPost->deadline)->format('d M Y') : null,
            'categoryIcon' => ($jobPost->category && $jobPost->category->icon) ? asset('media/' . $jobPost->category->icon) : null
        ];

        $isApplied = false;
        $profileCompleteness = [
            'is_complete' => true,
            'missing' => []
        ];

        if (auth()->check() && auth()->user()->role === 'applicant') {
            $applicant = \App\Models\Applicant::where('email', auth()->user()->email)->first();
            if ($applicant) {
                $isApplied = \App\Models\JobApplication::where('job_post_id', $jobPost->id)
                    ->where('applicant_id', $applicant->id)->exists();
                
                $missing = [];
                if (!$applicant->hasCompleteProfile($missing)) {
                    $profileCompleteness = [
                        'is_complete' => false,
                        'missing' => $missing
                    ];
                }
            } else {
                $profileCompleteness = [
                    'is_complete' => false,
                    'missing' => ['Resume (CV)', 'Phone Number', 'Location', 'Job Sector', 'Education or Work Experience']
                ];
            }
        }

        $isExpired = $jobPost->calculated_status !== 'Active';

        return view('jobs.show', compact('job', 'isApplied', 'profileCompleteness', 'isExpired'));
    }

    /**
     * 1-click apply: create a JobApplication record for the logged-in user.
     */
    public function apply(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'applicant') {
            return response()->json(['error' => 'Only applicants can apply.'], 403);
        }

        $job = \App\Models\JobPost::findOrFail($id);

        if ($job->calculated_status !== 'Active') {
            return response()->json(['error' => 'This position is no longer accepting applications.'], 422);
        }
        $applicant = \App\Models\Applicant::where('email', $user->email)->first();

        if (!$applicant) {
            return response()->json([
                'error' => 'Applicant profile not found. Please complete your profile first.',
                'redirect' => route('applicant.profile.edit') . '?redirect_back=' . urlencode(url()->previous())
            ], 422);
        }

        $missing = [];
        if (!$applicant->hasCompleteProfile($missing)) {
            $msg = 'Please complete the following profile fields before applying: ' . implode(', ', $missing);
            return response()->json([
                'error' => $msg,
                'missing' => $missing,
                'redirect' => route('applicant.profile.edit') . '?redirect_back=' . urlencode(url()->previous())
            ], 422);
        }

        // Prevent duplicate
        $existing = \App\Models\JobApplication::where('job_post_id', $job->id)
            ->where('applicant_id', $applicant->id)->first();

        if ($existing) {
            return response()->json(['error' => 'You have already applied for this job.'], 400);
        }

        $application = \App\Models\JobApplication::create([
            'job_post_id' => $job->id,
            'applicant_id' => $applicant->id,
            'status' => 'Pending',
            'cover_letter' => $request->input('cover_letter'),
        ]);

        return response()->json(['message' => 'Application submitted successfully!', 'id' => $application->id]);
    }

    /**
     * Return the list of jobs the current applicant has applied to.
     */
    public function appliedJobs()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->guest(route('login'));
        }

        $applicant = \App\Models\Applicant::where('email', $user->email)->first();

        $applications = collect();
        if ($applicant) {
            $applications = \App\Models\JobApplication::with('jobPost')
                ->where('applicant_id', $applicant->id)
                ->latest()
                ->get();
        }

        return view('jobs.applied', compact('applications'));
    }
}
