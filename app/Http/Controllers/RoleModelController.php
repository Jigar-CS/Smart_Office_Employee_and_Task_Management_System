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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than :max characters.',
            'description.string' => 'Description must be a string.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $caller = $request->user();
            $roleName = $request->input('name');
            
            // Check if role with same name exists
            $existingRole = RoleModel::where('name', $roleName)->first();
            
            if ($existingRole && $existingRole->status == 1) {
                // Role already exists and is active
                return response()->json(['status' => 400, 'error' => ['name' => ['Role name has already been taken.']]], 400);
            }
            
            if ($existingRole && $existingRole->status == 0) {
                // Role exists but is deleted - restore it
                $existingRole->description = $request->input('description');
                $existingRole->created_by = $caller?->user_id;
                $existingRole->created_at = now();
                $existingRole->status = 1;
                $existingRole->save();
                
                return response()->json(['status' => 200, 'data' => $existingRole], 200);
            }
            
            // Create new role
            $role = new RoleModel();
            $role->name = $roleName;
            $role->description = $request->input('description');
            $role->created_by = $caller?->user_id;
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
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'id.required' => 'Role id is required.',
            'id.integer' => 'Role id must be an integer.',
            'id.exists' => 'Role not found.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than :max characters.',
            'description.string' => 'Description must be a string.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $caller = $request->user();
            $role = RoleModel::find($request->input('id'));
            
            // If name is being updated, check for conflicts with active roles
            if ($request->has('name') && $request->input('name') !== $role->name) {
                $existingRole = RoleModel::where('name', $request->input('name'))
                    ->where('status', 1)
                    ->first();
                
                if ($existingRole) {
                    return response()->json(['status' => 400, 'error' => ['name' => ['Role name has already been taken.']]], 400);
                }
                
                $role->name = $request->input('name');
            }
            
            if ($request->has('description')) {
                $role->description = $request->input('description');
            }
            if ($request->has('status')) {
                $role->status = $request->input('status');
            }
            $role->updated_by = $caller?->user_id;
            $role->updated_at = now();
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
            $caller = $request->user();
            $role->status = 0;
            $role->updated_by = $caller?->user_id;
            $role->updated_at = now();
            $role->save();

            return response()->json(['status' => 200, 'data' => $role], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
