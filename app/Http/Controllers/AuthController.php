<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Admin login: requires an Authorization header first, then validates ID/password.
    public function login(Request $request)
    {
        $authorizationHeader = $request->bearerToken();
        if (! $authorizationHeader) {
            return response()->json([
                'status' => 401,
                'error' => 'Authorization is required before login.',
            ], 401);
        }

        $authUser = $request->user();
        $adminRoleId = RoleModel::where('name', 'Admin')->value('role_id');
        $isAdminUser = $authUser && (
            $authUser->user_id === 1 || ($adminRoleId !== null && (int) $authUser->role_id === (int) $adminRoleId)
        );

        if (! $authUser || ! $isAdminUser) {
            return response()->json([
                'status' => 403,
                'error' => 'Only the admin token can access this login route.',
            ], 403);
        }

        $valid = validator($request->all(), [
            'id' => 'required|integer|exists:tbl_users,user_id',
            'password' => 'required|string',
        ], [
            'id.required' => 'User id is required.',
            'id.integer' => 'User id must be an integer.',
            'id.exists' => 'User not found.',
            'password.required' => 'Password is required.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $user = User::where('user_id', $request->input('id'))->where('status', 1)->first();
        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return response()->json(['status' => 401, 'error' => 'Invalid credentials.'], 401);
        }

        $isAdminUser = $user->user_id === 1 || ($adminRoleId !== null && (int) $user->role_id === (int) $adminRoleId);
        $tokenName = $isAdminUser ? 'admin-token' : 'api-token';

        $token = $user->createToken($tokenName)->plainTextToken;
        $user->last_login_at = now();
        $user->save();

        return response()->json([
            'status' => 200,
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'authorization' => 'Bearer ' . $token,
                'is_admin' => $isAdminUser,
                'user' => $user,
            ],
        ], 200);
    }

    // Public user login: email/password token generation.
    public function loginUser(Request $request)
    {
        $valid = validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'password.required' => 'Password is required.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $user = User::where('email', $request->input('email'))->where('status', 1)->first();
        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return response()->json(['status' => 401, 'error' => 'Invalid credentials.'], 401);
        }

        $adminRoleId = RoleModel::where('name', 'Admin')->value('role_id');
        $isAdminUser = $user->user_id === 1 || ($adminRoleId !== null && (int) $user->role_id === (int) $adminRoleId);
        $tokenName = $isAdminUser ? 'admin-token' : 'api-token';

        $token = $user->createToken($tokenName)->plainTextToken;
        $user->last_login_at = now();
        $user->save();

        return response()->json([
            'status' => 200,
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'authorization' => 'Bearer ' . $token,
                'is_admin' => $isAdminUser,
                'user' => $user,
            ],
        ], 200);
    }

    // Logout (revoke tokens) — user may call with his own token
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }
        return response()->json(['status' => 200, 'data' => 'Logged out.'], 200);
    }
}
