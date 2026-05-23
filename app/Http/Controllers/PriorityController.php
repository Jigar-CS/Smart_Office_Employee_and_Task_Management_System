<?php

namespace App\Http\Controllers;

use App\PriorityModel;
use Illuminate\Http\Request;
use Validator;

class PriorityController extends Controller
{
    public function addpriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'key' => 'required|unique:priorities,key',
            'label' => 'required',
            'level' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $priority = new PriorityModel();
        $priority->key = $request->input('key');
        $priority->label = $request->input('label');
        $priority->level = $request->input('level');
        $priority->status = $request->input('status', 1);

        try {
            $result = $priority->save();
            if ($result) {
                return response()->json(['status' => 200, 'data' => $priority], 200);
            }

            return response()->json(['status' => 400, 'error' => 'Save returned false.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function getallpriority(Request $request)
    {
        $priority = new PriorityModel();
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

        $rows = $priority->getallpriority($data);

        if (isset($rows['total_count'])) {
            return response()->json(['status' => 200, 'count' => $rows['total_count'], 'data' => $rows['data']], 200);
        }

        return response()->json(['status' => 200, 'count' => count($rows), 'data' => $rows], 200);
    }

    public function updatepriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $priority = new PriorityModel();
        $update = $request->except(['id']);

        try {
            $result = $priority->where('id', $request->input('id'))->update($update);

            if ($result) {
                return response()->json(['status' => 200, 'data' => $result], 200);
            }

            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletepriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $result = PriorityModel::where('id', $request->input('id'))->update(['status' => 0]);
            if ($result) {
                return response()->json(['status' => 200, 'data' => $request->all()], 200);
            }
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
