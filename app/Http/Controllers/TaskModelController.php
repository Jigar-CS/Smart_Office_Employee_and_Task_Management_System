<?php

namespace App\Http\Controllers;

use App\Models\TaskModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaskModelController extends Controller
{
    public function getalltask(Request $request)
    {
        $tasks = TaskModel::where('status', 1)->orderBy('task_id', 'desc')->get();

        return response()->json(['status' => 200, 'count' => $tasks->count(), 'data' => $tasks], 200);
    }

    public function addtask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'priority_id' => 'required|integer|exists:tbl_priorities,priority_id',
            'task_status_id' => 'required|integer|exists:tbl_task_status,task_status_id',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
            'created_by' => 'nullable|integer|exists:tbl_users,user_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $task = new TaskModel();
            $task->title = $request->input('title');
            $task->description = $request->input('description');
            $task->start_date = $request->input('start_date');
            $task->due_date = $request->input('due_date');
            $task->priority_id = $request->input('priority_id');
            $task->task_status_id = $request->input('task_status_id');
            $task->department_id = $request->input('department_id');
            $task->created_by = $request->input('created_by');
            $task->status = 1;
            $result = $task->save();

            return response()->json(['status' => 200, 'data' => $result ? $task : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatetask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_tasks,task_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'priority_id' => 'required|integer|exists:tbl_priorities,priority_id',
            'task_status_id' => 'required|integer|exists:tbl_task_status,task_status_id',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $task = TaskModel::find($request->input('id'));
            $task->title = $request->input('title');
            $task->description = $request->input('description');
            $task->start_date = $request->input('start_date');
            $task->due_date = $request->input('due_date');
            $task->priority_id = $request->input('priority_id');
            $task->task_status_id = $request->input('task_status_id');
            $task->department_id = $request->input('department_id');
            $task->created_by = $request->input('created_by');
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
