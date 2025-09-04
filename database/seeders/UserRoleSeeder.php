<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserRole::truncate();
        // Create sample users for each individual role
        $users = [
            [
                'name' => 'User Manager',
                'email' => 'user@neti.com.ph',
                'password' => Hash::make('admin123'),
                'roles' => ['user_manager'],
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Events Manager',
                'email' => 'events@neti.com.ph',
                'password' => Hash::make('admin123'),
                'roles' => ['events_manager'],
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'News Manager',
                'email' => 'news@neti.com.ph',
                'password' => Hash::make('admin123'),
                'roles' => ['news_manager'],
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign roles to user
            $roleIds = Role::whereIn('name', $roles)->pluck('id');
            $user->roles()->sync($roleIds);

            $this->command->info("User created/updated: {$user->name} ({$user->email}) with roles: " . implode(', ', $roles));
        }

        // Assign user_id = 7 all available roles
        $superUser = User::find(7);
        if ($superUser) {
            $allRoles = Role::where('is_active', true)->pluck('id');
            $superUser->roles()->sync($allRoles);

            $roleNames = Role::where('is_active', true)->pluck('name')->toArray();
            $this->command->info("User ID 7 ({$superUser->name} - {$superUser->email}) assigned all roles: " . implode(', ', $roleNames));
        } else {
            $this->command->warn("User with ID 7 not found. Cannot assign all roles.");
        }

        // Update NOC user to have user_manager role
        $nocUser = User::updateOrCreate(
            ['email' => 'noc@neti.com.ph'],
            [
                'name' => 'NOC Administrator',
                'email' => 'noc@neti.com.ph',
                'password' => Hash::make('admin123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $userManagerRole = Role::where('name', 'user_manager')->first();
        if ($userManagerRole) {
            $nocUser->roles()->sync([$userManagerRole->id]);
        }

        $this->command->info("NOC user updated: {$nocUser->name} ({$nocUser->email}) with user_manager role");
        $this->command->info('All users have been seeded with their respective roles!');
        $this->command->info('Available roles: user_manager, events_manager, news_manager');
    }
}
