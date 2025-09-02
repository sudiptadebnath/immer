<?php

namespace Database\Seeders;

use App\Models\ActionArea;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActionAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ActionArea::create([
            'name'     => 'I',
        ]);
        ActionArea::create([
            'name'     => 'II',
        ]);
        ActionArea::create([
            'name'     => 'III',
        ]);
    }
}
