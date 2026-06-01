<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentModelController;
use App\Http\Controllers\DocumentModelController;
use App\Http\Controllers\PriorityModelController;
use App\Http\Controllers\RoleModelController;
use App\Http\Controllers\TaskAssignmentModelController;
use App\Http\Controllers\TaskModelController;
use App\Http\Controllers\TaskStatusLogModelController;
use App\Http\Controllers\TaskStatusModelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

// Public admin login; optional token is resolved when present for normal-user login checks
Route::post('userlogin', [AuthController::class, 'userlogin'])->middleware('optional.token.auth');

// Readable by any authenticated user; task access is further filtered in middleware/controller
Route::middleware('auth:sanctum')->group(function () {
	Route::post('getalldepartment', [DepartmentModelController::class, 'getalldepartment']);
	Route::post('getdepartment', [DepartmentModelController::class, 'getdepartment']);

	Route::post('getallpriority', [PriorityModelController::class, 'getallpriority']);
	Route::post('getpriority', [PriorityModelController::class, 'getpriority']);

	Route::post('getalltaskstatus', [TaskStatusModelController::class, 'getalltaskstatus']);
	Route::post('gettaskstatus', [TaskStatusModelController::class, 'gettaskstatus']);

	Route::post('getalltask', [TaskModelController::class, 'getalltask']);
	Route::post('gettask', [TaskModelController::class, 'gettask'])->middleware('task.assigned');
	Route::post('updatetask', [TaskModelController::class, 'updatetask'])->middleware('task.assigned');
	Route::post('deletetask', [TaskModelController::class, 'deletetask'])->middleware('task.assigned');

	Route::post('getalltaskstatuslog', [TaskStatusLogModelController::class, 'getalltaskstatuslog']);
	Route::post('getalluser', [UserController::class, 'getalluser'])->middleware('task.manage');
	Route::post('updateprofile', [UserController::class, 'updateprofile']);


	Route::post('logout', [AuthController::class, 'logout']);
});

// Admin-only CRUD actions
Route::middleware(['auth:sanctum', 'admin.token'])->group(function () {
	Route::post('adddepartment', [DepartmentModelController::class, 'adddepartment']);
	Route::post('updatedepartment', [DepartmentModelController::class, 'updatedepartment']);
	Route::post('deletedepartment', [DepartmentModelController::class, 'deletedepartment']);

	Route::post('addtaskstatuslog', [TaskStatusLogModelController::class, 'addtaskstatuslog']);
	Route::post('updatetaskstatuslog', [TaskStatusLogModelController::class, 'updatetaskstatuslog']);
	Route::post('deletetaskstatuslog', [TaskStatusLogModelController::class, 'deletetaskstatuslog']);

	Route::post('addtaskstatus', [TaskStatusModelController::class, 'addtaskstatus']);
	Route::post('updatetaskstatus', [TaskStatusModelController::class, 'updatetaskstatus']);
	Route::post('deletetaskstatus', [TaskStatusModelController::class, 'deletetaskstatus']);

	Route::post('getalldocument', [DocumentModelController::class, 'getalldocument']);
	Route::post('getdocument', [DocumentModelController::class, 'getdocument']);
	Route::post('getuser', [UserController::class, 'getuser']);
	Route::post('createuser', [UserController::class, 'adduser'])->middleware('admin.token');
	Route::post('updateuser', [UserController::class, 'updateuser']);
	Route::post('deleteuser', [UserController::class, 'deleteuser']);

	Route::post('getallrole', [RoleModelController::class, 'getallrole']);
	Route::post('getrole', [RoleModelController::class, 'getrole']);
	Route::post('addrole', [RoleModelController::class, 'addrole']);
	Route::post('updaterole', [RoleModelController::class, 'updaterole']);
	Route::post('deleterole', [RoleModelController::class, 'deleterole']);

	// Priorities (admin CRUD)
	Route::post('addpriority', [PriorityModelController::class, 'addpriority']);
	Route::post('updatepriority', [PriorityModelController::class, 'updatepriority']);
	Route::post('deletepriority', [PriorityModelController::class, 'deletepriority']);

	Route::post('getalltaskassignment', [TaskAssignmentModelController::class, 'getalltaskassignment']);
	Route::post('gettaskassignment', [TaskAssignmentModelController::class, 'gettaskassignment']);
	Route::post('updatetaskassignment', [TaskAssignmentModelController::class, 'updatetaskassignment']);
	Route::post('deletetaskassignment', [TaskAssignmentModelController::class, 'deletetaskassignment']);
});

// Admin/manager task creation remains separate so the existing task middleware still applies
Route::middleware(['auth:sanctum', 'task.manage'])->group(function () {
	Route::post('createtask', [TaskModelController::class, 'addtask']);
});
