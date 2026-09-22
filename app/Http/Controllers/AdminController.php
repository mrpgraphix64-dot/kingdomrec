<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // 1. TODAY'S OPERATIONS (ShiftSlot as source of truth)
        $todaySlots = \App\Models\ShiftSlot::whereDate('shift_date', $today)
            ->with(['applicant:id,name,role,profile_photo_path', 'staffQuotation:id,event_booking_id,event_name,venue,category,sub_category'])
            ->get();

        $eventsTodayCount = $todaySlots->map(fn($s) => $s->staffQuotation?->event_name)->filter()->unique()->count();
        if ($eventsTodayCount === 0) {
            // Check active event bookings today
            $eventsTodayCount = \App\Models\EventBooking::whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->whereIn('status', ['Approved', 'Confirmed', 'Active'])
                ->count();
        }

        $staffRequiredToday = $todaySlots->count();
        $staffAssignedToday = $todaySlots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->count();
        $vacanciesToday = $todaySlots->filter(fn($s) => empty($s->applicant_id) || $s->applicant_id == 0)->count();
        
        $onShiftApplicants = $todaySlots->where('status', 'Checked In')
            ->whereNotNull('applicant_id')
            ->where('applicant_id', '!=', 0)
            ->map(fn($s) => $s->applicant?->name)
            ->filter()
            ->unique()
            ->values()
            ->all();
        $onShiftCount = count($onShiftApplicants);

        $todayOps = [
            'events_today' => $eventsTodayCount,
            'staff_required' => $staffRequiredToday,
            'staff_assigned' => $staffAssignedToday,
            'vacancies_today' => $vacanciesToday,
            'on_shift_count' => $onShiftCount,
            'on_shift_names' => $onShiftApplicants,
        ];

        // 2. OPERATIONAL PIPELINE COUNTS (Live Workflow Stages)
        // Stage 1: Bookings / Quotes Awaiting Approval
        $pendingQuotesCount = \App\Models\EventBooking::whereIn('status', ['Pending', 'Sent', 'Draft'])->count();

        // Stage 2: Active Vacancies (Approved upcoming/today bookings needing staff)
        $activeVacanciesCount = \App\Models\ShiftSlot::whereDate('shift_date', '>=', $today)
            ->where(fn($q) => $q->whereNull('applicant_id')->orWhere('applicant_id', 0))
            ->whereHas('staffQuotation.eventBooking', fn($q) => $q->whereIn('status', ['Approved', 'Confirmed', 'Active']))
            ->count();

        // Stage 3: Timesheets Pending Finalisation (past/today slots needing review)
        $pendingTimesheetsCount = \App\Models\ShiftSlot::whereDate('shift_date', '<=', $today)
            ->whereNotNull('applicant_id')
            ->where('applicant_id', '!=', 0)
            ->whereIn('status', ['Checked Out', 'Submitted', 'Draft'])
            ->whereHas('staffQuotation.eventBooking', fn($q) => $q->whereIn('status', ['Approved', 'Confirmed', 'Active']))
            ->count();

        // Stage 4: Ready for Invoicing / Billing
        $readyForBillingCount = \App\Models\EventBooking::where('event_bookings.status', '!=', 'Closed')
            ->whereHas('shiftSlots', fn($q) => $q->where('shift_slots.status', 'Approved'))
            ->whereDoesntHave('invoices')
            ->count();

        $pipeline = [
            'pending_quotes' => $pendingQuotesCount,
            'active_vacancies' => $activeVacanciesCount,
            'pending_timesheets' => $pendingTimesheetsCount,
            'ready_for_billing' => $readyForBillingCount,
        ];

        // 3. "NEEDS YOUR ATTENTION" ACTIONABLE LIST (Max 5-7 Priority Items)
        $attentionItems = collect();

        // A. Immediate Staffing Needed (Upcoming/today approved shifts with vacancies)
        $understaffedShifts = \App\Models\StaffQuotation::whereHas('eventBooking', fn($q) => $q->whereIn('status', ['Approved', 'Confirmed', 'Active']))
            ->whereDate('start_date', '>=', $today)
            ->with(['eventBooking', 'shiftSlots'])
            ->orderBy('start_date', 'asc')
            ->take(10)
            ->get()
            ->filter(fn($sq) => $sq->vacancy_count > 0);

        foreach ($understaffedShifts->take(4) as $shift) {
            $attentionItems->push([
                'type' => 'staffing',
                'badge' => 'Staff Needed',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
                'title' => $shift->event_name ?: ($shift->eventBooking?->event_name ?? 'Event Booking'),
                'subtitle' => ($shift->sub_category ?: $shift->category) . ' · ' . ($shift->start_date ? $shift->start_date->format('M j, Y') : 'Upcoming'),
                'highlight' => $shift->vacancy_count . ' staff needed (' . ($shift->quantity - $shift->vacancy_count) . '/' . $shift->quantity . ' filled)',
                'action_label' => 'Assign Staff',
                'action_url' => route('admin.assignments', ['event' => $shift->event_name ?: $shift->eventBooking?->event_name]),
                'action_icon' => 'user-plus',
                'action_style' => 'bg-kingdom-navy hover:bg-slate-800 text-white shadow-sm',
            ]);
        }

        // B. Pending Quotations / Bookings Awaiting Review
        $pendingBookings = \App\Models\EventBooking::whereIn('status', ['Pending', 'Sent'])
            ->latest()
            ->take(3)
            ->get();

        foreach ($pendingBookings as $pb) {
            if ($attentionItems->count() >= 6) break;
            $attentionItems->push([
                'type' => 'approval',
                'badge' => 'Approval Needed',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/40 dark:text-blue-300',
                'title' => $pb->event_name ?: 'Booking Ref: ' . $pb->booking_ref,
                'subtitle' => ($pb->client ?: 'Client Request') . ' · ' . ($pb->start_date ? $pb->start_date->format('M j') : 'TBD'),
                'highlight' => 'Quote awaiting admin sign-off (' . ($pb->total_amount ?: '£0.00') . ')',
                'action_label' => 'Review Quote',
                'action_url' => route('admin.staff-quotation', ['status' => 'Pending']),
                'action_icon' => 'clipboard-check',
                'action_style' => 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm',
            ]);
        }

        // C. Timesheets Awaiting Finalisation
        if ($pendingTimesheetsCount > 0 && $attentionItems->count() < 6) {
            $attentionItems->push([
                'type' => 'timesheet',
                'badge' => 'Timesheets',
                'badge_color' => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/40 dark:text-purple-300',
                'title' => $pendingTimesheetsCount . ' Completed Timesheets',
                'subtitle' => 'Shifts waiting for hours verification & approval',
                'highlight' => 'Verify attendance and finalize hours for billing',
                'action_label' => 'Finalize Timesheets',
                'action_url' => route('admin.time-shifting'),
                'action_icon' => 'check-circle-2',
                'action_style' => 'bg-purple-600 hover:bg-purple-700 text-white shadow-sm',
            ]);
        }

        // D. Ready for Invoicing / Billing
        if ($readyForBillingCount > 0 && $attentionItems->count() < 6) {
            $attentionItems->push([
                'type' => 'billing',
                'badge' => 'Ready to Bill',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
                'title' => $readyForBillingCount . ' Events Ready for Invoicing',
                'subtitle' => 'Approved shifts ready to be billed to clients',
                'highlight' => 'Generate and dispatch partner invoices',
                'action_label' => 'View Invoicing',
                'action_url' => route('admin.finance', ['status' => 'Awaiting Invoice']),
                'action_icon' => 'receipt',
                'action_style' => 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm',
            ]);
        }

        // E. Uncategorised Applicant Category Requests (Other / Not Listed)
        $uncategorizedApplicantsCount = \App\Models\Applicant::whereIn('role', ['Other', 'Other / Not Listed'])->count();
        if ($uncategorizedApplicantsCount > 0 && $attentionItems->count() < 6) {
            $attentionItems->push([
                'type' => 'category_review',
                'badge' => 'Category Review',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
                'title' => $uncategorizedApplicantsCount . ' Applicant Category ' . ($uncategorizedApplicantsCount === 1 ? 'Request' : 'Requests'),
                'subtitle' => 'Applicants selected roles not currently available in your category structure.',
                'highlight' => 'Review requested roles and map them to official categories',
                'action_label' => 'Review Requests',
                'action_url' => route('admin.applicant', ['filter' => 'uncategorized']),
                'action_icon' => 'tag',
                'action_style' => 'bg-amber-600 hover:bg-amber-700 text-white shadow-sm',
            ]);
        }

        // 4. UPCOMING OPERATIONS SCHEDULE (Upcoming Events Table)
        $upcomingEvents = \App\Models\EventBooking::whereIn('status', ['Approved', 'Confirmed', 'Active', 'Pending'])
            ->whereDate('start_date', '>=', $today)
            ->with(['shifts.shiftSlots'])
            ->orderBy('start_date', 'asc')
            ->take(8)
            ->get()
            ->map(function($booking) use ($today) {
                $totalSlots = 0;
                $assignedSlots = 0;

                foreach ($booking->shifts as $shift) {
                    $slotsCount = $shift->shiftSlots->count();
                    $req = max($shift->quantity, $slotsCount);
                    $totalSlots += $req;
                    $assignedSlots += $shift->shiftSlots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->count();
                }

                $vacancies = max(0, $totalSlots - $assignedSlots);
                $progress = $totalSlots > 0 ? min(100, round(($assignedSlots / $totalSlots) * 100)) : 100;

                $dateDisplay = $booking->start_date ? $booking->start_date->format('M j, Y') : 'TBD';
                if ($booking->start_date && $booking->start_date->isToday()) {
                    $dateDisplay = 'Today';
                } elseif ($booking->start_date && $booking->start_date->isTomorrow()) {
                    $dateDisplay = 'Tomorrow';
                }

                return [
                    'id' => $booking->id,
                    'event_name' => $booking->event_name ?: 'Event Booking',
                    'client' => $booking->client ?: 'Private Client',
                    'venue' => $booking->venue ?: 'Location TBD',
                    'date_display' => $dateDisplay,
                    'start_date' => $booking->start_date,
                    'total_required' => $totalSlots,
                    'total_assigned' => $assignedSlots,
                    'vacancies' => $vacancies,
                    'progress' => $progress,
                    'status' => $booking->status,
                    'is_fully_staffed' => $vacancies === 0 && $totalSlots > 0,
                ];
            });

        // 5. TOP LEVEL KPI CARDS & TEAM INSIGHTS
        $totalJobs = \App\Models\JobPost::count();
        $activeJobs = \App\Models\JobPost::where('status', 'Active')->count();
        $expiredJobs = \App\Models\JobPost::whereIn('status', ['Expired', 'Closed', 'Archived'])->count();

        $totalApplicants = \App\Models\Applicant::count();
        $newApplicantsThisWeek = \App\Models\Applicant::where('created_at', '>=', now()->subDays(7))->count();
        $hiredApplicants = \App\Models\Applicant::where('status', 'Hired')->count();

        $totalTeam = \App\Models\Team::count();
        $assignedApplicantIdsToday = $todaySlots->whereNotNull('applicant_id')->pluck('applicant_id')->filter()->unique();
        $availableTalentToday = max(0, $totalApplicants - $assignedApplicantIdsToday->count());

        $totalEvents = \App\Models\EventBooking::count();
        $upcomingEventsCount = \App\Models\EventBooking::whereDate('start_date', '>=', $today)->count();

        $totalQuotes = \App\Models\StaffQuotation::count();

        $kpis = [
            'jobs' => ['total' => $totalJobs, 'active' => $activeJobs, 'expired' => $expiredJobs],
            'applicants' => ['total' => $totalApplicants, 'new_this_week' => $newApplicantsThisWeek, 'hired' => $hiredApplicants],
            'team' => ['total' => $totalTeam, 'available' => $availableTalentToday],
            'events' => ['total' => $totalEvents, 'ongoing_today' => $eventsTodayCount, 'upcoming' => $upcomingEventsCount],
            'quotes' => ['total' => $totalQuotes, 'pending' => $pendingQuotesCount],
            'open_roles' => ['total' => $activeVacanciesCount, 'urgent_today' => $vacanciesToday],
        ];

        $teamInsights = [
            'available_today' => $availableTalentToday,
            'on_shift_count' => $onShiftCount,
            'on_shift_names' => $onShiftApplicants,
            'assigned_today' => $staffAssignedToday,
            'staff_required_today' => $staffRequiredToday,
            'utilization_rate' => $staffRequiredToday > 0 ? min(100, round(($staffAssignedToday / $staffRequiredToday) * 100)) : ($staffAssignedToday > 0 ? 100 : 0),
        ];

        // Secondary Summary Info (Non-intrusive)
        $summary = [
            'total_talent' => $totalApplicants,
            'hired_talent' => $hiredApplicants,
            'total_partners' => \App\Models\User::where('role', 'partner')->count(),
            'active_jobs' => $activeJobs,
        ];

        return view('admin.dashboard', compact('kpis', 'teamInsights', 'todayOps', 'pipeline', 'attentionItems', 'upcomingEvents', 'summary'));
    }

    // ...

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        if ($request->hasFile('profile')) {
            $request->validate([
                'profile' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Delete old photo if exists
            if ($user->profile_photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $request->file('profile')->store('uploads/profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();
        
        // Also update linked Team Member if exists
        $staff = \App\Models\Team::where('user_id', $user->id)->first();
        if ($staff) {
            $staff->update([
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }
        
         \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated Profile',
            'description' => "Updated own profile details"
         ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    // Venue CRUD
    public function venue()
    {
        $venues = \App\Models\Venue::paginate(15);
        return view('admin.venue', compact('venues'));
    }

    public function jobPost(Request $request)
    {
        $query = \App\Models\JobPost::with(['category', 'staffQuotation.eventBooking'])->latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sub_category', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $jobs = $query->paginate(15)->withQueryString();
        $categories = \App\Models\JobCategory::where('status', 'Active')->get();
        $subCategories = \App\Models\SubCategory::where('status', 'Active')->get();

        // Full lists (incl. inactive/archived) for the "Manage Categories" panel
        $allCategories = \App\Models\JobCategory::withCount('jobs')->get();
        $allSubCategories = \App\Models\SubCategory::with('parent')->get()->map(function($sub) {
            $sub->rate_cards_count = \App\Models\RateCard::where('sub_category', $sub->name)->count();
            $sub->linked_rate_cards = \App\Models\RateCard::where('sub_category', $sub->name)->pluck('sub_category')->toArray();
            return $sub;
        });
        $parentCategories = $categories;

        return view('admin.job-post', compact('jobs', 'categories', 'subCategories', 'allCategories', 'allSubCategories', 'parentCategories'));
    }

    public function applicant(Request $request)
    {
        $query = \App\Models\Applicant::with(['experiences', 'educations', 'user'])->withCount('jobApplications');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('other_category_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->get('filter') === 'uncategorized' || $request->get('category_review') === '1') {
            $query->whereIn('role', ['Other', 'Other / Not Listed']);
        } elseif ($request->filled('role')) {
            $query->where(function($q) use ($request) {
                $q->where('role', 'like', "%{$request->role}%")
                  ->orWhere('other_category_description', 'like', "%{$request->role}%");
            });
        }

        $applicants = $query->paginate(15)->withQueryString();

        foreach ($applicants as $c) {
            $cvExists = false;
            if ($c->cv_link) {
                $path = str_replace(['storage/app/private/', 'storage/app/'], '', ltrim($c->cv_link, '/'));
                $cvExists = \Illuminate\Support\Facades\Storage::disk('local')->exists($path);
            }
            $c->setAttribute('cv_exists', $cvExists);
        }

        $categories = \App\Models\JobCategory::where('status', 'Active')->orderBy('name')->get();
        $subCategories = \App\Models\SubCategory::where('status', 'Active')->orderBy('name')->get();
        $uncategorizedCount = \App\Models\Applicant::whereIn('role', ['Other', 'Other / Not Listed'])->count();

        return view('admin.applicant', compact('applicants', 'categories', 'subCategories', 'uncategorizedCount'));
    }

    public function assignApplicantCategory(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string|max:255',
            'sub_category' => 'nullable|string|max:255',
        ]);

        $applicant = \App\Models\Applicant::findOrFail($id);
        
        $oldRole = $applicant->role;
        $applicant->role = $request->role;
        if ($request->filled('sub_category')) {
            $applicant->sub_category = $request->sub_category;
        }
        $applicant->save();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Mapped Category',
            'description' => "Mapped applicant {$applicant->name} from '{$oldRole}' to '{$request->role}'" . ($request->sub_category ? " ({$request->sub_category})" : "")
        ]);

        return redirect()->back()->with('success', "Applicant category updated to '{$applicant->role}' successfully.");
    }

    public function partners(Request $request)
    {
        $query = \App\Models\User::where('role', 'partner')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $partners = $query->withCount([
            'eventBookings as active_events_count' => function ($q) {
                $q->whereIn('status', ['Approved', 'Confirmed', 'Active']);
            },
            'eventBookings as total_events_count',
        ])->paginate(15)->withQueryString();

        $stats = [
            'total' => \App\Models\User::where('role', 'partner')->count(),
            'active' => \App\Models\User::where('role', 'partner')->where('is_active', true)->count(),
            'inactive' => \App\Models\User::where('role', 'partner')->where('is_active', false)->count(),
            'recent' => \App\Models\User::where('role', 'partner')->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('admin.partners', compact('partners', 'stats'));
    }

    public function updatePartner(Request $request, $id)
    {
        $partner = \App\Models\User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);
        
        $partner->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'website' => $request->website,
            'address' => $request->address,
        ]);
        
        return redirect()->back()->with('success', 'Partner details updated successfully.');
    }

    public function team()
    {
        $staffMembers = \App\Models\Team::with('user.roles')->orderBy('id', 'desc')->paginate(15);
        
        $staffMembers->each(function ($member) {
            $member->linkedUser = $member->user;
        });
        
        $activities = \App\Models\ActivityLog::with('user')->latest()->take(20)->get();
        $roles = \Spatie\Permission\Models\Role::all(); // For dashboard access dropdown
        return view('admin.team', compact('staffMembers', 'activities', 'roles'));
    }

    public function events()
    {
        $today = now()->startOfDay();

        // 1. Fetch light bookings details globally for stats calculations
        $allBookingsForStats = \App\Models\EventBooking::whereIn('status', ['Approved', 'Confirmed', 'Active'])
            ->with(['shifts' => function($q) {
                $q->select('id', 'event_booking_id', 'quantity');
            }, 'shifts.shiftSlots' => function($q) {
                $q->select('id', 'staff_quotation_id', 'applicant_id', 'status');
            }])
            ->get(['id', 'status', 'start_date', 'end_date']);

        foreach ($allBookingsForStats as $booking) {
            $required = 0;
            $assigned = 0;
            foreach ($booking->shifts as $shift) {
                $required += $shift->quantity;
                $assigned += $shift->shiftSlots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->count();
            }
            $booking->total_staff_required = $required;
            $booking->total_staff_assigned = $assigned;

            $opStatus = 'Approved';
            if (in_array(strtolower($booking->status), ['cancelled', 'rejected'])) {
                $opStatus = 'Cancelled';
            } else {
                $startDate = $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->startOfDay() : null;
                $endDate = $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->startOfDay() : ($startDate ?: null);

                if ($endDate && $endDate->lt($today)) {
                    $opStatus = 'Completed';
                } elseif ($startDate && $endDate && $today->gte($startDate) && $today->lte($endDate)) {
                    $opStatus = 'Live Event';
                } else {
                    if ($assigned === 0) {
                        $opStatus = 'Approved';
                    } elseif ($assigned < $required) {
                        $opStatus = 'Staffing In Progress';
                    } else {
                        $opStatus = 'Fully Staffed';
                    }
                }
            }
            $booking->operational_status = $opStatus;
        }

        $activeEvents = $allBookingsForStats->filter(fn($b) => !in_array($b->operational_status, ['Completed', 'Cancelled']));
        
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $eventsThisWeekCount = $allBookingsForStats->filter(function($b) use ($startOfWeek, $endOfWeek) {
            if (!$b->start_date) return false;
            $date = \Carbon\Carbon::parse($b->start_date);
            return $date->between($startOfWeek, $endOfWeek);
        })->count();

        $fullyStaffedEventsCount = $activeEvents->filter(fn($b) => $b->total_staff_assigned >= $b->total_staff_required && $b->total_staff_required > 0)->count();
        $eventsRequiringStaffCount = $activeEvents->filter(fn($b) => $b->total_staff_assigned < $b->total_staff_required)->count();
        $liveEventsTodayCount = $allBookingsForStats->filter(fn($b) => $b->operational_status === 'Live Event')->count();

        $stats = [
            'active_events' => $activeEvents->count(),
            'events_this_week' => $eventsThisWeekCount,
            'fully_staffed_events' => $fullyStaffedEventsCount,
            'events_requiring_staff' => $eventsRequiringStaffCount,
            'live_events_today' => $liveEventsTodayCount,
        ];

        // 2. Fetch the paginated grid records
        $eventBookings = \App\Models\EventBooking::whereIn('status', ['Approved', 'Confirmed', 'Active'])
            ->with(['shifts.shiftSlots.applicant', 'shifts.events', 'shifts.jobPosts', 'user'])
            ->orderBy('start_date', 'asc')
            ->paginate(15);

        foreach ($eventBookings as $booking) {
            $required = 0;
            $assigned = 0;
            foreach ($booking->shifts as $shift) {
                $required += $shift->quantity;
                $assigned += $shift->shiftSlots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->count();
            }

            $booking->total_staff_required = $required;
            $booking->total_staff_assigned = $assigned;
            $booking->remaining_vacancies = max(0, $required - $assigned);
            $booking->completion_percentage = $required > 0 ? min(100, round(($assigned / $required) * 100)) : 0;

            $opStatus = 'Approved';
            if (in_array(strtolower($booking->status), ['cancelled', 'rejected'])) {
                $opStatus = 'Cancelled';
            } else {
                $startDate = $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->startOfDay() : null;
                $endDate = $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->startOfDay() : ($startDate ?: null);

                if ($endDate && $endDate->lt($today)) {
                    $opStatus = 'Completed';
                } elseif ($startDate && $endDate && $today->gte($startDate) && $today->lte($endDate)) {
                    $opStatus = 'Live Event';
                } else {
                    if ($assigned === 0) {
                        $opStatus = 'Approved';
                    } elseif ($assigned < $required) {
                        $opStatus = 'Staffing In Progress';
                    } else {
                        if ($startDate && $startDate->diffInHours($today) <= 48) {
                            $opStatus = 'Ready';
                        } else {
                            $opStatus = 'Fully Staffed';
                        }
                    }
                }
            }
            $booking->operational_status = $opStatus;

            $startDate = $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->startOfDay() : null;
            $hoursToStart = $startDate ? now()->diffInHours($startDate, false) : 999;
            $vacant = $booking->remaining_vacancies;
            
            if ($booking->operational_status === 'Completed') {
                $priority = 'Completed';
            } elseif ($vacant > 0 && $hoursToStart <= 24) {
                $priority = 'Critical';
            } elseif ($vacant > 0 && $hoursToStart <= 72) {
                $priority = 'Warning';
            } else {
                $priority = 'Healthy';
            }
            $booking->priority = $priority;

            $readiness = 'Ready for Operations';
            if ($booking->remaining_vacancies > 0) {
                if ($booking->completion_percentage < 50) {
                    $readiness = 'Critical Shortage';
                } else {
                    $readiness = 'Needs Staffing';
                }
            }
            $booking->readiness = $readiness;
        }

        $venues = \App\Models\Venue::all();
        return view('admin.events', compact('eventBookings', 'stats', 'venues'));
    }

    public function staffQuotation(Request $request)
    {
        $query = \App\Models\EventBooking::with(['shifts', 'user'])->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_ref', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $statusVal = strtolower($request->status);
            if ($statusVal === 'approved' || $statusVal === 'active' || $statusVal === 'confirmed') {
                $query->whereIn('status', ['Approved', 'Confirmed', 'Active', ucfirst($request->status), strtolower($request->status)]);
            } elseif ($statusVal === 'pending' || $statusVal === 'draft') {
                $query->whereIn('status', ['Pending', 'Draft', ucfirst($request->status), strtolower($request->status)]);
            } else {
                $query->where(function($q) use ($request) {
                    $q->where('status', $request->status)
                      ->orWhere('status', ucfirst(strtolower($request->status)))
                      ->orWhere('status', strtolower($request->status));
                });
            }
        }

        $eventBookings = $query->paginate(15)->withQueryString();
        $orphanedQuotations = collect();
        $categories = \App\Models\JobCategory::with('subCategories')->get();
        $venues = \App\Models\Venue::all();
        $partners = \App\Models\User::where('role', 'partner')->orderBy('name')->get();
        $editing = null;
        $editingType = null;

        // Global stats across all bookings
        $statTotalBookings = \App\Models\EventBooking::count();
        $statActiveApproved = \App\Models\EventBooking::whereIn('status', ['Approved', 'Confirmed', 'Active'])->count();
        $statPending = \App\Models\EventBooking::whereIn('status', ['Pending', 'Draft'])->count();
        $statTotalValue = 0;
        foreach (\App\Models\StaffQuotation::whereHas('eventBooking')->get() as $s) {
            $statTotalValue += floatval(preg_replace('/[^0-9.]/', '', $s->amount));
        }

        if ($request->has('edit') && $request->get('edit')) {
            if ($request->get('type') === 'booking') {
                $bookingToEdit = \App\Models\EventBooking::find($request->get('edit'));
                if ($bookingToEdit && !in_array(strtolower($bookingToEdit->status), ['approved', 'confirmed', 'active', 'billed'])) {
                    $editing = $bookingToEdit->load('shifts');
                    $editingType = 'booking';
                }
            } else {
                $editing = \App\Models\StaffQuotation::find($request->get('edit'));
                $editingType = 'orphaned';
            }
        }
        return view('admin.staff-quotation', compact(
            'eventBookings', 
            'orphanedQuotations', 
            'categories', 
            'venues', 
            'partners', 
            'editing', 
            'editingType',
            'statTotalBookings',
            'statActiveApproved',
            'statPending',
            'statTotalValue'
        ));
    }

    public function assignments()
    {
        $now = now();
        $todayStr = $now->toDateString();
        $thirtyDaysAgo = now()->subDays(30)->toDateString();
        $thirtyDaysAhead = now()->addDays(30)->toDateString();

        // 1. Fetch ShiftSlots for active/approved bookings within a sliding 60-day window
        $slots = \App\Models\ShiftSlot::with(['applicant', 'staffQuotation.eventBooking'])
            ->whereHas('staffQuotation.eventBooking', function($q) use ($thirtyDaysAgo, $thirtyDaysAhead) {
                $q->whereIn('status', ['Approved', 'Confirmed', 'Active'])
                  ->where(function($sub) use ($thirtyDaysAgo, $thirtyDaysAhead) {
                      $sub->whereBetween('start_date', [$thirtyDaysAgo, $thirtyDaysAhead])
                          ->orWhereBetween('end_date', [$thirtyDaysAgo, $thirtyDaysAhead])
                          ->orWhereNull('start_date');
                  });
            })
            ->orderBy('shift_date', 'asc')
            ->get();

        // 2. Fetch active EventBookings within the sliding 60-day window
        $eventBookings = \App\Models\EventBooking::whereIn('status', ['Approved', 'Confirmed', 'Active'])
            ->where(function($q) use ($thirtyDaysAgo, $thirtyDaysAhead) {
                $q->whereBetween('start_date', [$thirtyDaysAgo, $thirtyDaysAhead])
                  ->orWhereBetween('end_date', [$thirtyDaysAgo, $thirtyDaysAhead])
                  ->orWhereNull('start_date');
            })
            ->with(['shifts.shiftSlots.applicant'])
            ->orderBy('start_date', 'asc')
            ->get();

        // 3. Compute conflicts: overlapping assignments on the same day for the same applicant
        $conflicts = [];
        $assignedSlots = $slots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0);
        foreach ($assignedSlots as $slot) {
            $applicantId = $slot->applicant_id;
            $date = $slot->shift_date ? $slot->shift_date->toDateString() : null;
            if (!$date || !$applicantId) continue;

            $overlapping = $assignedSlots->filter(function($other) use ($slot, $applicantId, $date) {
                if ($other->id === $slot->id) return false;
                if ($other->applicant_id !== $applicantId) return false;
                if (!$other->shift_date || $other->shift_date->toDateString() !== $date) return false;

                $start1 = $slot->start_time ?: ($slot->staffQuotation->start_time ?: '09:00');
                $end1 = $slot->end_time ?: ($slot->staffQuotation->end_time ?: '17:00');
                $start2 = $other->start_time ?: ($other->staffQuotation->start_time ?: '09:00');
                $end2 = $other->end_time ?: ($other->staffQuotation->end_time ?: '17:00');

                $s1 = strtotime($start1);
                $e1 = strtotime($end1);
                if ($e1 < $s1) $e1 += 86400;

                $s2 = strtotime($start2);
                $e2 = strtotime($end2);
                if ($e2 < $s2) $e2 += 86400;

                return ($s1 < $e2 && $s2 < $e1);
            });

            if ($overlapping->count() > 0) {
                $conflicts[$slot->id] = [
                    'has_conflict' => true,
                    'message' => 'Overlaps with ' . $overlapping->map(fn($o) => ($o->staffQuotation->eventBooking->event_name ?? 'other shift'))->unique()->implode(', ')
                ];
            }
        }

        // 4. Applicant Pool mapping with availability status
        $assignedTodayApplicantIds = \App\Models\ShiftSlot::whereDate('shift_date', $todayStr)
            ->whereNotNull('applicant_id')
            ->where('applicant_id', '!=', 0)
            ->pluck('applicant_id')
            ->toArray();

        // Applicants who applied to a job posting created for a specific shift shortfall,
        // and were marked Qualified — they should be prioritised when filling that shift.
        $qualifiedByApplicant = \App\Models\JobApplication::where('status', 'Qualified')
            ->whereHas('jobPost', function($q) {
                $q->whereNotNull('staff_quotation_id');
            })
            ->with('jobPost:id,staff_quotation_id')
            ->get()
            ->groupBy('applicant_id')
            ->map(fn($apps) => $apps->pluck('jobPost.staff_quotation_id')->unique()->values());

        $applicants = \App\Models\Applicant::orderBy('name', 'asc')->get();
        $applicantPool = $applicants->map(function($c) use ($assignedTodayApplicantIds, $qualifiedByApplicant) {
            $status = 'Unavailable';
            if (in_array($c->id, $assignedTodayApplicantIds)) {
                $status = 'Assigned';
            } elseif (strtolower($c->status) !== 'rejected') {
                $status = 'Available';
            }
            return [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'role' => $c->sub_category ?: $c->role ?: 'General Staff',
                'status' => $status,
                'location' => $c->location,
                'qualified_staff_quotation_ids' => $qualifiedByApplicant->get($c->id, collect())->values(),
            ];
        });

        // 5. Recruitment gap check counts: count available applicants by sub_category/role name
        $rolePoolCounts = [];
        $uniqueRoles = $slots->map(fn($s) => $s->role_name ?? $s->staffQuotation->sub_category)->unique()->filter()->values();
        foreach ($uniqueRoles as $r) {
            $rolePoolCounts[strtolower($r)] = $applicantPool->filter(function($c) use ($r) {
                return strtolower($c['role']) === strtolower($r) && $c['status'] === 'Available';
            })->count();
        }

        // 6. Statistics Calculations
        // NOTE: must not add whereNull()->count() + where('applicant_id', 0)->count() — Collection::where()
        // uses loose '==' comparison, and PHP treats null == 0 as true, so every null row was counted twice.
        $totalOpenVacancies = $slots->filter(fn($s) => empty($s->applicant_id))->count();
        
        $fullyStaffedEventsCount = 0;
        $eventsRequiringAttentionCount = 0;
        foreach ($eventBookings as $booking) {
            $required = 0;
            $assigned = 0;
            foreach ($booking->shifts as $s) {
                $required += $s->quantity;
                $assigned += $s->shiftSlots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->count();
            }
            if ($required > 0 && $assigned >= $required) {
                $fullyStaffedEventsCount++;
            } else {
                $eventsRequiringAttentionCount++;
            }
        }

        $assignedToday = \App\Models\ShiftSlot::whereDate('updated_at', $todayStr)
            ->whereNotNull('applicant_id')
            ->where('applicant_id', '!=', 0)
            ->count();

        $stats = [
            'total_open_vacancies' => $totalOpenVacancies,
            'fully_staffed_events' => $fullyStaffedEventsCount,
            'events_requiring_attention' => $eventsRequiringAttentionCount,
            'assigned_today' => $assignedToday,
        ];

        // 7. Extract the assigned assignments for the Kanban Board
        // ->values() re-indexes from 0 — without it, a filtered Collection with gapped keys
        // json_encodes as a JS object (not an array), so allAssignments.length reads as undefined.
        $assignments = $slots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->values();

        return view('admin.assignments', compact(
            'assignments',
            'slots',
            'eventBookings',
            'conflicts',
            'applicantPool',
            'rolePoolCounts',
            'stats'
        ));
    }

    public function updateAssignmentStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'nullable|string|in:Assigned,Confirmed,Checked In,Checked Out,Completed,No Show,Unassigned',
                'applicant_id' => 'nullable|exists:applicants,id'
            ]);

            $slot = \App\Models\ShiftSlot::find($id);
            if (!$slot) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected shift slot was not found. Please refresh and try again.'
                ], 404);
            }

            $updates = [];
            if ($request->has('status')) {
                $updates['status'] = $request->status;
                if ($request->status === 'Checked In') {
                    $updates['start_time'] = now()->format('H:i');
                } elseif (in_array($request->status, ['Checked Out', 'Completed'])) {
                    $updates['end_time'] = now()->format('H:i');
                }
            }

            if ($request->has('applicant_id')) {
                if ($request->applicant_id) {
                    $alreadyAssigned = \App\Models\ShiftSlot::where('staff_quotation_id', $slot->staff_quotation_id)
                        ->where('applicant_id', $request->applicant_id)
                        ->where('id', '!=', $slot->id)
                        ->exists();
                    if ($alreadyAssigned) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This staff member is already assigned to this shift.'
                        ], 422);
                    }
                }
                $updates['applicant_id'] = $request->applicant_id;
                // Transition to Assigned status if currently unassigned
                if (!$slot->status || $slot->status === 'Unassigned') {
                    $updates['status'] = 'Assigned';
                }
            } elseif ($request->status === 'Unassigned') {
                $updates['applicant_id'] = null;
            }

            $slot->update($updates);

            return response()->json([
                'success' => true,
                'message' => 'Assignment updated successfully.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMsg = implode(' ', array_map(fn($errs) => implode(' ', $errs), $e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation Error: ' . $errorMsg
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to update assignment at this time: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ratings()
    {
        $ratings = \App\Models\Rating::with(['partner', 'eventBooking'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalReviews = \App\Models\Rating::count();
        $averageRating = \App\Models\Rating::avg('rating') ?: 0;
        
        $starCounts = [
            5 => \App\Models\Rating::where('rating', 5)->count(),
            4 => \App\Models\Rating::where('rating', 4)->count(),
            3 => \App\Models\Rating::where('rating', 3)->count(),
            2 => \App\Models\Rating::where('rating', 2)->count(),
            1 => \App\Models\Rating::where('rating', 1)->count(),
        ];

        $topStaff = \DB::table('applicants')
            ->join('shift_slots', 'shift_slots.applicant_id', '=', 'applicants.id')
            ->join('staff_quotations', 'staff_quotations.id', '=', 'shift_slots.staff_quotation_id')
            ->join('ratings', 'ratings.event_booking_id', '=', 'staff_quotations.event_booking_id')
            ->whereNotNull('ratings.staff_performance_rating')
            ->select(
                'applicants.id',
                'applicants.name',
                'applicants.role',
                'applicants.image',
                'applicants.image_position',
                \DB::raw('AVG(ratings.staff_performance_rating) as avg_rating'),
                \DB::raw('COUNT(DISTINCT ratings.event_booking_id) as events_count')
            )
            ->groupBy('applicants.id', 'applicants.name', 'applicants.role', 'applicants.image', 'applicants.image_position')
            ->orderByDesc('avg_rating')
            ->orderByDesc('events_count')
            ->limit(5)
            ->get();

        return view('admin.ratings', compact('ratings', 'totalReviews', 'averageRating', 'starCounts', 'topStaff'));
    }

    public function shifts()
    {
        // Fetch all shifts
        $shifts = \App\Models\Shift::all(); 
        // Fetch all active team members for the assignment dropdown
        $teamMembers = \App\Models\Team::all();
        
        return view('admin.shifts', compact('shifts', 'teamMembers'));
    }



    public function jobCategory()
    {
        $categories = \App\Models\JobCategory::withCount('jobs')->get();
        
        // Load sub-categories with counts for the combined page
        $subCategories = \App\Models\SubCategory::with('parent')->get()->map(function($sub) {
            $sub->rate_cards_count = \App\Models\RateCard::where('sub_category', $sub->name)->count();
            $sub->linked_rate_cards = \App\Models\RateCard::where('sub_category', $sub->name)->pluck('sub_category')->toArray();
            return $sub;
        });

        $parentCategories = \App\Models\JobCategory::where('status', 'Active')->get();
        
        return view('admin.job-category', compact('categories', 'subCategories', 'parentCategories'));
    }

    public function subCategory()
    {
        // Redirect to the combined categories page
        return redirect()->route('admin.job-category');
    }

    public function rateCard(Request $request)
    {
        $query = \App\Models\RateCard::with('venue');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('sub_category', 'like', "%{$search}%")
                  ->orWhereHas('venue', function($vq) use ($search) {
                      $vq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rates = $query->paginate(15)->withQueryString();
        $categories = \App\Models\JobCategory::where('status', 'Active')->get();
        $subCategories = \App\Models\SubCategory::where('status', 'Active')->get();
        $venues = \App\Models\Venue::all();
        $staffMembers = \App\Models\Team::all();
        $partners = \App\Models\User::where('role', 'partner')->orderBy('name')->get(['id', 'name', 'company_name']);

        // Detect duplicate active rate cards (same company + role + venue)
        $duplicateWarnings = \App\Models\RateCard::where('status', 'Active')
            ->selectRaw('company_name, sub_category, venue_id, COUNT(*) as cnt')
            ->groupBy('company_name', 'sub_category', 'venue_id')
            ->having('cnt', '>', 1)
            ->get();

        return view('admin.rate-card', compact('rates', 'categories', 'subCategories', 'venues', 'staffMembers', 'partners', 'duplicateWarnings'));
    }

    public function timeShifting(Request $request)
    {
        // 1. Query only shift slots that have assigned staff
        $query = \App\Models\ShiftSlot::with([
            'applicant.user',
            'staffQuotation.eventBooking.user'
        ])->whereNotNull('applicant_id');

        // Apply deep linking filters
        if ($request->filled('event')) {
            $query->whereHas('staffQuotation.eventBooking', function($q) use ($request) {
                $q->where('event_name', 'like', '%' . $request->event . '%');
            });
        }
        if ($request->filled('booking')) {
            $query->whereHas('staffQuotation.eventBooking', function($q) use ($request) {
                $q->where('booking_ref', $request->booking);
            });
        }
        if ($request->filled('staff')) {
            $query->whereHas('applicant', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->staff . '%');
            });
        }

        $slots = $query->paginate(15)->withQueryString();

        // 2. Compute global metrics across all slots in the system using query counts
        $pendingCount = \App\Models\ShiftSlot::whereNotNull('applicant_id')
            ->where(function($q) {
                $q->whereNull('status')
                  ->orWhereIn('status', ['Draft', 'Unassigned', 'Assigned', 'Pending', 'Checked In']);
            })->count();

        $submittedCount = \App\Models\ShiftSlot::whereNotNull('applicant_id')
            ->whereIn('status', ['Submitted', 'Reviewed', 'Checked Out', 'Completed'])->count();

        $approvedCount = \App\Models\ShiftSlot::whereNotNull('applicant_id')
            ->where('status', 'Approved')->count();
        
        $approvedSlotsForSum = \App\Models\ShiftSlot::whereNotNull('applicant_id')
            ->where('status', 'Approved')
            ->get(['start_time', 'end_time', 'rate']);

        $readyForInvoiceAmount = 0.0;
        foreach ($approvedSlotsForSum as $s) {
            $readyForInvoiceAmount += $s->getSubtotal();
        }

        $stats = [
            'pending' => $pendingCount,
            'submitted' => $submittedCount,
            'approved' => $approvedCount,
            'ready_for_invoice' => $readyForInvoiceAmount,
        ];

        $categories = \App\Models\JobCategory::with('subCategories')->get();
        $venues = \App\Models\Venue::all();
        
        return view('admin.time-shifting', compact('slots', 'stats', 'categories', 'venues'));
    }

    public function syncTimesheets(Request $request)
    {
        $request->validate([
            'sheets' => 'required|array',
            'sheets.*.rows' => 'nullable|array',
            'sheets.*.rows.*.db_id' => 'required|integer|exists:shift_slots,id',
            'sheets.*.rows.*.break_mins' => 'nullable|integer|min:0',
            'sheets.*.rows.*.rate' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->sheets as $sheet) {
            if (!isset($sheet['rows'])) continue;
            foreach ($sheet['rows'] as $row) {
                $slot = \App\Models\ShiftSlot::find($row['db_id']);
                if ($slot) {
                    $oldStatus = $slot->status;
                    $newStatus = $row['status'] ?? $oldStatus;
                    
                    // Block changes to hours, breaks, and rate on Approved shifts
                    if (in_array($oldStatus, ['Approved', 'Billed']) && $newStatus === $oldStatus) {
                        $row['start_time'] = $slot->start_time;
                        $row['end_time'] = $slot->end_time;
                        $row['break_mins'] = $slot->break_mins;
                        $row['rate'] = $slot->rate;
                    }

                    // Track manual adjustments
                    $wasAdjusted = false;
                    $adjustmentDetails = [];

                    // Normalise values for clean comparisons
                    $newStartTime = isset($row['start_time']) ? substr($row['start_time'], 0, 5) : null;
                    $newEndTime = isset($row['end_time']) ? substr($row['end_time'], 0, 5) : null;
                    $newBreakMins = isset($row['break_mins']) ? (int)$row['break_mins'] : null;
                    $newRate = isset($row['rate']) ? (float)$row['rate'] : null;

                    $oldStartTime = $slot->start_time ? substr($slot->start_time, 0, 5) : null;
                    $oldEndTime = $slot->end_time ? substr($slot->end_time, 0, 5) : null;
                    $oldBreakMins = $slot->break_mins !== null ? (int)$slot->break_mins : null;
                    $oldRate = $slot->rate !== null ? (float)$slot->rate : null;

                    if ($newStartTime !== null && $newStartTime !== $oldStartTime) {
                        $wasAdjusted = true;
                        $adjustmentDetails['start_time'] = ['old' => $oldStartTime, 'new' => $newStartTime];
                    }
                    if ($newEndTime !== null && $newEndTime !== $oldEndTime) {
                        $wasAdjusted = true;
                        $adjustmentDetails['end_time'] = ['old' => $oldEndTime, 'new' => $newEndTime];
                    }
                    if ($newBreakMins !== null && $newBreakMins !== $oldBreakMins) {
                        $wasAdjusted = true;
                        $adjustmentDetails['break_mins'] = ['old' => $oldBreakMins, 'new' => $newBreakMins];
                    }
                    if ($newRate !== null && $newRate !== $oldRate) {
                        $wasAdjusted = true;
                        $adjustmentDetails['rate'] = ['old' => $oldRate, 'new' => $newRate];
                    }

                    // Perform update
                    $slot->update([
                        'start_time' => $row['start_time'] ?? $slot->start_time,
                        'end_time' => $row['end_time'] ?? $slot->end_time,
                        'break_mins' => $row['break_mins'] ?? $slot->break_mins,
                        'rate' => $row['rate'] ?? $slot->rate,
                        'status' => $row['status'] ?? $slot->status,
                        'shift_date' => isset($row['date']) ? \Carbon\Carbon::parse($row['date'])->format('Y-m-d') : $slot->shift_date,
                    ]);

                    // Log activity if manually adjusted
                    if ($wasAdjusted) {
                        \App\Models\ActivityLog::create([
                            'user_id' => auth()->id(),
                            'action' => 'Timesheet Adjusted',
                            'description' => 'ShiftSlot ID ' . $slot->id . ' adjusted by Admin.',
                            'details' => [
                                'message' => 'Admin manually adjusted timesheet hours/rate.',
                                'adjustments' => $adjustmentDetails
                            ]
                        ]);
                    }
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Timesheets synced successfully']);
    }

    public function users(Request $request)
    {
        $query = \App\Models\User::with('applicant')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $users = $query->paginate(15)->withQueryString();

        $users->transform(function($user) {
            $user->discipline = $user->applicant->role ?? null;
            $user->phone = $user->applicant->phone ?? $user->phone ?? 'N/A';
            return $user;
        });

        $stats = [
            'total' => \App\Models\User::count(),
            'admins' => \App\Models\User::where('role', 'admin')->count(),
            'staff' => \App\Models\User::whereIn('role', ['staff', 'team'])->count(),
            'partners' => \App\Models\User::where('role', 'partner')->count(),
            'applicants' => \App\Models\User::where('role', 'applicant')->count(),
        ];

        $categories = \App\Models\JobCategory::all();

        return view('admin.users', compact('users', 'stats', 'categories'));
    }

    // CRUD Methods: Job Category
    public function storeJobCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Active,Archived,Inactive',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sub_categories' => 'nullable|array',
            'sub_categories.*' => 'nullable|string|max:255',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('uploads/icons', 'public');
        }

        // Map 'Inactive' (UI) to 'Archived' (DB Legacy)
        $status = $request->status === 'Inactive' ? 'Archived' : $request->status;

        $category = \App\Models\JobCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $status,
            'icon' => $iconPath,
        ]);

        // Process dynamically added sub-categories
        if ($request->has('sub_categories')) {
            foreach($request->sub_categories as $subName) {
                if (!empty(trim($subName))) {
                    \App\Models\SubCategory::create([
                        'name' => trim($subName),
                        'parent_category' => $category->name,
                        'status' => 'Active',
                    ]);
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'category' => collect($category)->except('icon')->toArray() // Avoid returning local path if not full URL
            ]);
        }

        return redirect()->back()->with('success', 'Job Category created successfully.');
    }

    public function updateJobCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Active,Archived,Inactive',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sub_categories' => 'nullable|array',
            'sub_categories.*' => 'nullable|string|max:255',
        ]);

        $category = \App\Models\JobCategory::findOrFail($id);
        
        // Map 'Inactive' (UI) to 'Archived' (DB Legacy)
        $status = $request->status === 'Inactive' ? 'Archived' : $request->status;

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'status' => $status,
        ];

        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($category->icon && \Illuminate\Support\Facades\Storage::disk('public')->exists($category->icon)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->icon);
            }
            $data['icon'] = $request->file('icon')->store('uploads/icons', 'public');
        }

        $category->update($data);

        // Process newly added dynamic sub-categories
        if ($request->has('sub_categories')) {
            foreach($request->sub_categories as $subName) {
                if (!empty(trim($subName))) {
                    // Check if it already exists to prevent duplicates
                    $exists = \App\Models\SubCategory::where('name', trim($subName))
                        ->where('parent_category', $category->name)->exists();
                        
                    if (!$exists) {
                        \App\Models\SubCategory::create([
                            'name' => trim($subName),
                            'parent_category' => $category->name,
                            'status' => 'Active',
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Job Category updated successfully.');
    }

    public function destroyJobCategory($id)
    {
        $category = \App\Models\JobCategory::findOrFail($id);
        
        // User requested to allow deletion despite linked items, 
        // relying on frontend warnings instead of server-side blocks.
        
        // Note: Linked sub-categories and rate cards will be orphaned.
        // If cascading deletion is preferred, we would delete them here.
        
        $category->delete();

        return redirect()->back()->with('success', 'Job Category deleted successfully.');
    }

    // CRUD Methods: Sub Category
    public function storeSubCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_category' => 'required|string|exists:job_categories,name',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:Active,Inactive'
        ]);

        \App\Models\SubCategory::create([
            'name' => $request->name,
            'parent_category' => $request->parent_category,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Sub-Category created successfully.');
    }

    public function updateSubCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_category' => 'required|string|exists:job_categories,name',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:Active,Inactive'
        ]);

        $sub = \App\Models\SubCategory::findOrFail($id);
        
        $sub->update([
            'name' => $request->name,
            'parent_category' => $request->parent_category,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Sub-Category updated successfully.');
    }

    public function destroySubCategory($id)
    {
        $sub = \App\Models\SubCategory::findOrFail($id);
        
        // User requested to allow deletion despite linked items.
        // Linked rate cards will become orphaned.
        
        $sub->delete();
        return redirect()->back()->with('success', 'Sub-Category deleted successfully.');
    }

    // CRUD Methods: Job Post
    public function storeJobPost(Request $request)
    {
        $data = $request->validate([
             'dept' => 'required|string',
             'location' => 'required|string',
             'type' => 'nullable|string',
             'sub_category' => 'required|string',
             'salary_type' => 'required|string',
             'salary_amount' => 'nullable|numeric',
             'description' => 'nullable|string',
             'company_name' => 'nullable|string',
             'company_email' => 'nullable|email',
             'deadline' => 'nullable|date',
             'staff_quotation_id' => 'nullable|exists:staff_quotations,id',
        ]);

        $salary = $request->salary_type === 'Negotiable'
            ? 'Negotiable'
            : '£' . $request->salary_amount . ' / ' . $request->salary_type;

        // Auto-create category if it doesn't exist
        if (!empty($request->dept) && !\App\Models\JobCategory::where('name', $request->dept)->exists()) {
            \App\Models\JobCategory::create([
                'name' => $request->dept,
                'status' => 'Active',
            ]);
        }

        // Auto-create sub-category if it doesn't exist
        if (!empty($request->sub_category) && !empty($request->dept)) {
            if (!\App\Models\SubCategory::where('name', $request->sub_category)->where('parent_category', $request->dept)->exists()) {
                \App\Models\SubCategory::create([
                    'name' => $request->sub_category,
                    'parent_category' => $request->dept,
                    'status' => 'Active',
                ]);
            }
        }

        $job = \App\Models\JobPost::create([
             'dept' => $request->dept,
            'location' => $request->location,
            'sub_category' => $request->sub_category,
            'company_name' => $request->company_name,
            'company_email' => $request->company_email,
            'type' => $request->type ?? 'Contract',
            'deadline' => $request->deadline,
            'description' => $request->description,
            'salary' => $salary,
            'status' => 'Active',
            'staff_quotation_id' => $request->staff_quotation_id,
        ]);

         \App\Models\ActivityLog::create([
             'user_id' => auth()->id(),
             'action' => 'Created Job Post',
             'description' => "Posted job: {$job->sub_category}"
         ]);

        return redirect()->back()->with('success', 'Job Post created successfully.');
    }

    public function updateJobPost(Request $request, $id)
    {
         $data = $request->validate([
             'dept' => 'required|string',
             'location' => 'required|string',
             // 'salary' => 'required|string', // Removed
             'company_name' => 'nullable|string',
             'company_email' => 'nullable|string',
             'company_website' => 'nullable|string',
             'deadline' => 'nullable|date',
             'sub_category' => 'required|string',
             'salary_type' => 'required|string',
             'salary_amount' => 'nullable|numeric',
        ]);

        $salary = $request->salary_type === 'Negotiable' 
            ? 'Negotiable' 
            : '£' . $request->salary_amount . ' / ' . $request->salary_type;

        $job = \App\Models\JobPost::findOrFail($id);
        
        // Remove helper fields from data before saving
        $cleanData = \Illuminate\Support\Arr::except($data, ['salary_type', 'salary_amount']);
        
        // Auto-create category if it doesn't exist
        if (!empty($request->dept) && !\App\Models\JobCategory::where('name', $request->dept)->exists()) {
            \App\Models\JobCategory::create([
                'name' => $request->dept,
                'status' => 'Active',
            ]);
        }

        // Auto-create sub-category if it doesn't exist
        if (!empty($request->sub_category) && !empty($request->dept)) {
            if (!\App\Models\SubCategory::where('name', $request->sub_category)->where('parent_category', $request->dept)->exists()) {
                \App\Models\SubCategory::create([
                    'name' => $request->sub_category,
                    'parent_category' => $request->dept,
                    'status' => 'Active',
                ]);
            }
        }

        $job->update(array_merge($cleanData, [
            'type' => $request->type ?? $job->type,
            'description' => $request->description ?? $job->description,
            'deadline' => $request->deadline,
            'sub_category' => $request->sub_category,
            'salary' => $salary,
        ]));

        return redirect()->back()->with('success', 'Job Post updated successfully.');
    }


    // CRUD Methods: Delete Actions for other Resources
    public function destroyJobPost($id)
    {
        \App\Models\JobPost::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Job Post deleted successfully.');
    }

    public function destroyApplicant($id)
    {
        $applicant = \App\Models\Applicant::findOrFail($id);

        // Find and delete the corresponding user with applicant role
        if ($applicant->email) {
            \App\Models\User::where('email', $applicant->email)
                ->where('role', 'applicant')
                ->delete();
        }

        $applicant->delete();
        return redirect()->back()->with('success', 'Applicant and associated user account deleted successfully.');
    }

    public function updateApplicantStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'cv' => 'nullable|file|mimes:pdf|max:10240'
        ]);

        $applicant = \App\Models\Applicant::findOrFail($id);

        // Prevent backward transition
        $order = ['Pending', 'Hired', 'Rejected'];
        $currentIdx = array_search($applicant->status, $order);
        $newIdx = array_search($request->status, $order);
        if ($currentIdx !== false && $newIdx !== false && $newIdx < $currentIdx) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot revert to a previous status.'], 422);
            }
            return redirect()->back()->with('error', 'Cannot revert to a previous status.');
        }

        // Handle CV upload/update
        if ($request->hasFile('cv')) {
            // Delete old file if exists
            if ($applicant->cv_link) {
                $oldPath = str_replace(['storage/app/private/', 'storage/app/'], '', ltrim($applicant->cv_link, '/'));
                \Illuminate\Support\Facades\Storage::disk('local')->delete($oldPath);
            }

            // Store new file
            $path = $request->file('cv')->store('cvs', 'local');
            $applicant->update(['cv_link' => '/' . $path]);
        }

        $applicant->update(['status' => $request->status]);

        // Compute cv_exists
        $cvExists = false;
        if ($applicant->cv_link) {
            $path = str_replace(['storage/app/private/', 'storage/app/'], '', ltrim($applicant->cv_link, '/'));
            $cvExists = \Illuminate\Support\Facades\Storage::disk('local')->exists($path);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Applicant updated successfully.',
                'cv_link' => $applicant->cv_link,
                'cv_exists' => $cvExists
            ]);
        }

        return redirect()->back()->with('success', 'Applicant updated successfully.');
    }



    public function updateUserStatus(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $user = \App\Models\User::findOrFail($id);
        
        // Prevent deactivating own account
        if ($user->id === auth()->id()) {
             return response()->json(['error' => 'You cannot deactivate your own account.'], 403);
        }

        $user->is_active = $request->is_active;
        $user->save();

        return response()->json(['success' => true, 'message' => 'User status updated successfully.']);
    }

    public function updateJobPostStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Active,Expired,Closed,Archived'
        ]);

        $job = \App\Models\JobPost::findOrFail($id);
        
        $status = $request->status;
        if ($status === 'Closed') {
            $status = 'Expired';
        }

        $job->status = $status;
        
        // If reactivating a job with a past deadline, clear the deadline to prevent immediate re-expiration
        if ($status === 'Active' && $job->deadline && now()->greaterThan($job->deadline)) {
            $job->deadline = null;
        }

        $job->save();

        return response()->json([
            'success' => true, 
            'message' => 'Job status updated successfully.',
            'calculated_status' => $job->calculated_status
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'nullable|in:admin,partner,applicant',
        ]);

        $user = \App\Models\User::findOrFail($id);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('role')) {
            $data['role'] = $request->role;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroyUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
             return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Find and delete the corresponding applicant/applicant profile
        if ($user->email) {
            \App\Models\Applicant::where('email', $user->email)->delete();
        }

        $user->delete();
        
        return redirect()->back()->with('success', 'User and associated applicant profile deleted successfully.');
    }

    public function destroyPartner($id)
    {
        $partner = \App\Models\User::findOrFail($id);
        
        if ($partner->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $partner->delete();

        return redirect()->back()->with('success', 'Partner account deleted successfully.');
    }

    public function destroyRating($id)
    {
        $rating = \App\Models\Rating::findOrFail($id);
        $rating->delete();

        return redirect()->back()->with('success', 'Rating and review deleted successfully.');
    }

    // --- EXPORT METHODS ---

    public function updateJobCategoryStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Active,Inactive,Review'
        ]);

        $category = \App\Models\JobCategory::findOrFail($id);
        $category->status = $request->status;
        $category->save();

        return response()->json(['success' => true, 'message' => 'Category status updated successfully.']);
    }

    public function updateSubCategoryStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Active,Inactive'
        ]);

        $subCategory = \App\Models\SubCategory::findOrFail($id);
        $subCategory->status = $request->status;
        $subCategory->save();

        return response()->json(['success' => true, 'message' => 'Sub-Category status updated successfully.']);
    }

    public function updateRateCardStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Active,Inactive'
        ]);

        $rateCard = \App\Models\RateCard::findOrFail($id);

        if ($request->status === 'Active') {
            $duplicateExists = \App\Models\RateCard::where('status', 'Active')
                ->where('id', '!=', $rateCard->id)
                ->where('company_name', $rateCard->company_name)
                ->where('sub_category', $rateCard->sub_category)
                ->where('venue_id', $rateCard->venue_id)
                ->exists();

            if ($duplicateExists) {
                return response()->json(['success' => false, 'message' => 'An active rate card already exists for this company, role and venue.'], 422);
            }
        }

        $rateCard->status = $request->status;
        $rateCard->save();

        return response()->json(['success' => true, 'message' => 'Rate Card status updated successfully.']);
    }

    private function applyExportFilters($query, Request $request)
    {
        $type = $request->query('type', 'all');
        $limit = $request->query('limit');
        $ids = $request->query('ids');

        if ($type === 'selected' && !empty($ids)) {
             $idArray = is_array($ids) ? $ids : explode(',', $ids);
             return $query->whereIn('id', $idArray);
        }

        if ($type === 'last' && $limit) {
            return $query->orderBy('created_at', 'desc')->take($limit);
        }

        if ($type === 'first' && $limit) {
            return $query->orderBy('created_at', 'asc')->take($limit);
        }

        if ($type === 'date_range') {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            
            if ($startDate && $endDate) {
                 return $query->whereDate('created_at', '>=', $startDate)
                              ->whereDate('created_at', '<=', $endDate);
            }
        }

        return $query;
    }

    public function exportUsers(Request $request)
    {
        // Increase execution time and memory limit for large exports
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $query = \App\Models\User::query();
        $users = $this->applyExportFilters($query, $request)->get();

        if ($request->query('type') === 'last') { 
             // Restore correct chronological order for 'last' if desired, or keep desc? 
             // Usually lists are preferred newest first, so desc is fine.
        }

        if ($request->query('format') === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.exports.users-pdf', compact('users'));
            return $pdf->download('users_export_' . date('Y-m-d') . '.pdf');
        }

        // Default CSV
        $csvFileName = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['User ID', 'Name', 'Email', 'Role', 'Company Name', 'Joined At']);
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role === 'applicant' ? 'Applicant' : ucfirst($user->role),
                    $user->company_name ?? '-',
                    $user->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportRateCards(Request $request)
    {
        $query = \App\Models\RateCard::query();
        $rates = $this->applyExportFilters($query, $request)->get();
        $venues = \App\Models\Venue::all();

        if ($request->query('format') === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.exports.rate-cards-pdf', compact('rates', 'venues'));
            $pdf->setPaper('a4', 'portrait');
            return $pdf->download('Kingdom_Rate_Card_' . date('Y-m-d') . '.pdf');
        }

        $csvFileName = 'rate_cards_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
             "Content-type"        => "text/csv",
             "Content-Disposition" => "attachment; filename=$csvFileName",
             "Pragma"              => "no-cache",
             "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
             "Expires"             => "0"
        ];

        $callback = function() use($rates, $venues) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Rate ID', 'Category', 'Role', 'Venue', 'Rate Nett', 'Rate Gross']);
            foreach ($rates as $rate) {
                $venueName = $venues->firstWhere('id', $rate->venue_id)->name ?? 'N/A';
                fputcsv($file, [
                    $rate->id,
                    $rate->type,
                    $rate->sub_category,
                    $venueName,
                    $rate->hourly_rate,
                    $rate->overtime_rate
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportApplicants(Request $request)
    {
        $query = \App\Models\Applicant::query();
        $applicants = $this->applyExportFilters($query, $request)->get();

        if ($request->query('format') === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.exports.applicants-pdf', compact('applicants'));
            return $pdf->download('applicants_export_' . date('Y-m-d') . '.pdf');
        }

        $csvFileName = 'applicants_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($applicants) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Applicant ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Location', 'Applied Date']);
            foreach ($applicants as $app) {
                fputcsv($file, [
                    $app->id,
                    $app->name,
                    $app->email,
                    $app->phone,
                    $app->role,
                    $app->status,
                    $app->location,
                    $app->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportTimeSheets(Request $request)
    {
        $query = \App\Models\StaffQuotation::query();
        
        // Default sort is created_at desc for Time Sheets usually, ensuring filter applies correctly
        // applyExportFilters handles sorting for Last/First, but for 'all' we might want default sort
        if (!$request->has('type') || $request->query('type') === 'all') {
             $query->orderBy('created_at', 'desc');
        }
        
        $timeSheets = $this->applyExportFilters($query, $request)->get();

        if ($request->query('format') === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.exports.time-sheets-pdf', compact('timeSheets'));
            return $pdf->download('time_sheets_export_' . date('Y-m-d') . '.pdf');
        }

        $csvFileName = 'time_sheets_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
             "Content-type"        => "text/csv",
             "Content-Disposition" => "attachment; filename=$csvFileName",
             "Pragma"              => "no-cache",
             "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
             "Expires"             => "0"
        ];

        $callback = function() use($timeSheets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Ref', 'Client', 'Category', 'Sub Category', 'Quantity', 'Hours', 'End Date', 'Total Days', 'Amount', 'Status']);
            foreach ($timeSheets as $item) {
                fputcsv($file, [
                    $item->quotation_ref,
                    $item->client,
                    $item->category,
                    $item->sub_category,
                    $item->quantity,
                    $item->shift_hours,
                    $item->end_date,
                    $item->total_days,
                    $item->amount,
                    $item->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Rate Card CRUD
    public function storeRateCard(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'sub_category' => 'required|string',
            'company_name' => 'required|string|max:255',
            'venue_id' => 'required|exists:venues,id',
            'hourly' => 'required|numeric',
            'overtime' => 'required|numeric',
        ]);

        $duplicateExists = \App\Models\RateCard::where('status', 'Active')
            ->where('company_name', $request->company_name)
            ->where('sub_category', $request->sub_category)
            ->where('venue_id', $request->venue_id)
            ->exists();

        if ($duplicateExists) {
            return redirect()->back()->with('error', 'An active rate card already exists for this company, role and venue. Please edit the existing entry instead.');
        }

        \App\Models\RateCard::create([
            'type' => $request->type, // This is "Category"
            'sub_category' => $request->sub_category,
            'company_name' => $request->company_name,
            'venue_id' => $request->venue_id,
            'hourly_rate' => $request->hourly, // Rate Nett
            'overtime_rate' => $request->overtime, // Rate Gross
            'hours' => $request->hours,
            'total_amount' => $request->total_amount,
            // Assuming 'tier' is standard or nullable?
        ]);

        return redirect()->back()->with('success', 'Rate Card created successfully.');
    }

    public function updateRateCard(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string',
            'sub_category' => 'required|string',
            'company_name' => 'required|string|max:255',
            'venue_id' => 'required|exists:venues,id',
            'hourly' => 'required|numeric',
            'overtime' => 'required|numeric',
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        $rate = \App\Models\RateCard::findOrFail($id);
        $newStatus = $request->status ?? $rate->status;

        if ($newStatus === 'Active') {
            $duplicateExists = \App\Models\RateCard::where('status', 'Active')
                ->where('id', '!=', $rate->id)
                ->where('company_name', $request->company_name)
                ->where('sub_category', $request->sub_category)
                ->where('venue_id', $request->venue_id)
                ->exists();

            if ($duplicateExists) {
                return redirect()->back()->with('error', 'An active rate card already exists for this company, role and venue. Please edit the existing entry instead.');
            }
        }

        $rate->update([
            'type' => $request->type,
            'sub_category' => $request->sub_category,
            'company_name' => $request->company_name,
            'venue_id' => $request->venue_id,
            'hourly_rate' => $request->hourly,
            'overtime_rate' => $request->overtime,
            'hours' => $request->hours,
            'total_amount' => $request->total_amount,
            'status' => $newStatus,
        ]);

        return redirect()->back()->with('success', 'Rate Card updated successfully.');
    }

    public function destroyRateCard($id)
    {
        $rate = \App\Models\RateCard::findOrFail($id);
        $rate->delete();
        return redirect()->back()->with('success', 'Rate Card deleted successfully.');
    }

    // Venue CRUD
    public function storeVenue(Request $request)
    {
         $request->validate([
             'name' => 'required', 
             'email' => 'nullable|email',
             'contact_info' => 'nullable|string',
             'address' => 'required|string',
             'city' => 'nullable|string',
             'postcode' => 'nullable|string',
             'country' => 'nullable|string',
             'description' => 'nullable|string',
         ]);
         
         $data = $request->all();

         \App\Models\Venue::create($data);
         return redirect()->back()->with('success', 'Venue created successfully.');
    }

    public function updateVenue(Request $request, $id)
    {
         $request->validate([
             'name' => 'required',
             'email' => 'nullable|email',
             'contact_info' => 'nullable|string',
             'address' => 'required|string',
             'city' => 'nullable|string',
             'postcode' => 'nullable|string',
             'country' => 'nullable|string',
             'description' => 'nullable|string',
         ]);
         
         $venue = \App\Models\Venue::findOrFail($id);
         $data = $request->all();

         $venue->update($data);
         return redirect()->back()->with('success', 'Venue updated successfully.');
    }

    public function destroyVenue($id)
    {
         $venue = \App\Models\Venue::findOrFail($id);
         $venue->delete();
         return redirect()->back()->with('success', 'Venue deleted successfully.');
    }

    // Team CRUD
    public function storeTeam(Request $request)
    {
         $request->validate([
             'name' => 'required',
             'email' => 'required|email|unique:users,email',
             'password' => 'required|min:6',
             'role' => 'required',
             'dashboard_role' => 'nullable|string',
         ]);

         // Create User/Login Creds
         $user = \App\Models\User::create([
             'name' => $request->name,
             'email' => strtolower($request->email),
             'password' => $request->password,
             'role' => $request->filled('dashboard_role') ? 'admin' : 'staff',
         ]);

         // Assign Spatie dashboard role if selected
         if ($request->filled('dashboard_role')) {
             $user->assignRole($request->dashboard_role);
         }

         // Create Staff Profile
         \App\Models\Team::create([
             'user_id' => $user->id,
             'name' => $request->name,
             'role' => $request->role,
             'email' => $request->email,
             'phone' => $request->phone,
         ]);
         
         \App\Models\ActivityLog::create([
             'user_id' => auth()->id(),
             'action' => 'Added New Team Member',
             'description' => "Added {$request->name}" . ($request->filled('dashboard_role') ? " with dashboard role: {$request->dashboard_role}" : '')
         ]);

         return redirect()->back()->with('success', 'Team member and account created successfully.');
    }
    public function updateTeam(Request $request, $id)
    {
         $request->validate([
             'name' => 'required',
             'email' => 'required|email',
             'dashboard_role' => 'nullable|string',
         ]);
         
         $staff = \App\Models\Team::findOrFail($id);
         
         // Sync User
         if ($staff->user_id) {
                 $user = \App\Models\User::find($staff->user_id);
                 if ($user) {
                     $request->validate([
                         'email' => 'unique:users,email,' . $user->id,
                         'password' => 'nullable|min:6'
                     ]);
                     
                     $userData = [
                         'name' => $request->name,
                         'email' => $request->email,
                     ];
                     
                     if ($request->filled('password')) {
                         $userData['password'] = $request->password;
                     }

                     // Handle dashboard access role
                     if ($request->filled('dashboard_role')) {
                         $userData['role'] = 'admin';
                         $user->update($userData);
                         $user->syncRoles([$request->dashboard_role]);
                     } else {
                         $userData['role'] = 'staff';
                         $user->update($userData);
                         $user->syncRoles([]); // Remove all Spatie roles
                     }
                 }
         }

         $staff->update($request->except(['password', 'dashboard_role'])); // Staff table doesn't have these
         return redirect()->back()->with('success', 'Team member updated successfully.');
    }
    public function destroyTeam($id)
    {
         $staff = \App\Models\Team::findOrFail($id);
         if ($staff->user_id) {
             \App\Models\User::destroy($staff->user_id); // This cascades to delete staff member due to FK constraint
         } else {
             $staff->delete();
         }
         return redirect()->back()->with('success', 'Team member account deleted successfully.');
    }

    // Events CRUD
    public function storeEvent(Request $request)
    {
         $request->validate(['name' => 'required']);
         \App\Models\Event::create($request->all());
         return redirect()->back()->with('success', 'Event created successfully.');
    }
    public function updateEvent(Request $request, $id)
    {
         $request->validate(['name' => 'required']);
         \App\Models\Event::findOrFail($id)->update($request->all());
         return redirect()->back()->with('success', 'Event updated successfully.');
    }
    public function destroyEvent($id)
    {
         \App\Models\Event::findOrFail($id)->delete();
         return redirect()->back()->with('success', 'Event deleted successfully.');
    }

    // Staff Quotation CRUD
    public function storeStaffQuotation(Request $request)
    {
         $data = $request->validate([
             'client' => 'required|string',
             'category' => 'nullable|string',
             'sub_category' => 'nullable|string',
             'quantity' => 'required|integer',
             'shift_hours' => 'nullable|string',
             'start_date' => 'nullable|date',
             'end_date' => 'nullable|date',
             'total_days' => 'nullable|integer',
             'venue' => 'nullable|string',
             'rate' => 'nullable|numeric',
             'amount' => 'required|numeric',
             'status' => 'required|string',
             'shift' => 'nullable|string',
             'special_requirements' => 'nullable|string'
         ]);
         
         $user = \App\Models\User::where('company_name', $request->client)
             ->orWhere('name', $request->client)
             ->first();
         $userId = $user ? $user->id : null;
         
         $bookingRef = 'BK-' . strtoupper(substr(uniqid(), -4));
         
         // Create the parent EventBooking
         $eventBooking = \App\Models\EventBooking::create([
             'user_id' => $userId,
             'booking_ref' => $bookingRef,
             'event_name' => $request->event_name ?? $request->sub_category ?? 'Booking',
             'client' => $request->client,
             'venue' => $request->venue,
             'start_date' => $request->start_date ?? now(),
             'end_date' => $request->end_date ?? $request->start_date ?? now(),
             'special_requirements' => $request->special_requirements,
             'status' => $request->status,
         ]);
         
         $rateVal = $request->rate;
         if ($rateVal !== null && $rateVal !== '') {
             $rateVal = number_format((float)$rateVal, 2, '.', '');
         }
         $amountVal = '£' . number_format((float)$request->amount, 2);
         
         // Create child shift (StaffQuotation)
         $shift = \App\Models\StaffQuotation::create([
             'event_booking_id' => $eventBooking->id,
             'quotation_ref' => $bookingRef,
             'user_id' => $userId,
             'client' => $request->client,
             'event_name' => $request->event_name ?? $request->sub_category ?? 'Booking',
             'category' => $request->category,
             'sub_category' => $request->sub_category,
             'quantity' => $request->quantity,
             'shift_hours' => $request->shift_hours ?? 8,
             'shift' => $request->shift,
             'rate' => $rateVal,
             'amount' => $amountVal,
             'date' => now()->format('M d, Y'),
             'start_date' => $request->start_date,
             'end_date' => $request->end_date,
             'total_days' => $request->total_days,
             'venue' => $request->venue,
             'special_requirements' => $request->special_requirements,
             'status' => $request->status,
             'booked_at' => now(),
         ]);
         
         // Auto-generate ShiftSlots
         for ($i = 0; $i < $request->quantity; $i++) {
             \App\Models\ShiftSlot::create([
                 'staff_quotation_id' => $shift->id,
                 'role_name' => $request->sub_category,
                 'shift_date' => $request->start_date ?? now(),
                 'rate' => $rateVal ?: 0,
                 'status' => 'Unassigned',
             ]);
         }
         
         $eventBooking->recalculateTotal();
         
         // Cascade status and generate events if Approved/Confirmed
         $this->cascadeBookingStatus($eventBooking);
         
         return redirect()->back()->with('success', 'Quotation and Booking created successfully.');
    }

    private function cascadeBookingStatus(\App\Models\EventBooking $booking)
    {
        $status = $booking->status;
        
        // Update all child shifts status
        $booking->shifts()->update(['status' => $status]);
        
        // Load shifts to iterate
        $booking->load('shifts');
        
        // If approved/confirmed, generate Events for each shift
        if (in_array($status, ['Approved', 'Confirmed', 'Active'])) {
            foreach ($booking->shifts as $shift) {
                if (!$shift->events()->exists()) {
                    \App\Models\Event::create([
                        'title' => $shift->event_name ?? $shift->sub_category,
                        'date' => $shift->start_date,
                        'end_date' => $shift->end_date,
                        'time' => $shift->shift ?? 'TBD',
                        'type' => $shift->category ?? 'Event',
                        'location' => $shift->venue ?? 'TBD',
                        'required_sh' => $shift->quantity,
                        'description' => "Client: {$shift->client}\nRole: {$shift->sub_category}\nRef: {$shift->quotation_ref}\nNotes: {$shift->special_requirements}",
                        'staff_quotation_id' => $shift->id
                    ]);
                }
            }
        }
    }
    public function updateStaffQuotation(Request $request, $id)
    {
        $type = $request->input('type', 'orphaned');
        
        if ($type === 'booking') {
            $request->validate([
                'client' => 'required|string',
                'event_name' => 'required|string|max:255',
                'venue' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'special_requirements' => 'nullable|string',
                'status' => 'required|string',
            ]);
            
            $booking = \App\Models\EventBooking::findOrFail($id);

            // Prevent editing if already approved, confirmed, active, or billed
            $lockedStatuses = ['approved', 'confirmed', 'active', 'billed'];
            if (in_array(strtolower($booking->status), $lockedStatuses)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Approved/Active bookings cannot be edited.'], 422);
                }
                return redirect()->back()->with('error', 'Approved/Active bookings cannot be edited.');
            }

            // Prevent backward transition
            $order = ['Draft', 'Pending', 'Sent', 'Approved', 'Rejected', 'Confirmed', 'Active'];
            $currentIdx = array_search($booking->status, $order);
            $newIdx = array_search($request->status, $order);
            if ($currentIdx !== false && $newIdx !== false && $newIdx < $currentIdx) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Cannot revert to a previous status.'], 422);
                }
                return redirect()->back()->with('error', 'Cannot revert to a previous status.');
            }
            
            $user = \App\Models\User::where('company_name', $request->client)
                ->orWhere('name', $request->client)
                ->first();
            $userId = $user ? $user->id : $booking->user_id;

            $booking->update([
                'user_id' => $userId,
                'client' => $request->client,
                'event_name' => $request->event_name,
                'venue' => $request->venue,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'special_requirements' => $request->special_requirements,
                'status' => $request->status,
            ]);
            
            // Sync/update child shifts if submitted
            if ($request->has('items') && is_array($request->items)) {
                $existingShiftIds = $booking->shifts->pluck('id')->toArray();
                $processedIds = [];
                $isRateLocked = in_array($booking->status, ['Approved', 'Confirmed', 'Active']);
                
                foreach ($request->items as $item) {
                    $hours = $item['calculated_hours'] ?? ($item['shift_hours'] ?? 8);
                    $qty = (int)($item['quantity'] ?? 1);
                    
                    $shiftId = $item['id'] ?? null;
                    
                    if ($shiftId && in_array($shiftId, $existingShiftIds)) {
                        $shift = \App\Models\StaffQuotation::findOrFail($shiftId);
                        
                        // Rate lock: preserve existing rate for approved bookings
                        if ($isRateLocked) {
                            $secureRate = $shift->rate;
                        } else {
                            $rateCard = \App\Models\RateCard::resolveRate($item['sub_category'] ?? '', $booking->client);
                            $secureRate = $rateCard ? $rateCard->hourly_rate : ($item['rate'] ?? 0);
                        }
                        $total = $qty * (float)$secureRate * (float)$hours;
                        
                        $shift->update([
                            'user_id' => $userId,
                            'client' => $booking->client,
                            'event_name' => $booking->event_name,
                            'sub_category' => $item['sub_category'],
                            'quantity' => $qty,
                            'shift_hours' => $hours,
                            'calculated_hours' => $item['calculated_hours'] ?? null,
                            'shift' => $item['shift'] ?? null,
                            'shift_label' => $item['shift_label'] ?? null,
                            'rate' => $secureRate,
                            'amount' => '£' . number_format($total, 2),
                            'start_date' => $booking->start_date,
                            'end_date' => $booking->end_date,
                            'venue' => $booking->venue,
                            'status' => $booking->status,
                        ]);
                        
                        // Sync ShiftSlots quantity
                        $existingSlotsCount = \App\Models\ShiftSlot::where('staff_quotation_id', $shift->id)->count();
                        if ($qty > $existingSlotsCount) {
                            for ($i = 0; $i < ($qty - $existingSlotsCount); $i++) {
                                \App\Models\ShiftSlot::create([
                                    'staff_quotation_id' => $shift->id,
                                    'role_name' => $item['sub_category'],
                                    'shift_date' => $booking->start_date,
                                    'rate' => $secureRate,
                                    'status' => 'Unassigned',
                                ]);
                            }
                        } elseif ($qty < $existingSlotsCount) {
                            \App\Models\ShiftSlot::where('staff_quotation_id', $shift->id)
                                ->where('status', 'Unassigned')
                                ->orderBy('id', 'desc')
                                ->take($existingSlotsCount - $qty)
                                ->delete();
                        }
                        
                        // Update remaining slots (rate only if not locked)
                        $slotUpdate = [
                            'role_name' => $item['sub_category'],
                            'shift_date' => $booking->start_date,
                        ];
                        if (!$isRateLocked) {
                            $slotUpdate['rate'] = $secureRate;
                        }
                        \App\Models\ShiftSlot::where('staff_quotation_id', $shift->id)->update($slotUpdate);
                        
                        $processedIds[] = $shift->id;
                    } else {
                        // New shift: always fetch current rate from rate card
                        $rateCard = \App\Models\RateCard::resolveRate($item['sub_category'] ?? '', $booking->client);
                        $secureRate = $rateCard ? $rateCard->hourly_rate : ($item['rate'] ?? 0);
                        $total = $qty * (float)$secureRate * (float)$hours;
                        
                        $newShift = \App\Models\StaffQuotation::create([
                            'event_booking_id' => $booking->id,
                            'quotation_ref' => $booking->booking_ref,
                            'user_id' => $userId,
                            'client' => $booking->client,
                            'event_name' => $booking->event_name,
                            'sub_category' => $item['sub_category'],
                            'quantity' => $qty,
                            'shift_hours' => $hours,
                            'calculated_hours' => $item['calculated_hours'] ?? null,
                            'shift' => $item['shift'] ?? null,
                            'shift_label' => $item['shift_label'] ?? null,
                            'rate' => $secureRate,
                            'amount' => '£' . number_format($total, 2),
                            'date' => now()->format('M d, Y'),
                            'start_date' => $booking->start_date,
                            'end_date' => $booking->end_date,
                            'venue' => $booking->venue,
                            'special_requirements' => $booking->special_requirements,
                            'status' => $booking->status,
                            'booked_at' => now(),
                        ]);
                        
                        for ($i = 0; $i < $qty; $i++) {
                            \App\Models\ShiftSlot::create([
                                'staff_quotation_id' => $newShift->id,
                                'role_name' => $item['sub_category'],
                                'shift_date' => $booking->start_date,
                                'rate' => $secureRate,
                                'status' => 'Unassigned',
                            ]);
                        }
                        
                        $processedIds[] = $newShift->id;
                    }
                }
                
                // Delete shifts that were removed
                $removedIds = array_diff($existingShiftIds, $processedIds);
                if (!empty($removedIds)) {
                    \App\Models\ShiftSlot::whereIn('staff_quotation_id', $removedIds)->delete();
                    \App\Models\StaffQuotation::whereIn('id', $removedIds)->delete();
                }
            }
            
            $booking->recalculateTotal();
            
            // Cascade status & events
            $this->cascadeBookingStatus($booking);
            
            return redirect()->back()->with('success', 'Event Booking updated successfully.');
            
        } else {
            // Edit legacy orphaned StaffQuotation
            $data = $request->validate([
                'client' => 'required|string',
                'category' => 'nullable|string',
                'sub_category' => 'nullable|string',
                'quantity' => 'required|integer',
                'shift_hours' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'total_days' => 'nullable|integer',
                'venue' => 'nullable|string',
                'rate' => 'nullable|numeric',
                'amount' => 'required|numeric',
                'status' => 'required|string',
                'shift' => 'nullable|string',
                'special_requirements' => 'nullable|string',
            ]);
            
            if (isset($data['amount'])) {
                $data['amount'] = '£' . number_format((float)$data['amount'], 2);
            }
            if (isset($data['rate']) && $data['rate'] !== null && $data['rate'] !== '') {
                $data['rate'] = number_format((float)$data['rate'], 2, '.', '');
            }
            
            $quotation = \App\Models\StaffQuotation::findOrFail($id);
            $oldStatus = $quotation->status;
            
            $quotation->update($data);
            
            // Sync ShiftSlots quantity
            $existingSlotsCount = \App\Models\ShiftSlot::where('staff_quotation_id', $quotation->id)->count();
            $qty = (int)$data['quantity'];
            if ($qty > $existingSlotsCount) {
                for ($i = 0; $i < ($qty - $existingSlotsCount); $i++) {
                    \App\Models\ShiftSlot::create([
                        'staff_quotation_id' => $quotation->id,
                        'role_name' => $quotation->sub_category,
                        'shift_date' => $quotation->start_date ?? now(),
                        'rate' => $quotation->rate ?: 0,
                        'status' => 'Unassigned',
                    ]);
                }
            } elseif ($qty < $existingSlotsCount) {
                \App\Models\ShiftSlot::where('staff_quotation_id', $quotation->id)
                    ->where('status', 'Unassigned')
                    ->orderBy('id', 'desc')
                    ->take($existingSlotsCount - $qty)
                    ->delete();
            }
            
            // Auto-create Event when Approved/Confirmed if it hasn't been created yet
            if (in_array($quotation->status, ['Approved', 'Confirmed']) && !in_array($oldStatus, ['Approved', 'Confirmed'])) {
                if (!$quotation->events()->exists()) {
                    \App\Models\Event::create([
                        'title' => $quotation->event_name ?? $quotation->sub_category,
                        'date' => $quotation->start_date,
                        'end_date' => $quotation->end_date,
                        'time' => $quotation->shift ?? 'TBD',
                        'type' => $quotation->category ?? 'Event',
                        'location' => $quotation->venue ?? 'TBD',
                        'required_sh' => $quotation->quantity,
                        'description' => "Client: {$quotation->client}\nRole: {$quotation->sub_category}\nRef: {$quotation->quotation_ref}\nNotes: {$quotation->special_requirements}",
                        'staff_quotation_id' => $quotation->id
                    ]);
                }
            }
            
            return redirect()->back()->with('success', 'Quotation updated successfully.');
        }
    }
    public function destroyStaffQuotation(Request $request, $id)
    {
        $type = $request->input('type', 'orphaned');
        
        if ($type === 'booking') {
            $booking = \App\Models\EventBooking::findOrFail($id);
            $lockedStatuses = ['approved', 'confirmed', 'active', 'billed'];
            if (in_array(strtolower($booking->status), $lockedStatuses)) {
                return redirect()->back()->with('error', 'Approved/Active bookings cannot be deleted.');
            }
            foreach ($booking->shifts as $shift) {
                $shift->shiftSlots()->delete();
                $shift->events()->delete();
                $shift->delete();
            }
            $booking->delete();
            return redirect()->back()->with('success', 'Event Booking and all associated shifts deleted successfully.');
        } else {
            $quotation = \App\Models\StaffQuotation::findOrFail($id);
            $quotation->shiftSlots()->delete();
            $quotation->events()->delete();
            $quotation->delete();
            return redirect()->back()->with('success', 'Quotation deleted successfully.');
        }
    }

    // Shift Management
    public function assignStaffToShift(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'staff_member_id' => 'required|exists:teams,id',
        ]);

        $shift = \App\Models\Shift::findOrFail($request->shift_id);
        $staff = \App\Models\Team::findOrFail($request->staff_member_id);

        $currentStaff = $shift->staff_names ?? [];
        
        // Prevent duplicates
        if (!in_array($staff->name, $currentStaff)) {
            $currentStaff[] = $staff->name;
            $shift->update(['staff_names' => $currentStaff]);
            
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Assigned Shift',
                'description' => "Assigned {$staff->name} to shift {$shift->time}"
            ]);

            return redirect()->back()->with('success', 'Staff assigned to shift successfully.');
        }

        return redirect()->back()->with('info', 'Staff member already assigned to this shift.');
    }

    public function removeStaffFromShift(Request $request, $id)
    {
        $folder = $request->query('name'); // Using query param for name or post body?
        // Better to use POST for removal?
        // But the view uses `onclick="... this.remove()"` which is frontend only?
        // The view needs to be updated to submit a removal request.
        // For now, I will implement the assignment part which is the main "sync" request.
        // The removal will purely be frontend unless I update that too.
        // Let's implement removal via a route too.
        
        $shift = \App\Models\Shift::findOrFail($id);
        $nameToRemove = $request->input('staff_name');

        $currentStaff = $shift->staff_names ?? [];
        if (($key = array_search($nameToRemove, $currentStaff)) !== false) {
            unset($currentStaff[$key]);
            $shift->update(['staff_names' => array_values($currentStaff)]);
            return redirect()->back()->with('success', 'Staff removed from shift.');
        }
        
        return redirect()->back()->with('error', 'Staff member not found in this shift.');
    }
    public function exportApplicantPdf($id)
    {
        $applicant = \App\Models\Applicant::with(['experiences', 'educations'])->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.exports.applicant-pdf', compact('applicant'));
        return $pdf->download('Applicant_' . str_replace(' ', '_', $applicant->name) . '_Profile.pdf');
    }

    public function viewResume($id)
    {
        $applicant = \App\Models\Applicant::findOrFail($id);
        
        if (!$applicant->cv_link) {
            return redirect()->back()->with('error', 'Resume file not found.');
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

        return redirect()->back()->with('error', 'Resume file not found.');
    }

    public function downloadResume($id)
    {
        $applicant = \App\Models\Applicant::findOrFail($id);
        
        if (!$applicant->cv_link) {
            return redirect()->back()->with('error', 'Resume file not found.');
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

        return redirect()->back()->with('error', 'Resume file not found.');
    }

    // ── Chat Support Management ──

    public function chatSupport()
    {
        $user = Auth::user();
        
        // Fetch all conversations
        $allMessages = \App\Models\ChatMessage::orderBy('created_at', 'desc')->get();
        
        // Group by conversation_id
        $conversations = $allMessages->groupBy('conversation_id')->map(function ($msgs) {
            $latest = $msgs->first();
            $unread = $msgs->where('status', 'open')->count();
            
            // Resolve contact name and type
            $contactName = $latest->visitor_name ?: 'Guest';
            $isDirect = false;
            
            if (str_starts_with($latest->conversation_id, 'direct-')) {
                $isDirect = true;
                $contactName = $latest->visitor_name ?: 'Partner/Staff';
            }
            
            return [
                'conversation_id' => $latest->conversation_id,
                'contact_name' => $contactName,
                'last_message' => $latest->message ?: $latest->admin_reply,
                'time' => $latest->created_at->diffForHumans(),
                'status' => $latest->status,
                'unread' => $unread,
                'is_direct' => $isDirect,
                'created_at' => $latest->created_at,
            ];
        })->values();
        
        $activeConversationId = request('conversation', $conversations->first()['conversation_id'] ?? null);
        $activeMessages = $activeConversationId 
            ? \App\Models\ChatMessage::where('conversation_id', $activeConversationId)
                ->orderBy('created_at', 'asc')
                ->get()
            : collect([]);

        // Determine active contact name
        $activeContactName = 'Guest';
        if ($activeMessages->count() > 0) {
            $lastPmsg = $activeMessages->where('user_type', '!=', 'admin')->last();
            if ($lastPmsg) {
                $activeContactName = $lastPmsg->visitor_name ?: 'Guest';
            } else {
                $activeContactName = $activeMessages->last()->visitor_name ?: 'Guest';
            }
        }

        return view('admin.chat-support', compact('conversations', 'activeMessages', 'activeConversationId', 'activeContactName'));
    }

    public function storeAdminMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|string',
            'message' => 'required|string|max:2000',
        ]);

        $conversationId = $request->conversation_id;
        
        // Find the last open message to reply to
        $lastOpen = \App\Models\ChatMessage::where('conversation_id', $conversationId)
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastOpen) {
            $lastOpen->update([
                'admin_reply' => $request->message,
                'status' => 'replied',
                'replied_at' => now(),
            ]);
        } else {
            // Find the most recent message to copy visitor info
            $lastMsg = \App\Models\ChatMessage::where('conversation_id', $conversationId)->orderBy('created_at', 'desc')->first();
            
            // Create a new row representing a new admin message
            \App\Models\ChatMessage::create([
                'conversation_id' => $conversationId,
                'user_id' => $lastMsg ? $lastMsg->user_id : null,
                'user_type' => $lastMsg ? $lastMsg->user_type : 'guest',
                'visitor_name' => $lastMsg ? $lastMsg->visitor_name : 'Guest',
                'visitor_email' => $lastMsg ? $lastMsg->visitor_email : null,
                'message' => ' ', // Use a single space since column might not be nullable and blade trims it
                'admin_reply' => $request->message,
                'status' => 'replied',
                'replied_at' => now(),
            ]);
        }

        return redirect()->route('admin.chat-support', ['conversation' => $conversationId]);
    }

    public function replyChatMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = \App\Models\ChatMessage::findOrFail($id);
        $message->update([
            'admin_reply' => $request->message,
            'status' => 'replied',
            'replied_at' => now(),
        ]);

        return redirect()->route('admin.chat-support', ['conversation' => $message->conversation_id]);
    }

    public function resolveChatMessage($id)
    {
        $message = \App\Models\ChatMessage::findOrFail($id);
        $message->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Message marked as resolved.');
    }
}
