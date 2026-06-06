<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use App\Models\TaskAssignmentModel;
use App\Models\TaskModel;
use App\Models\TaskStatusLogModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskModelController extends Controller
{
    private function canViewAllTasks(?\App\Models\User $user): bool
    {
        if (! $user) {
            return false;
        }

        return in_array((int) $user->role_id, [1, 2], true);
    }

    private function canManageTasks(?\App\Models\User $user): bool
    {
        if (! $user) {
            return false;
        }

        if (in_array((int) $user->role_id, [1, 2], true)) {
            return true;
        }

        $role = RoleModel::find($user->role_id);

        return $role && in_array($role->name, ['Admin', 'Manager'], true);
    }

    public function gettask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_tasks,task_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['status' => 401, 'error' => 'Unauthorized.'], 401);
        }

        $task = TaskModel::where('task_id', $request->input('id'))->where('status', 1)->first();

        if (! $task) {
            return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
        }

        if ($this->canViewAllTasks($authUser)) {
            return response()->json(['status' => 200, 'data' => $task], 200);
        }

        $assigned = TaskAssignmentModel::where('task_id', $task->task_id)
            ->where('user_id', $authUser->user_id)
            ->where('status', 1)
            ->exists();

        if (! $assigned) {
            return response()->json(['status' => 403, 'error' => 'This task is not assigned to you.'], 403);
        }

        return response()->json(['status' => 200, 'data' => $task], 200);
    }

    public function getalltask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
            'search' => 'nullable|string|max:255',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ], [
            'limit.required' => 'Limit is required.',
            'limit.integer' => 'Limit must be an integer.',
            'limit.min' => 'Limit must be at least :min.',
            'offset.required' => 'Offset is required.',
            'offset.integer' => 'Offset must be an integer.',
            'offset.min' => 'Offset must be at least :min.',
            'search.string' => 'Search must be a string.',
            'search.max' => 'Search may not be greater than :max characters.',
            'from_date.date' => 'From date must be a valid date.',
            'to_date.date' => 'To date must be a valid date.',
            'to_date.after_or_equal' => 'To date must be the same as or later than from date.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['status' => 401, 'error' => 'Unauthorized.'], 401);
        }

        $tasksQuery = TaskModel::query()
            ->leftJoin('tbl_priorities as p', 'tbl_tasks.priority_id', '=', 'p.priority_id')
            ->leftJoin('tbl_task_status as ts', 'tbl_tasks.task_status_id', '=', 'ts.task_status_id')
            ->leftJoin('tbl_departments as d', 'tbl_tasks.department_id', '=', 'd.department_id')
            ->where('tbl_tasks.status', 1)
            ->select('tbl_tasks.*', 'p.title as priority_title', 'ts.title as task_status_title', 'd.name as department_name')
            ->orderBy('tbl_tasks.task_id', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            if (preg_match('/<\s*\/?[a-z][a-z0-9]*\b[^>]*>/i', $search) || preg_match('/\b(script|onload|onerror|onmouseover|onclick)\b/i', $search)) {
                return response()->json(['status' => 400, 'error' => 'Invalid search input.'], 400);
            }

            $tasksQuery->where(function($q) use ($search) {
                $q->where('tbl_tasks.title', 'like', '%' . $search . '%')
                  ->orWhere('tbl_tasks.description', 'like', '%' . $search . '%')
                  ->orWhere('p.title', 'like', '%' . $search . '%')
                  ->orWhere('ts.title', 'like', '%' . $search . '%')
                  ->orWhere('d.name', 'like', '%' . $search . '%')
                  ->orWhere('tbl_tasks.start_date', 'like', '%' . $search . '%')
                  ->orWhere('tbl_tasks.due_date', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $tasksQuery->whereDate('start_date', '<=', $request->input('to_date'))
                ->whereDate('due_date', '>=', $request->input('from_date'));
        } elseif ($request->filled('from_date')) {
            $tasksQuery->whereDate('due_date', '>=', $request->input('from_date'));
        } elseif ($request->filled('to_date')) {
            $tasksQuery->whereDate('start_date', '<=', $request->input('to_date'));
        }

        if (! $this->canViewAllTasks($authUser)) {
            $assignedTaskIds = TaskAssignmentModel::where('user_id', $authUser->user_id)
                ->where('status', 1)
                ->pluck('task_id');

            $tasksQuery = $tasksQuery->whereIn('task_id', $assignedTaskIds);
        }

        $limit = $request->input('limit', 5);
        $page = $request->input('page');
        $offset = $request->input('offset');

        if ($page) {
            $offset = ($page - 1) * $limit;
        }
        $offset = $offset ?? 0;

        $count = $tasksQuery->count();
        $tasks = $tasksQuery->skip($offset)->take($limit)->get();

        // --- NEW FIX: Fetch and map assigned user names for all rows ---
        $allTaskIds = $tasks->pluck('task_id')->all();
        if (!empty($allTaskIds)) {
            // Get all active assignments combined with user information for the loaded tasks
            $allAssignments = TaskAssignmentModel::leftJoin('tbl_users as u', 'tbl_task_assignments.user_id', '=', 'u.user_id')
                ->whereIn('tbl_task_assignments.task_id', $allTaskIds)
                ->where('tbl_task_assignments.status', 1)
                ->select('tbl_task_assignments.task_id', 'u.name')
                ->get()
                ->groupBy('task_id');

            foreach ($tasks as $t) {
                $taskAssignments = $allAssignments->get($t->task_id);
                if ($taskAssignments) {
                    // Extract names and combine them into a comma-separated list
                    $namesArray = $taskAssignments->pluck('name')->filter()->all();
                    $t->assigned_users = implode(', ', $namesArray);
                } else {
                    $t->assigned_users = 'Unassigned';
                }
            }
        }
        // --- END OF FIX ---

        if (! $this->canViewAllTasks($authUser)) {
            $taskIds = $tasks->pluck('task_id')->all();
            if (!empty($taskIds)) {
                $assignments = TaskAssignmentModel::whereIn('task_id', $taskIds)
                    ->where('user_id', $authUser->user_id)
                    ->where('status', 1)
                    ->get()
                    ->keyBy('task_id');

                $assignerIds = array_values(array_filter(array_map(function($a){ return $a->assigned_by ?? null; }, $assignments->all())));
                $assigners = [];
                if (!empty($assignerIds)) {
                    $users = User::whereIn('user_id', $assignerIds)->get()->keyBy('user_id');
                    foreach ($users as $uid => $u) {
                        $assigners[$uid] = $u->name;
                    }
                }

                foreach ($tasks as $t) {
                    $assigned = $assignments[$t->task_id] ?? null;
                    $t->assigned_by = $assigned->assigned_by ?? null;
                    $t->assigned_by_name = $assigners[$t->assigned_by] ?? null;
                }
            }
        }

        $meta = [
            'total' => $count,
            'per_page' => (int) $limit,
            'current_page' => $page ? (int) $page : (int) floor($offset / $limit) + 1,
            'current_offset' => (int) $offset,
            'has_next' => ($offset + $limit) < $count,
        ];

        return response()->json(['status' => 200, 'count' => $count, 'data' => $tasks, 'meta' => $meta], 200);
    }

    public function addtask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'priority_id' => 'required|integer|exists:tbl_priorities,priority_id',
            'task_status_id' => 'required|integer|exists:tbl_task_status,task_status_id',
            'department_id' => 'required|integer|exists:tbl_departments,department_id',
            'assigned_user_ids' => 'required|array|min:1',
            'assigned_user_ids.*' => 'integer|exists:tbl_users,user_id',
        ], [
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a string.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $authUser = $request->user();
            if (! $authUser) {
                return response()->json(['status' => 401, 'error' => 'Unauthorized.'], 401);
            }

            if (! $this->canManageTasks($authUser)) {
                return response()->json(['status' => 403, 'error' => 'Only Admin or Manager can assign tasks.'], 403);
            }

            $task = DB::transaction(function () use ($request, $authUser) {
                $task = new TaskModel();
                $task->title = $request->input('title');
                $task->description = $request->input('description');
                $task->start_date = $request->input('start_date');
                $task->due_date = $request->input('due_date');
                $task->priority_id = $request->input('priority_id');
                $task->task_status_id = $request->input('task_status_id');
                $task->department_id = $request->input('department_id');
                $task->created_by = $authUser->user_id;
                $task->status = 1;
                $task->save();

                $assignedIds = $request->input('assigned_user_ids');
                foreach ($assignedIds as $uid) {
                    $assignment = new TaskAssignmentModel();
                    $assignment->task_id = $task->task_id;
                    $assignment->task_priority = $task->priority_id;
                    $assignment->user_id = $uid;
                    $assignment->assigned_by = $authUser->user_id;
                    $assignment->assigned_at = now();
                    $assignment->status = 1;
                    $assignment->save();
                }

                $log = new TaskStatusLogModel();
                $log->task_id = $task->task_id;
                $log->from_status_id = (int) $task->task_status_id;
                $log->to_status_id = (int) $task->task_status_id;
                $log->changed_by = $authUser->user_id;
                $log->remarks = 'Assigned to: ' . implode(',', $assignedIds);
                $log->department_id = $task->department_id;
                $log->status = 1;
                $log->save();

                return $task;
            });

            return response()->json(['status' => 200, 'data' => $task], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatetask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_tasks,task_id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'priority_id' => 'nullable|integer|exists:tbl_priorities,priority_id',
            'task_status_id' => 'nullable|integer|exists:tbl_task_status,task_status_id',
            'department_id' => 'nullable|integer|exists:tbl_departments,department_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $authUser = $request->user();
            if (! $authUser) {
                return response()->json(['status' => 401, 'error' => 'Unauthorized.'], 401);
            }

            $task = TaskModel::find($request->input('id'));
            if (! $task) {
                return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
            }

            $isManager = $this->canManageTasks($authUser);

            if (! $isManager) {
                $assigned = TaskAssignmentModel::where('task_id', $request->input('id'))
                    ->where('user_id', $authUser->user_id)
                    ->where('status', 1)
                    ->exists();

                if (! $assigned) {
                    return response()->json(['status' => 403, 'error' => 'This task is not assigned to you.'], 403);
                }

                $allowed = ['id', 'task_status_id'];
                $provided = array_keys($request->all());
                $extra = array_diff($provided, $allowed);
                if (count($extra) > 0) {
                    return response()->json(['status' => 403, 'error' => 'You are only allowed to update the task status.'], 403);
                }
            }

            if ($isManager) {
                if ($request->has('title')) $task->title = $request->input('title');
                if ($request->has('description')) $task->description = $request->input('description');
                if ($request->has('start_date')) $task->start_date = $request->input('start_date');
                if ($request->has('due_date')) $task->due_date = $request->input('due_date');
                if ($request->has('priority_id')) $task->priority_id = $request->input('priority_id');
                if ($request->has('department_id')) $task->department_id = $request->input('department_id');
                if ($request->has('status')) $task->status = $request->input('status');
            }

            if ($request->has('task_status_id')) {
                $task->task_status_id = $request->input('task_status_id');
            }

            $task->save();

            return response()->json(['status' => 200, 'data' => $task], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletetask(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_tasks,task_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $caller = $request->user();
            if (! $caller) {
                return response()->json(['status' => 401, 'error' => 'Unauthorized.'], 401);
            }

            if (! $this->canManageTasks($caller)) {
                return response()->json(['status' => 403, 'error' => 'Only Admins or Managers can delete tasks.'], 403);
            }

            $task = TaskModel::find($request->input('id'));
            if (! $task) {
                return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
            }

            $task->status = 0;
            $task->updated_by = $caller->user_id;
            $task->updated_at = now();
            $task->save();

            return response()->json(['status' => 200, 'data' => $task], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}