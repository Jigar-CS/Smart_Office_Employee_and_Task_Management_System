<?php

namespace App\Http\Middleware;

use App\Models\TaskAssignmentModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskAssignedMiddleware
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

        $taskId = $request->input('id');
        if (! $taskId) {
            return response()->json([
                'status' => 400,
                'error' => ['id' => ['Task id is required.']],
            ], 400);
        }

        $assigned = TaskAssignmentModel::where('task_id', $taskId)
            ->where('user_id', $user->user_id)
            ->where('status', 1)
            ->exists();

        if (! $assigned) {
            return response()->json([
                'status' => 403,
                'error' => 'This task is not assigned to you.',
            ], 403);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}