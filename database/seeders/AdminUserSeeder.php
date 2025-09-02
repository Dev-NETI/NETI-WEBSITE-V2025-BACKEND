<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::firstOrCreate(
            ['email' => 'admin@neti.com.ph'],
            [
                'name' => 'NETI Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create sample admin users for different roles
        $admins = [
            [
                'name' => 'Events Manager',
                'email' => 'events@neti.com.ph',
                'password' => Hash::make('events123'),
                'role' => 'events_manager',
            ],
            [
                'name' => 'News Manager',
                'email' => 'news@neti.com.ph',
                'password' => Hash::make('news123'),
                'role' => 'news_manager',
            ],
            [
                'name' => 'User Manager',
                'email' => 'users@neti.com.ph',
                'password' => Hash::make('users123'),
                'role' => 'user_manager',
            ],
        ];

        foreach ($admins as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                array_merge($admin, [
                    'is_active' => true,
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
