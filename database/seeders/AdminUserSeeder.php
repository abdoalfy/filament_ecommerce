<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the admin user
        Admin::updateOrCreate([
            'email' => 'admin@admin.com' // Unique key to avoid duplicate entries
        ], [
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'), // Replace with a strong password
            'role' => 'super_admin', // Assuming you have a role column to flag users roles
        ]);
    }
}
