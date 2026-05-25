<?php

namespace App\Http\Controllers;

use App\Models\PriorityModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PriorityModelController extends Controller
{
    public function getpriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_priorities,priority_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $priority = PriorityModel::where('priority_id', $request->input('id'))->where('status', 1)->first();

        if (!$priority) {
            return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
        }

        return response()->json(['status' => 200, 'data' => $priority], 200);
    }

    public function getallpriority(Request $request)
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

        $prioritiesQuery = PriorityModel::where('status', 1)->orderBy('priority_id', 'desc');
        $count = $prioritiesQuery->count();
        $priorities = $prioritiesQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        return response()->json(['status' => 200, 'count' => $count, 'data' => $priorities], 200);
    }

    public function addpriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:tbl_priorities,title',
            'level' => 'required|string|max:255',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $priority = new PriorityModel();
            $priority->title = $request->input('title');
            $priority->level = $request->input('level');
            $priority->status = 1;
            $result = $priority->save();

            return response()->json(['status' => 200, 'data' => $result ? $priority : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatepriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_priorities,priority_id',
            'title' => ['nullable', 'string', 'max:255', Rule::unique('tbl_priorities', 'title')->ignore($request->input('id'), 'priority_id')],
            'level' => 'nullable|string|max:255',
        ], [
            'id.required' => 'Priority id is required.',
            'id.integer' => 'Priority id must be an integer.',
            'id.exists' => 'Priority not found.',
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title may not be greater than :max characters.',
            'title.unique' => 'Priority title has already been taken.',
            'level.string' => 'Level must be a string.',
            'level.max' => 'Level may not be greater than :max characters.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $priority = PriorityModel::find($request->input('id'));
            if ($request->has('title')) {
                $priority->title = $request->input('title');
            }
            if ($request->has('level')) {
                $priority->level = $request->input('level');
            }
            if ($request->has('status')) {
                $priority->status = $request->input('status');
            }
            $priority->save();

            return response()->json(['status' => 200, 'data' => $priority], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletepriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_priorities,priority_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $priority = PriorityModel::find($request->input('id'));
            $priority->status = 0;
            $priority->save();

            return response()->json(['status' => 200, 'data' => $priority], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
