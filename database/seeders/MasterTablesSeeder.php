<?php

namespace Database\Seeders;

use App\Models\DepartmentModel;
use App\Models\PriorityModel;
use App\Models\RoleModel;
use App\Models\TaskStatusModel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasterTablesSeeder extends Seeder
{
	public function run(): void
	{
		$priorities = [
			['title' => 'High', 'level' => 'High'],
			['title' => 'Low', 'level' => 'Low'],
			['title' => 'Avg', 'level' => 'Avg'],
		];

		foreach ($priorities as $priorityData) {
			PriorityModel::updateOrCreate(
				['title' => $priorityData['title']],
				[
					'level' => $priorityData['level'],
					'status' => 1,
				]
			);
		}

		$roles = [
			['name' => 'Admin', 'description' => null],
			['name' => 'Manager', 'description' => null],
			['name' => 'Employee', 'description' => null],
		];

		foreach ($roles as $roleData) {
			RoleModel::updateOrCreate(
				['name' => $roleData['name']],
				[
					'description' => $roleData['description'],
					'status' => 1,
				]
			);
		}

		$taskStatuses = [
			['title' => 'Open', 'description' => null],
			['title' => 'In-progress', 'description' => null],
			['title' => 'Done', 'description' => null],
		];

		foreach ($taskStatuses as $taskStatusData) {
			TaskStatusModel::updateOrCreate(
				['title' => $taskStatusData['title']],
				[
					'description' => $taskStatusData['description'],
					'status' => 1,
				]
			);
		}

		$departments = [
			['name' => 'HR', 'description' => null],
			['name' => 'Web Development', 'description' => null],
			['name' => 'Cyber Security', 'description' => null],
		];

		foreach ($departments as $departmentData) {
			DepartmentModel::updateOrCreate(
				['name' => $departmentData['name']],
				[
					'description' => $departmentData['description'],
					'status' => 1,
				]
			);
		}

		$adminRole = RoleModel::where('name', 'Admin')->first();
		$adminDepartment = DepartmentModel::where('name', 'HR')->first();

		User::updateOrCreate(
			['email' => 'admin@smartoffice.com'],
			[
				'name' => 'System Admin',
				'password' => Hash::make('Admin@123'),
				'role_id' => $adminRole?->role_id ?? 1,
				'department_id' => $adminDepartment?->department_id ?? 1,
				'mobile' => null,
				'image' => null,
				'status' => 1,
			]
		);
	}
}
