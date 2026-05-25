<?php

namespace App\Http\Controllers;

use App\Models\DocumentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentModelController extends Controller
{
    public function getdocument(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_documents,document_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        $document = DocumentModel::where('document_id', $request->input('id'))->where('status', 1)->first();

        if (!$document) {
            return response()->json(['status' => 404, 'error' => 'Record not found.'], 404);
        }

        return response()->json(['status' => 200, 'data' => $document], 200);
    }

    public function getalldocument(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'limit' => 'required|integer|min:1',
            'offset' => 'required|integer|min:0',
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

        $documentsQuery = DocumentModel::where('status', 1)->orderBy('document_id', 'desc');
        $count = $documentsQuery->count();
        $documents = $documentsQuery->skip($request->input('offset'))->take($request->input('limit'))->get();

        return response()->json(['status' => 200, 'count' => $count, 'data' => $documents], 200);
    }

    public function adddocument(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'task_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'file_name' => 'required|string|max:255',
            'file_path' => 'required|string|max:255',
            'mime_type' => 'required|string|max:255',
            'file_size' => 'nullable|integer',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $document = new DocumentModel();
            $document->task_id = $request->input('task_id');
            $document->title = $request->input('title');
            $document->file_name = $request->input('file_name');
            $document->file_path = $request->input('file_path');
            $document->mime_type = $request->input('mime_type');
            $document->file_size = $request->input('file_size');
            $document->status = 1;
            $result = $document->save();

            return response()->json(['status' => 200, 'data' => $result ? $document : null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function updatedocument(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_documents,document_id',
            'task_id' => 'nullable|integer',
            'title' => 'nullable|string|max:255',
            'file_name' => 'nullable|string|max:255',
            'file_path' => 'nullable|string|max:255',
            'mime_type' => 'nullable|string|max:255',
            'file_size' => 'nullable|integer',
        ], [
            'id.required' => 'Document id is required.',
            'id.integer' => 'Document id must be an integer.',
            'id.exists' => 'Document not found.',
            'task_id.integer' => 'Task id must be an integer.',
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title may not be greater than :max characters.',
            'file_name.string' => 'File name must be a string.',
            'file_name.max' => 'File name may not be greater than :max characters.',
            'file_path.string' => 'File path must be a string.',
            'file_path.max' => 'File path may not be greater than :max characters.',
            'mime_type.string' => 'Mime type must be a string.',
            'mime_type.max' => 'Mime type may not be greater than :max characters.',
            'file_size.integer' => 'File size must be an integer.',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $document = DocumentModel::find($request->input('id'));
            if ($request->has('task_id')) {
                $document->task_id = $request->input('task_id');
            }
            if ($request->has('title')) {
                $document->title = $request->input('title');
            }
            if ($request->has('file_name')) {
                $document->file_name = $request->input('file_name');
            }
            if ($request->has('file_path')) {
                $document->file_path = $request->input('file_path');
            }
            if ($request->has('mime_type')) {
                $document->mime_type = $request->input('mime_type');
            }
            if ($request->has('file_size')) {
                $document->file_size = $request->input('file_size');
            }
            if ($request->has('status')) {
                $document->status = $request->input('status');
            }
            $document->save();

            return response()->json(['status' => 200, 'data' => $document], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletedocument(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_documents,document_id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        }

        try {
            $document = DocumentModel::find($request->input('id'));
            $document->status = 0;
            $document->save();

            return response()->json(['status' => 200, 'data' => $document], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
