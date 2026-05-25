<?php

namespace App\Http\Controllers;

use App\Models\TaskStatusLogModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskStatusLogModelController extends Controller
{
    public function getalltaskstatuslog(Request $request)
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

        $taskStatusLogsQuery = TaskStatusLogModel::orderBy('status_log_id', 'desc');
        $count = $taskStatusLogsQuery->count();
        $taskStatusLogs = $taskStatusLogsQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        return response()->json(['status' => 200, 'count' => $count, 'data' => $taskStatusLogs], 200);
    }

    public function addtaskstatuslog(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'task_id' => 'required|integer|exists:tbl_tasks,task_id',
            'from_status_id' => 'nullable|integer|exists:tbl_task_status,task_status_id',
            'to_status_id' => 'nullable|integer|exists:tbl_task_status,task_status_id',
            'changed_by' => 'required|integer|exists:tbl_users,user_id',
            'remarks' => 'nullable|string',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $taskStatusLog = new TaskStatusLogModel();
            $taskStatusLog->task_id = $request->input('task_id');
            $taskStatusLog->from_status_id = $request->input('from_status_id');
            $taskStatusLog->to_status_id = $request->input('to_status_id');
            $taskStatusLog->changed_by = $request->input('changed_by');
            $taskStatusLog->remarks = $request->input('remarks');
            $taskStatusLog->department_id = $request->input('department_id');
            $result = $taskStatusLog->save();

            return response()->json(['status' => 200, 'data' => $result ? $taskStatusLog : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatetaskstatuslog(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_task_status_log,status_log_id',
            'task_id' => 'nullable|integer|exists:tbl_tasks,task_id',
            'from_status_id' => 'nullable|integer|exists:tbl_task_status,task_status_id',
            'to_status_id' => 'nullable|integer|exists:tbl_task_status,task_status_id',
            'changed_by' => 'nullable|integer|exists:tbl_users,user_id',
            'remarks' => 'nullable|string',
            'department_id' => 'nullable|integer|exists:tbl_departments,department_id',
        ], [
            'id.required' => 'Status log id is required.',
            'id.integer' => 'Status log id must be an integer.',
            'id.exists' => 'Status log not found.',
            'task_id.integer' => 'Task id must be an integer.',
            'task_id.exists' => 'Selected task is invalid.',
            'from_status_id.integer' => 'From status id must be an integer.',
            'from_status_id.exists' => 'Selected from status is invalid.',
            'to_status_id.integer' => 'To status id must be an integer.',
            'to_status_id.exists' => 'Selected to status is invalid.',
            'changed_by.integer' => 'Changed by must be an integer.',
            'changed_by.exists' => 'Selected user is invalid.',
            'remarks.string' => 'Remarks must be a string.',
            'department_id.integer' => 'Department id must be an integer.',
            'department_id.exists' => 'Selected department is invalid.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $taskStatusLog = TaskStatusLogModel::find($request->input('id'));
            if ($request->has('task_id')) {
                $taskStatusLog->task_id = $request->input('task_id');
            }
            if ($request->has('from_status_id')) {
                $taskStatusLog->from_status_id = $request->input('from_status_id');
            }
            if ($request->has('to_status_id')) {
                $taskStatusLog->to_status_id = $request->input('to_status_id');
            }
            if ($request->has('changed_by')) {
                $taskStatusLog->changed_by = $request->input('changed_by');
            }
            if ($request->has('remarks')) {
                $taskStatusLog->remarks = $request->input('remarks');
            }
            if ($request->has('department_id')) {
                $taskStatusLog->department_id = $request->input('department_id');
            }
            $taskStatusLog->save();

            return response()->json(['status' => 200, 'data' => $taskStatusLog], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletetaskstatuslog(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_task_status_log,status_log_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $taskStatusLog = TaskStatusLogModel::find($request->input('id'));
            $taskStatusLog->delete();

            return response()->json(['status' => 200, 'data' => $request->all()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
