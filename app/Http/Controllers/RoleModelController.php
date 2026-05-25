<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleModelController extends Controller
{
    public function getrole(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_roles,role_id',
        ], [
            'id.required' => 'Role id is required.',
            'id.integer' => 'Role id must be an integer.',
            'id.exists' => 'Role not found.',
            'required' => 'The :attribute field is required.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $role = RoleModel::where('role_id', $request->input('id'))->where('status', 1)->first();

        if (!$role) {
            return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
        }

        return response()->json(['status' => 200, 'data' => $role], 200);
    }

    public function getallrole(Request $request)
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

        $rolesQuery = RoleModel::where('status', 1)->orderBy('role_id', 'desc');
        $count = $rolesQuery->count();
        $roles = $rolesQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        return response()->json(['status' => 200, 'count' => $count, 'data' => $roles], 200);
    }

    public function addrole(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tbl_roles,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than :max characters.',
            'name.unique' => 'Role name has already been taken.',
            'description.string' => 'Description must be a string.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $role = new RoleModel();
            $role->name = $request->input('name');
            $role->description = $request->input('description');
            $role->status = 1;
            $result = $role->save();

            return response()->json(['status' => 200, 'data' => $result ? $role : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updaterole(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_roles,role_id',
            'name' => ['nullable', 'string', 'max:255', Rule::unique('tbl_roles', 'name')->ignore($request->input('id'), 'role_id')],
            'description' => 'nullable|string',
        ], [
            'id.required' => 'Role id is required.',
            'id.integer' => 'Role id must be an integer.',
            'id.exists' => 'Role not found.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than :max characters.',
            'name.unique' => 'Role name has already been taken.',
            'description.string' => 'Description must be a string.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $role = RoleModel::find($request->input('id'));
            if ($request->has('name')) {
                $role->name = $request->input('name');
            }
            if ($request->has('description')) {
                $role->description = $request->input('description');
            }
            if ($request->has('status')) {
                $role->status = $request->input('status');
            }
            $role->save();

            return response()->json(['status' => 200, 'data' => $role], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deleterole(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_roles,role_id',
        ], [
            'id.required' => 'Role id is required.',
            'id.integer' => 'Role id must be an integer.',
            'id.exists' => 'Role not found.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $role = RoleModel::find($request->input('id'));
            $role->status = 0;
            $role->save();

            return response()->json(['status' => 200, 'data' => $role], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
