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
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default albums
        $albums = [
            'Corporate Events',
            'Recruitment Drives',
            'Training Sessions',
            'Team Activities',
            'Awards',
            'Office',
            'Community',
            'Miscellaneous'
        ];

        foreach ($albums as $index => $name) {
            \Illuminate\Support\Facades\DB::table('albums')->insert([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'sort_order' => $index,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
