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

        $role = RoleModel::find($user->role_id);
        $roleName = strtolower(trim((string) ($role?->name ?? '')));

        if ($user->user_id === 1) {
            return true;
        }

        if (in_array((int) $user->role_id, [1, 2], true)) {
            return true;
        }

        return $roleName !== '' && (str_contains($roleName, 'admin') || str_contains($roleName, 'manager'));
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