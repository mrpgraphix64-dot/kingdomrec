<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JobCategory;
use App\Models\SubCategory;
use App\Models\RateCard;
use App\Models\Venue;
use App\Models\Shift;
use App\Models\StaffQuotation;
use App\Models\JobPost;
use App\Models\Applicant;
use App\Models\Team;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminDashboardSeeder extends Seeder
{
    public function run()
    {
        // Truncate tables to prevent duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        JobCategory::truncate();
        SubCategory::truncate();
        RateCard::truncate();
        Venue::truncate();
        Shift::truncate();
        StaffQuotation::truncate();
        JobPost::truncate();
        Applicant::truncate();
        Team::truncate();
        Event::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Job Categories
        $cats = [
            ['name' => 'Security', 'description' => 'SIA Licensed Door Supervisors, CCTV, and Close Protection', 'status' => 'Active'],
            ['name' => 'Events', 'description' => 'Event Planners, Coordinators, and Production Managers', 'status' => 'Active'],
            ['name' => 'Hospitality', 'description' => 'Bartenders, Baristas, Chefs and Waiting Staff', 'status' => 'Active'],
            ['name' => 'Cleaning', 'description' => 'Commercial, Event, and Deep Cleaning services', 'status' => 'Active'],
            ['name' => 'Construction', 'description' => 'Laborers, Site Managers, and skilled trades', 'status' => 'Review'],
        ];
        foreach($cats as $c) JobCategory::create($c);

        // Sub Categories
        $subs = [
            ['name' => 'Door Supervisor', 'parent_category' => 'Security'],
            ['name' => 'CCTV Operator', 'parent_category' => 'Security'],
            ['name' => 'Close Protection', 'parent_category' => 'Security'],
            ['name' => 'Bartender', 'parent_category' => 'Hospitality'],
            ['name' => 'Waiter/Waitress', 'parent_category' => 'Hospitality'],
            ['name' => 'Head Chef', 'parent_category' => 'Hospitality'],
            ['name' => 'Event Planner', 'parent_category' => 'Events'],
            ['name' => 'Production Assistant', 'parent_category' => 'Events'],
            ['name' => 'General Laborer', 'parent_category' => 'Construction'],
        ];
        foreach($subs as $s) SubCategory::create($s);
    }
}
