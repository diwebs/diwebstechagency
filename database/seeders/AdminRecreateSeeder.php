<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminRecreateSeeder extends Seeder
{
    /**
     * Run the database seeds to recreate/reset the admin login.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@diwebstechagency.website'],
            [
                'name' => 'Diwebs Administrator',
                'password' => 'password', // Auto-hashed due to 'password' => 'hashed' cast on User model
                'role' => 'super_admin',
                'status' => 'active'
            ]
        );
    }
}
