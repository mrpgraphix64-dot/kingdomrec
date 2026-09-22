<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Rating;

class PartnerController extends Controller
{
    /**
     * Show the partner dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // All event bookings for this user
        $allEventBookings = \App\Models\EventBooking::where('user_id', $user->id)
            ->with('shifts.shiftSlots')
            ->orderBy('created_at', 'desc')
            ->get();

        // Active Events (Confirmed/Approved)
        $activePlacements = $allEventBookings->whereIn('status', ['Confirmed', 'Approved', 'Active'])->count();

        // Total staff requested across all shifts
        $totalStaff = 0;
        foreach ($allEventBookings as $eb) {
            $totalStaff += $eb->shifts->sum('quantity');
        }

        // Open Events (Pending)
        $openShifts = $allEventBookings->whereIn('status', ['Pending', 'Draft', 'Sent'])->count();

        // Monthly Billing
        $monthlyTotal = 0;
        foreach ($allEventBookings as $eb) {
            $amt = preg_replace('/[^0-9.]/', '', $eb->total_amount ?? '0');
            $monthlyTotal += (float) $amt;
        }

        $partnerStats = [
            'active_placements' => $totalStaff,
            'open_shifts' => $openShifts,
            'monthly_billing' => number_format($monthlyTotal, 2),
            'total_bookings' => $allEventBookings->count(),
        ];

        // Upcoming Event Staffing — EventBooking has no sub_category/quantity/shift_hours of
        // its own (those live on its child shift rows), so compute and attach the real,
        // per-booking staffing figures the dashboard table displays.
        $partnerJobs = $allEventBookings->take(4)->each(function ($eb) {
            $firstShift = $eb->shifts->first();
            $eb->sub_category = $firstShift->sub_category ?? null;
            $eb->quantity = $eb->shifts->sum('quantity');
            $eb->shift_hours = $firstShift->shift_hours ?? 0;
            $eb->assigned_count = $eb->shifts->sum(function ($shift) {
                return $shift->shiftSlots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->count();
            });
        });

        // Pending Quotations
        $partnerQuotations = $allEventBookings->whereIn('status', ['Pending', 'Draft', 'Sent'])->take(3);

        // Recent Activity
        $recentBookings = $allEventBookings->take(5);
        $activities = [];
        foreach ($recentBookings as $eb) {
            $shiftCount = $eb->shifts->count();
            $timeAgo = $eb->created_at ? $eb->created_at->diffForHumans() : 'Recently';
            $activities[] = [
                'title' => $eb->event_name,
                'desc' => $shiftCount . ' shift(s) · ' . ($eb->venue ?: 'No venue') . ' · ' . $eb->status,
                'time' => $timeAgo,
                'icon' => $eb->status === 'Approved' ? 'check_circle' : ($eb->status === 'Pending' ? 'pending' : 'event'),
                'color' => $eb->status === 'Approved' ? 'bg-emerald-500 shadow-emerald-500/30' : ($eb->status === 'Pending' ? 'bg-amber-500 shadow-amber-500/30' : 'bg-blue-500 shadow-blue-500/30'),
            ];
        }

        // Chart data (last 6 months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $monthBookings = $allEventBookings->filter(function($eb) use ($monthDate) {
                return $eb->created_at && $eb->created_at->month === $monthDate->month && $eb->created_at->year === $monthDate->year;
            });
            $totalMonthBookings = $monthBookings->count();
            $approvedMonthBookings = $monthBookings->whereIn('status', ['Approved', 'Confirmed', 'Active'])->count();
            
            $val = $totalMonthBookings > 0 ? min(round(($approvedMonthBookings / $totalMonthBookings) * 100), 100) : 0;
            $chartData[] = ['month' => $monthDate->format('M'), 'val' => $val];
        }

        return view('partner.dashboard', compact('partnerStats', 'partnerJobs', 'partnerQuotations', 'activities', 'chartData'));
    }

    public function events() 
    { 
        $user = Auth::user();
        
        $companyName = $user->company_name ?: $user->name;
        $allBookings = \App\Models\StaffQuotation::where('user_id', $user->id)
                    ->orWhere('client', $user->name)
                    ->orWhere('client', $companyName)
                    ->with('shiftSlots.applicant')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $today = now()->startOfDay();

        // Group bookings by event_name to create parent event containers
        $events = $allBookings->groupBy(function($b) {
            return $b->event_name ?: $b->sub_category ?: 'Untitled Event';
        })->map(function($shifts, $eventName) use ($today) {
            $first = $shifts->first();
            $assignedCount = 0;
            foreach($shifts as $s) {
                $assignedCount += $s->shiftSlots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->count();
            }

            // Calculate operational status matching admin logic
            $totalStaff = $shifts->sum('quantity');
            $statusStr = $first->status ?? 'Pending';
            
            $opStatus = 'Approved';
            if (in_array(strtolower($statusStr), ['cancelled', 'rejected'])) {
                $opStatus = 'Cancelled';
            } else {
                $startDateVal = $shifts->min('start_date');
                $endDateVal = $shifts->max('end_date');
                
                $startDate = $startDateVal ? \Carbon\Carbon::parse($startDateVal)->startOfDay() : null;
                $endDate = $endDateVal ? \Carbon\Carbon::parse($endDateVal)->startOfDay() : ($startDate ?: null);

                if ($endDate && $endDate->lt($today)) {
                    $opStatus = 'Completed';
                } elseif ($startDate && $endDate && $today->gte($startDate) && $today->lte($endDate)) {
                    $opStatus = 'Live Event';
                } else {
                    if ($assignedCount === 0) {
                        $opStatus = 'Approved';
                    } elseif ($assignedCount < $totalStaff) {
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

            $booking = $first->event_booking_id ? \App\Models\EventBooking::find($first->event_booking_id) : null;
            $areTimesheetsFinalized = $booking ? $booking->areTimesheetsFinalized() : false;
            $hasRating = $booking ? $booking->rating()->exists() : false;

            return (object)[
                'name' => $eventName,
                'venue' => $first->venue ?? 'No venue',
                'start_date' => $shifts->min('start_date'),
                'end_date' => $shifts->max('end_date'),
                'status' => $opStatus,
                'db_status' => $statusStr,
                'total_staff' => $totalStaff,
                'total_shifts' => $shifts->count(),
                'total_cost' => $shifts->sum(fn($s) => ($s->quantity ?? 0) * ($s->rate ?? 0) * ($s->shift_hours ?? 0)),
                'assigned_count' => $assignedCount,
                'shifts' => $shifts,
                'quotation_ref' => $first->quotation_ref,
                'id' => $first->event_booking_id ?: $first->id,
                'event_booking_id' => $first->event_booking_id,
                'are_timesheets_finalized' => $areTimesheetsFinalized,
                'has_rating' => $hasRating,
                'special_requirements' => $first->special_requirements,
                'is_editable' => $first->isEditable(),
                'created_at' => $first->created_at,
            ];
        })->values();

        return view('partner.events', compact('events', 'allBookings')); 
    }

    public function shifts(Request $request) 
    { 
        return redirect()->route('partner.event-list');
    }

    /**
     * Rate an assigned applicant.
     */
    public function rateStaff(Request $request)
    {
        $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'event_assignment_id' => 'required|exists:event_assignments,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string'
        ]);

        Rating::updateOrCreate(
            [
                'partner_id' => Auth::id(),
                'applicant_id' => $request->applicant_id,
                'event_assignment_id' => $request->event_assignment_id,
            ],
            [
                'rating' => $request->rating,
                'review' => $request->review,
            ]
        );

        return back()->with('success', 'Staff rated successfully!');
    }

    /**
     * Rate an event booking.
     */
    public function rateEvent(Request $request)
    {
        $request->validate([
            'event_booking_id' => 'required|exists:event_bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'staff_performance_rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $booking = \App\Models\EventBooking::where('user_id', Auth::id())
            ->findOrFail($request->event_booking_id);

        // Check if event is completed (status must be 'Completed')
        if ($booking->status !== 'Completed') {
            return back()->with('error', 'You can only rate completed events.');
        }

        // Check if timesheets are finalized
        if (!$booking->areTimesheetsFinalized()) {
            return back()->with('error', 'Timesheets must be finalized before leaving feedback.');
        }

        Rating::updateOrCreate(
            [
                'partner_id' => Auth::id(),
                'event_booking_id' => $booking->id,
            ],
            [
                'rating' => $request->rating,
                'staff_performance_rating' => $request->staff_performance_rating,
                'review' => $request->review,
            ]
        );

        return back()->with('success', 'Thank you for your feedback!');
    }


    public function timesheets() 
    { 
        $user = Auth::user();
        $companyName = $user->company_name ?: $user->name;
        
        $bookings = \App\Models\StaffQuotation::with(['shiftSlots' => function($query) {
                        $query->whereNotNull('applicant_id')->with('applicant');
                    }])
                    ->where(function($q) use ($user, $companyName) {
                        $q->where('user_id', $user->id)
                          ->orWhere('client', $user->name)
                          ->orWhere('client', $companyName);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('partner.timesheets', compact('bookings')); 
    }

    public function rateCard() { 
        $user = Auth::user();
        $companyName = $user->company_name ?: $user->name;
        $rates = \App\Models\RateCard::where('status', 'Active')
            ->where('company_name', $companyName)
            ->whereHas('category', function($q) {
                $q->where('status', 'Active');
            })->get();
        $venues = \App\Models\Venue::all();
        return view('partner.rate-card', compact('rates', 'venues')); 
    }

    public function exportRateCards(Request $request)
    {
        $user = Auth::user();
        $companyName = $user->company_name ?: $user->name;
        $rates = \App\Models\RateCard::where('status', 'Active')
            ->where('company_name', $companyName)
            ->whereHas('category', function($q) {
                $q->where('status', 'Active');
            })->get();
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
    public function quotations() 
    { 
        $user = Auth::user();
        
        // Load master event bookings with their child shifts
        $eventBookings = \App\Models\EventBooking::where('user_id', $user->id)
                        ->with('shifts')
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Also load any orphaned quotations (legacy records without event_booking_id)
        $orphanedQuotations = \App\Models\StaffQuotation::where('user_id', $user->id)
                        ->whereNull('event_booking_id')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('partner.quotations', compact('eventBookings', 'orphanedQuotations')); 
    }
    
    public function bookStaff(Request $request) 
{ 
    $booking = null;
    if ($request->has('edit') && $request->get('edit')) {
        // Scoped to the authenticated partner — an unscoped find() here let any partner
        // read another partner's booking (event name, roles, quantities, rates) by guessing
        // ?edit={id}. updateBooking() below already scopes correctly; this now matches it.
        $booking = \App\Models\StaffQuotation::where('user_id', Auth::id())->find($request->get('edit'));
    }

    $partnerId = Auth::id();
    $workedApplicantIds = \App\Models\ShiftSlot::whereNotNull('applicant_id')
        ->whereIn('status', ['Completed', 'Approved'])
        ->whereHas('staffQuotation.eventBooking', function ($query) use ($partnerId) {
            $query->where('user_id', $partnerId);
        })
        ->pluck('applicant_id')
        ->unique()
        ->toArray();

    $favouriteStaff = Auth::user()->favouriteStaff()
        ->whereIn('applicants.id', $workedApplicantIds)
        ->get();

    // Build roles catalog from rate cards for JS
    $iconMap = [
        'Security' => 'shield',
        'SIA Security' => 'shield-check',
        'Hospitality' => 'glass-water',
        'Events' => 'users',
        'Management' => 'user-cog',
        'Construction' => 'hard-hat',
        'Cleaning' => 'spray-can',
        'Facilities' => 'wrench',
    ];
    $partnerCompany = Auth::user()->company_name ?: Auth::user()->name;
    $rolesJson = \App\Models\RateCard::where('status', 'Active')
        ->where('company_name', $partnerCompany)
        ->whereHas('category', function($q) {
            $q->where('status', 'Active');
        })->with('venue')->get()->map(function($rc) use ($iconMap) {
        return [
            'id' => $rc->id,
            'name' => $rc->sub_category,
            'rate' => (float) $rc->hourly_rate,
            'icon' => $iconMap[$rc->type] ?? 'briefcase',
            'desc' => $rc->type . ($rc->sub_category ? ' — ' . $rc->sub_category : '') . ($rc->venue ? ' · ' . $rc->venue->name : ''),
        ];
    })->values();

    return view('partner.book-staff', compact('booking', 'favouriteStaff', 'rolesJson')); 
}    

    public function storeBooking(Request $request) 
    { 
        // Shared event details validation
        $request->validate([
            'event_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'venue' => 'required|string|max:255',
            'special_requirements' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $bookingRef = 'BK-' . strtoupper(substr(uniqid(), -4));

        // Step 1: Create the master EventBooking
        $eventBooking = \App\Models\EventBooking::create([
            'user_id' => $user?->id,
            'booking_ref' => $bookingRef,
            'event_name' => $request->event_name,
            'client' => $user?->company_name ?: ($user?->name ?? 'Guest'),
            'venue' => $request->venue,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'special_requirements' => $request->special_requirements,
            'status' => 'Pending',
        ]);

        // Step 2: Create child shifts from the cart
        if ($request->has('items') && is_array($request->items)) {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.sub_category' => 'required|string|max:255',
                'items.*.quantity' => 'required|integer|min:1|max:100',
                'items.*.shift' => 'nullable|string',
                'items.*.calculated_hours' => 'nullable|numeric|min:0',
                'items.*.start_time' => 'nullable|date_format:H:i',
                'items.*.end_time' => 'nullable|date_format:H:i',
                'items.*.shift_label' => 'nullable|string',
                'items.*.preferred_staff' => 'nullable|string',
            ]);

            foreach ($request->items as $item) {
                // SECURE: Always fetch rate from DB using partner-specific lookup
                $rateCard = \App\Models\RateCard::resolveRate($item['sub_category'], $user?->company_name ?: $user?->name);
                $secureRate = $rateCard ? $rateCard->hourly_rate : 0;
                $hours = $item['calculated_hours'] ?? ($item['shift_hours'] ?? 8);
                $total = $item['quantity'] * $secureRate * $hours;

                $shift = \App\Models\StaffQuotation::create([
                    'event_booking_id' => $eventBooking->id,
                    'quotation_ref' => $bookingRef,
                    'user_id' => $user?->id,
                    'client' => $user?->company_name ?: ($user?->name ?? 'Guest'),
                    'event_name' => $request->event_name,
                    'sub_category' => $item['sub_category'],
                    'quantity' => $item['quantity'],
                    'shift_hours' => $hours,
                    'calculated_hours' => $item['calculated_hours'] ?? null,
                    'shift' => $item['shift'] ?? null,
                    'shift_label' => $item['shift_label'] ?? null,
                    'start_time' => $item['start_time'] ?? null,
                    'end_time' => $item['end_time'] ?? null,
                    'shift_date' => $request->start_date,
                    'rate' => $secureRate,
                    'amount' => '£' . number_format($total, 2),
                    'date' => now()->format('M d, Y'),
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'venue' => $request->venue,
                    'special_requirements' => $request->special_requirements,
                    'preferred_staff' => $item['preferred_staff'] ?? null,
                    'status' => 'Pending',
                    'booked_at' => now(),
                ]);

                // Auto-generate ShiftSlots
                // Note: start_time/end_time are intentionally left null here — those columns
                // record the candidate's ACTUAL clocked-in/out time (see ApplicantController::
                // clockIn/clockOut and admin/time-shifting.blade.php, which already treats them
                // as the editable worked-time value distinct from the booking's scheduled time
                // on staffQuotation->start_time/end_time). Pre-filling them from the booking at
                // creation made every slot look "already clocked in" before anyone worked it.
                for ($i = 0; $i < $item['quantity']; $i++) {
                    \App\Models\ShiftSlot::create([
                        'staff_quotation_id' => $shift->id,
                        'role_name' => $item['sub_category'],
                        'shift_date' => $request->start_date,
                        'rate' => $secureRate,
                        'status' => 'Unassigned',
                    ]);
                }
            }
        } else {
            // Backward compatibility: single-item submission
            $request->validate([
                'sub_category' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1|max:100',
            ]);

            $rateCard = \App\Models\RateCard::resolveRate($request->sub_category, $user?->company_name ?: $user?->name);
            $secureRate = $rateCard ? $rateCard->hourly_rate : 0;
            $hours = $request->calculated_hours ?? ($request->shift_hours ?? 8);
            $total = $request->quantity * $secureRate * $hours;

            $shift = \App\Models\StaffQuotation::create([
                'event_booking_id' => $eventBooking->id,
                'quotation_ref' => $bookingRef,
                'user_id' => $user?->id,
                'client' => $user?->company_name ?: ($user?->name ?? 'Guest'),
                'event_name' => $request->event_name,
                'sub_category' => $request->sub_category,
                'quantity' => $request->quantity,
                'shift_hours' => $hours,
                'calculated_hours' => $request->calculated_hours ?? null,
                'start_time' => $request->start_time ?? null,
                'end_time' => $request->end_time ?? null,
                'shift_date' => $request->start_date,
                'shift' => $request->shift ?? null,
                'shift_label' => $request->shift_label ?? null,
                'rate' => $secureRate,
                'amount' => '£' . number_format($total, 2),
                'date' => now()->format('M d, Y'),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'venue' => $request->venue,
                'special_requirements' => $request->special_requirements,
                'preferred_staff' => $request->preferred_staff,
                'status' => 'Pending',
                'booked_at' => now(),
            ]);

            // start_time/end_time intentionally omitted — see note above; these record the
            // candidate's actual clock-in/out, not the booking's scheduled time.
            for ($i = 0; $i < $request->quantity; $i++) {
                \App\Models\ShiftSlot::create([
                    'staff_quotation_id' => $shift->id,
                    'role_name' => $request->sub_category,
                    'shift_date' => $request->start_date,
                    'rate' => $secureRate,
                    'status' => 'Unassigned',
                ]);
            }
        }

        // Recalculate the parent event total
        $eventBooking->recalculateTotal();

        $shiftCount = $eventBooking->shifts()->count();
        $message = "Booking submitted successfully! Ref: {$bookingRef} ({$shiftCount} shift" . ($shiftCount > 1 ? 's' : '') . " configured)";

        return redirect()->route('partner.quotations')->with('success', $message);
    }    

    public function editBooking($id)
    {
        // Load the EventBooking (master event) with its child shifts
        $eventBooking = Auth::user()->eventBookings()->with('shifts')->findOrFail($id);
        
        if (!$eventBooking->isEditable()) {
            return redirect()->route('partner.quotations')
                ->with('error', 'This booking can no longer be edited. The edit window has passed. Please contact admin to make changes.');
        }

        $partnerId = Auth::id();
        $workedApplicantIds = \App\Models\ShiftSlot::whereNotNull('applicant_id')
            ->whereIn('status', ['Completed', 'Approved'])
            ->whereHas('staffQuotation.eventBooking', function ($query) use ($partnerId) {
                $query->where('user_id', $partnerId);
            })
            ->pluck('applicant_id')
            ->unique()
            ->toArray();

        $favouriteStaff = Auth::user()->favouriteStaff()
            ->whereIn('applicants.id', $workedApplicantIds)
            ->get();

        // Build roles catalog from rate cards for JS
        $iconMap = [
            'Security' => 'shield',
            'SIA Security' => 'shield-check',
            'Hospitality' => 'glass-water',
            'Events' => 'users',
            'Management' => 'user-cog',
            'Construction' => 'hard-hat',
            'Cleaning' => 'spray-can',
            'Facilities' => 'wrench',
        ];
        $partnerCompany = Auth::user()->company_name ?: Auth::user()->name;
        $rolesJson = \App\Models\RateCard::where('status', 'Active')
            ->where('company_name', $partnerCompany)
            ->whereHas('category', function($q) {
                $q->where('status', 'Active');
            })->with('venue')->get()->map(function($rc) use ($iconMap) {
            return [
                'id' => $rc->id,
                'name' => $rc->sub_category,
                'rate' => (float) $rc->hourly_rate,
                'icon' => $iconMap[$rc->type] ?? 'briefcase',
                'desc' => $rc->type . ($rc->sub_category ? ' — ' . $rc->sub_category : '') . ($rc->venue ? ' · ' . $rc->venue->name : ''),
            ];
        })->values();

        // Pass the EventBooking as $booking for backward compat in the view
        $booking = $eventBooking;

        return view('partner.book-staff', compact('booking', 'favouriteStaff', 'rolesJson'));
    }

    public function updateBooking(Request $request, $id)
    {
        // Load the EventBooking (master event)
        $eventBooking = Auth::user()->eventBookings()->with('shifts')->findOrFail($id);
        
        if (!$eventBooking->isEditable()) {
            return redirect()->route('partner.quotations')
                ->with('error', 'Edit window expired. Contact admin for changes.');
        }

        // Validate event-level details
        $request->validate([
            'event_name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'venue' => 'nullable|string|max:255',
            'special_requirements' => 'nullable|string|max:2000',
        ]);

        // Update the parent EventBooking
        $eventBooking->update([
            'event_name' => $request->event_name,
            'venue' => $request->venue,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'special_requirements' => $request->special_requirements,
            'status' => 'Pending',
        ]);

        // Sync child shifts from the cart payload
        if ($request->has('items') && is_array($request->input('items'))) {
            $submittedItems = array_values($request->input('items'));
            $existingShiftIds = $eventBooking->shifts->pluck('id')->toArray();
            $processedIds = [];
            $user = Auth::user();

            $isRateLocked = in_array($eventBooking->status, ['Approved', 'Confirmed', 'Active']);

            foreach ($submittedItems as $item) {
                $hours = $item['calculated_hours'] ?? ($item['shift_hours'] ?? 8);

                // Check if this shift already exists (match by sub_category + shift timing)
                $existingShift = $eventBooking->shifts
                    ->where('sub_category', $item['sub_category'])
                    ->where('shift_label', $item['shift_label'] ?? null)
                    ->whereNotIn('id', $processedIds)
                    ->first();

                if ($existingShift) {
                    // Rate lock: preserve existing rate for approved bookings, re-fetch for pending
                    if ($isRateLocked) {
                        $secureRate = $existingShift->rate;
                    } else {
                        $rateCard = \App\Models\RateCard::resolveRate($item['sub_category'] ?? '', $user?->company_name ?: $user?->name);
                        $secureRate = $rateCard ? $rateCard->hourly_rate : 0;
                    }
                    $total = ($item['quantity'] ?? 1) * (float)$secureRate * (float)$hours;

                    // Update existing shift
                    $existingShift->update([
                        'sub_category' => $item['sub_category'],
                        'quantity' => $item['quantity'],
                        'shift_hours' => $hours,
                        'calculated_hours' => $item['calculated_hours'] ?? null,
                        'shift' => $item['shift'] ?? null,
                        'shift_label' => $item['shift_label'] ?? null,
                        'start_time' => $item['start_time'] ?? null,
                        'end_time' => $item['end_time'] ?? null,
                        'rate' => $secureRate,
                        'amount' => '£' . number_format($total, 2),
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'shift_date' => $request->start_date,
                        'venue' => $request->venue,
                        'event_name' => $request->event_name,
                        'special_requirements' => $request->special_requirements,
                        'preferred_staff' => $item['preferred_staff'] ?? null,
                        'status' => 'Pending',
                    ]);

                    // Sync ShiftSlots quantity
                    $existingCount = \App\Models\ShiftSlot::where('staff_quotation_id', $existingShift->id)->count();
                    $newQty = (int) ($item['quantity'] ?? 1);
                    if ($newQty > $existingCount) {
                        for ($i = 0; $i < ($newQty - $existingCount); $i++) {
                            // start_time/end_time intentionally omitted — see note above.
                            \App\Models\ShiftSlot::create([
                                'staff_quotation_id' => $existingShift->id,
                                'role_name' => $item['sub_category'],
                                'shift_date' => $request->start_date,
                                'rate' => $secureRate,
                                'status' => 'Unassigned',
                            ]);
                        }
                    } elseif ($newQty < $existingCount) {
                        \App\Models\ShiftSlot::where('staff_quotation_id', $existingShift->id)
                            ->where('status', 'Unassigned')
                            ->orderBy('id', 'desc')
                            ->take($existingCount - $newQty)
                            ->delete();
                    }

                    // Update shared details on remaining slots (rate only if not locked)
                    $slotUpdate = [
                        'role_name' => $item['sub_category'],
                        'shift_date' => $request->start_date,
                    ];
                    if (!$isRateLocked) {
                        $slotUpdate['rate'] = $secureRate;
                    }
                    \App\Models\ShiftSlot::where('staff_quotation_id', $existingShift->id)->update($slotUpdate);

                    $processedIds[] = $existingShift->id;
                } else {
                    // New shift: always fetch current rate
                    $rateCard = \App\Models\RateCard::resolveRate($item['sub_category'] ?? '', $user?->company_name ?: $user?->name);
                    $secureRate = $rateCard ? $rateCard->hourly_rate : 0;
                    $total = ($item['quantity'] ?? 1) * (float)$secureRate * (float)$hours;

                    // Create new shift under the same event
                    $newShift = \App\Models\StaffQuotation::create([
                        'event_booking_id' => $eventBooking->id,
                        'quotation_ref' => $eventBooking->booking_ref,
                        'user_id' => $user?->id,
                        'client' => $user?->company_name ?: ($user?->name ?? 'Guest'),
                        'event_name' => $request->event_name,
                        'sub_category' => $item['sub_category'],
                        'quantity' => $item['quantity'],
                        'shift_hours' => $hours,
                        'calculated_hours' => $item['calculated_hours'] ?? null,
                        'shift' => $item['shift'] ?? null,
                        'shift_label' => $item['shift_label'] ?? null,
                        'start_time' => $item['start_time'] ?? null,
                        'end_time' => $item['end_time'] ?? null,
                        'shift_date' => $request->start_date,
                        'rate' => $secureRate,
                        'amount' => '£' . number_format($total, 2),
                        'date' => now()->format('M d, Y'),
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'venue' => $request->venue,
                        'special_requirements' => $request->special_requirements,
                        'preferred_staff' => $item['preferred_staff'] ?? null,
                        'status' => 'Pending',
                        'booked_at' => now(),
                    ]);

                    // start_time/end_time intentionally omitted — see note above.
                    for ($j = 0; $j < ($item['quantity'] ?? 1); $j++) {
                        \App\Models\ShiftSlot::create([
                            'staff_quotation_id' => $newShift->id,
                            'role_name' => $item['sub_category'],
                            'shift_date' => $request->start_date,
                            'rate' => $secureRate,
                            'status' => 'Unassigned',
                        ]);
                    }

                    $processedIds[] = $newShift->id;
                }
            }

            // Delete any shifts that were removed from the cart
            $removedIds = array_diff($existingShiftIds, $processedIds);
            if (!empty($removedIds)) {
                \App\Models\ShiftSlot::whereIn('staff_quotation_id', $removedIds)->delete();
                \App\Models\StaffQuotation::whereIn('id', $removedIds)->delete();
            }
        }

        // Recalculate the parent event total
        $eventBooking->recalculateTotal();

        return redirect()->route('partner.quotations')->with('success', 'Event booking updated successfully!');
    }

    public function destroyBooking($id)
    {
        // Load the EventBooking (master event)
        $eventBooking = Auth::user()->eventBookings()->findOrFail($id);
        
        if (!$eventBooking->isEditable()) {
            return redirect()->back()->with('error', 'Delete window expired. Contact admin to remove this booking.');
        }

        // Cascade delete: shift slots -> shifts -> event
        foreach ($eventBooking->shifts as $shift) {
            $shift->shiftSlots()->delete();
            $shift->delete();
        }
        $eventBooking->delete();

        return redirect()->back()->with('success', 'Event booking and all associated shifts deleted successfully.');
    }

    public function favouriteStaff() 
    { 
        $partnerId = Auth::id();

        // Get unique worked applicant ids and their stats
        $workedSlots = \App\Models\ShiftSlot::whereNotNull('applicant_id')
            ->whereIn('status', ['Completed', 'Approved'])
            ->whereHas('staffQuotation.eventBooking', function ($query) use ($partnerId) {
                $query->where('user_id', $partnerId);
            })
            ->with(['applicant', 'staffQuotation.eventBooking'])
            ->get();

        // Group slots by applicant_id
        $workedGrouped = $workedSlots->groupBy('applicant_id');
        $workedApplicants = collect();

        if ($workedGrouped->isNotEmpty()) {
            foreach ($workedGrouped as $applicantId => $slots) {
                $applicant = $slots->first()->applicant;
                if (!$applicant) continue;
                
                // Count unique event bookings worked on
                $uniqueBookings = $slots->pluck('staffQuotation.eventBooking')->filter()->unique('id');
                $totalEventsWorked = $uniqueBookings->count();
                
                // Find latest event booking by start_date
                $latestBooking = $uniqueBookings->sortByDesc('start_date')->first();
                $lastEventWorked = $latestBooking ? $latestBooking->event_name : 'N/A';
                
                $workedApplicants->push((object) [
                    'id' => $applicant->id,
                    'name' => $applicant->name,
                    'role' => $applicant->role,
                    'image' => $applicant->image,
                    'location' => $applicant->location,
                    'image_position' => $applicant->image_position,
                    'total_events_worked' => $totalEventsWorked,
                    'last_event_worked' => $lastEventWorked,
                ]);
            }
        }

        $favouriteIds = Auth::user()->favouriteStaff()->pluck('applicants.id')->toArray();

        // Favourite staff list: subset of workedApplicants that are favourited
        $favourites = $workedApplicants->filter(function ($wa) use ($favouriteIds) {
            return in_array($wa->id, $favouriteIds);
        })->values();

        return view('partner.favourite-staff', compact('favourites', 'workedApplicants', 'favouriteIds')); 
    }

    public function toggleFavourite(Request $request)
    {
        $request->validate(['applicant_id' => 'required|exists:applicants,id']);
        
        $user = Auth::user();

        $applicantId = $request->applicant_id;

        if ($user->favouriteStaff()->where('applicant_id', $applicantId)->exists()) {
            $user->favouriteStaff()->detach($applicantId);
            return response()->json(['status' => 'removed']);
        } else {
            $user->favouriteStaff()->attach($applicantId);
            return response()->json(['status' => 'added']);
        }
    }
    
    public function messages() 
    { 
        $user = Auth::user();
        
        // Fetch admin/staff contacts for "New Chat" panel
        $teamContacts = User::whereIn('role', ['admin', 'staff'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Fetch partner's conversations from chat_messages
        $allMessages = \App\Models\ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group by conversation_id
        $conversations = $allMessages->groupBy('conversation_id')->map(function ($msgs) use ($teamContacts) {
            $latest = $msgs->first();
            
            // Try to resolve contact name from conversation_id (format: direct-{partnerId}-{contactId})
            $contactName = null;
            if (str_starts_with($latest->conversation_id, 'direct-')) {
                $parts = explode('-', $latest->conversation_id);
                $contactId = $parts[2] ?? null;
                if ($contactId) {
                    $contact = $teamContacts->firstWhere('id', $contactId);
                    $contactName = $contact ? $contact->name : null;
                }
            }
            
            return [
                'conversation_id' => $latest->conversation_id,
                'contact_name' => $contactName,
                'last_message' => $latest->admin_reply ?: $latest->message,
                'time' => $latest->created_at->diffForHumans(),
                'status' => $latest->status,
                'unread' => $msgs->where('status', 'replied')->where('admin_reply', '!=', null)->count(),
                'created_at' => $latest->created_at,
            ];
        })->values();
        
        // Get messages for the active conversation (first one or selected)
        $activeConversationId = request('conversation', $conversations->first()['conversation_id'] ?? null);
        $activeMessages = $activeConversationId 
            ? \App\Models\ChatMessage::where('conversation_id', $activeConversationId)
                ->orderBy('created_at', 'asc')
                ->get()
            : collect([]);
        
        // Resolve active contact name
        $activeContactName = null;
        if ($activeConversationId && str_starts_with($activeConversationId, 'direct-')) {
            $parts = explode('-', $activeConversationId);
            $contactId = $parts[2] ?? null;
            if ($contactId) {
                $contact = $teamContacts->firstWhere('id', $contactId);
                $activeContactName = $contact ? $contact->name : null;
            }
        }
        
        return view('partner.messages', compact('conversations', 'activeMessages', 'activeConversationId', 'teamContacts', 'activeContactName')); 
    }

    public function storeMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|string',
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        \App\Models\ChatMessage::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => $user->id,
            'user_type' => 'partner',
            'visitor_name' => $user->name,
            'visitor_email' => $user->email,
            'message' => $request->message,
            'status' => 'open',
        ]);

        return redirect()->route('partner.messages', ['conversation' => $request->conversation_id]);
    }

    public function newConversation($contactId)
    {
        $user = Auth::user();
        $contact = User::whereIn('role', ['admin', 'staff'])->findOrFail($contactId);
        
        // Use a deterministic conversation ID so repeated visits find the same thread
        $conversationId = 'direct-' . $user->id . '-' . $contact->id;
        
        // Check if conversation already exists
        $existing = \App\Models\ChatMessage::where('conversation_id', $conversationId)->exists();
        
        if (!$existing) {
            // Create an initial system message to establish the conversation
            \App\Models\ChatMessage::create([
                'conversation_id' => $conversationId,
                'user_id' => $user->id,
                'user_type' => 'partner',
                'visitor_name' => $user->name,
                'visitor_email' => $user->email,
                'message' => 'Started a conversation with ' . $contact->name,
                'status' => 'open',
            ]);
        }
        
        return redirect()->route('partner.messages', ['conversation' => $conversationId]);
    }

    public function contacts() 
    { 
        // Fetch admin and staff users as contacts
        $contacts = User::whereIn('role', ['admin', 'staff'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('partner.contacts', compact('contacts')); 
    }
    
    public function alerts() 
    { 
        // Fetch Laravel database notifications for the user
        $notifications = Auth::user()->notifications()->take(20)->get();
        
        return view('partner.alerts', compact('notifications')); 
    }
    
    public function profile() 
    { 
        $user = Auth::user();

        return view('partner.profile', compact('user')); 
    }

    public function updateProfile(Request $request)
{
    $user = User::findOrFail(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:500',
        'website' => 'nullable|url|max:255',
            // Email is usually not updatable or needs re-verification, 
            // but if we allow it, we would validate uniqueness:
            // 'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $oldClientName = $user->company_name ?: $user->name;

        $user->name = $request->name;
        $user->company_name = $request->company_name;
        $user->phone = $request->phone;
    $user->address = $request->address;
    $user->website = $request->website;
        // $user->email = $request->email;

        if ($request->hasFile('profile_photo')) {
            // SECURE UPLOAD FIX: Use Storage facade and hashname
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo_path = 'storage/' . $path;
        }

        $user->save();

        // Sync old bookings with the new profile name so Admin Panel reflects changes
        $newClientName = $user->company_name ?: $user->name;
        if ($newClientName) {
            \App\Models\StaffQuotation::where('user_id', $user->id)
                ->update(['client' => $newClientName]);
            
            \App\Models\ChatMessage::where('user_id', $user->id)
                ->where('user_type', 'partner')
                ->update(['visitor_name' => $user->name]);

            if ($oldClientName && $oldClientName !== $newClientName) {
                // Sync Rate Cards assigned to this company
                \App\Models\RateCard::where('company_name', $oldClientName)
                    ->update(['company_name' => $newClientName]);
                    
                // Sync Job Posts assigned to this company (if any)
                \App\Models\JobPost::where('company_name', $oldClientName)
                    ->update(['company_name' => $newClientName]);
            }
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function syncTimesheets(Request $request)
    {
        $request->validate([
            'sheets' => 'required|array',
            'sheets.*.rows' => 'nullable|array',
            'sheets.*.rows.*.db_id' => 'required|integer|exists:shift_slots,id',
            'sheets.*.rows.*.break_mins' => 'nullable|integer|min:0',
        ]);

        foreach ($request->sheets as $sheet) {
            if (!isset($sheet['rows'])) continue;
            foreach ($sheet['rows'] as $row) {
                $slot = \App\Models\ShiftSlot::find($row['db_id']);
                if ($slot) {
                    // Enforce Tenancy Check: ensure partner owns this slot via staff_quotation
                    $ownsSlot = \App\Models\StaffQuotation::where('id', $slot->staff_quotation_id)
                                    ->where(function($q) {
                                        $q->where('user_id', Auth::id())
                                          ->orWhere('client', Auth::user()->name)
                                          ->orWhere('client', Auth::user()->company_name);
                                    })->exists();

                    if ($ownsSlot) {
                        // Enforce Lock: once Approved or Billed, no partner edits allowed
                        if ($slot->status === 'Approved' || $slot->status === 'Billed') {
                            continue;
                        }

                        // Track manual adjustments
                        $wasAdjusted = false;
                        $adjustmentDetails = [];

                        $newStartTime = isset($row['start_time']) ? substr($row['start_time'], 0, 5) : null;
                        $newEndTime = isset($row['end_time']) ? substr($row['end_time'], 0, 5) : null;
                        $newBreakMins = isset($row['break_mins']) ? (int)$row['break_mins'] : null;

                        $oldStartTime = $slot->start_time ? substr($slot->start_time, 0, 5) : null;
                        $oldEndTime = $slot->end_time ? substr($slot->end_time, 0, 5) : null;
                        $oldBreakMins = $slot->break_mins !== null ? (int)$slot->break_mins : null;

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

                        // Partners may only update times and breaks — rate is locked
                        // Set status to 'Submitted' when partner submits corrections
                        $slot->update([
                            'start_time' => $row['start_time'] ?? $slot->start_time,
                            'end_time' => $row['end_time'] ?? $slot->end_time,
                            'break_mins' => $row['break_mins'] ?? $slot->break_mins,
                            'status' => 'Submitted',
                        ]);

                        // Log activity if manually adjusted by partner
                        if ($wasAdjusted) {
                            \App\Models\ActivityLog::create([
                                'user_id' => auth()->id(),
                                'action' => 'Timesheet Adjusted by Partner',
                                'description' => 'ShiftSlot ID ' . $slot->id . ' adjusted by Partner.',
                                'details' => [
                                    'message' => 'Partner submitted corrections/adjustments.',
                                    'adjustments' => $adjustmentDetails
                                ]
                            ]);
                        }
                    }
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Timesheets synced successfully']);
    }
}
