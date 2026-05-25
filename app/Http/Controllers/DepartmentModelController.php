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

        $departmentsQuery = DepartmentModel::where('status', 1)->orderBy('department_id', 'desc');
        $count = $departmentsQuery->count();
        $departments = $departmentsQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        return response()->json(['status' => 200, 'count' => $count, 'data' => $departments], 200);
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
            'name' => ['nullable', 'string', 'max:255', Rule::unique('tbl_departments', 'name')->ignore($request->input('id'), 'department_id')],
            'description' => 'nullable|string',
        ], [
            'id.required' => 'Department id is required.',
            'id.integer' => 'Department id must be an integer.',
            'id.exists' => 'Department not found.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than :max characters.',
            'name.unique' => 'Department name has already been taken.',
            'description.string' => 'Description must be a string.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $department = DepartmentModel::find($request->input('id'));
            if ($request->has('name')) {
                $department->name = $request->input('name');
            }
            if ($request->has('description')) {
                $department->description = $request->input('description');
            }
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
