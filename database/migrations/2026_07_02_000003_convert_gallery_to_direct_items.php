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
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('albums');

        if (!Schema::hasTable('gallery_items')) {
            Schema::create('gallery_items', function (Blueprint $table) {
                $table->id();
                $table->string('type')->default('image'); // 'image' or 'video'
                $table->string('file_path');
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
        Schema::dropIfExists('gallery_items');
    }
};
