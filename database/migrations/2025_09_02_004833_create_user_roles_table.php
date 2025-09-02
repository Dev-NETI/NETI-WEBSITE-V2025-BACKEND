<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // events, news, user_management, etc.
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create user_roles pivot table for many-to-many relationship
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate role assignments
            $table->unique(['user_id', 'role_id']);
        });

        // Remove the old single role column from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'events',
                'display_name' => 'Events Management',
                'description' => 'Can manage events, create, edit, and delete events',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'news',
                'display_name' => 'News Management',
                'description' => 'Can manage news articles, create, edit, and delete news',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user_management',
                'display_name' => 'User Management',
                'description' => 'Can manage users, create, edit, and delete user accounts',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full system access, can manage everything',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the single role column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'events_manager', 'news_manager', 'user_manager'])->default('user_manager')->after('password');
        });

        // Drop the new tables
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
    }
};
