<?php

namespace App\Http\Controllers;

use App\Models\TaskModel;
use App\Models\TaskAssignmentModel;
use App\Models\TaskStatusLogModel;
use App\Models\User;
use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskModelController extends Controller
{
    public function gettask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_tasks,task_id',
            'user_id' => 'required|integer|exists:tbl_users,user_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $task = TaskModel::where('task_id', $request->input('id'))->where('status', 1)->first();

        if (!$task) {
            return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
        }

        $user = User::find($request->input('user_id'));
        $role = RoleModel::find($user->role_id);

        $isPrivileged = $role && in_array($role->name, ['Admin', 'Manager']);

        if (!$isPrivileged) {
            $assigned = TaskAssignmentModel::where('task_id', $task->task_id)
                ->where('user_id', $user->user_id)
                ->where('status', 1)
                ->exists();

            if (!$assigned) {
                return response()->json(['status' => 403, 'error' => 'Not authorized to view this task.'], 403);
            }
        }

        return response()->json(['status' => 200, 'data' => $task], 200);
    }

    public function getalltask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'limit' => 'required|integer|min:1',
            'offset' => 'required|integer|min:0',
            'user_id' => 'required|integer|exists:tbl_users,user_id',
        ], [
            'limit.required' => 'Limit is required.',
            'limit.integer' => 'Limit must be an integer.',
            'limit.min' => 'Limit must be at least :min.',
            'offset.required' => 'Offset is required.',
            'offset.integer' => 'Offset must be an integer.',
            'offset.min' => 'Offset must be at least :min.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $user = User::find($request->input('user_id'));
        $role = RoleModel::find($user->role_id);
        $isPrivileged = $role && in_array($role->name, ['Admin', 'Manager']);

        $tasksQuery = TaskModel::where('status', 1)->orderBy('task_id', 'desc');

        if (!$isPrivileged) {
            $assignedTaskIds = TaskAssignmentModel::where('user_id', $user->user_id)->where('status', 1)->pluck('task_id');
            $tasksQuery = $tasksQuery->whereIn('task_id', $assignedTaskIds);
        }

        $count = $tasksQuery->count();
        $tasks = $tasksQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        return response()->json(['status' => 200, 'count' => $count, 'data' => $tasks], 200);
    }

    public function addtask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'priority_id' => 'required|integer|exists:tbl_priorities,priority_id',
            'task_status_id' => 'required|integer|exists:tbl_task_status,task_status_id',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
            'assigned_user_ids' => 'required|array|min:1',
            'assigned_user_ids.*' => 'integer|exists:tbl_users,user_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $authUser = $request->user();
            if (!$authUser) {
                return response()->json(['status' => 401, 'error' => 'Unauthorized.'], 401);
            }

            $authRole = RoleModel::find($authUser->role_id);
            $canAssign = $authRole && in_array($authRole->name, ['Admin', 'Manager']);
            if (!$canAssign) {
                return response()->json(['status' => 403, 'error' => 'Only Admin or Manager can assign tasks.'], 403);
            }

            $task = new TaskModel();
            $task->title = $request->input('title');
            $task->start_date = $request->input('start_date');
            $task->due_date = $request->input('due_date');
            $task->priority_id = $request->input('priority_id');
            $task->task_status_id = $request->input('task_status_id');
            $task->department_id = $request->input('department_id');
            $task->status = 1;
            $result = $task->save();

            if ($result && $request->has('assigned_user_ids')) {
                $assignedIds = $request->input('assigned_user_ids');
                foreach ($assignedIds as $uid) {
                    $assignment = new TaskAssignmentModel();
                    $assignment->task_id = $task->task_id;
                    $assignment->task_priority = $task->priority_id;
                    $assignment->user_id = $uid;
                    $assignment->assigned_by = $authUser->user_id;
                    $assignment->assigned_at = $request->input('assigned_at', now());
                    $assignment->status = 1;
                    $assignment->save();
                }

                // create a status log entry recording the assignment
                $log = new TaskStatusLogModel();
                $log->task_id = $task->task_id;
                $log->from_status_id = null;
                $log->to_status_id = $task->task_status_id;
                $log->assigned_by = $authUser->user_id;
                $log->changed_by = $authUser->user_id;
                $log->remarks = 'Assigned to: ' . implode(',', $assignedIds);
                $log->department_id = $task->department_id;
                $log->save();
            }

            return response()->json(['status' => 200, 'data' => $result ? $task : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatetask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_tasks,task_id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'priority_id' => 'nullable|integer|exists:tbl_priorities,priority_id',
            'task_status_id' => 'nullable|integer|exists:tbl_task_status,task_status_id',
            'department_id' => 'nullable|integer|exists:tbl_departments,department_id',
        ], [
            'id.required' => 'Task id is required.',
            'id.integer' => 'Task id must be an integer.',
            'id.exists' => 'Task not found.',
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title may not be greater than :max characters.',
            'description.string' => 'Description must be a string.',
            'start_date.date' => 'Start date must be a valid date.',
            'due_date.date' => 'Due date must be a valid date.',
            'priority_id.integer' => 'Priority id must be an integer.',
            'priority_id.exists' => 'Selected priority is invalid.',
            'task_status_id.integer' => 'Task status id must be an integer.',
            'task_status_id.exists' => 'Selected task status is invalid.',
            'department_id.integer' => 'Department id must be an integer.',
            'department_id.exists' => 'Selected department is invalid.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $task = TaskModel::find($request->input('id'));
            if ($request->has('title')) {
                $task->title = $request->input('title');
            }
            if ($request->has('description')) {
                $task->description = $request->input('description');
            }
            if ($request->has('start_date')) {
                $task->start_date = $request->input('start_date');
            }
            if ($request->has('due_date')) {
                $task->due_date = $request->input('due_date');
            }
            if ($request->has('priority_id')) {
                $task->priority_id = $request->input('priority_id');
            }
            if ($request->has('task_status_id')) {
                $task->task_status_id = $request->input('task_status_id');
            }
            if ($request->has('department_id')) {
                $task->department_id = $request->input('department_id');
            }
            if ($request->has('status')) {
                $task->status = $request->input('status');
            }
            $task->save();

            return response()->json(['status' => 200, 'data' => $task], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletetask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_tasks,task_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $task = TaskModel::find($request->input('id'));
            $task->status = 0;
            $task->save();

            return response()->json(['status' => 200, 'data' => $task], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
