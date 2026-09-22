<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\EventBooking;
use App\Models\ShiftSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display the Admin Finance Center.
     */
    public function index(Request $request)
    {
        // Calculate general financial metrics across invoices using quick DB queries
        $totalInvoiced = Invoice::sum('total_amount');
        $totalPaid = Invoice::where('status', 'Paid')->sum('total_amount');
        $outstanding = Invoice::where('status', 'Unpaid')->sum('total_amount');
        $totalInvoicesCount = Invoice::count();
        $totalPaidCount = Invoice::where('status', 'Paid')->count();
        $totalUnpaidCount = Invoice::where('status', 'Unpaid')->count();

        // Base query for event bookings ready for invoicing
        // Ready means has at least one approved/billed shift slot and no invoices
        $baseReadyQuery = EventBooking::where('event_bookings.status', '!=', 'Closed')
            ->whereHas('shiftSlots', function ($q) {
                $q->where('shift_slots.status', 'Approved');
            })
            ->whereDoesntHave('invoices');

        $totalReadyCount = (clone $baseReadyQuery)->count();

        $stats = [
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'outstanding' => $outstanding,
            'total_invoices_count' => $totalInvoicesCount,
            'total_ready_count' => $totalReadyCount,
            'total_paid_count' => $totalPaidCount,
            'total_unpaid_count' => $totalUnpaidCount,
        ];

        // Fetch event bookings ready for invoicing with eager loading
        $readyBookingsQuery = (clone $baseReadyQuery)->with(['user', 'shiftSlots']);

        if ($request->filled('search')) {
            $search = $request->search;
            $readyBookingsQuery->where(function($q) use ($search) {
                $q->where('booking_ref', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Client/Company
        if ($request->filled('client_id')) {
            $clientId = $request->client_id;
            $readyBookingsQuery->where('user_id', $clientId);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $readyBookingsQuery->where('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $readyBookingsQuery->where('end_date', '<=', $request->date_to);
        }

        // Fetch all invoices for grid
        $query = Invoice::with(['user', 'eventBooking'])->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_ref', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('eventBooking', function($bq) use ($search) {
                      $bq->where('event_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Client/Company for Invoices
        if ($request->filled('client_id')) {
            $query->where('user_id', $request->client_id);
        }

        // Filter by Date Range for Invoices
        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        // Filter by Status (combines both virtual and database statuses)
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'Awaiting Invoice') {
                $query->whereRaw('1 = 0');
            } elseif ($status === 'Invoice Generated') {
                $readyBookingsQuery->whereRaw('1 = 0');
            } else {
                $query->where('status', $status);
                $readyBookingsQuery->whereRaw('1 = 0');
            }
        }

        $readyBookings = $readyBookingsQuery->get();
        $invoices = $query->paginate(15)->withQueryString();

        // Fetch partners for the filter dropdown
        $partners = \App\Models\User::where('role', 'partner')
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'name']);

        return view('admin.finance.index', compact('stats', 'readyBookings', 'invoices', 'partners'));
    }

    /**
     * Generate an invoice for an EventBooking.
     */
    public function generateInvoice(Request $request, $bookingId)
    {
        $booking = EventBooking::with(['user', 'shiftSlots'])->findOrFail($bookingId);

        // Check if invoice already exists for this booking to prevent duplicate generation
        $existingInvoice = Invoice::where('event_booking_id', $booking->id)->first();
        if ($existingInvoice) {
            return redirect()->route('admin.finance')->with('info', "Invoice {$existingInvoice->invoice_ref} has already been generated for this booking.");
        }

        // Verify has approved slots
        $approvedSlots = $booking->shiftSlots()->where('shift_slots.status', 'Approved')->get();
        if ($approvedSlots->isEmpty()) {
            return redirect()->back()->with('error', 'No approved timesheet shifts found for this booking.');
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($booking, $approvedSlots) {
            // Re-check inside transaction with lock to prevent race conditions
            $existing = Invoice::where('event_booking_id', $booking->id)->lockForUpdate()->first();
            if ($existing) {
                return redirect()->route('admin.finance')->with('info', "Invoice {$existing->invoice_ref} has already been generated for this booking.");
            }

            // Generate unique reference
            $year = date('Y');
            $maxRef = Invoice::where('invoice_ref', 'like', "INV-{$year}-%")->lockForUpdate()->max('invoice_ref');
            $nextNum = 1;
            if ($maxRef) {
                $parts = explode('-', $maxRef);
                $nextNum = intval(end($parts)) + 1;
            }
            $ref = "INV-{$year}-" . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            // Ensure user link exists
            $userId = $booking->user_id ?: Auth::id();

            // Group slots by role_name and rate to create invoice items
            $grouped = $approvedSlots->groupBy(function ($s) {
                $role = $s->role_name ?: 'General Staff';
                $rate = floatval($s->rate ?: 0);
                return "{$role}||{$rate}";
            });

            $subtotal = 0.0;
            $itemsData = [];

            foreach ($grouped as $key => $slots) {
                list($role, $rate) = explode('||', $key);
                $rate = floatval($rate);
                $totalHours = 0.0;
                foreach ($slots as $slot) {
                    $totalHours += $slot->getHours();
                }

                $amount = round($totalHours * $rate, 2);
                $subtotal += $amount;

                $itemsData[] = [
                    'description' => "{$role} services for event: " . $booking->event_name,
                    'quantity' => $slots->count(),
                    'hours' => $totalHours,
                    'rate' => $rate,
                    'amount' => $amount
                ];
            }

            $taxRate = 0.00; // 0% VAT (removed)
            $taxAmount = 0.00;
            $totalAmount = $subtotal;

            // Create Invoice as Unpaid
            $invoice = Invoice::create([
                'invoice_ref' => $ref,
                'event_booking_id' => $booking->id,
                'user_id' => $userId,
                'company_name' => $booking->client ?: ($booking->user->company_name ?? $booking->user->name ?? 'Unknown Partner'),
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0.00,
                'total_amount' => $totalAmount,
                'amount_paid' => 0.00,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(), // 14-day terms
                'status' => 'Unpaid',
                'notes' => 'Generated automatically from approved timesheets.'
            ]);

            // Save Invoice Items
            foreach ($itemsData as $item) {
                $invoice->items()->create($item);
            }

            return redirect()->route('admin.finance')->with('success', "Invoice {$ref} generated successfully.");
        });
    }

    /**
     * Mark invoice as Paid.
     */
    public function markPaid(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status === 'Paid') {
            return redirect()->back()->with('error', 'Invoice is already paid.');
        }

        $invoice->update([
            'status' => 'Paid',
            'amount_paid' => $invoice->total_amount
        ]);

        return redirect()->back()->with('success', "Invoice {$invoice->invoice_ref} has been marked as Paid.");
    }

    /**
     * Download the invoice PDF (Admin).
     */
    public function downloadPdf($id)
    {
        $invoice = Invoice::with(['items', 'eventBooking.user'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.exports.invoice-pdf', compact('invoice'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Kingdom_Invoice_{$invoice->invoice_ref}.pdf");
    }

    /**
     * Display Partner Billing Center.
     */
    public function partnerBilling(Request $request)
    {
        $user = Auth::user();

        // Fetch all invoices linked to the partner
        $invoices = Invoice::where('user_id', $user->id)
            ->with(['eventBooking'])
            ->orderBy('id', 'desc')
            ->get();

        $totalInvoices = $invoices->count();
        $latestInvoiceDate = $invoices->max('issue_date');
        $totalInvoicedAmount = $invoices->sum('total_amount');

        $stats = [
            'total_invoices' => $totalInvoices,
            'latest_invoice_date' => $latestInvoiceDate,
            'total_invoiced_amount' => $totalInvoicedAmount
        ];

        return view('partner.billing.index', compact('invoices', 'stats'));
    }

    /**
     * Download the invoice PDF (Partner).
     */
    public function partnerDownloadPdf($id)
    {
        $user = Auth::user();

        // Enforce tenancy
        $invoice = Invoice::where('user_id', $user->id)
            ->with(['items', 'eventBooking.user'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('admin.exports.invoice-pdf', compact('invoice'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Kingdom_Invoice_{$invoice->invoice_ref}.pdf");
    }
}
