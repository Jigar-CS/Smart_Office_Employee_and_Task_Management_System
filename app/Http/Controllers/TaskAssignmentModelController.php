<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignmentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskAssignmentModelController extends Controller
{
    public function getalltaskassignment(Request $request)
    {
        $taskAssignments = TaskAssignmentModel::orderBy('assignment_id', 'desc')->get();

        return response()->json(['status' => 200, 'count' => $taskAssignments->count(), 'data' => $taskAssignments], 200);
    }

    public function addtaskassignment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'task_id' => 'required|integer|exists:tbl_tasks,task_id',
            'user_id' => 'required|integer|exists:tbl_users,user_id',
            'assigned_by' => 'required|integer|exists:tbl_users,user_id',
            'assigned_at' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $taskAssignment = new TaskAssignmentModel();
            $taskAssignment->task_id = $request->input('task_id');
            $taskAssignment->user_id = $request->input('user_id');
            $taskAssignment->assigned_by = $request->input('assigned_by');
            $taskAssignment->assigned_at = $request->input('assigned_at', now());
            $taskAssignment->status = 1;
            $result = $taskAssignment->save();

            return response()->json(['status' => 200, 'data' => $result ? $taskAssignment : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatetaskassignment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_task_assignments,assignment_id',
            'task_id' => 'required|integer|exists:tbl_tasks,task_id',
            'user_id' => 'required|integer|exists:tbl_users,user_id',
            'assigned_by' => 'required|integer|exists:tbl_users,user_id',
            'assigned_at' => 'nullable|date',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $taskAssignment = TaskAssignmentModel::find($request->input('id'));
            $taskAssignment->task_id = $request->input('task_id');
            $taskAssignment->user_id = $request->input('user_id');
            $taskAssignment->assigned_by = $request->input('assigned_by');
            $taskAssignment->assigned_at = $request->input('assigned_at', $taskAssignment->assigned_at);
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
            $taskAssignment->delete();

            return response()->json(['status' => 200, 'data' => $request->all()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
