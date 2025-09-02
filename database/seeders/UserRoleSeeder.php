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
        // Find user with ID 7
        $user = User::find(7);

        if (!$user) {
            $this->command->error('User with ID 7 not found. Please ensure user exists before running this seeder.');
            return;
        }

        // Get all available role names
        $allRoleNames = ['events', 'news', 'user_management', 'super_admin'];

        // Assign all roles to user ID 7
        $user->syncRoles($allRoleNames);

        $this->command->info("All roles assigned to user ID 7 ({$user->name} - {$user->email}) successfully!");

        // Display the user and their roles
        $user->load('roles');
        $roleNames = $user->getRoleNames();
        $this->command->info("User roles: " . implode(', ', $roleNames));
    }
}
