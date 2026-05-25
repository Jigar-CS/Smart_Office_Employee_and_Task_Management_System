x`<?php

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

// Public login to generate own token
Route::post('login', [AuthController::class, 'login']);
Route::post('loginuser', [AuthController::class, 'loginUser']);

// All API routes require a valid token
Route::middleware('auth:sanctum')->group(function () {
	Route::post('logout', [AuthController::class, 'logout']);
	Route::post('logoutuser', [AuthController::class, 'logout']);

	Route::post('getalldepartment', [DepartmentModelController::class, 'getalldepartment']);
	Route::post('getdepartment', [DepartmentModelController::class, 'getdepartment']);
	Route::post('adddepartment', [DepartmentModelController::class, 'adddepartment']);
	Route::post('updatedepartment', [DepartmentModelController::class, 'updatedepartment']);
	Route::post('deletedepartment', [DepartmentModelController::class, 'deletedepartment']);

	Route::post('getalldocument', [DocumentModelController::class, 'getalldocument']);
	Route::post('getdocument', [DocumentModelController::class, 'getdocument']);
	Route::post('adddocument', [DocumentModelController::class, 'adddocument']);
	Route::post('updatedocument', [DocumentModelController::class, 'updatedocument']);
	Route::post('deletedocument', [DocumentModelController::class, 'deletedocument']);

	Route::post('getallpriority', [PriorityModelController::class, 'getallpriority']);
	Route::post('getpriority', [PriorityModelController::class, 'getpriority']);
	Route::post('addpriority', [PriorityModelController::class, 'addpriority']);
	Route::post('updatepriority', [PriorityModelController::class, 'updatepriority']);
	Route::post('deletepriority', [PriorityModelController::class, 'deletepriority']);

	Route::post('getallrole', [RoleModelController::class, 'getallrole']);
	Route::post('getrole', [RoleModelController::class, 'getrole']);
	Route::post('addrole', [RoleModelController::class, 'addrole']);
	Route::post('updaterole', [RoleModelController::class, 'updaterole']);
	Route::post('deleterole', [RoleModelController::class, 'deleterole']);

	Route::post('getalltaskassignment', [TaskAssignmentModelController::class, 'getalltaskassignment']);
	Route::post('gettaskassignment', [TaskAssignmentModelController::class, 'gettaskassignment']);
	Route::post('addtaskassignment', [TaskAssignmentModelController::class, 'addtaskassignment']);
	Route::post('updatetaskassignment', [TaskAssignmentModelController::class, 'updatetaskassignment']);
	Route::post('deletetaskassignment', [TaskAssignmentModelController::class, 'deletetaskassignment']);

	Route::post('getalltask', [TaskModelController::class, 'getalltask']);
	Route::post('gettask', [TaskModelController::class, 'gettask']);
	Route::post('addtask', [TaskModelController::class, 'addtask']);
	Route::post('updatetask', [TaskModelController::class, 'updatetask']);
	Route::post('deletetask', [TaskModelController::class, 'deletetask']);

	Route::post('getalltaskstatuslog', [TaskStatusLogModelController::class, 'getalltaskstatuslog']);
	Route::post('addtaskstatuslog', [TaskStatusLogModelController::class, 'addtaskstatuslog']);
	Route::post('updatetaskstatuslog', [TaskStatusLogModelController::class, 'updatetaskstatuslog']);
	Route::post('deletetaskstatuslog', [TaskStatusLogModelController::class, 'deletetaskstatuslog']);

	Route::post('getalltaskstatus', [TaskStatusModelController::class, 'getalltaskstatus']);
	Route::post('gettaskstatus', [TaskStatusModelController::class, 'gettaskstatus']);
	Route::post('addtaskstatus', [TaskStatusModelController::class, 'addtaskstatus']);
	Route::post('updatetaskstatus', [TaskStatusModelController::class, 'updatetaskstatus']);
	Route::post('deletetaskstatus', [TaskStatusModelController::class, 'deletetaskstatus']);

	Route::post('getalluser', [UserController::class, 'getalluser']);
	Route::post('getuser', [UserController::class, 'getuser']);
	Route::post('adduser', [UserController::class, 'adduser']);
	Route::post('updateuser', [UserController::class, 'updateuser']);
	Route::post('deleteuser', [UserController::class, 'deleteuser']);
});
