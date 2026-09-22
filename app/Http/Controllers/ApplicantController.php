<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\EventAssignment;
use Carbon\Carbon;
class ApplicantController extends Controller
{
    public function index()
    {
        // Define fixed categories with metadata (icons/colors) - Mirrored from PageController
        $metaCategories = [
            'SIA Security' => ['icon' => 'shield', 'color' => 'text-blue-500'],
            'Security' => ['icon' => 'shield', 'color' => 'text-blue-500'],
            'Accounting' => ['icon' => 'line-chart', 'color' => 'text-green-500'],
            'Cleaning' => ['icon' => 'line-chart', 'color' => 'text-green-500'],
            'Event Management' => ['icon' => 'calendar', 'color' => 'text-purple-500'],
            'Events' => ['icon' => 'calendar', 'color' => 'text-purple-500'],
            'Hospitality Staff' => ['icon' => 'users', 'color' => 'text-orange-500'],
            'Hospitality' => ['icon' => 'users', 'color' => 'text-orange-500'],
            'Customer Service' => ['icon' => 'headphones', 'color' => 'text-pink-500'],
            'Construction' => ['icon' => 'headphones', 'color' => 'text-pink-500'],
            'Waiting Staff' => ['icon' => 'utensils', 'color' => 'text-yellow-500'],
        ];

        $activeCategories = \App\Models\JobCategory::where('status', 'Active')->get();

        $categories = [];
        $normalizedMeta = [];
        foreach($metaCategories as $key => $val) {
            $normalizedMeta[strtolower($key)] = $val;
        }

        foreach ($activeCategories as $cat) {
            $catNameLower = strtolower($cat->name);
            $meta = $normalizedMeta[$catNameLower] ?? ['icon' => 'briefcase', 'color' => 'text-slate-500'];

            $categories[] = [
                'name' => $cat->name,
                'icon' => $meta['icon'],
                'color' => $meta['color']
            ];
        }

        return view('applicants.index', compact('categories'));
    }

    public function mySchedule()
    {
        $applicant = \App\Models\Applicant::where('email', auth()->user()->email)->first();
        
        if (!$applicant) {
            return redirect()->route('portal')->with('warning', 'Please complete your applicant profile first.');
        }

        $assignments = \App\Models\ShiftSlot::with(['staffQuotation.eventBooking'])
            ->where('applicant_id', $applicant->id)
            ->where('status', 'Assigned') // Slots land here when an admin assigns staff (see AdminController::updateAssignmentStatus)
            ->latest()
            ->get();

        return view('applicants.schedule', compact('assignments'));
    }

    public function upload()
    {
        if (!auth()->check()) {
            // Using guest() properly sets url.intended
            return redirect()->guest(route('login'))->with('message', 'Please log in to upload your CV');
        }

        // Check if user already has an applicant profile, unless in edit mode
        $existingApplicant = \App\Models\Applicant::where('email', auth()->user()->email)->first();
        
        // Redirect legacy 'edit' mode to new profile edit page
        if (request('mode') === 'edit') {
            return redirect()->route('applicant.profile.edit');
        }

        if ($existingApplicant) {
            return redirect()->route('portal')->with('info', 'You have already uploaded your CV. View your profile here.');
        }

        $categories = \App\Models\JobCategory::with(['subCategories' => function($query) {
            $query->where('status', 'Active');
        }])->where('status', 'Active')->orderBy('name')->get();
        return view('applicants.upload', compact('categories', 'existingApplicant'));
    }

    public function list()
    {
        $allApplicants = \App\Models\Applicant::latest()->get();
        
        $stats = [
            'total' => $allApplicants->count(),
            'new' => $allApplicants->where('status', 'New')->count(),
            'interviewing' => $allApplicants->where('status', 'Interviewing')->count(),
            'shortlisted' => $allApplicants->where('status', 'Shortlisted')->count(),
            'offer' => $allApplicants->where('status', 'Offer')->count(),
        ];

        $applicants = $allApplicants->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'role' => $c->role,
                'email' => $c->email,
                'phone' => $c->phone ?? 'Not provided',
                'location' => $c->location ?? 'Remote',
                'status' => $c->status,
                'date' => $c->created_at->diffForHumans(),
                'img' => $c->profile_photo_url,
                'image_position' => $c->image_position ?? 'object-center',
                'skills' => ['Communication', 'Adaptability'], // Mock
                'about' => 'No detailed bio available for this applicant yet.',
                'experience' => []
            ];
        });

        $categories = \App\Models\JobCategory::where('status', 'Active')->pluck('name'); // For filter list

        return view('applicants.list', compact('applicants', 'categories', 'stats'));
    }

    public function profile()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->guest(route('login'));
        }

        // Fetch User's Applicant Record with relations
        $applicant = \App\Models\Applicant::with(['educations', 'experiences'])->where('email', $user->email)->first();

        // If no applicant record exists, create a temporary object for the view
        if (!$applicant) {
            $applicant = (object) [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => 'Not provided',
                'location' => 'Not listed',
                'role' => 'Applicant',
                'cv_link' => null,
                'image' => null,
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0F1D33&color=fff'
            ];
            $experience = collect([]);
            $education = collect([]);
        } else {
            $experience = $applicant->experiences;
            $education = $applicant->educations;
        }

        $applications = collect([]);
        if ($applicant && isset($applicant->id)) {
            $applications = \App\Models\JobApplication::with('jobPost')
                ->where('applicant_id', $applicant->id)
                ->latest()
                ->get();
        }

        // Derive CV Name from link if it exists
        if (!empty($applicant->cv_link)) {
            $applicant->cvName = $applicant->cv_name;
        }

        return view('applicants.profile', compact('applicant', 'experience', 'education', 'applications'));
    }

    public function storeCv(Request $request)
    {
        $isOtherCategory = in_array($request->category, ['Other / Not Listed', 'Other']);

        $request->validate([
            'cv' => 'required|mimes:pdf|max:5120', // Max 5MB
            'category' => 'required|string',
            'sub_category' => 'nullable|string',
            'other_category_description' => $isOtherCategory ? 'required|string|max:255' : 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'location' => 'required|string|max:255|not_in:Not provided',
            'photo' => 'required|image|max:2048',
        ], [
            'other_category_description.required' => 'Please tell us what type of work you are looking for.',
        ]);

        if (auth()->check()) {
            $user = auth()->user();
            
            // Update User Phone
            if ($request->phone && $user->phone !== $request->phone) {
                $user->phone = $request->phone;
                $user->save();
            }

            $photoPath = null;
            if ($request->hasFile('photo')) {
                // SECURE UPLOAD FIX: Use Storage facade and hashname
                $path = $request->file('photo')->store('photos', 'public');
                $photoPath = '/media/' . $path;
            }

            if ($request->hasFile('cv')) {
                // Securely store CVs privately, keeping original sanitized filename
                $file = $request->file('cv');
                $originalName = $file->getClientOriginalName();
                $sanitizedName = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $originalName);
                $sanitizedName = ltrim($sanitizedName, '.');
                $fileName = \Illuminate\Support\Str::random(12) . '_' . (empty($sanitizedName) ? 'resume.pdf' : $sanitizedName);
                $path = $file->storeAs('cvs', $fileName, 'local');
                $cvPath = 'storage/app/' . $path;

                // DATA INTEGRITY FIX: Wrap multi-table inserts in a transaction
                \Illuminate\Support\Facades\DB::transaction(function () use ($user, $request, $cvPath, $photoPath, $isOtherCategory) {
                    // Update Applicant record
                    $applicant = \App\Models\Applicant::where('email', $user->email)->first();
                    
                    $roleData = [
                        'role' => $request->category,
                        'sub_category' => $isOtherCategory ? null : $request->sub_category,
                        'other_category_description' => $isOtherCategory ? $request->other_category_description : null,
                        'phone' => $request->phone,
                        'location' => $request->location,
                        'image_position' => $request->image_position ?? 'object-center'
                    ];

                    if ($applicant) {
                        $applicant->update(array_merge($roleData, [
                            'cv_link' => $cvPath,
                            'image' => $photoPath ?? $applicant->image,
                        ]));
                    } else {
                        $applicant = \App\Models\Applicant::create(array_merge($roleData, [
                            'name' => $user->name,
                            'email' => $user->email,
                            'cv_link' => $cvPath,
                            'image' => $photoPath,
                            'status' => 'Pending'
                        ]));
                    }

                    // Save Education
                    if ($request->has('education')) {
                        $applicant->educations()->delete(); // Clear old to replace with new set
                        foreach ($request->education as $edu) {
                            if (!empty($edu['institution'])) { // Basic validation check
                                $applicant->educations()->create([
                                    'institution' => $edu['institution'],
                                    'degree' => $edu['degree'],
                                    'start_date' => $edu['start_date'] ?? null,
                                    'end_date' => $edu['end_date'] ?? null,
                                    'is_current' => isset($edu['is_current']) ? true : false,
                                    'description' => $edu['description'] ?? null,
                                ]);
                            }
                        }
                    }

                    // Save Experience
                    if ($request->has('experience')) {
                        $applicant->experiences()->delete();
                        foreach ($request->experience as $exp) {
                            if (!empty($exp['company'])) {
                                $applicant->experiences()->create([
                                    'company' => $exp['company'],
                                    'job_title' => $exp['job_title'],
                                    'start_date' => $exp['start_date'] ?? null,
                                    'end_date' => $exp['end_date'] ?? null,
                                    'is_current' => isset($exp['is_current']) ? true : false,
                                    'description' => $exp['description'] ?? null,
                                ]);
                            }
                        }
                    }
                });
            }

            return redirect()->route('applicants.index')->with('success', 'CV and profile updated successfully!');
        }

        return redirect()->back()->with('error', 'Please log in to upload CV.');
    }



    public function updateImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ], [
            'image.required' => 'Please select an image file to upload.',
            'image.image' => 'The file you selected is not an image.',
            'image.mimes' => 'We only support JPEG, PNG, JPG, GIF, and SVG images.',
            'image.max' => 'Oops! Your profile photo is too large. Please select an image under 5MB.',
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $applicant = \App\Models\Applicant::where('email', $user->email)->first();

        if ($request->hasFile('image')) {
            // SECURE UPLOAD FIX: Use Storage facade and hashname
            $path = $request->file('image')->store('photos', 'public');
            $imagePath = '/media/' . $path;

            if ($applicant) {
                $applicant->update(['image' => $imagePath]);
            } else {
                 \App\Models\Applicant::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'image' => $imagePath,
                    'status' => 'Pending'
                ]);
            }

            return redirect()->back()->with('success', 'Profile picture updated successfully.');
        }

        return redirect()->back()->with('error', 'No image uploaded.');
    }
    public function editProfile()
    {
        if (!auth()->check()) {
            return redirect()->guest(route('login'));
        }

        $user = auth()->user();
        $applicant = \App\Models\Applicant::with(['educations', 'experiences'])->where('email', $user->email)->first();

        if (!$applicant) {
            return redirect()->route('applicants.upload')->with('info', 'Please create your profile first.');
        }

        $categories = \App\Models\JobCategory::with(['subCategories' => function($query) {
            $query->where('status', 'Active');
        }])->where('status', 'Active')->orderBy('name')->get();
        return view('applicants.edit-profile', compact('applicant', 'categories'));
    }

    public function updateProfile(Request $request)
    {
        $isOtherCategory = in_array($request->category, ['Other / Not Listed', 'Other']);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|not_in:Not provided',
            'location' => 'required|string|max:255|not_in:Not provided',
            'category' => 'required|string',
            'sub_category' => 'nullable|string',
            'other_category_description' => $isOtherCategory ? 'required|string|max:255' : 'nullable|string|max:255',
            'cv' => 'nullable|mimes:pdf|max:5120',
            'photo' => 'nullable|image|max:2048',
            'image_position' => 'nullable|string|max:50',
            'education' => 'nullable|array',
            'education.*.institution' => 'required_with:education|string|max:255',
            'education.*.degree' => 'required_with:education|string|max:255',
            'education.*.start_date' => 'required_with:education|date',
            'education.*.end_date' => 'required_without:education.*.is_current|nullable|date',
            'experience' => 'nullable|array',
            'experience.*.company' => 'required_with:experience|string|max:255',
            'experience.*.job_title' => 'required_with:experience|string|max:255',
            'experience.*.start_date' => 'required_with:experience|date',
            'experience.*.end_date' => 'required_without:experience.*.is_current|nullable|date',
        ], [
            'other_category_description.required' => 'Please tell us what type of work you are looking for.',
        ]);

        $user = auth()->user();
        $applicant = \App\Models\Applicant::where('email', $user->email)->first();

        if (!$applicant) {
            return redirect()->back()->with('error', 'Applicant profile not found.');
        }

        // Update User Name and Phone
        if ($user->name !== $request->name || ($request->phone && $user->phone !== $request->phone)) {
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->save();
        }

        // Handle CV Upload if present
        $cvPath = $applicant->cv_link;
        if ($request->hasFile('cv')) {
            // Delete old file if exists to save disk space
            if ($applicant->cv_link) {
                $oldPath = str_replace(['storage/app/private/', 'storage/app/'], '', ltrim($applicant->cv_link, '/'));
                \Illuminate\Support\Facades\Storage::disk('local')->delete($oldPath);
            }

            // Securely store CVs privately, keeping original sanitized filename
            $file = $request->file('cv');
            $originalName = $file->getClientOriginalName();
            $sanitizedName = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $originalName);
            $sanitizedName = ltrim($sanitizedName, '.');
            $fileName = \Illuminate\Support\Str::random(12) . '_' . (empty($sanitizedName) ? 'resume.pdf' : $sanitizedName);
            $path = $file->storeAs('cvs', $fileName, 'local');
            $cvPath = 'storage/app/' . $path;
        }

        // Handle Photo Upload if present
        $imagePath = $applicant->image;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $imagePath = '/media/' . $path;
        }

        // DATA INTEGRITY FIX: Wrap multi-table inserts in a transaction
        \Illuminate\Support\Facades\DB::transaction(function () use ($applicant, $request, $cvPath, $imagePath, $isOtherCategory) {
            // Update Applicant Data
            $applicant->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'location' => $request->location,
                'role' => $request->category,
                'sub_category' => $isOtherCategory ? null : $request->sub_category,
                'other_category_description' => $isOtherCategory ? $request->other_category_description : null,
                'cv_link' => $cvPath,
                'image' => $imagePath,
                'image_position' => $request->image_position ?? $applicant->image_position
            ]);

            // Update Education
            // Strategy: Delete all and recreate (simplest for repeater fields)
            $applicant->educations()->delete();
            if ($request->has('education')) {
                foreach ($request->education as $edu) {
                    if (!empty($edu['institution'])) {
                        $applicant->educations()->create([
                            'institution' => $edu['institution'],
                            'degree' => $edu['degree'],
                            'start_date' => $edu['start_date'] ?? null,
                            'end_date' => $edu['end_date'] ?? null,
                            'is_current' => isset($edu['is_current']) ? true : false,
                            'description' => $edu['description'] ?? null,
                        ]);
                    }
                }
            }

            // Update Experience
            $applicant->experiences()->delete();
            if ($request->has('experience')) {
                foreach ($request->experience as $exp) {
                    if (!empty($exp['company'])) {
                            $applicant->experiences()->create([
                            'company' => $exp['company'],
                            'job_title' => $exp['job_title'],
                            'start_date' => $exp['start_date'] ?? null,
                            'end_date' => $exp['end_date'] ?? null,
                            'is_current' => isset($exp['is_current']) ? true : false,
                            'description' => $exp['description'] ?? null,
                        ]);
                    }
                }
            }
        });

        if ($request->filled('redirect_back') && filter_var($request->input('redirect_back'), FILTER_VALIDATE_URL)) {
            return redirect($request->input('redirect_back'))->with('success', 'Profile updated successfully. You can now apply for the job!');
        }

        return redirect()->route('applicant.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Timesheets view for applicant
     */
    public function timesheets()
    {
        $applicant = \Illuminate\Support\Facades\Auth::user()->applicant;
        if (!$applicant) {
            abort(403, 'Applicant profile not found.');
        }

        $timesheets = \App\Models\ShiftSlot::with(['staffQuotation.eventBooking'])
            ->where('applicant_id', $applicant->id)
            ->whereIn('status', ['Submitted', 'Reviewed', 'Approved', 'Billed'])
            ->latest()
            ->get();

        return view('applicants.timesheets', compact('timesheets', 'applicant'));
    }

    /**
     * Clock in
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'event_assignment_id' => 'required|exists:shift_slots,id'
        ]);

        $applicant = \Illuminate\Support\Facades\Auth::user()->applicant;
        
        $slot = \App\Models\ShiftSlot::where('applicant_id', $applicant->id)
            ->where('id', $request->event_assignment_id)
            ->firstOrFail();

        if ($slot->status !== 'Assigned') {
            return back()->with('error', 'This shift is already processed or completed.');
        }

        if ($slot->start_time !== null && $slot->end_time === null) {
            return back()->with('error', 'You are already clocked into this shift.');
        }

        $slot->update([
            'start_time' => \Carbon\Carbon::now()->format('H:i'),
        ]);

        return back()->with('success', 'Successfully clocked in!');
    }

    /**
     * Clock out
     */
    public function clockOut(Request $request, $id)
    {
        $slot = \App\Models\ShiftSlot::findOrFail($id);
        
        if ($slot->applicant_id !== \Illuminate\Support\Facades\Auth::user()->applicant->id) {
            abort(403);
        }

        if ($slot->status !== 'Assigned') {
            return back()->with('error', 'This shift is already completed.');
        }

        $slot->update([
            'end_time' => \Carbon\Carbon::now()->format('H:i'),
            'status' => 'Submitted' // Transition to Submitted on clock out!
        ]);

        return back()->with('success', 'Successfully clocked out!');
    }

    /**
     * View applicant's own resume
     */
    public function viewResume()
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $user = auth()->user();
        $applicant = \App\Models\Applicant::where('email', $user->email)->first();
        
        if (!$applicant || !$applicant->cv_link) {
            return redirect()->route('portal')->with('error', 'No resume file associated with your profile.');
        }

        // Clean up path to match local disk structure
        $path = str_replace(['storage/app/private/', 'storage/app/'], '', ltrim($applicant->cv_link, '/'));

        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('local')->response($path);
        }

        if (file_exists(storage_path('app/' . $path))) {
            return response()->file(storage_path('app/' . $path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
            ]);
        }

        return redirect()->route('portal')->with('error', 'The uploaded resume file could not be found on the server. Please re-upload a copy.');
    }

    /**
     * Download applicant's own resume
     */
    public function downloadResume()
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $user = auth()->user();
        $applicant = \App\Models\Applicant::where('email', $user->email)->first();
        
        if (!$applicant || !$applicant->cv_link) {
            return redirect()->route('portal')->with('error', 'No resume file associated with your profile.');
        }

        // Clean up path to match local disk structure
        $path = str_replace(['storage/app/private/', 'storage/app/'], '', ltrim($applicant->cv_link, '/'));

        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'pdf';
        $filename = str_replace(' ', '_', $applicant->name) . '_Resume.' . $extension;

        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('local')->download($path, $filename);
        }

        if (file_exists(storage_path('app/' . $path))) {
            return response()->download(storage_path('app/' . $path), $filename);
        }

        return redirect()->route('portal')->with('error', 'The uploaded resume file could not be found on the server. Please re-upload a copy.');
    }
}
