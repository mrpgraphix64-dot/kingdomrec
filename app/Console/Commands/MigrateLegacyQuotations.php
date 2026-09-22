<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateLegacyQuotations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:legacy-quotations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy StaffQuotation rows into the new EventBooking parent-child hierarchy.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting legacy quotations migration...');

        $quotations = \App\Models\StaffQuotation::whereNull('event_booking_id')->get();
        if ($quotations->isEmpty()) {
            $this->info('No unmigrated quotations found.');
            return;
        }

        $grouped = [];
        foreach ($quotations as $q) {
            // Strip any -uniq or -123 suffix
            $baseRef = $q->quotation_ref;
            if (strpos($baseRef, '-') !== false && preg_match('/-\w+$/', $baseRef)) {
                $baseRef = preg_replace('/-\w+$/', '', $baseRef);
            }
            $grouped[$baseRef][] = $q;
        }

        $this->info('Found ' . count($grouped) . ' unique events to create.');

        foreach ($grouped as $ref => $shifts) {
            $first = $shifts[0];
            
            // Calculate total amount
            $totalAmount = 0;
            foreach ($shifts as $s) {
                $amt = str_replace(['£', ','], '', $s->amount);
                $totalAmount += (float) $amt;
            }

            // Determine parent status - if any shift is approved, event is approved, etc.
            // For simplicity, just take the first shift's status.
            $status = $first->status;

            $event = \App\Models\EventBooking::create([
                'user_id' => $first->user_id,
                'booking_ref' => $ref,
                'event_name' => $first->event_name ?: 'Unnamed Event',
                'client' => $first->client,
                'venue' => $first->venue,
                'start_date' => $first->start_date,
                'end_date' => $first->end_date,
                'special_requirements' => $first->special_requirements,
                'status' => $status,
                'total_amount' => '£' . number_format($totalAmount, 2),
                'created_at' => $first->created_at,
                'updated_at' => $first->updated_at,
            ]);

            foreach ($shifts as $s) {
                $s->event_booking_id = $event->id;
                $s->save();
            }

            $this->line("Created event {$ref} with " . count($shifts) . " shifts.");
        }

        $this->info('Migration complete!');
    }
}
