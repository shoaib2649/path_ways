<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Asad Test',
            'email' => 'asad25@gmail.com',
            'user_role' => 'provider',
            'password' => Hash::make('password123'), // encrypting password
        ]);
    }
}
