<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::POST('roles/getall', 'RoleController@getallroles');
Route::POST('roles/add', 'RoleController@addrole');
Route::POST('roles/update', 'RoleController@updaterole');
Route::POST('roles/delete', 'RoleController@deleterole');

Route::POST('statuses/getall', 'TaskStatusController@getallstatus');
Route::POST('statuses/add', 'TaskStatusController@addstatus');
Route::POST('statuses/update', 'TaskStatusController@updatestatus');
Route::POST('statuses/delete', 'TaskStatusController@deletestatus');

Route::POST('priorities/getall', 'PriorityController@getallpriority');
Route::POST('priorities/add', 'PriorityController@addpriority');
Route::POST('priorities/update', 'PriorityController@updatepriority');
Route::POST('priorities/delete', 'PriorityController@deletepriority');

Route::POST('departments/getall', 'DepartmentController@getalldepartment');
Route::POST('departments/add', 'DepartmentController@adddepartment');
Route::POST('departments/update', 'DepartmentController@updatedepartment');
Route::POST('departments/delete', 'DepartmentController@deletedepartment');

Route::POST('users/getall', 'UserController@getallusers');
Route::POST('users/add', 'UserController@adduser');
Route::POST('users/update', 'UserController@updateuser');
Route::POST('users/delete', 'UserController@deleteuser');

Route::POST('tasks/getall', 'TaskController@getalltasks');
Route::POST('tasks/add', 'TaskController@addtask');
Route::POST('tasks/update', 'TaskController@updatetask');
Route::POST('tasks/delete', 'TaskController@deletetask');

Route::POST('task-assignments/getall', 'TaskAssignmentController@getalltaskassignments');
Route::POST('task-assignments/add', 'TaskAssignmentController@addtaskassignment');
Route::POST('task-assignments/update', 'TaskAssignmentController@updatetaskassignment');
Route::POST('task-assignments/delete', 'TaskAssignmentController@deletetaskassignment');

Route::POST('documents/getall', 'DocumentController@getalldocuments');
Route::POST('documents/add', 'DocumentController@adddocument');
Route::POST('documents/update', 'DocumentController@updatedocument');
Route::POST('documents/delete', 'DocumentController@deletedocument');

Route::POST('role-permissions/getall', 'RolePermissionController@getallrolepermissions');
Route::POST('role-permissions/add', 'RolePermissionController@addrolepermission');
Route::POST('role-permissions/update', 'RolePermissionController@updaterolepermission');
Route::POST('role-permissions/delete', 'RolePermissionController@deleterolepermission');
