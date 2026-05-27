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
    private function isAdminUser(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $adminRoleId = \App\Models\RoleModel::where('name', 'Admin')->value('role_id');

        return $user->user_id === 1 || ($adminRoleId !== null && (int) $user->role_id === (int) $adminRoleId);
    }

    public function getuser(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_users,user_id',
        ], [
            'id.required' => 'User id is required.',
            'id.integer' => 'User id must be an integer.',
            'id.exists' => 'User not found.',
            'required' => 'The :attribute field is required.',
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
        $valid = Validator::make($request->all(), [
            'limit' => 'required|integer|min:1',
            'offset' => 'required|integer|min:0',
            'search' => 'nullable|string|max:255',
        ], [
            'limit.required' => 'Limit is required.',
            'limit.integer' => 'Limit must be an integer.',
            'limit.min' => 'Limit must be at least :min.',
            'offset.required' => 'Offset is required.',
            'offset.integer' => 'Offset must be an integer.',
            'offset.min' => 'Offset must be at least :min.',
            'search.string' => 'Search must be a string.',
            'search.max' => 'Search may not be greater than :max characters.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $usersQuery = User::where('status', 1)->orderBy('user_id', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $usersQuery->where('name', 'like', '%' . $search . '%');
        }

        $count = $usersQuery->count();
        $users = $usersQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        return response()->json(['status' => 200, 'count' => $count, 'data' => $users], 200);
    }

    public function adduser(Request $request)
    {
        $caller = $request->user();
        if (! $this->isAdminUser($caller)) {
            return response()->json(['status' => 403, 'error' => 'Action requires admin token.'], 403);
        }
        $valid = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tbl_users,email',
            'password' => ['required', 'string', Password::min(5)->mixedCase()->numbers()->symbols()],
            'role_id' => 'required|integer|exists:tbl_roles,role_id',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
            'mobile' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than :max characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email may not be greater than :max characters.',
            'email.unique' => 'Email has already been taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least :min characters.',
            'password.string' => 'Password must be a string.',
            'password.mixedCase' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one symbol.',
            'role_id.required' => 'Role is required.',
            'role_id.integer' => 'Role id must be an integer.',
            'role_id.exists' => 'Selected role is invalid.',
            'department_id.required' => 'Department is required.',
            'department_id.integer' => 'Department id must be an integer.',
            'department_id.exists' => 'Selected department is invalid.',
            'mobile.string' => 'Mobile must be a string.',
            'image.string' => 'Image must be a string.',
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
            'name' => 'nullable|string|max:255',
            'email' => [ 'nullable', 'email', 'max:255', Rule::unique('tbl_users', 'email')->ignore($request->input('id'), 'user_id') ],
            'password' => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role_id' => 'nullable|integer|exists:tbl_roles,role_id',
            'department_id' => 'nullable|integer|exists:tbl_departments,department_id',
            'mobile' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
        ], [
            'id.required' => 'User id is required.',
            'id.integer' => 'User id must be an integer.',
            'id.exists' => 'User not found.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than :max characters.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email may not be greater than :max characters.',
            'email.unique' => 'Email has already been taken.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least :min characters.',
            'password.mixedCase' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one symbol.',
            'role_id.integer' => 'Role id must be an integer.',
            'role_id.exists' => 'Selected role is invalid.',
            'department_id.integer' => 'Department id must be an integer.',
            'department_id.exists' => 'Selected department is invalid.',
            'mobile.string' => 'Mobile must be a string.',
            'image.string' => 'Image must be a string.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $user = User::find($request->input('id'));
            $caller = $request->user();
            $user->old_vallue = $user->toArray();
            if ($request->has('name')) {
                $user->name = $request->input('name');
            }
            if ($request->has('email')) {
                $user->email = $request->input('email');
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            if ($request->has('role_id')) {
                $user->role_id = $request->input('role_id');
            }
            if ($request->has('department_id')) {
                $user->department_id = $request->input('department_id');
            }
            if ($request->has('mobile')) {
                $user->mobile = $request->input('mobile');
            }
            if ($request->has('image')) {
                $user->image = $request->input('image');
            }
            if ($request->has('status')) {
                $user->status = $request->input('status');
            }
            $user->updated_by = $caller?->user_id;
            $user->updated_at = now();
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
        ], [
            'id.required' => 'User id is required.',
            'id.integer' => 'User id must be an integer.',
            'id.exists' => 'User not found.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $user = User::find($request->input('id'));
            $caller = $request->user();
            $user->old_vallue = $user->toArray();
            $user->status = 0;
            $user->updated_by = $caller?->user_id;
            $user->updated_at = now();
            $user->save();

            return response()->json(['status' => 200, 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
