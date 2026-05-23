<?php

namespace App\Http\Controllers;

use App\TaskStatusModel;
use Illuminate\Http\Request;
use Validator;

class TaskStatusController extends Controller
{
    public function addstatus(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'key' => 'required|unique:task_statuses,key',
            'label' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $status = new TaskStatusModel();
        $status->key = $request->input('key');
        $status->label = $request->input('label');
        $status->status = $request->input('status', 1);

        try {
            $result = $status->save();
            if ($result) {
                return response()->json(['status' => 200, 'data' => $status], 200);
            }

            return response()->json(['status' => 400, 'error' => 'Save returned false.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function getallstatus(Request $request)
    {
        $status = new TaskStatusModel();
        $data = [];

        if ($request->has('offset') && $request->filled('offset')) {
            $data['offset'] = $request->input('offset');
        }

        if ($request->has('limit') && $request->filled('limit')) {
            $data['limit'] = $request->input('limit');
        }

        if ($request->has('search') && $request->filled('search')) {
            $data['search'] = $request->input('search');
        }
        if ($request->has('is_admin') && $request->filled('is_admin')) {
            $data['is_admin'] = $request->input('is_admin');
        }

        $rows = $status->getallstatus($data);

        if (isset($rows['total_count'])) {
            return response()->json(['status' => 200, 'count' => $rows['total_count'], 'data' => $rows['data']], 200);
        }

        return response()->json(['status' => 200, 'count' => count($rows), 'data' => $rows], 200);
    }

    public function updatestatus(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $status = new TaskStatusModel();
        $update = $request->except(['id']);

        try {
            $result = $status->where('id', $request->input('id'))->update($update);

            if ($result) {
                return response()->json(['status' => 200, 'data' => $result], 200);
            }

            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletestatus(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $result = TaskStatusModel::where('id', $request->input('id'))->update(['status' => 0]);
            if ($result) {
                return response()->json(['status' => 200, 'data' => $request->all()], 200);
            }
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
