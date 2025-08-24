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
            'uid' => 'admin',
            'password' => 'admin', 
            'role' => 'a'
        ]);

        User::create([
            'uid' => 'operator',
            'password' => 'operator',
            'role' => 'o',
        ]);

        User::create([
            'uid' => 'user',
            'password' => 'user',
            'role' => 'u'
        ]);    
    }
}
