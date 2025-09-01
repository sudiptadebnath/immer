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
            'name'     => 'Admin',
            'email'    => 'admin@mail.com',
            'phone'    => '9111111111',
            'password' => 'admin',
            'role'     => 'a',
        ]);

        User::create([
            'name'     => 'Operator1',
            'email'    => 'operator1@mail.com',
            'phone'    => '9222222222',
            'password' => 'operator',
            'role'     => 'o',
        ]);

        User::create([
            'name'     => 'Scanner1',
            'email'    => 'scanner1@mail.com',
            'phone'    => '9333333333',
            'password' => 'scanner',
            'role'     => 's',
        ]);
    }
}
