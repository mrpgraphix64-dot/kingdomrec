<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_ref')->unique();
            $table->unsignedBigInteger('event_booking_id')->nullable();
            $table->unsignedBigInteger('user_id'); // Partner user
            $table->string('company_name');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(20.00); // Configurable tax rate
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('status')->default('Unpaid'); // Unpaid, Paid
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('event_booking_id')->references('id')->on('event_bookings')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
