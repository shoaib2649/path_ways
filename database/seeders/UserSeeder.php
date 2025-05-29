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
        // User::create([
        //     'name' => 'Asad Test',
        //     'email' => 'asad25@gmail.com',
        //     'user_role' => 'provider',
        //     'password' => Hash::make('12345678'), // encrypting password
        // ]);
         User::create([
        'name' => 'Op Test',
        'email' => 'op@gmail.com',
        'user_role' => 'operational_director',
        'password' => Hash::make('12345678'),
    ]);
    }
}
