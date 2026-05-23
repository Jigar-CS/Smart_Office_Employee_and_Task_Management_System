<?php

namespace App\Http\Controllers;

use App\RolePermissionModel;
use Illuminate\Http\Request;
use Validator;

class RolePermissionController extends Controller
{
    public function addrolepermission(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'role_id' => 'required|numeric',
            'permission_key' => 'required',
            'permission_name' => 'required',
        ]);

        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        $permission = new RolePermissionModel();
        $permission->role_id = $request->input('role_id');
        $permission->permission_key = $request->input('permission_key');
        $permission->permission_name = $request->input('permission_name');
        $permission->status = $request->input('status', 1);

        try {
            $result = $permission->save();
            if ($result) return response()->json(['status' => 200, 'data' => $permission], 200);
            return response()->json(['status' => 400, 'error' => 'Save returned false.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function getallrolepermissions(Request $request)
    {
        $data = [];
        if ($request->has('offset') && $request->filled('offset')) $data['offset'] = $request->input('offset');
        if ($request->has('limit') && $request->filled('limit')) $data['limit'] = $request->input('limit');
        if ($request->has('role_id') && $request->filled('role_id')) $data['role_id'] = $request->input('role_id');
        if ($request->has('is_admin') && $request->filled('is_admin')) $data['is_admin'] = $request->input('is_admin');
        if ($request->has('is_admin') && $request->filled('is_admin')) $data['is_admin'] = $request->input('is_admin');

        $permission = new RolePermissionModel();
        $rows = $permission->getallrolepermissions($data);
        if (isset($rows['total_count'])) return response()->json(['status' => 200, 'count' => $rows['total_count'], 'data' => $rows['data']], 200);
        return response()->json(['status' => 200, 'count' => count($rows), 'data' => $rows], 200);
    }

    public function updaterolepermission(Request $request)
    {
        $valid = Validator::make($request->all(), ['id' => 'required|numeric']);
        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        $update = $request->except(['id']);
        try {
            $result = RolePermissionModel::where('id', $request->input('id'))->update($update);
            if ($result) return response()->json(['status' => 200, 'data' => $result], 200);
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deleterolepermission(Request $request)
    {
        $valid = Validator::make($request->all(), ['id' => 'required|numeric']);
        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        try {
            $result = RolePermissionModel::where('id', $request->input('id'))->update(['status' => 0]);
            if ($result) return response()->json(['status' => 200, 'data' => $request->all()], 200);
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
