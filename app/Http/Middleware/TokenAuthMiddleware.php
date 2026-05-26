<?php

namespace App\Http\Middleware;

use App\Models\RoleModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenAuthMiddleware
{
    private function isAdminUser(?\App\Models\User $user): bool
    {
        if (! $user) {
            return false;
        }

        $adminRoleId = RoleModel::where('name', 'Admin')->value('role_id');

        return $user->user_id === 1 || ($adminRoleId !== null && (int) $user->role_id === (int) $adminRoleId);
    }

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'status' => 401,
                'error' => 'Authorization is required!!!',
            ], 401);
        }

        if (! $this->isAdminUser($user)) {
            return response()->json([
                'status' => 403,
                'error' => 'Admin token is required.',
            ], 403);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}