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
        if (!Schema::hasTable('gallery_images')) {
            Schema::create('gallery_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('album_id')->constrained('albums')->onDelete('cascade');
                $table->string('image_path');
                $table->string('thumbnail_path')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('alt_text')->nullable();
                $table->string('status')->default('published'); // 'published' or 'hidden'
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
