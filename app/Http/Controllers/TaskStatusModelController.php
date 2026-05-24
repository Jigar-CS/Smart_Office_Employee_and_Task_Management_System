<?php

namespace App\Http\Controllers;

use App\Models\TaskStatusModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaskStatusModelController extends Controller
{
    public function gettaskstatus(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_task_status,task_status_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $taskStatus = TaskStatusModel::where('task_status_id', $request->input('id'))->where('status', 1)->first();

        if (!$taskStatus) {
            return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
        }

        return response()->json(['status' => 200, 'data' => $taskStatus], 200);
    }

    public function getalltaskstatus(Request $request)
    {
        $taskStatuses = TaskStatusModel::where('status', 1)->orderBy('task_status_id', 'desc')->get();

        return response()->json(['status' => 200, 'count' => $taskStatuses->count(), 'data' => $taskStatuses], 200);
    }

    public function addtaskstatus(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:tbl_task_status,title',
            'description' => 'nullable|string',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $taskStatus = new TaskStatusModel();
            $taskStatus->title = $request->input('title');
            $taskStatus->description = $request->input('description');
            $taskStatus->status = 1;
            $result = $taskStatus->save();

            return response()->json(['status' => 200, 'data' => $result ? $taskStatus : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatetaskstatus(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_task_status,task_status_id',
            'title' => ['required', 'string', 'max:255', Rule::unique('tbl_task_status', 'title')->ignore($request->input('id'), 'task_status_id')],
            'description' => 'nullable|string',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $taskStatus = TaskStatusModel::find($request->input('id'));
            $taskStatus->title = $request->input('title');
            $taskStatus->description = $request->input('description');
            if ($request->has('status')) {
                $taskStatus->status = $request->input('status');
            }
            $taskStatus->save();

            return response()->json(['status' => 200, 'data' => $taskStatus], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletetaskstatus(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_task_status,task_status_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $taskStatus = TaskStatusModel::find($request->input('id'));
            $taskStatus->status = 0;
            $taskStatus->save();

            return response()->json(['status' => 200, 'data' => $taskStatus], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
