# Project Tasks, Requirements & Change Policy

Created: 2026-05-22

Purpose
- This file records everything the user tells the assistant about tasks, problem definitions, requirements, formats, and related notes.
- Rule: The assistant must consult this file before creating, updating, or modifying any file, configuration, or task in the repository.

Initial Entry
- Date: 2026-05-22
- Source: User message
- Category: Instruction / Policy
- Description: "do one thing create a tasks.md file in which you make a note of everything i tell you like my tasks problem defination req format etc like things and everytime you make any change refer that file before making any change or updating or creating anything"

Entry Template
- Date: YYYY-MM-DD
- Author: (User / Assistant)
- Category: Task | Problem Definition | Requirement | Format | Note
- Description: Short, clear description of the item
- Status: not-started | in-progress | completed
- Related files: (paths)

Change Log
- 2026-05-22: Created file and recorded initial user instruction.

How I'll use this file
- I will append new entries here whenever you tell me tasks or requirements.
- I will read and refer to this file before making or proposing any repository changes.

Recorded Tasks (user-provided)

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Validation System Improvement
- Description: Apply proper validation in every API; return custom error messages; required validation; email validation; unique validation; min/max length validation.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Common API Response Structure
- Description: Define and implement success response, validation error response, unauthorized response, server error response.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Middleware Implementation
- Description: Token verification, role checking, request logging.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Pagination
- Description: Implement current page, total pages, total records in API responses.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Search & Filters
- Description: Search by name, filter by status, date range filter.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Soft Delete
- Description: Add deleted_at support; implement restore() and forceDelete().
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: File Upload API
- Description: Profile image upload; document upload; file storage; file validation; unique filenames.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Database Relationships
- Description: Implement One-to-Many and Belongs-To relationships where required.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Role & Permission System
- Description: Admin, Manager, Employee roles with appropriate permissions.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Exception Handling
- Description: try-catch handling; database error handling; token error handling; 404 error handling.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Activity Logs
- Description: Record who updated, what updated, old value, new value.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Postman Documentation
- Description: Environment variables; auth token setup; request examples.
- Status: not-started

- Date: 2026-05-22
- Author: User
- Category: Task
- Title: Scheduler & Cron Jobs
- Description: Daily cleanup command; inactive user checker.
- Status: not-started

Change Log
- 2026-05-22: Created file and recorded initial user instruction.
- 2026-05-22: Appended 13 user-provided tasks and updated todo list.
- 2026-05-26: Added create-task-with-auto-assignment requirement and RBAC constraints.
- 2026-05-26: Locked down separate assignment-create API route per user confirmation.

- Date: 2026-05-26
- Author: User
- Category: Requirement
- Title: Create Task With Auto Assignment And Status Log
- Description:
	- Remove `created_by` from create-task API payload.
	- Only Admin/Manager can assign tasks.
	- Create-task input fields: `title`, `start_date`, `due_date`, `priority_id`, `task_status_id`, `department_id`, `assigned_user_ids`.
	- On create-task, auto-create task assignments for all `assigned_user_ids` with assignment details.
	- On create-task, auto-create task status log with assignment-related details (including who assigned).
- Status: in-progress

- Date: 2026-05-26
- Author: User
- Category: Requirement
- Title: Disable Separate Assignment Create API
- Description:
	- Do not use a separate task-assignment create API for normal flow.
	- Task assignment should happen via create-task flow.
- Status: completed

Project Summary (selected problem statement)

- Date: 2026-05-22
- Author: User
- Category: Problem Definition
- Project Title: Smart Office Employee & Task Management System API
- Project Objective:
	- Create a secure API system for office management
	- Manage employees and tasks efficiently
	- Implement role-based access control
	- Improve API response consistency
	- Maintain proper activity logs
	- Handle file uploads securely
	- Automate cleanup and inactive user checking
- Status: recorded

Backend Requirements (user-provided)

- Date: 2026-05-22
- Author: User
- Category: Requirement
- Description:
	- Central authorization directly in `routes/api.php` to enforce auth centrally for API routes.
	- Implement necessary SQL/ORM joins in model layer (Eloquent) rather than controller, to keep controllers thin.
	- Role-based access controls (Admin/Manager/Employee) enforced via middleware and policies.
	- Proper, predictable backend flow: validation → authorization → business logic → persistence → response.
	- Ensure every task is accomplished while preserving system stability; apply non-breaking changes and run tests where possible.
	- When fixing or adding features, ensure nothing breaks existing functionality; prefer incremental changes and feature flags if needed.
- Status: recorded

Code Format Templates (must be followed strictly)

Note: The assistant will follow these model, controller, and route formats exactly when creating or updating code. Before any change, the assistant will read and respect the content in this file.

MODEL example:

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class UserRoleModel extends Model
{
	Protected $table="tbl_user_role";
	protected $primaryKey = 'user_role_id';
	public $timestamps = false;
	Protected $fillable = ["user_role", "user_role_description", "user_role_status", "browser_name", "browser_version", "browser_platform", "ip_address", "created_by", "updated_by", "accesstoken"];

	public function getalluserrole($data){
		$query = DB::table('tbl_user_role as ur')->select('ur.*');
        
		if (array_key_exists('search', $data) && isset($data['search'])) {
			$query = $query->where(function($q) use ($data) {
				$q->where('ur.user_role', 'like', '%' . $data['search'] . '%')
				  ->orWhere('ur.user_role_description', 'like', '%' . $data['search'] . '%');
			});
		}

		if (array_key_exists('column_searches', $data) && is_array($data['column_searches'])) {
			foreach ($data['column_searches'] as $col => $val) {
				if (!empty($val)) {
					$query = $query->where($col, 'like', '%' . $val . '%');
				}
			}
		}
		if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
			$query = $query->whereDate('ur.created_at', '>=', $data['from_date'])
						   ->whereDate('ur.created_at', '<=', $data['to_date']);
		}
		if (array_key_exists('user_role_id', $data) && isset($data['user_role_id'])) {
			$query = $query->where('ur.user_role_id', '=' ,$data['user_role_id']);
		}

		if (array_key_exists('user_role', $data) && isset($data['user_role'])) {
			if (is_array($data['user_role'])) {
				$query = $query->whereIn('ur.user_role', $data['user_role']);
			} elseif (strpos($data['user_role'], ',') !== false) {
				$query = $query->whereIn('ur.user_role', explode(',', $data['user_role']));
			} else {
				$query = $query->where('ur.user_role', '=', $data['user_role']);
			}
		}

		if (array_key_exists('sort_column', $data) && isset($data['sort_column']) && array_key_exists('sort_dir', $data) && isset($data['sort_dir'])) {
			$query = $query->orderBy($data['sort_column'], $data['sort_dir']);
		} else {
			$query = $query->orderBy('ur.user_role_id', 'asc');
		}

		$query = $query->where('ur.user_role_status','1');

		if (array_key_exists('offset', $data) && isset($data['offset']) && array_key_exists('limit', $data) && isset($data['limit'])) {
			$total_count = $query->count();
			$result = $query->offset($data['offset'])->limit($data['limit'])->get();
			return ['data' => $result, 'total_count' => $total_count];
		}

		$result = $query->get();
		return $result;
	}
}

CONTROLLER example:

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\UserRoleModel;
class UserRoleController extends Controller
{
	public function adduserrole(Request $request){
		$valid=Validator::make($request->all(),[
			"user_role" => "required",
		]);
		if ($valid->fails()) {
			return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
		} else {
			$userrole = new UserRoleModel();
           
			$userrole->user_role = $request->input('user_role');
			$userrole->user_role_description = $request->input('user_role_description');
			$userrole->user_role_status = 1;
			$userrole->browser_name = $request->input('browser_name');
			$userrole->browser_version = $request->input('browser_version');
			$userrole->browser_platform = $request->input('browser_platform');
			$userrole->ip_address = $request->ip();
			$userrole->created_by = $request->input('created_by');
			$userrole->accesstoken = $request->input('accesstoken');
			try {
				$result = $userrole->save();
				if($result){
				   return response()->json(['status' => 200,'data' => $result],200);
				}else{
					return response()->json(['status' => 400, 'error' => 'Save returned false.'], 400); 
				}
			} catch (\Exception $e) {
				return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
			}
		}
	}
	public function getalluserrole(Request $request) {
        
		$valid = Validator::make($request->all(), [
					// "limit" => "required|numeric",
					// "offset" => "required|numeric",
		]);

		if ($valid->fails()) {
			return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
		} else {
			$userrole = new UserRoleModel();
			$data = array();
          
			if ($request->has('offset') && $request->filled('offset')) {
				$data["offset"] = $request->input("offset");
			}

			if ($request->has('limit') && $request->filled('limit')) {
				$data["limit"] = $request->input("limit");
			}

			if ($request->has('sort_column') && $request->filled('sort_column')) {
				$data["sort_column"] = $request->input("sort_column");
			}
			if ($request->has('sort_dir') && $request->filled('sort_dir')) {
				$data["sort_dir"] = $request->input("sort_dir");
			}
			if ($request->has('column_searches') && is_array($request->input('column_searches'))) {
				$data["column_searches"] = $request->input("column_searches");
			}

			if ($request->has('user_role_id') && $request->input('user_role_id') != "") {
				$data['user_role_id'] = $request->input('user_role_id');
			}

			if ($request->has('user_role') && $request->input('user_role') != "") {
				$data['user_role'] = $request->input('user_role');
			}

			if ($request->has('search') && $request->input('search') != "") {
				$data['search'] = $request->input('search');
			}

			if ($request->has('from_date') && $request->filled('from_date')) {
				$data["from_date"] = $request->input("from_date");
			}
			if ($request->has('to_date') && $request->filled('to_date')) {
				$data["to_date"] = $request->input("to_date");
			}

			$userroles = $userrole->getalluserrole($data);
			if (isset($userroles['total_count'])) {
				return response()->json(['status' => 200, 'count' => $userroles['total_count'], 'data' => $userroles['data']], 200);
			}
			return response()->json(['status' => 200, 'count' => count($userroles), 'data' => $userroles],200);
		}
	}
	public function updateuserrole(Request $request) {
		//validations
		$valid = Validator::make($request->all(), [
			"user_role_id" => "required",
		]);

		if ($valid->fails()) {
			return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
		} else {
			$userrole = new UserRoleModel();
			$newrequest = $request->except(['user_role_id']);
			try {
				$result = $userrole->where('user_role_id', $request->input('user_role_id'))->update($newrequest);
                
				if ($result) {
					return response()->json(['status' => 200, 'data' =>  $result],200);
				} else {
					return response()->json(['status' => 400, 'error' => "No rows updated or something went wrong."], 400);
				}
			} catch (\Exception $e) {
				return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
			}
		}
	}
	public function deleteuserrole(Request $request) {
		$valid = Validator::make($request->all(), [
					"user_role_id" => "required",
		]);

		if ($valid->fails()) {
			return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
		} else {
			$userrole = new UserRoleModel();
			$request->request->add(['user_role_status' => 0]);
			$newrequest = $request->except(['user_role_id']);
			try {
				$result = $userrole->where('user_role_id', $request->input('user_role_id'))->update($newrequest);
				if ($result) {
					return response()->json(['status' => 200, 'data' => $request->all()],200);
				} else {
					return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
				}
			} catch (\Exception $e) {
				 return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
			}
		}
	}
}

ROUTES (all routes should use POST method):

Route::POST('getalluserrole', 'UserRoleController@getalluserrole');
Route::POST('adduserrole', 'UserRoleController@adduserrole');
Route::POST('updateuserrole', 'UserRoleController@updateuserrole');
Route::POST('deleteuserrole', 'UserRoleController@deleteuserrole');

Enforcement rule:

- The assistant must follow the above code structure and naming conventions when creating or modifying models, controllers, and routes.
- Controllers should handle validation, try-catch exception handling, and return JSON responses with `status` and either `data` or `error` keys as shown.
- Models should encapsulate query logic (joins, filters, pagination) in methods and return either raw result sets or arrays with `data` and `total_count` when pagination is used.
- All API routes must be `POST` and declared centrally in `routes/api.php` unless the user specifies otherwise in `tasks.md`.

RBAC & Admin Bootstrap Requirement

- Role-based access control: Admin can perform all actions; Manager and Employee have restricted access enforced via role middleware and policies.
- Seed one Admin user in the database at deployment for bootstrapping.
- Protect user-creation/registration endpoints so only Admin (using Admin token) can create/register other users during bootstrap; after users are created they authenticate with their own tokens.
- Implementation note: create an `admins` seeder (or a `users` seeder with role=admin) and store an initial personal access token for the Admin to use in Postman.
- Status: not-started

Change Log
- 2026-05-22: Created file and recorded initial user instruction.
- 2026-05-22: Appended 13 user-provided tasks and updated todo list.
- 2026-05-22: Appended project problem statement and backend requirements.
- 2026-05-22: Appended code format templates and enforcement rule.
- 2026-05-22: Appended RBAC & Admin bootstrap requirement.

