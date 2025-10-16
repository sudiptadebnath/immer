<?php

namespace Database\Seeders;

use App\Models\ActionArea;
use App\Models\PujaCommitteeRepo;
use Illuminate\Database\Seeder;

class PujaCommitteeRepoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PujaCommitteeRepo::create([
            'name' => 'FD Block Durga Puja',
        ]);
        PujaCommitteeRepo::create([
            'name' => 'AB Block Durga Puja',
        ]);
        PujaCommitteeRepo::create([
            'name' => 'BJ Block Durga Puja',
        ]);
        PujaCommitteeRepo::create([
            'name' => 'BC Block Durga Puja',
        ]);
        PujaCommitteeRepo::create([
            'name' => 'AJ Block Durga Puja',
        ]);
        PujaCommitteeRepo::create([
            'name' => 'GD Block Durga Puja',
        ]);
        PujaCommitteeRepo::create([
            'name' => 'CJ Block Durga Puja',
        ]);
        PujaCommitteeRepo::create([
            'name' => 'DL Block Durga Puja',
        ]);
        PujaCommitteeRepo::create([
            'name' => 'FE Block Durga Puja',
        ]);
        PujaCommitteeRepo::create([
            'name' => 'IA Block Durga Puja',
        ]);
    }
}
