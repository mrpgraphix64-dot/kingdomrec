<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('rate_cards', 'title')) {
            Schema::table('rate_cards', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
        
        if (Schema::hasColumn('staff_quotations', 'role')) {
            Schema::table('staff_quotations', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        if (Schema::hasColumn('job_posts', 'title')) {
            Schema::table('job_posts', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
    }

    public function down(): void
    {
        Schema::table('rate_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('rate_cards', 'title')) {
                $table->string('title')->nullable();
            }
        });
        
        Schema::table('staff_quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_quotations', 'role')) {
                $table->string('role')->nullable();
            }
        });

        Schema::table('job_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('job_posts', 'title')) {
                $table->string('title')->nullable();
            }
        });
    }
};
