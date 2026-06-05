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

        $role = \App\Models\RoleModel::find($user->role_id);
        $roleName = strtolower(trim((string) ($role?->name ?? '')));

        return $user->user_id === 1
            || in_array((int) $user->role_id, [1, 2], true)
            || ($roleName !== '' && (str_contains($roleName, 'admin') || str_contains($roleName, 'manager')));
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
            'limit' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
            'search' => 'nullable|string|max:255',
            'department_id' => 'nullable|integer|exists:tbl_departments,department_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $usersQuery = User::query()
            ->leftJoin('tbl_roles as r', 'tbl_users.role_id', '=',  'r.role_id')
            ->leftJoin('tbl_departments as d', 'tbl_users.department_id', '=', 'd.department_id')
            ->where('tbl_users.status', 1)
            ->select('tbl_users.*','r.name as role_name', 'd.name as department_name')
            ->orderBy('tbl_users.user_id', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $usersQuery->where(function($q) use ($search) {
                $q->where('tbl_users.name', 'like', '%' . $search . '%')
                  ->orWhere('tbl_users.email', 'like', '%' . $search . '%')
                  ->orWhere('tbl_users.mobile', 'like', '%' . $search . '%')
                  ->orWhere('r.name', 'like', '%' . $search . '%')
                  ->orWhere('d.name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('department_id')) {
            $usersQuery->where('tbl_users.department_id', $request->input('department_id'));
        }
                        
        // Pagination: prefer page-based, fallback to offset/limit. Default 5 per page.
        $limit = $request->input('limit', 5);
        $page = $request->input('page');
        $offset = $request->input('offset');

        if ($page) {
            $offset = ($page - 1) * $limit;
        }

        $offset = $offset ?? 0;

        $count = $usersQuery->count();
        $users = $usersQuery->skip($offset)->take($limit)->get();

        // Provide pagination metadata
        $meta = [
            'total' => $count,
            'per_page' => (int) $limit,
            'current_offset' => (int) $offset,
            'current_page' => $page ? (int) $page : (int) floor($offset / $limit) + 1,
            'has_next' => ($offset + $limit) < $count,
        ];

        return response()->json(['status' => 200, 'count' => $count, 'data' => $users, 'meta' => $meta], 200);

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
            // relax complexity for admin-created accounts (min length enforced)
            'password' => ['required', 'string', Password::min(5)],
            'role_id' => 'required|integer|exists:tbl_roles,role_id',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
            'mobile' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|max:2048',
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
            if ($request->hasFile('image_file')) {
                $user->image = $request->file('image_file')->store('profile-images', 'public');
            } else {
                $user->image = $request->input('image');
            }
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
              'image_file' => 'nullable|image|max:2048',
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
            if ($request->hasFile('image_file')) {
                $user->image = $request->file('image_file')->store('profile-images', 'public');
            } elseif ($request->has('image')) {
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

    public function updateprofile(Request $request)
    {
        $caller = $request->user();

        if (! $caller) {
            return response()->json(['status' => 401, 'error' => 'Unauthorized.'], 401);
        }

        $valid = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('tbl_users', 'email')->ignore($caller->user_id, 'user_id'),
            ],
            'mobile' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than :max characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email may not be greater than :max characters.',
            'email.unique' => 'Email has already been taken.',
            'mobile.string' => 'Mobile must be a string.',
            'image.string' => 'Image must be a string.',
            'image_file.image' => 'Profile image must be an image file.',
            'image_file.max' => 'Profile image may not be greater than :max kilobytes.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $user = User::find($caller->user_id);

            if (! $user) {
                return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
            }

            $user->old_vallue = $user->toArray();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->mobile = $request->input('mobile');

            if ($request->hasFile('image_file')) {
                $user->image = $request->file('image_file')->store('profile-images', 'public');
            } elseif ($request->filled('image')) {
                $user->image = $request->input('image');
            }

            $user->updated_by = $caller->user_id;
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
