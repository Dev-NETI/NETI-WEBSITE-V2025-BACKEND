<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        // Define the three main roles for the system
        $roles = [
            [
                'name' => 'user_manager',
                'display_name' => 'User Management',
                'description' => 'Manages user accounts, roles, and permissions',
                'is_active' => true,
            ],
            [
                'name' => 'events_manager',
                'display_name' => 'Events Management',
                'description' => 'Manages training events, schedules, and registrations',
                'is_active' => true,
            ],
            [
                'name' => 'news_manager',
                'display_name' => 'News Management',
                'description' => 'Manages news articles, announcements, and publications',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );

            $this->command->info("Role created/updated: {$role->display_name} ({$role->name})");
        }

        $this->command->info('All roles have been seeded successfully!');
        $this->command->info('Available roles: user_manager, events_manager, news_manager');
    }
}
