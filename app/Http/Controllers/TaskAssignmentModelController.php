<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignmentModel;
use App\Models\TaskStatusLogModel;
use App\Models\TaskModel;
use App\Models\User;
use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskAssignmentModelController extends Controller
{
    public function gettaskassignment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_task_assignments,assignment_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $taskAssignment = TaskAssignmentModel::where('assignment_id', $request->input('id'))->where('status', 1)->first();

        if (!$taskAssignment) {
            return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
        }

        return response()->json(['status' => 200, 'data' => $taskAssignment], 200);
    }

    public function getalltaskassignment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'limit' => 'required|integer|min:1',
            'offset' => 'required|integer|min:0',
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

        $taskAssignmentsQuery = TaskAssignmentModel::where('status', 1)->orderBy('assignment_id', 'desc');
        $count = $taskAssignmentsQuery->count();
        $taskAssignments = $taskAssignmentsQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        return response()->json(['status' => 200, 'count' => $count, 'data' => $taskAssignments], 200);
    }

    public function addtaskassignment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'task_id' => 'required|integer|exists:tbl_tasks,task_id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:tbl_users,user_id',
            'assigned_at' => 'nullable|date',
            'remarks' => 'nullable|string',
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

            $task = TaskModel::find($request->input('task_id'));
            $createdAssignments = [];
            $userIds = $request->input('user_ids');
            foreach ($userIds as $uid) {
                $taskAssignment = new TaskAssignmentModel();
                $taskAssignment->task_id = $request->input('task_id');
                $taskAssignment->task_priority = $task->priority_id;
                $taskAssignment->user_id = $uid;
                $taskAssignment->assigned_by = $authUser->user_id;
                $taskAssignment->assigned_at = $request->input('assigned_at', now());
                $taskAssignment->status = 1;
                $taskAssignment->save();
                $createdAssignments[] = $taskAssignment;
            }

            // log assignment in task status log (records who assigned)
            $log = new TaskStatusLogModel();
            $log->task_id = $task->task_id;
            $log->from_status_id = $task->task_status_id ?? null;
            $log->to_status_id = $task->task_status_id ?? null;
            $log->assigned_by = $authUser->user_id;
            $log->changed_by = $authUser->user_id;
            $log->remarks = $request->input('remarks', 'Assigned to: ' . implode(',', $userIds));
            $log->department_id = $task->department_id;
            $log->save();

            return response()->json(['status' => 200, 'data' => $createdAssignments], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatetaskassignment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_task_assignments,assignment_id',
            'task_id' => 'nullable|integer|exists:tbl_tasks,task_id',
            'user_id' => 'nullable|integer|exists:tbl_users,user_id',
            'assigned_by' => 'nullable|integer|exists:tbl_users,user_id',
            'assigned_at' => 'nullable|date',
        ], [
            'id.required' => 'Assignment id is required.',
            'id.integer' => 'Assignment id must be an integer.',
            'id.exists' => 'Assignment not found.',
            'task_id.integer' => 'Task id must be an integer.',
            'task_id.exists' => 'Selected task is invalid.',
            'user_id.integer' => 'User id must be an integer.',
            'user_id.exists' => 'Selected user is invalid.',
            'assigned_by.integer' => 'Assigned by must be an integer.',
            'assigned_by.exists' => 'Selected assigner is invalid.',
            'assigned_at.date' => 'Assigned at must be a valid date.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $taskAssignment = TaskAssignmentModel::find($request->input('id'));
            if ($request->has('task_id')) {
                $taskAssignment->task_id = $request->input('task_id');
            }
            if ($request->has('user_id')) {
                $taskAssignment->user_id = $request->input('user_id');
            }
            if ($request->has('assigned_by')) {
                $taskAssignment->assigned_by = $request->input('assigned_by');
            }
            if ($request->has('assigned_at')) {
                $taskAssignment->assigned_at = $request->input('assigned_at');
            }
            if ($request->has('status')) {
                $taskAssignment->status = $request->input('status');
            }
            $taskAssignment->save();

            return response()->json(['status' => 200, 'data' => $taskAssignment], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletetaskassignment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_task_assignments,assignment_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $taskAssignment = TaskAssignmentModel::find($request->input('id'));
            $taskAssignment->status = 0;
            $taskAssignment->save();

            return response()->json(['status' => 200, 'data' => $taskAssignment], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
