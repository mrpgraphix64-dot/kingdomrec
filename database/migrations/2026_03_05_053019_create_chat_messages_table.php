<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id', 36)->index();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('user_type', ['visitor', 'applicant', 'partner', 'admin'])->default('visitor');
            $table->text('message');
            $table->text('admin_reply')->nullable();
            $table->string('page_url')->nullable();
            $table->enum('status', ['open', 'replied', 'resolved'])->default('open');
            $table->timestamp('replied_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
