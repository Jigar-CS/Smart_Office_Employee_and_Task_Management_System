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
        $documents = DocumentModel::where('status', 1)->orderBy('document_id', 'desc')->get();

        return response()->json(['status' => 200, 'count' => $documents->count(), 'data' => $documents], 200);
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
            $document = DocumentModel::find($request->input('id'));
            $document->task_id = $request->input('task_id');
            $document->title = $request->input('title');
            $document->file_name = $request->input('file_name');
            $document->file_path = $request->input('file_path');
            $document->mime_type = $request->input('mime_type');
            $document->file_size = $request->input('file_size');
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
