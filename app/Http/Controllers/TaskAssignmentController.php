<?php

namespace App\Http\Controllers;

use App\TaskAssignmentModel;
use Illuminate\Http\Request;
use Validator;

class TaskAssignmentController extends Controller
{
    public function addtaskassignment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'task_id' => 'required|numeric',
            'assigned_to' => 'required|numeric',
            'assigned_by' => 'required|numeric',
        ]);

        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        $assignment = new TaskAssignmentModel();
        $assignment->task_id = $request->input('task_id');
        $assignment->assigned_to = $request->input('assigned_to');
        $assignment->assigned_by = $request->input('assigned_by');
        $assignment->assigned_at = $request->input('assigned_at', now());
        $assignment->remarks = $request->input('remarks');

        try {
            $result = $assignment->save();
            if ($result) return response()->json(['status' => 200, 'data' => $assignment], 200);
            return response()->json(['status' => 400, 'error' => 'Save returned false.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function getalltaskassignments(Request $request)
    {
        $data = [];
        if ($request->has('offset') && $request->filled('offset')) $data['offset'] = $request->input('offset');
        if ($request->has('limit') && $request->filled('limit')) $data['limit'] = $request->input('limit');
        if ($request->has('task_id') && $request->filled('task_id')) $data['task_id'] = $request->input('task_id');
        if ($request->has('is_admin') && $request->filled('is_admin')) $data['is_admin'] = $request->input('is_admin');

        $assignment = new TaskAssignmentModel();
        $rows = $assignment->getalltaskassignments($data);
        if (isset($rows['total_count'])) return response()->json(['status' => 200, 'count' => $rows['total_count'], 'data' => $rows['data']], 200);
        return response()->json(['status' => 200, 'count' => count($rows), 'data' => $rows], 200);
    }

    public function updatetaskassignment(Request $request)
    {
        $valid = Validator::make($request->all(), ['id' => 'required|numeric']);
        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        $update = $request->except(['id']);
        try {
            $result = TaskAssignmentModel::where('id', $request->input('id'))->update($update);
            if ($result) return response()->json(['status' => 200, 'data' => $result], 200);
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletetaskassignment(Request $request)
    {
        $valid = Validator::make($request->all(), ['id' => 'required|numeric']);
        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        try {
            $result = TaskAssignmentModel::where('id', $request->input('id'))->update(['deleted_at' => now()]);
            if ($result) return response()->json(['status' => 200, 'data' => $request->all()], 200);
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
