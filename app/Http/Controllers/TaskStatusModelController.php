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

        $taskStatusesQuery = TaskStatusModel::where('status', 1)->orderBy('task_status_id', 'desc');
        $count = $taskStatusesQuery->count();
        $taskStatuses = $taskStatusesQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        return response()->json(['status' => 200, 'count' => $count, 'data' => $taskStatuses], 200);
    }

    public function addtaskstatus(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $caller = $request->user();
            $title = $request->input('title');

            $existing = TaskStatusModel::where('title', $title)->first();

            if ($existing && $existing->status == 1) {
                return response()->json(['status' => 400, 'error' => ['title' => ['Task status title has already been taken.']]], 400);
            }

            if ($existing && $existing->status == 0) {
                // restore
                $existing->description = $request->input('description');
                $existing->created_by = $caller?->user_id;
                $existing->created_at = now();
                $existing->status = 1;
                $existing->save();

                return response()->json(['status' => 200, 'data' => $existing], 200);
            }

            $taskStatus = new TaskStatusModel();
            $taskStatus->title = $title;
            $taskStatus->description = $request->input('description');
            $taskStatus->created_by = $caller?->user_id;
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
            'title' => ['nullable', 'string', 'max:255', Rule::unique('tbl_task_status', 'title')->ignore($request->input('id'), 'task_status_id')],
            'description' => 'nullable|string',
        ], [
            'id.required' => 'Task status id is required.',
            'id.integer' => 'Task status id must be an integer.',
            'id.exists' => 'Task status not found.',
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title may not be greater than :max characters.',
            'title.unique' => 'Task status title has already been taken.',
            'description.string' => 'Description must be a string.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $caller = $request->user();
            $taskStatus = TaskStatusModel::find($request->input('id'));

            if ($request->has('title') && $request->input('title') !== $taskStatus->title) {
                $existing = TaskStatusModel::where('title', $request->input('title'))->where('status', 1)->first();
                if ($existing) {
                    return response()->json(['status' => 400, 'error' => ['title' => ['Task status title has already been taken.']]], 400);
                }
                $taskStatus->title = $request->input('title');
            }

            if ($request->has('description')) {
                $taskStatus->description = $request->input('description');
            }
            if ($request->has('status')) {
                $taskStatus->status = $request->input('status');
            }
            $taskStatus->updated_by = $caller?->user_id;
            $taskStatus->updated_at = now();
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
            $caller = $request->user();
            $taskStatus->status = 0;
            $taskStatus->updated_by = $caller?->user_id;
            $taskStatus->updated_at = now();
            $taskStatus->save();

            return response()->json(['status' => 200, 'data' => $taskStatus], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
