<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function getuser(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_users,user_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $user = User::where('user_id', $request->input('id'))->where('status', 1)->first();

        if (!$user) {
            return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
        }

        return response()->json(['status' => 200, 'data' => $user], 200);
    }

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
            'password' => ['required', 'string', Password::min(5)->mixedCase()->numbers()->symbols()],
            'role_id' => 'required|integer|exists:tbl_roles,role_id',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
            'mobile' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
        ], [
            'password.min' => 'Password must be at least 5 characters.',
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
            $user->mobile = $request->input('mobile');
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
            'email' => ['required', 'email', 'max:255', Rule::unique('tbl_users', 'email')->ignore($request->input('id'), 'user_id')],
            'password' => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role_id' => 'required|integer|exists:tbl_roles,role_id',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
            'mobile' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
        ], [
            'password.min' => 'Password must be at least 5 characters.',
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
            $user->mobile = $request->input('mobile');
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
