<?php

namespace App\Http\Controllers;

use App\RoleModel;
use Illuminate\Http\Request;
use Validator;

class RoleController extends Controller
{
    public function addrole(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $role = new RoleModel();
        $role->name = $request->input('name');
        $role->description = $request->input('description');
        $role->status = $request->input('status', 1);

        try {
            $result = $role->save();
            if ($result) {
                return response()->json(['status' => 200, 'data' => $role], 200);
            }

            return response()->json(['status' => 400, 'error' => 'Save returned false.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function getallroles(Request $request)
    {
        $role = new RoleModel();
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

        if ($request->has('sort_column') && $request->filled('sort_column')) {
            $data['sort_column'] = $request->input('sort_column');
        }

        if ($request->has('sort_dir') && $request->filled('sort_dir')) {
            $data['sort_dir'] = $request->input('sort_dir');
        }

        if ($request->has('is_admin') && $request->filled('is_admin')) {
            $data['is_admin'] = $request->input('is_admin');
        }

        $roles = $role->getallroles($data);

        if (isset($roles['total_count'])) {
            return response()->json(['status' => 200, 'count' => $roles['total_count'], 'data' => $roles['data']], 200);
        }

        return response()->json(['status' => 200, 'count' => count($roles), 'data' => $roles], 200);
    }

    public function updaterole(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $role = new RoleModel();
        $update = $request->except(['id']);

        try {
            $result = $role->where('id', $request->input('id'))->update($update);

            if ($result) {
                return response()->json(['status' => 200, 'data' => $result], 200);
            }

            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deleterole(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $result = RoleModel::where('id', $request->input('id'))->update(['status' => 0]);

            if ($result) {
                return response()->json(['status' => 200, 'data' => $request->all()], 200);
            }

            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}

