<?php

namespace App\Http\Middleware;

use App\Models\RoleModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskManageMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'status' => 401,
                'error' => 'Unauthorized.',
            ], 401);
        }

        $role = RoleModel::find($user->role_id);

        if (! $role || ! in_array($role->name, ['Admin', 'Manager'], true)) {
            return response()->json([
                'status' => 403,
                'error' => 'Only Admin or Manager can assign tasks.',
            ], 403);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}