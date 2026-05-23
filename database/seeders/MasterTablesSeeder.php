<?php

namespace Database\Seeders;

use App\Models\DepartmentModel;
use App\Models\PriorityModel;
use App\Models\RoleModel;
use App\Models\TaskStatusModel;
use Illuminate\Database\Seeder;

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
	}
}
