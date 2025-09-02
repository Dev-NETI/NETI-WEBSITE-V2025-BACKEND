<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create multiple users to ensure we have a user with ID 7
        $users = [
            [
                'name' => 'Admin User 1',
                'email' => 'admin1@neti.com.ph',
                'password' => Hash::make('admin123'),
            ],
            [
                'name' => 'Admin User 2',
                'email' => 'admin2@neti.com.ph',
                'password' => Hash::make('admin123'),
            ],
            [
                'name' => 'Admin User 3',
                'email' => 'admin3@neti.com.ph',
                'password' => Hash::make('admin123'),
            ],
            [
                'name' => 'Admin User 4',
                'email' => 'admin4@neti.com.ph',
                'password' => Hash::make('admin123'),
            ],
            [
                'name' => 'Admin User 5',
                'email' => 'admin5@neti.com.ph',
                'password' => Hash::make('admin123'),
            ],
            [
                'name' => 'Admin User 6',
                'email' => 'admin6@neti.com.ph',
                'password' => Hash::make('admin123'),
            ],
            [
                'name' => 'NOC Administrator',
                'email' => 'noc@neti.com.ph',
                'password' => Hash::make('2025@Neti1'),
            ]
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $this->command->info("User created/updated: {$user->name} ({$user->email}) with ID: {$user->id}");
        }
    }
}
