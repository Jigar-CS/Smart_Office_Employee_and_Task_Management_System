<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignmentModel;
use App\Models\TaskModel;
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
            'user_id' => 'nullable|integer|exists:tbl_users,user_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $tasksQuery = TaskModel::query()
            ->leftJoin('tbl_departments as d', 'tbl_tasks.department_id', '=', 'd.department_id')
            ->leftJoin('tbl_users as created_by_user', 'tbl_tasks.created_by', '=', 'created_by_user.user_id')
            ->leftJoin('tbl_task_status as ts', 'tbl_tasks.task_status_id', '=', 'ts.task_status_id')
            ->select(
                'tbl_tasks.*',
                'd.name as department_name',
                'created_by_user.name as created_by_name',
                'ts.title as task_status_name'
            )
            ->where('tbl_tasks.status', 1)
            ->orderBy('tbl_tasks.task_id', 'desc');

        if ($request->filled('user_id')) {
            $tasksQuery->whereExists(function ($query) use ($request) {
                $query->selectRaw('1')
                    ->from('tbl_task_assignments as ta')
                    ->whereColumn('ta.task_id', 'tbl_tasks.task_id')
                    ->where('ta.status', 1)
                    ->where('ta.user_id', $request->input('user_id'));
            });
        }

        $count = $tasksQuery->count();
        $tasks = $tasksQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        foreach ($tasks as $task) {
            $task->assigned_users = TaskAssignmentModel::query()
                ->leftJoin('tbl_users as u', 'tbl_task_assignments.user_id', '=', 'u.user_id')
                ->where('tbl_task_assignments.task_id', $task->task_id)
                ->where('tbl_task_assignments.status', 1)
                ->select('u.user_id', 'u.name', 'u.email', 'u.mobile', 'u.image')
                ->get();
        }

        return response()->json(['status' => 200, 'count' => $count, 'data' => $tasks], 200);
    }

    public function addtaskstatuslog(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'task_id' => 'required|integer|exists:tbl_tasks,task_id',
            'assigned_by' => 'nullable|integer|exists:tbl_users,user_id',
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
            $taskStatusLog->assigned_by = $request->input('assigned_by');
            $taskStatusLog->from_status_id = $request->input('from_status_id');
            $taskStatusLog->to_status_id = $request->input('to_status_id');
            $taskStatusLog->changed_by = $request->input('changed_by');
            $taskStatusLog->remarks = $request->input('remarks');
            $taskStatusLog->department_id = $request->input('department_id');
            $taskStatusLog->status = 1;
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
            'assigned_by' => 'nullable|integer|exists:tbl_users,user_id',
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
            if ($request->has('assigned_by')) {
                $taskStatusLog->assigned_by = $request->input('assigned_by');
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
            $caller = $request->user();
            $taskStatusLog->status = 0;
            $taskStatusLog->updated_by = $caller?->user_id;
            $taskStatusLog->updated_at = now();
            $taskStatusLog->save();

            return response()->json(['status' => 200, 'data' => $taskStatusLog], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
