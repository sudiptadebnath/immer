<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::Create([
            'key' => "USER_SIGNUP",
            'val' => "1",
        ]);
        Setting::Create([
            'key' => "ACTION_AREA",
            'val' => "I~~II~~III",
        ]);
        Setting::Create([
            'key' => "CATEGORY",
            'val' => "Housing~~Block",
        ]);
        Setting::Create([
            'key' => "PUJA_COMMITTEE",
            'val' => "FD Block Durga Puja~~AB Block Durga Puja~~BJ Block Durga Puja~~BC Block Durga Puja~~AJ Block Durga Puja~~GD Block Durga Puja~~CJ Block Durga Puja~~DL Block Durga Puja~~FE Block Durga Puja~~IA Block Durga Puja",
        ]);
        Setting::Create([
            'key' => "IMMERSION_DATE",
            'val' => "2025-10-02~~2025-10-03~~2025-10-04",
        ]);
        Setting::Create([
            'key' => "DHUNUCHI_TEAM",
            'val' => "20",
        ]);
    }
}
