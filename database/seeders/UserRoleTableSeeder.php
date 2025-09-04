<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing user role assignments for user_id = 7
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserRole::where('user_id', 7)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get user_id = 7
        $user = User::find(7);
        
        if (!$user) {
            $this->command->error('User with ID 7 not found. Please create the user first.');
            return;
        }

        // Get all active roles
        $roles = Role::where('is_active', true)->get();

        if ($roles->isEmpty()) {
            $this->command->error('No active roles found. Please run RoleSeeder first.');
            return;
        }

        // Create UserRole entries for user_id = 7 with all roles
        $userRoles = [];
        foreach ($roles as $role) {
            $userRoles[] = [
                'user_id' => 7,
                'role_id' => $role->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert user role assignments
        UserRole::insert($userRoles);

        $roleNames = $roles->pluck('name')->toArray();
        $this->command->info("User ID 7 ({$user->name} - {$user->email}) has been assigned all roles:");
        foreach ($roleNames as $roleName) {
            $this->command->info("  - {$roleName}");
        }
        
        $this->command->info('UserRole seeding completed successfully!');
        $this->command->info("Total role assignments created: " . count($userRoles));
    }
}
