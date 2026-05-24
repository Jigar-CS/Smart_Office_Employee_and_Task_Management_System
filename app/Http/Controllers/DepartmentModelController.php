<?php

namespace App\Http\Controllers;

use App\Models\DepartmentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DepartmentModelController extends Controller
{
    public function getdepartment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_departments,department_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $department = DepartmentModel::where('department_id', $request->input('id'))->where('status', 1)->first();

        if (!$department) {
            return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
        }

        return response()->json(['status' => 200, 'data' => $department], 200);
    }

    public function getalldepartment(Request $request)
    {
        $departments = DepartmentModel::where('status', 1)->orderBy('department_id', 'desc')->get();

        return response()->json(['status' => 200, 'count' => $departments->count(), 'data' => $departments], 200);
    }

    public function adddepartment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tbl_departments,name',
            'description' => 'nullable|string',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $department = new DepartmentModel();
            $department->name = $request->input('name');
            $department->description = $request->input('description');
            $department->status = 1;
            $result = $department->save();

            return response()->json(['status' => 200, 'data' => $result ? $department : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatedepartment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_departments,department_id',
            'name' => ['required', 'string', 'max:255', Rule::unique('tbl_departments', 'name')->ignore($request->input('id'), 'department_id')],
            'description' => 'nullable|string',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $department = DepartmentModel::find($request->input('id'));
            $department->name = $request->input('name');
            $department->description = $request->input('description');
            if ($request->has('status')) {
                $department->status = $request->input('status');
            }
            $department->save();

            return response()->json(['status' => 200, 'data' => $department], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletedepartment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_departments,department_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $department = DepartmentModel::find($request->input('id'));
            $department->status = 0;
            $department->save();

            return response()->json(['status' => 200, 'data' => $department], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
