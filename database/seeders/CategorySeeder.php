<?php

namespace Database\Seeders;

use App\Models\ActionArea;
use App\Models\PujaCategorie;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PujaCategorie::create([
            'name'     => 'Housing',
        ]);
        PujaCategorie::create([
            'name'     => 'Block',
        ]);
    }
}
