<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@medicare.com',
            'phone' => '+966500000000',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'status' => 'active',
            'language_preference' => 'ar',
        ]);
    }
}
