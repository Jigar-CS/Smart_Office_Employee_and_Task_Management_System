<?php

namespace App\Http\Controllers;

use App\Models\PriorityModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PriorityModelController extends Controller
{
    public function getallpriority(Request $request)
    {
        $priorities = PriorityModel::where('status', 1)->orderBy('priority_id', 'desc')->get();

        return response()->json(['status' => 200, 'count' => $priorities->count(), 'data' => $priorities], 200);
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
            'title' => ['required', 'string', 'max:255', Rule::unique('tbl_priorities', 'title')->ignore($request->input('id'), 'priority_id')],
            'level' => 'required|string|max:255',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $priority = PriorityModel::find($request->input('id'));
            $priority->title = $request->input('title');
            $priority->level = $request->input('level');
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
