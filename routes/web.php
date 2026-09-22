<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register-partner', [AuthController::class, 'registerPartner'])->name('register.partner');

Route::get('/login', function () {
    $intended = session()->get('url.intended', '');
    $hash = '#applicant-login'; // default
    
    if (str_contains($intended, '/admin')) {
        $hash = '#admin-login';
    } elseif (str_contains($intended, '/partner')) {
        $hash = '#partner-login';
    }
    
    return redirect('/portal' . $hash);
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');


Route::get('/', PageController::class)->name('home');
Route::get('/home-video', [PageController::class, 'video'])->name('home.video');

// PERMANENT FIX: Serve Files via '/media' URL with Multi-Folder Auto Search
Route::get('media/{path}', function ($path) {
    $cleanPath = str_replace('photos/profile-photos/', 'profile-photos/', $path);
    $candidates = [
        storage_path('app/public/' . $cleanPath),
        storage_path('app/public/' . $path),
        storage_path('app/public/photos/' . $path),
        storage_path('app/public/gallery/' . $path),
        storage_path('app/public/uploads/' . $path),
        storage_path('app/public/profile-photos/' . $path),
        public_path('media/' . $path),
        public_path('images/' . $path),
        public_path('videos/' . $path),
    ];
    foreach ($candidates as $candidate) {
        if (file_exists($candidate) && !is_dir($candidate)) {
            return response()->file($candidate);
        }
    }
    abort(404);
})->where('path', '.*');

// PERMANENT FIX: Serve Files via '/storage' URL (For existing DB records and missing symlink on Windows)
Route::get('storage/{path}', function ($path) {
    $cleanPath = str_replace('photos/profile-photos/', 'profile-photos/', $path);
    $candidates = [
        storage_path('app/public/' . $cleanPath),
        storage_path('app/public/' . $path),
        storage_path('app/public/photos/' . $path),
        storage_path('app/public/gallery/' . $path),
        storage_path('app/public/uploads/' . $path),
        storage_path('app/public/profile-photos/' . $path),
        public_path('media/' . $path),
        public_path('images/' . $path),
        public_path('videos/' . $path),
    ];
    foreach ($candidates as $candidate) {
        if (file_exists($candidate) && !is_dir($candidate)) {
            return response()->file($candidate);
        }
    }
    abort(404);
})->where('path', '.*');

// ROUTE TO CREATE STORAGE SYMLINK ON LIVE SERVER
Route::get('/create-storage-link', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Storage link created successfully!';
    } catch (\Exception $e) {
        return 'Error creating storage link: ' . $e->getMessage();
    }
});

// PERMANENT FIX: Serve Files via '/images' URL
Route::get('images/{path}', function ($path) {
    $filePath = public_path('images/' . $path);
    if (!file_exists($filePath)) {
        $filePath = storage_path('app/public/images/' . $path);
        if (!file_exists($filePath)) abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');

// PERMANENT FIX: Serve Files via '/videos' URL
Route::get('videos/{path}', function ($path) {
    $filePath = public_path('videos/' . $path);
    if (!file_exists($filePath)) {
        $filePath = storage_path('app/public/videos/' . $path);
        if (!file_exists($filePath)) abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');

/*
Route::get('/home-new', function () {
    // 1. Fetch Categories (Reused logic from PageController)
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

    $activeCategories = \App\Models\JobCategory::where('status', '!=', 'Archived')
        ->withCount(['jobs' => function ($query) {
            $query->where('status', '!=', 'Archived');
        }])
        ->get();

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
            'jobs' => $cat->jobs_count . '+',
            'icon' => $cat->icon ?? $meta['icon'], // Use DB icon if exists, else meta
            'color' => $meta['color']
        ];
    }

    // 2. Mock Applicants (for Home V2 display)
    $applicants = collect([
        (object)[
            'name' => 'Sarah Jenkins',
            'title' => 'Senior Event Manager',
            'skills' => ['Event Planning', 'Budgeting', 'Team Leadership']
        ],
        (object)[
            'name' => 'David Miller',
            'title' => 'Security Supervisor',
            'skills' => ['SIA Licensed', 'Crowd Control', 'First Aid']
        ],
        (object)[
            'name' => 'Emily Chen',
            'title' => 'Head Chef',
            'skills' => ['Menu Design', 'Kitchen Management', 'HACCP']
        ]
    ]);

    return view('home-v2', compact('categories', 'applicants'));
})->name('home.v2');

Route::get('/new-home', function () {
    // 1. Fetch Categories (Reused logic from PageController)
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

    $activeCategories = \App\Models\JobCategory::where('status', '!=', 'Archived')
        ->withCount(['jobs' => function ($query) {
            $query->where('status', '!=', 'Archived');
        }])
        ->get();

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
            'jobs' => $cat->jobs_count . '+',
            'icon' => $meta['icon'],
            'color' => $meta['color']
        ];
    }

    // 2. Mock Applicants (for Home V2/V3 display)
    $applicants = collect([
        (object)[
            'name' => 'Sarah Jenkins',
            'title' => 'Senior Event Manager',
            'skills' => ['Event Planning', 'Budgeting', 'Team Leadership']
        ],
        (object)[
            'name' => 'David Miller',
            'title' => 'Security Supervisor',
            'skills' => ['SIA Licensed', 'Crowd Control', 'First Aid']
        ],
        (object)[
            'name' => 'Emily Chen',
            'title' => 'Head Chef',
            'skills' => ['Menu Design', 'Kitchen Management', 'HACCP']
        ]
    ]);

    return view('new-home', compact('categories', 'applicants'));
})->name('home.v3');

Route::get('/home-v4', function () {
    // ── Categories ──
    $metaCategories = [
        'SIA Security'=>['icon'=>'shield','color'=>'text-blue-500'],
        'Security'=>['icon'=>'shield','color'=>'text-blue-500'],
        'Accounting'=>['icon'=>'line-chart','color'=>'text-green-500'],
        'Cleaning'=>['icon'=>'line-chart','color'=>'text-green-500'],
        'Event Management'=>['icon'=>'calendar','color'=>'text-purple-500'],
        'Events'=>['icon'=>'calendar','color'=>'text-purple-500'],
        'Hospitality Staff'=>['icon'=>'users','color'=>'text-orange-500'],
        'Hospitality'=>['icon'=>'users','color'=>'text-orange-500'],
        'Customer Service'=>['icon'=>'headphones','color'=>'text-pink-500'],
        'Construction'=>['icon'=>'headphones','color'=>'text-pink-500'],
        'Waiting Staff'=>['icon'=>'utensils','color'=>'text-yellow-500'],
    ];
    $activeCategories = \App\Models\JobCategory::where('status','!=','Archived')
        ->withCount(['jobs'=>fn($q)=>$q->where('status','!=','Archived')])->get();
    $categories = [];
    $norm = collect($metaCategories)->mapWithKeys(fn($v,$k)=>[strtolower($k)=>$v])->all();
    foreach($activeCategories as $c){
        $m=$norm[strtolower($c->name)]??['icon'=>'briefcase','color'=>'text-slate-500'];
        $categories[]=['name'=>$c->name,'jobs'=>$c->jobs_count.'+','icon'=>$m['icon'],'color'=>$m['color']];
    }

    // ── Companies ──
    $companies = [
        ['name'=>'TOP LOOK','logo'=>asset('images/top-company/1.png')],
        ['name'=>'TROUT IT','logo'=>asset('images/top-company/2.png')],
        ['name'=>'SUSSEX LTD','logo'=>asset('images/top-company/3.png')],
        ['name'=>'LAWN HOPPER','logo'=>asset('images/top-company/4.png')],
        ['name'=>'TOP LOOK','logo'=>asset('images/top-company/1.png')],
        ['name'=>'TROUT IT','logo'=>asset('images/top-company/2.png')],
        ['name'=>'SUSSEX LTD','logo'=>asset('images/top-company/3.png')],
        ['name'=>'LAWN HOPPER','logo'=>asset('images/top-company/4.png')],
    ];

    // ── Candidates (carousel) ──
    $candidates = [
        (object)['name'=>'James Wilson','title'=>'SIA Security Officer','bio'=>'Highly trained security professional with 5+ years of experience in corporate and event security. SIA licensed and First Aid certified.','image'=>'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=800&q=80','skills'=>['Surveillance','Conflict Resolution','Crowd Control'],'moreSkillsCount'=>2,'available'=>true],
        (object)['name'=>'Sarah Chen','title'=>'Senior Accountant','bio'=>'Chartered Accountant (ACCA) with extensive experience in financial reporting and tax compliance for SMEs.','image'=>'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=800&q=80','skills'=>['Taxation','Auditing','Financial Analysis'],'moreSkillsCount'=>4,'available'=>false],
        (object)['name'=>'Emily Davis','title'=>'Event Manager','bio'=>'Creative event planner with a portfolio of successful corporate conferences and high-profile weddings.','image'=>'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=800&q=80','skills'=>['Event Planning','Logistics','Marketing'],'moreSkillsCount'=>2,'available'=>true],
    ];

    // ── Testimonials ──
    $testimonials = [
        ['id'=>1,'quote'=>"Kingdom recruitment's has been helping with our recruitment for years, placing some great candidates with us. They know the sort of people we look for & ensure they only send the right ones over.",'author'=>'Mr Sayed','role'=>'Event Manager','company'=>'Intercontinental Park Lane','image'=>'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=200&auto=format&fit=crop'],
        ['id'=>2,'quote'=>"Whoever I speak to at Kingdom recruitments they are always incredibly supportive & attentive. They always go over & above to provide great candidates who match the brief & are quick with responses.",'author'=>'Event & Banqueting Manager','role'=>'Manager','company'=>'The Tower Hotel London (Guoman)','image'=>'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=200&auto=format&fit=crop'],
    ];

    // ── Articles ──
    $articles = [
        ['id'=>1,'category'=>'Career Advice','title'=>'How to Introduce Yourself in a Job Interview?','excerpt'=>'First impressions matter. Learn the proven framework for answering "Tell me about yourself" with confidence.','readTime'=>'5 min read','date'=>'Nov 24, 2023','image'=>'https://images.unsplash.com/photo-1565688534245-05d6b5be184a?q=80&w=800&auto=format&fit=crop'],
        ['id'=>2,'category'=>'Management','title'=>'Looking for Highly Motivated Product Teams to Build','excerpt'=>'Why culture fit and intrinsic motivation outweigh raw technical skills when scaling your startup.','readTime'=>'4 min read','date'=>'Nov 22, 2023','image'=>'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=800&auto=format&fit=crop'],
        ['id'=>3,'category'=>'Industry Insights','title'=>'The Reason Why Software Developer is the Best Job','excerpt'=>'Analyzing salary trends, remote flexibility, and the creative satisfaction of building the future.','readTime'=>'6 min read','date'=>'Nov 15, 2023','image'=>'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=800&auto=format&fit=crop'],
    ];

    return view('home-v4', compact('categories','companies','candidates','testimonials','articles'));
})->name('home.v4');
*/
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/team', function () {
    return view('team');
})->name('team');
Route::get('/careers', function () {
    return view('careers');
})->name('careers');
Route::get('/privacy-policy', function () {
    return view('privacy');
})->name('privacy');
Route::get('/terms', function () {
    return view('terms');
})->name('terms');
Route::get('/gallery', [\App\Http\Controllers\GalleryController::class, 'index'])->name('gallery');
// Public Job Routes
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');

// Public Applicants Overview
Route::get('/applicants', [ApplicantController::class, 'index'])->name('applicants.index');

// Protected Applicant & Candidate Routes
Route::middleware(['auth', 'role:applicant,candidate'])->group(function () {
    Route::post('/jobs/{id}/apply', [JobController::class, 'apply'])->name('jobs.apply');
    Route::get('/my-applications', [JobController::class, 'appliedJobs'])->name('jobs.applied');

    Route::get('/applicant/profile', [ApplicantController::class, 'profile'])->name('applicant.profile');
    Route::get('/my-schedule', [ApplicantController::class, 'mySchedule'])->name('applicant.schedule');
    Route::get('/applicant/timesheets', [ApplicantController::class, 'timesheets'])->name('applicant.timesheets');
    Route::post('/applicant/timesheets/clock-in', [ApplicantController::class, 'clockIn'])->name('applicant.timesheets.clockIn');
    Route::patch('/applicant/timesheets/clock-out/{id}', [ApplicantController::class, 'clockOut'])->name('applicant.timesheets.clockOut');
    Route::get('/applicants/upload-cv', [ApplicantController::class, 'upload'])->name('applicants.upload');
    Route::post('/applicants/upload-cv', [ApplicantController::class, 'storeCv'])->name('applicants.storeCv');
    Route::post('/applicant/profile/image', [ApplicantController::class, 'updateImage'])->name('applicant.update.image');

    // Applicant Profile Update Routes
    Route::get('/applicant/profile/edit', [ApplicantController::class, 'editProfile'])->name('applicant.profile.edit');
    Route::put('/applicant/profile/update', [ApplicantController::class, 'updateProfile'])->name('applicant.profile.update');
    Route::get('/applicant/profile/update', function () {
        return redirect()->route('applicant.profile.edit');
    });
    Route::get('/applicant/view-resume', [ApplicantController::class, 'viewResume'])->name('applicant.view-resume');
    Route::get('/applicant/download-resume', [ApplicantController::class, 'downloadResume'])->name('applicant.download-resume');
});

Route::get('/applicants/list', [ApplicantController::class, 'list'])->name('applicants.list')->middleware(['auth', 'role:admin']);

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Shared Accessible Routes (or customize per role)

    /* 
    Route::get('/portal', function () {
        return view('portal');
    })->name('portal'); 
    */
    
    // Candidate Routes
    /*
    Route::middleware([\App\Http\Middleware\RoleMiddleware::class . ':candidate'])->group(function () {
        // Routes moved to public for testing
    });
    */

    // Partner Routes
    /*
    Route::middleware([\App\Http\Middleware\RoleMiddleware::class . ':partner'])->group(function () {
         // Routes moved to public for testing
    });
    */
});

// Revert Portal to public as it hosts the Single Page App logic including login forms
Route::get('/portal', function () {
    $applicant = null;
    $partnerStats = null;
    $partnerJobs = [];
    $partnerQuotations = [];

    // Re-instate the original data fetching
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'candidate' || $user->role === 'applicant') {
            $applicant = \App\Models\Applicant::where('email', $user->email)->first();
        } elseif ($user->role === 'partner') {
            $companyName = $user->company_name ?: $user->name;
            
            // Open Shifts (Jobs)
            $openShiftsCount = \App\Models\JobPost::where('company_name', $companyName)->where('status', 'Active')->count();
            
            // Upcoming Staffing (Jobs)
            $partnerJobs = \App\Models\JobPost::where('company_name', $companyName)->orderBy('created_at', 'desc')->take(4)->get();
            
            // Pending Quotations
            $partnerQuotations = \App\Models\StaffQuotation::where('client', $user->name)
                                    ->orWhere('client', $companyName)->orderBy('created_at', 'desc')->take(3)->get();
            
            // Active Placements (Dummy logic based on job count for now)
            $activePlacements = $partnerJobs->count() * 3 + rand(1, 10);
            
            // Monthly Billing (Dummy for now)
            
            $partnerStats = [
                'active_placements' => $activePlacements,
                'open_shifts' => $openShiftsCount,
                'monthly_billing' => number_format($activePlacements * 1500, 0),
                'rating' => '4.9/5.0'
            ];
        }
    }
    return view('portal', compact('applicant', 'partnerStats', 'partnerJobs', 'partnerQuotations'));
})->name('portal');

Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf-token');

// Admin Dashboard Routes
/** @var \Illuminate\Routing\RouteRegistrar $router */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\AdminController::class, 'updateProfile'])->name('profile.update');

    // Featured Candidates Management
    Route::get('/featured-candidates', [\App\Http\Controllers\Admin\FeaturedCandidateController::class, 'index'])->name('featured-candidates.index');
    Route::post('/featured-candidates/toggle/{id}', [\App\Http\Controllers\Admin\FeaturedCandidateController::class, 'toggle'])->name('featured-candidates.toggle');
    Route::post('/featured-candidates/bulk', [\App\Http\Controllers\Admin\FeaturedCandidateController::class, 'bulkUpdate'])->name('featured-candidates.bulk');

    // System Settings — requires the manage_settings permission on top of the base admin gate
    Route::middleware('permission:manage_settings')->group(function () {
        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings');
        Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    });

    // Gallery Management
    Route::get('/gallery', [\App\Http\Controllers\Admin\GalleryController::class, 'index'])->name('gallery.index');
    Route::post('/gallery/upload', [\App\Http\Controllers\Admin\GalleryController::class, 'storeItems'])->name('gallery.upload');
    Route::put('/gallery/items/{id}', [\App\Http\Controllers\Admin\GalleryController::class, 'updateItem'])->name('gallery.items.update');
    Route::delete('/gallery/items/{id}', [\App\Http\Controllers\Admin\GalleryController::class, 'destroyItem'])->name('gallery.items.destroy');
    Route::post('/gallery/items/reorder', [\App\Http\Controllers\Admin\GalleryController::class, 'reorderItems'])->name('gallery.items.reorder');
    Route::post('/gallery/items/bulk', [\App\Http\Controllers\Admin\GalleryController::class, 'bulkAction'])->name('gallery.items.bulk');

    // Job Posts + Categories + Sub-Categories — the seeder's own manage_jobs comment
    // reads "categories, Sub-Categories, Job Posts", so this mapping is explicit, not inferred.
    Route::middleware('permission:manage_jobs')->group(function () {
        Route::get('/job-post', [App\Http\Controllers\AdminController::class, 'jobPost'])->name('job-post');
        Route::get('/job-category', [App\Http\Controllers\AdminController::class, 'jobCategory'])->name('job-category');
        Route::get('/sub-category', [App\Http\Controllers\AdminController::class, 'subCategory'])->name('sub-category');
        Route::put('/job-category/{id}/status', [App\Http\Controllers\AdminController::class, 'updateJobCategoryStatus'])->name('job-category.status');
        Route::put('/sub-category/{id}/status', [App\Http\Controllers\AdminController::class, 'updateSubCategoryStatus'])->name('sub-category.status');
        Route::post('/sub-category', [App\Http\Controllers\AdminController::class, 'storeSubCategory'])->name('sub-category.store');
        Route::put('/sub-category/{id}', [App\Http\Controllers\AdminController::class, 'updateSubCategory'])->name('sub-category.update');
        Route::delete('/sub-category/{id}', [App\Http\Controllers\AdminController::class, 'destroySubCategory'])->name('sub-category.destroy');
        Route::post('/job-category', [App\Http\Controllers\AdminController::class, 'storeJobCategory'])->name('job-category.store');
        Route::put('/job-category/{id}', [App\Http\Controllers\AdminController::class, 'updateJobCategory'])->name('job-category.update');
        Route::delete('/job-category/{id}', [App\Http\Controllers\AdminController::class, 'destroyJobCategory'])->name('job-category.destroy');
        Route::post('/job-post', [App\Http\Controllers\AdminController::class, 'storeJobPost'])->name('job-post.store');
        Route::put('/job-post/{id}', [App\Http\Controllers\AdminController::class, 'updateJobPost'])->name('job-post.update');
        Route::delete('/job-post/{id}', [App\Http\Controllers\AdminController::class, 'destroyJobPost'])->name('job-post.destroy');
        Route::put('/job-post/{id}/status', [App\Http\Controllers\AdminController::class, 'updateJobPostStatus'])->name('job-post.status');
    });

    // Rate Card, Venue, Staff Quotation, Events, Time-Shifting, Shifts — the seeder's own
    // manage_operations comment reads "rate card, venue, shifts, staff quotation, events,
    // time shifting", so this mapping is explicit, not inferred. Staff Assignments and the
    // events.assign/unassign staffing actions are deliberately excluded — the seeder never
    // names them, so enforcing a permission there would be an invented mapping.
    Route::middleware('permission:manage_operations')->group(function () {
        Route::get('/venue', [App\Http\Controllers\AdminController::class, 'venue'])->name('venue');
        Route::get('/shifts', [App\Http\Controllers\AdminController::class, 'shifts'])->name('shifts');
        Route::get('/events', [App\Http\Controllers\AdminController::class, 'events'])->name('events');
        Route::get('/staff-quotation', [App\Http\Controllers\AdminController::class, 'staffQuotation'])->name('staff-quotation');
        Route::get('/rate-card', [App\Http\Controllers\AdminController::class, 'rateCard'])->name('rate-card');
        Route::get('/time-shifting', [App\Http\Controllers\AdminController::class, 'timeShifting'])->name('time-shifting');
        Route::post('/timesheets/sync', [App\Http\Controllers\AdminController::class, 'syncTimesheets'])->name('timesheets.sync');
        Route::get('/rate-card/export', [App\Http\Controllers\AdminController::class, 'exportRateCards'])->name('rate-card.export');
        Route::put('/rate-card/{id}/status', [App\Http\Controllers\AdminController::class, 'updateRateCardStatus'])->name('rate-card.status');
        Route::get('/time-shifting/export', [App\Http\Controllers\AdminController::class, 'exportTimeSheets'])->name('time-shifting.export');
        Route::post('/rate-card', [App\Http\Controllers\AdminController::class, 'storeRateCard'])->name('rate-card.store');
        Route::put('/rate-card/{id}', [App\Http\Controllers\AdminController::class, 'updateRateCard'])->name('rate-card.update');
        Route::delete('/rate-card/{id}', [App\Http\Controllers\AdminController::class, 'destroyRateCard'])->name('rate-card.destroy');
        Route::post('/venue', [App\Http\Controllers\AdminController::class, 'storeVenue'])->name('venue.store');
        Route::put('/venue/{id}', [App\Http\Controllers\AdminController::class, 'updateVenue'])->name('venue.update');
        Route::delete('/venue/{id}', [App\Http\Controllers\AdminController::class, 'destroyVenue'])->name('venue.destroy');
        Route::post('/events', [App\Http\Controllers\AdminController::class, 'storeEvent'])->name('events.store');
        Route::put('/events/{id}', [App\Http\Controllers\AdminController::class, 'updateEvent'])->name('events.update');
        Route::delete('/events/{id}', [App\Http\Controllers\AdminController::class, 'destroyEvent'])->name('events.destroy');
        Route::post('/staff-quotation', [App\Http\Controllers\AdminController::class, 'storeStaffQuotation'])->name('staff-quotation.store');
        Route::put('/staff-quotation/{id}', [App\Http\Controllers\AdminController::class, 'updateStaffQuotation'])->name('staff-quotation.update');
        Route::delete('/staff-quotation/{id}', [App\Http\Controllers\AdminController::class, 'destroyStaffQuotation'])->name('staff-quotation.destroy');
        Route::get('/staff-quotation/{id}', function () {
            return redirect()->route('admin.staff-quotation');
        });
    });

    // Users — permission name matches the page 1:1, no inference needed.
    Route::middleware('permission:manage_users')->group(function () {
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::get('/users/export', [App\Http\Controllers\AdminController::class, 'exportUsers'])->name('users.export');
        Route::put('/users/{id}', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [App\Http\Controllers\AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::put('/users/{id}/status', [App\Http\Controllers\AdminController::class, 'updateUserStatus'])->name('users.status');
    });

    // Applicants — permission name matches the page 1:1, no inference needed.
    Route::middleware('permission:manage_applicants')->group(function () {
        Route::get('/applicant', [App\Http\Controllers\AdminController::class, 'applicant'])->name('applicant');
        Route::put('/applicant/{id}/assign-category', [App\Http\Controllers\AdminController::class, 'assignApplicantCategory'])->name('applicant.assign-category');
        Route::get('/applicant/export', [App\Http\Controllers\AdminController::class, 'exportApplicants'])->name('applicant.export');
        Route::delete('/applicant/{id}', [App\Http\Controllers\AdminController::class, 'destroyApplicant'])->name('applicant.destroy');
        Route::put('/applicant/{id}/status', [App\Http\Controllers\AdminController::class, 'updateApplicantStatus'])->name('applicant.update-status');
        Route::get('/applicant/{id}/export-pdf', [App\Http\Controllers\AdminController::class, 'exportApplicantPdf'])->name('applicant.export-pdf');
        Route::get('/applicant/{id}/download-resume', [App\Http\Controllers\AdminController::class, 'downloadResume'])->name('applicant.download-resume');
        Route::get('/applicant/{id}/view-resume', [App\Http\Controllers\AdminController::class, 'viewResume'])->name('applicant.view-resume');
    });

    // Team — permission name matches the page 1:1, no inference needed.
    Route::middleware('permission:manage_team')->group(function () {
        Route::get('/team', [App\Http\Controllers\AdminController::class, 'team'])->name('team');
        Route::post('/team', [App\Http\Controllers\AdminController::class, 'storeTeam'])->name('team.store');
        Route::put('/team/{id}', [App\Http\Controllers\AdminController::class, 'updateTeam'])->name('team.update');
        Route::delete('/team/{id}', [App\Http\Controllers\AdminController::class, 'destroyTeam'])->name('team.destroy');
    });

    // Not gated by a granular permission — see the audit report: no permission is named for
    // Staff Assignments, Partners, Ratings, or Applications, so enforcing one here would be
    // an invented mapping rather than one the seeder/UI/architecture already supports.
    Route::get('/assignments', [App\Http\Controllers\AdminController::class, 'assignments'])->name('assignments');
    Route::put('/assignments/{id}/status', [App\Http\Controllers\AdminController::class, 'updateAssignmentStatus'])->name('assignments.update-status');
    Route::get('/ratings', [App\Http\Controllers\AdminController::class, 'ratings'])->name('ratings');
    Route::delete('/ratings/{id}', [App\Http\Controllers\AdminController::class, 'destroyRating'])->name('ratings.destroy');
    Route::get('/partners', [App\Http\Controllers\AdminController::class, 'partners'])->name('partners');
    Route::put('/partners/{id}', [App\Http\Controllers\AdminController::class, 'updatePartner'])->name('partners.update');
    Route::delete('/partners/{id}', [App\Http\Controllers\AdminController::class, 'destroyPartner'])->name('partners.destroy');

    // Dynamic Roles & Permissions, and Admin Management — both require manage_roles,
    // the seeder's own "SUPER POWER: can create and edit roles/admins" permission.
    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['create', 'show', 'edit']);
        Route::resource('admins', \App\Http\Controllers\Admin\AdminUserController::class)->except(['create', 'show', 'edit']);
    });

    // Shift Assignment (not gated — see note above)
    Route::post('/shift/assign', [App\Http\Controllers\AdminController::class, 'assignStaffToShift'])->name('shift.assign');
    Route::post('/shift/{id}/remove', [App\Http\Controllers\AdminController::class, 'removeStaffFromShift'])->name('shift.remove');

    // Chat Support
    Route::get('/chat-support', [App\Http\Controllers\AdminController::class, 'chatSupport'])->name('chat-support');
    Route::post('/chat-support/{id}/reply', [App\Http\Controllers\AdminController::class, 'replyChatMessage'])->name('chat-support.reply');
    Route::put('/chat-support/{id}/resolve', [App\Http\Controllers\AdminController::class, 'resolveChatMessage'])->name('chat-support.resolve');
    Route::post('/chat-support/message', [App\Http\Controllers\AdminController::class, 'storeAdminMessage'])->name('chat-support.store');

    // Job Applications Management
    Route::get('/applications', [App\Http\Controllers\Admin\JobApplicationController::class, 'index'])->name('applications');
    Route::put('/applications/{id}/status', [App\Http\Controllers\Admin\JobApplicationController::class, 'updateStatus'])->name('applications.update-status');
    Route::delete('/applications/{id}', [App\Http\Controllers\Admin\JobApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::get('/applications/qualified-applicants', [App\Http\Controllers\Admin\JobApplicationController::class, 'qualifiedApplicants'])->name('applications.qualified');

    // Event Assignments
    Route::post('/events/{eventId}/assign', [App\Http\Controllers\Admin\JobApplicationController::class, 'assignToEvent'])->name('events.assign');
    Route::delete('/events/{eventId}/assign/{applicantId}', [App\Http\Controllers\Admin\JobApplicationController::class, 'removeFromEvent'])->name('events.unassign');
    Route::get('/events/{eventId}/assignments', [App\Http\Controllers\Admin\JobApplicationController::class, 'eventAssignments'])->name('events.assignments');

    // Admin Invoicing & Finance
    Route::get('/finance', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('finance');
    Route::post('/invoices/generate/{bookingId}', [\App\Http\Controllers\Admin\InvoiceController::class, 'generateInvoice'])->name('invoices.generate');
    Route::post('/invoices/{id}/mark-paid', [\App\Http\Controllers\Admin\InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::get('/invoices/{id}/pdf', [\App\Http\Controllers\Admin\InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
});

// Cache Clearing Route for Shared Hosting environments
Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return response()->json([
        'status' => 'success',
        'message' => 'System cache cleared successfully.'
    ]);
});

// Run Database Migrations Route for Shared Hosting/Staging environments
Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return response()->json([
            'status' => 'success',
            'message' => \Illuminate\Support\Facades\Artisan::output() ?: 'Migrations executed successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});


// Partner Dashboard Routes
/** @var \Illuminate\Routing\RouteRegistrar $router */
Route::prefix('partner')->name('partner.')->middleware(['auth', 'role:partner'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('partner.dashboard');
    });
    Route::get('/dashboard', [App\Http\Controllers\PartnerController::class, 'index'])->name('dashboard');
    Route::post('/rate-staff', [App\Http\Controllers\PartnerController::class, 'rateStaff'])->name('rateStaff');
    Route::post('/rate-event', [App\Http\Controllers\PartnerController::class, 'rateEvent'])->name('rateEvent');
    
    // Recruitment Hub
    Route::get('/events', [App\Http\Controllers\PartnerController::class, 'events'])->name('event-list');
    Route::get('/shifts', [App\Http\Controllers\PartnerController::class, 'shifts'])->name('shift-listings');
    Route::get('/timesheets', [App\Http\Controllers\PartnerController::class, 'timesheets'])->name('timesheets');
    Route::post('/timesheets/sync', [App\Http\Controllers\PartnerController::class, 'syncTimesheets'])->name('timesheets.sync');
    Route::get('/rate-card', [App\Http\Controllers\PartnerController::class, 'rateCard'])->name('rate-card');
    Route::get('/rate-card/export', [App\Http\Controllers\PartnerController::class, 'exportRateCards'])->name('rate-card.export');
    Route::get('/quotations', [App\Http\Controllers\PartnerController::class, 'quotations'])->name('quotations');

    // Management
    Route::get('/book-staff', [App\Http\Controllers\PartnerController::class, 'bookStaff'])->name('book-staff');
    Route::post('/book-staff', [App\Http\Controllers\PartnerController::class, 'storeBooking'])->name('book-staff.store');
    Route::get('/booking/{id}/edit', [App\Http\Controllers\PartnerController::class, 'editBooking'])->name('booking.edit');
    Route::put('/booking/{id}', [App\Http\Controllers\PartnerController::class, 'updateBooking'])->name('booking.update');
    Route::delete('/booking/{id}', [App\Http\Controllers\PartnerController::class, 'destroyBooking'])->name('booking.destroy');
    Route::get('/favourite-staff', [App\Http\Controllers\PartnerController::class, 'favouriteStaff'])->name('favourite-staff');
    Route::post('/favourite-staff/toggle', [App\Http\Controllers\PartnerController::class, 'toggleFavourite'])->name('favourite-staff.toggle');
    Route::get('/messages', [App\Http\Controllers\PartnerController::class, 'messages'])->name('messages');
    Route::post('/messages', [App\Http\Controllers\PartnerController::class, 'storeMessage'])->name('messages.store');
    Route::get('/messages/new/{contactId}', [App\Http\Controllers\PartnerController::class, 'newConversation'])->name('messages.new');
    Route::get('/contacts', [App\Http\Controllers\PartnerController::class, 'contacts'])->name('contacts');

    // Account
    Route::get('/alerts', [App\Http\Controllers\PartnerController::class, 'alerts'])->name('alerts');
    Route::get('/profile', [App\Http\Controllers\PartnerController::class, 'profile'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\PartnerController::class, 'updateProfile'])->name('profile.update');

    // Partner Billing Center
    Route::get('/billing', [\App\Http\Controllers\Admin\InvoiceController::class, 'partnerBilling'])->name('billing');
    Route::get('/invoices/{id}/pdf', [\App\Http\Controllers\Admin\InvoiceController::class, 'partnerDownloadPdf'])->name('invoices.pdf');
});

// ── Chatbot Routes (public, rate-limited) ──
/** @var \Illuminate\Routing\RouteRegistrar $router */
Route::prefix('chatbot')->middleware('throttle:20,1')->group(function () {
    Route::post('/respond', [App\Http\Controllers\ChatbotController::class, 'respond']);
    Route::post('/message', [App\Http\Controllers\ChatbotController::class, 'store']);
});



