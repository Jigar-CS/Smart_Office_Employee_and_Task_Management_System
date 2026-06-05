<?php

namespace App\Http\Controllers;

use App\Models\PriorityModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PriorityModelController extends Controller
{
    public function getpriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_priorities,priority_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $priority = PriorityModel::where('priority_id', $request->input('id'))->where('status', 1)->first();

        if (!$priority) {
            return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
        }

        return response()->json(['status' => 200, 'data' => $priority], 200);
    }   

    public function getallpriority(Request $request)
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
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $prioritiesQuery = PriorityModel::where('status', 1)
            ->orderBy('priority_id', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            // Security: reject HTML/script input
            if (
                preg_match('/<\s*\/?[a-z][a-z0-9]*\b[^>]*>/i', $search) ||
                preg_match('/\b(script|onload|onerror|onmouseover|onclick)\b/i', $search)
            ) {
                return response()->json(['status' => 400, 'error' => 'Invalid search input.'], 400);
            }

            $prioritiesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('level', 'like', '%' . $search . '%');
            });
        }


        $count = $prioritiesQuery->count();
        $priorities = $prioritiesQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        return response()->json(['status' => 200, 'count' => $count, 'data' => $priorities], 200);
    }

    public function addpriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'level' => 'required|string|max:255',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        // Security: reject HTML/script payloads for create
        $title = (string) $request->input('title', '');
        $level = (string) $request->input('level', '');

        if (
            preg_match('/<\s*\/?[a-z][a-z0-9]*\b[^>]*>/i', $title) ||
            preg_match('/\b(script|onload|onerror|onmouseover|onclick)\b/i', $title) ||
            preg_match('/<\s*\/?[a-z][a-z0-9]*\b[^>]*>/i', $level) ||
            preg_match('/\b(script|onload|onerror|onmouseover|onclick)\b/i', $level)
        ) {
            return response()->json(['status' => 400, 'error' => 'Invalid input.'], 400);
        }

        try {

            $caller = $request->user();
            $title = $request->input('title');

            // Check if a priority with same title exists
            $existing = PriorityModel::where('title', $title)->first();

            if ($existing && $existing->status == 1) {
                return response()->json(['status' => 400, 'error' => ['title' => ['Priority title has already been taken.']]], 400);
            }

            if ($existing && $existing->status == 0) {
                // Restore deleted priority
                $existing->level = $request->input('level');
                $existing->created_by = $caller?->user_id;
                $existing->created_at = now();
                $existing->status = 1;
                $existing->save();

                return response()->json(['status' => 200, 'data' => $existing], 200);
            }

            $priority = new PriorityModel();
            $priority->title = $title;
            $priority->level = $request->input('level');
            $priority->created_by = $caller?->user_id;
            $priority->status = 1;
            $result = $priority->save();

            return response()->json(['status' => 200, 'data' => $result ? $priority : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatepriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_priorities,priority_id',
            'title' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:255',
        ], [

            'id.required' => 'Priority id is required.',
            'id.integer' => 'Priority id must be an integer.',
            'id.exists' => 'Priority not found.',
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title may not be greater than :max characters.',
            'level.string' => 'Level must be a string.',
            'level.max' => 'Level may not be greater than :max characters.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $caller = $request->user();
            $priority = PriorityModel::find($request->input('id'));

            // If title is being updated, ensure no active conflict
            if ($request->has('title') && $request->input('title') !== $priority->title) {
                $existing = PriorityModel::where('title', $request->input('title'))->where('status', 1)->first();
                if ($existing) {
                    return response()->json(['status' => 400, 'error' => ['title' => ['Priority title has already been taken.']]], 400);
                }
                $priority->title = $request->input('title');
            }

            if ($request->has('level')) {
                $priority->level = $request->input('level');
            }
            if ($request->has('status')) {
                $priority->status = $request->input('status');
            }
            $priority->updated_by = $caller?->user_id;
            $priority->updated_at = now();
            $priority->save();

            return response()->json(['status' => 200, 'data' => $priority], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletepriority(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_priorities,priority_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $priority = PriorityModel::find($request->input('id'));
            $caller = $request->user();
            $priority->status = 0;
            $priority->updated_by = $caller?->user_id;
            $priority->updated_at = now();
            $priority->save();

            return response()->json(['status' => 200, 'data' => $priority], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
