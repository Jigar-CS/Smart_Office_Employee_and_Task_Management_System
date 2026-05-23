<?php

namespace App\Http\Controllers;

use App\Models\TaskStatusLogModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskStatusLogModelController extends Controller
{
    public function getalltaskstatuslog(Request $request)
    {
        $taskStatusLogs = TaskStatusLogModel::orderBy('status_log_id', 'desc')->get();

        return response()->json(['status' => 200, 'count' => $taskStatusLogs->count(), 'data' => $taskStatusLogs], 200);
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
            $taskStatusLog = TaskStatusLogModel::find($request->input('id'));
            $taskStatusLog->task_id = $request->input('task_id');
            $taskStatusLog->from_status_id = $request->input('from_status_id');
            $taskStatusLog->to_status_id = $request->input('to_status_id');
            $taskStatusLog->changed_by = $request->input('changed_by');
            $taskStatusLog->remarks = $request->input('remarks');
            $taskStatusLog->department_id = $request->input('department_id');
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
