<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterTablesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->updateOrInsert(
            ['name' => 'Admin'],
            ['description' => 'System administrator', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('roles')->updateOrInsert(
            ['name' => 'Manager'],
            ['description' => 'Team manager', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('roles')->updateOrInsert(
            ['name' => 'Employee'],
            ['description' => 'Regular employee', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('task_statuses')->updateOrInsert(
            ['key' => 'open'],
            ['label' => 'Open', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('task_statuses')->updateOrInsert(
            ['key' => 'in_progress'],
            ['label' => 'In Progress', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('task_statuses')->updateOrInsert(
            ['key' => 'done'],
            ['label' => 'Done', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('priorities')->updateOrInsert(
            ['key' => 'low'],
            ['label' => 'Low', 'level' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('priorities')->updateOrInsert(
            ['key' => 'medium'],
            ['label' => 'Medium', 'level' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('priorities')->updateOrInsert(
            ['key' => 'high'],
            ['label' => 'High', 'level' => 3, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('departments')->updateOrInsert(
            ['name' => 'HR'],
            ['description' => 'Human Resources', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

         DB::table('departments')->updateOrInsert(
            ['name' => 'Web Development'],
            ['description' => 'Web Development', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('departments')->updateOrInsert(
            ['name' => 'Cyber Security'],
            ['description' => 'Cyber Security', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('departments')->updateOrInsert(
            ['name' => 'General'],
            ['description' => 'General department', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');

        if ($adminRoleId && !DB::table('users')->where('email', 'admin@example.com')->exists()) {
            DB::table('users')->insert([
                'role_id' => $adminRoleId,
                'department_id' => null,
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'phone' => null,
                'image' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@123'),
                'status' => 1,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

