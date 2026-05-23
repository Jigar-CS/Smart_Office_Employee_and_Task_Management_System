<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
    public function adduser(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'role_id' => 'required|numeric',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $user = new User();
        $user->role_id = $request->input('role_id');
        $user->department_id = $request->input('department_id');
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->image = $request->input('image');
        $user->password = Hash::make($request->input('password'));
        $user->status = $request->input('status', 1);

        try {
            $result = $user->save();
            if ($result) return response()->json(['status' => 200, 'data' => $user], 200);
            return response()->json(['status' => 400, 'error' => 'Save returned false.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function getallusers(Request $request)
    {
        $data = [];
        if ($request->has('offset') && $request->filled('offset')) $data['offset'] = $request->input('offset');
        if ($request->has('limit') && $request->filled('limit')) $data['limit'] = $request->input('limit');
        if ($request->has('search') && $request->filled('search')) $data['search'] = $request->input('search');
        if ($request->has('is_admin') && $request->filled('is_admin')) $data['is_admin'] = $request->input('is_admin');

        $query = User::query()->leftJoin('roles as r', 'r.id', '=', 'users.role_id')->leftJoin('departments as d', 'd.id', '=', 'users.department_id')->select('users.*', 'r.name as role_name', 'd.name as department_name');
        if (isset($data['search'])) {
            $query->where(function ($q) use ($data) {
                $q->where('users.name', 'like', '%' . $data['search'] . '%')
                  ->orWhere('users.email', 'like', '%' . $data['search'] . '%');
            });
        }

        if (!(array_key_exists('is_admin', $data) && isset($data['is_admin']) && $data['is_admin'] == 1)) {
            $query->where('users.status', 1);
        }

        $query->orderBy('users.id', 'desc');

        if (isset($data['offset']) && isset($data['limit'])) {
            $total_count = $query->count();
            $result = $query->offset($data['offset'])->limit($data['limit'])->get();
            return response()->json(['status' => 200, 'count' => $total_count, 'data' => $result], 200);
        }

        $result = $query->get();
        return response()->json(['status' => 200, 'count' => count($result), 'data' => $result], 200);
    }

    public function updateuser(Request $request)
    {
        $valid = Validator::make($request->all(), ['id' => 'required|numeric']);
        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        $update = $request->except(['id']);
        if (isset($update['password'])) $update['password'] = Hash::make($update['password']);

        try {
            $result = User::where('id', $request->input('id'))->update($update);
            if ($result) return response()->json(['status' => 200, 'data' => $result], 200);
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deleteuser(Request $request)
    {
        $valid = Validator::make($request->all(), ['id' => 'required|numeric']);
        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        try {
            $user = User::where('id', $request->input('id'))->first();
            if ($user) {
                $user->delete();
                return response()->json(['status' => 200, 'data' => $request->all()], 200);
            }
            return response()->json(['status' => 400, 'error' => 'No rows found to delete.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
