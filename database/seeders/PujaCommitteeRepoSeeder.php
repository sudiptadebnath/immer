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
        $committees = [
            ['name' => 'FD Block Durga Puja', 'puja_address' => 'FD Block, Salt Lake'],
            ['name' => 'AB Block Durga Puja', 'puja_address' => 'AB Block, Salt Lake'],
            ['name' => 'BJ Block Durga Puja', 'puja_address' => 'BJ Block, Salt Lake'],
            ['name' => 'BC Block Durga Puja', 'puja_address' => 'BC Block, Salt Lake'],
            ['name' => 'AJ Block Durga Puja', 'puja_address' => 'AJ Block, Salt Lake'],
            ['name' => 'GD Block Durga Puja', 'puja_address' => 'GD Block, Salt Lake'],
            ['name' => 'CJ Block Durga Puja', 'puja_address' => 'CJ Block, Salt Lake'],
            ['name' => 'DL Block Durga Puja', 'puja_address' => 'DL Block, Salt Lake'],
            ['name' => 'FE Block Durga Puja', 'puja_address' => 'FE Block, Salt Lake'],
            ['name' => 'IA Block Durga Puja', 'puja_address' => 'IA Block, Salt Lake'],
        ];

        $actionAreas = [1, 2, 3];
        $pujaCategories = [1, 2];

        foreach ($committees as $index => $data) {
            PujaCommitteeRepo::create([
                'action_area_id'   => $actionAreas[$index % count($actionAreas)],
                'puja_category_id' => $pujaCategories[$index % count($pujaCategories)],
                'name'             => $data['name'],
                'puja_address'     => $data['puja_address'],
                'view_order'       => $index + 1,
            ]);
        }
    }
}
