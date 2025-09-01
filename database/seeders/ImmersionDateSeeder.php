<?php

namespace Database\Seeders;

use App\Models\ActionArea;
use App\Models\ImmersionDate;
use App\Models\PujaCategorie;
use Illuminate\Database\Seeder;

class ImmersionDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ImmersionDate::create(['name' => '02 Oct', 'idate' => '2025-10-02']);
        ImmersionDate::create(['name' => '03 Oct', 'idate' => '2025-10-03']);
    }
}
