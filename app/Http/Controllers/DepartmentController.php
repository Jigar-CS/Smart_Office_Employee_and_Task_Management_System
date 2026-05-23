<?php

namespace App\Http\Controllers;

use App\DepartmentModel;
use Illuminate\Http\Request;
use Validator;

class DepartmentController extends Controller
{
    public function adddepartment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|unique:departments,name',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $department = new DepartmentModel();
        $department->name = $request->input('name');
        $department->description = $request->input('description');
        $department->status = $request->input('status', 1);

        try {
            $result = $department->save();
            if ($result) {
                return response()->json(['status' => 200, 'data' => $department], 200);
            }

            return response()->json(['status' => 400, 'error' => 'Save returned false.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function getalldepartment(Request $request)
    {
        $department = new DepartmentModel();
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

        $rows = $department->getalldepartment($data);

        if (isset($rows['total_count'])) {
            return response()->json(['status' => 200, 'count' => $rows['total_count'], 'data' => $rows['data']], 200);
        }

        return response()->json(['status' => 200, 'count' => count($rows), 'data' => $rows], 200);
    }

    public function updatedepartment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $department = new DepartmentModel();
        $update = $request->except(['id']);

        try {
            $result = $department->where('id', $request->input('id'))->update($update);

            if ($result) {
                return response()->json(['status' => 200, 'data' => $result], 200);
            }

            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletedepartment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $result = DepartmentModel::where('id', $request->input('id'))->update(['status' => 0]);
            if ($result) {
                return response()->json(['status' => 200, 'data' => $request->all()], 200);
            }
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
