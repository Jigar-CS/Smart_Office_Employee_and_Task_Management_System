<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function userlogin(Request $request)
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

        $tokenName = 'api-token';

        $token = $user->createToken($tokenName)->plainTextToken;
        $user->last_login_at = now();
        $user->save();

        return response()->json([
            'status' => 200,
            'data' => [
                'token' => $token,
               // 'token_type' => 'Bearer',
                //'authorization' => 'Bearer ' . $token,
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
