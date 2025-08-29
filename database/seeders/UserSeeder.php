<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'secretary_mobile' => '9111111111',
            'password' => 'admin',
            'role' => 'a'
        ]);

        User::create([
            'secretary_mobile' => '9222222222',
            'password' => 'operator',
            'role' => 'o',
        ]);

        User::create([
            'secretary_mobile' => '9333333333',
            'password' => 'scanner',
            'role' => 's'
        ]);

        User::create([
            'secretary_mobile' => '944444444',
            'password' => 'user',
            'role' => 'u'
        ]);
    }
}
