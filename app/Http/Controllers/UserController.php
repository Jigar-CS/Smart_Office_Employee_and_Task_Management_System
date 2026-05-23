<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getalluser(Request $request)
    {
        $users = User::where('status', 1)->orderBy('user_id', 'desc')->get();

        return response()->json(['status' => 200, 'count' => $users->count(), 'data' => $users], 200);
    }

    public function adduser(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tbl_users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|integer|exists:tbl_roles,role_id',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
            'phone' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->role_id = $request->input('role_id');
            $user->department_id = $request->input('department_id');
            $user->phone = $request->input('phone');
            $user->image = $request->input('image');
            $user->status = 1;
            $result = $user->save();

            return response()->json(['status' => 200, 'data' => $result ? $user : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updateuser(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_users,user_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|integer|exists:tbl_roles,role_id',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
            'phone' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $user = User::find($request->input('id'));
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->role_id = $request->input('role_id');
            $user->department_id = $request->input('department_id');
            $user->phone = $request->input('phone');
            $user->image = $request->input('image');
            if ($request->has('status')) {
                $user->status = $request->input('status');
            }
            $user->save();

            return response()->json(['status' => 200, 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deleteuser(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_users,user_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $user = User::find($request->input('id'));
            $user->status = 0;
            $user->save();

            return response()->json(['status' => 200, 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
