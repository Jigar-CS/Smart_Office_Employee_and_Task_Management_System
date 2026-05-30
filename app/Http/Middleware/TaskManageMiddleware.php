<?php

namespace App\Http\Middleware;

use App\Models\RoleModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskManageMiddleware
{
    private function canManageTasks(?\App\Models\User $user): bool
    {
        if (! $user) {
            return false;
        }

        $role = RoleModel::find($user->role_id);
        $roleName = strtolower(trim((string) ($role?->name ?? '')));

        return $user->user_id === 1
            || in_array((int) $user->role_id, [1, 2], true)
            || ($roleName !== '' && (str_contains($roleName, 'admin') || str_contains($roleName, 'manager')));
    }

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'status' => 401,
                'error' => 'Unauthorized.',
            ], 401);
        }

        if (! $this->canManageTasks($user)) {
            return response()->json([
                'status' => 403,
                'error' => 'Only Admin or Manager can assign tasks.',
            ], 403);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}