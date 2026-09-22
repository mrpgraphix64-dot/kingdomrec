<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\EventAssignment;
use App\Models\Applicant;
use App\Models\Event;

class JobApplicationController extends Controller
{
    /**
     * List all job applications for admin.
     */
    public function index(Request $request)
    {
        $query = JobApplication::with(['jobPost', 'applicant.experiences', 'applicant.educations'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('applicant', function($aq) use ($search) {
                    $aq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('jobPost', function($jq) use ($search) {
                    $jq->where('sub_category', 'like', "%{$search}%")
                       ->orWhere('company_name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => JobApplication::count(),
            'pending' => JobApplication::where('status', 'Pending')->count(),
            'reviewed' => JobApplication::where('status', 'Reviewed')->count(),
            'qualified' => JobApplication::where('status', 'Qualified')->count(),
            'rejected' => JobApplication::where('status', 'Rejected')->count(),
        ];

        return view('admin.applications', compact('applications', 'stats'));
    }

    /**
     * Update the status of a job application.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Reviewed,Qualified,Rejected'
        ]);

        $application = JobApplication::findOrFail($id);

        // Check special rule: Rejected can be moved to Qualified once only
        $isTransitionFromRejectedToQualified = ($application->status === 'Rejected' && $request->status === 'Qualified');

        if ($isTransitionFromRejectedToQualified) {
            if ($application->requalified_count >= 1) {
                $errorMessage = 'This application has already been requalified once and cannot be qualified again.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $errorMessage], 422);
                }
                return redirect()->back()->with('error', $errorMessage);
            }
        }

        // Prepare update data
        $updateData = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes ?? $application->admin_notes,
        ];

        // Increment requalified count if transitioning from Rejected to Qualified
        if ($isTransitionFromRejectedToQualified) {
            $updateData['requalified_count'] = $application->requalified_count + 1;
        }

        $application->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Application status updated.']);
        }

        return redirect()->back()->with('success', 'Application status updated successfully.');
    }

    /**
     * Get qualified applicants for assignment to an event (JSON endpoint).
     */
    public function qualifiedApplicants()
    {
        $qualified = Applicant::whereHas('jobApplications', function ($q) {
            $q->where('status', 'Qualified');
        })->get(['id', 'name', 'email', 'role', 'image']);

        return response()->json($qualified);
    }

    /**
     * Assign an applicant to an event.
     */
    public function assignToEvent(Request $request, $eventId)
    {
        $request->validate([
            'applicant_id' => 'required|exists:applicants,id'
        ]);

        $event = Event::findOrFail($eventId);

        // Prevent duplicate assignment on ShiftSlot
        $existing = \App\Models\ShiftSlot::where('staff_quotation_id', $event->staff_quotation_id)
            ->where('applicant_id', $request->applicant_id)
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'Applicant is already assigned to this event.'], 422);
        }

        // Assign to the next empty ShiftSlot
        $slot = \App\Models\ShiftSlot::where('staff_quotation_id', $event->staff_quotation_id)
            ->where(function($q) {
                $q->whereNull('applicant_id')->orWhere('applicant_id', 0);
            })
            ->first();

        if (!$slot) {
            return response()->json(['error' => 'No open slots remaining for this shift.'], 422);
        }

        $slot->update([
            'applicant_id' => $request->applicant_id,
            'status' => 'Assigned',
        ]);

        $applicant = Applicant::findOrFail($request->applicant_id);
        if ($applicant->user) {
            $applicant->user->notify(new \App\Notifications\ShiftAssignedNotification([
                'event_name' => $event->title,
                'date' => $event->date,
                'location' => $event->location ?? 'TBD',
            ]));
        }

        return response()->json(['success' => true, 'message' => 'Applicant assigned to event successfully.']);
    }

    /**
     * Remove an applicant from an event.
     */
    public function removeFromEvent($eventId, $applicantId)
    {
        $event = Event::findOrFail($eventId);
        $slot = \App\Models\ShiftSlot::where('staff_quotation_id', $event->staff_quotation_id)
            ->where('applicant_id', $applicantId)
            ->first();

        if ($slot) {
            $slot->update([
                'applicant_id' => null,
                'status' => 'Unassigned',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Assignment removed.']);
    }

    /**
     * Get assignments for a specific event (JSON endpoint).
     */
    public function eventAssignments($eventId)
    {
        $event = Event::findOrFail($eventId);
        $slots = \App\Models\ShiftSlot::with('applicant')
            ->where('staff_quotation_id', $event->staff_quotation_id)
            ->whereNotNull('applicant_id')
            ->get()
            ->map(function($slot) {
                return (object)[
                    'id' => $slot->id,
                    'applicant_id' => $slot->applicant_id,
                    'applicant' => $slot->applicant,
                    'status' => $slot->status,
                    'notes' => $slot->role_name,
                ];
            });

        return response()->json($slots);
    }

    /**
     * Delete a job application.
     */
    public function destroy($id)
    {
        $application = JobApplication::findOrFail($id);
        $application->delete();

        return redirect()->back()->with('success', 'Job application deleted successfully.');
    }
}
