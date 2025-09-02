<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin user if it doesn't exist
        $superAdmin = User::where('email', 'admin@neti.com.ph')->first();
        
        if (!$superAdmin) {
            $superAdmin = User::create([
                'name' => 'NETI Super Administrator',
                'email' => 'admin@neti.com.ph',
                'password' => Hash::make('admin123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Assign super admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdmin->syncRoles(['super_admin']);
        }

        // Create test users with different role combinations
        $testUsers = [
            [
                'name' => 'Events Manager',
                'email' => 'events@neti.com.ph',
                'password' => Hash::make('events123'),
                'roles' => ['events']
            ],
            [
                'name' => 'News Manager',
                'email' => 'news@neti.com.ph',
                'password' => Hash::make('news123'),
                'roles' => ['news']
            ],
            [
                'name' => 'User Manager',
                'email' => 'users@neti.com.ph',
                'password' => Hash::make('users123'),
                'roles' => ['user_management']
            ],
            [
                'name' => 'Content Manager',
                'email' => 'content@neti.com.ph',
                'password' => Hash::make('content123'),
                'roles' => ['events', 'news'] // Multiple roles
            ],
            [
                'name' => 'Operations Manager',
                'email' => 'operations@neti.com.ph',
                'password' => Hash::make('operations123'),
                'roles' => ['events', 'news', 'user_management'] // Multiple roles
            ],
        ];

        foreach ($testUsers as $userData) {
            // Check if user already exists
            $user = User::where('email', $userData['email'])->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            }

            // Assign roles
            $user->syncRoles($userData['roles']);
        }

        $this->command->info('User roles seeded successfully!');
        
        // Display created users and their roles
        $users = User::with('roles')->get();
        $this->command->info("\nCreated users:");
        foreach ($users as $user) {
            $roleNames = $user->getRoleNames();
            $this->command->info("- {$user->name} ({$user->email}): " . implode(', ', $roleNames));
        }
    }
}