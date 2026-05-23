<?php

namespace App\Http\Controllers;

use App\TaskModel;
use Illuminate\Http\Request;
use Validator;

class TaskController extends Controller
{
    public function addtask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'task_code' => 'required|unique:tasks,task_code',
            'created_by' => 'required|numeric',
            'title' => 'required',
        ]);

        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        $task = new TaskModel();
        $task->task_code = $request->input('task_code');
        $task->created_by = $request->input('created_by');
        $task->assigned_to = $request->input('assigned_to');
        $task->department_id = $request->input('department_id');
        $task->task_status_id = $request->input('task_status_id');
        $task->priority_id = $request->input('priority_id');
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->due_date = $request->input('due_date');
        $task->status = $request->input('status', 1);

        try {
            $result = $task->save();
            if ($result) return response()->json(['status' => 200, 'data' => $task], 200);
            return response()->json(['status' => 400, 'error' => 'Save returned false.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function getalltasks(Request $request)
    {
        $data = [];
        if ($request->has('offset') && $request->filled('offset')) $data['offset'] = $request->input('offset');
        if ($request->has('limit') && $request->filled('limit')) $data['limit'] = $request->input('limit');
        if ($request->has('search') && $request->filled('search')) $data['search'] = $request->input('search');
        if ($request->has('task_status_id') && $request->filled('task_status_id')) $data['task_status_id'] = $request->input('task_status_id');
        if ($request->has('assigned_to') && $request->filled('assigned_to')) $data['assigned_to'] = $request->input('assigned_to');
        if ($request->has('is_admin') && $request->filled('is_admin')) $data['is_admin'] = $request->input('is_admin');

        $task = new TaskModel();
        $rows = $task->getalltasks($data);
        if (isset($rows['total_count'])) return response()->json(['status' => 200, 'count' => $rows['total_count'], 'data' => $rows['data']], 200);
        return response()->json(['status' => 200, 'count' => count($rows), 'data' => $rows], 200);
    }

    public function updatetask(Request $request)
    {
        $valid = Validator::make($request->all(), ['id' => 'required|numeric']);
        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        $update = $request->except(['id']);
        try {
            $result = TaskModel::where('id', $request->input('id'))->update($update);
            if ($result) return response()->json(['status' => 200, 'data' => $result], 200);
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletetask(Request $request)
    {
        $valid = Validator::make($request->all(), ['id' => 'required|numeric']);
        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        try {
            $result = TaskModel::where('id', $request->input('id'))->update(['status' => 0]);
            if ($result) return response()->json(['status' => 200, 'data' => $request->all()], 200);
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
