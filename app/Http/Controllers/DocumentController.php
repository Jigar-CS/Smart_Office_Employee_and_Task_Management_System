<?php

namespace App\Http\Controllers;

use App\DocumentModel;
use Illuminate\Http\Request;
use Validator;

class DocumentController extends Controller
{
    public function adddocument(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required',
            'uploaded_by' => 'required|numeric',
            'document' => 'required|file|max:5120',
        ]);

        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        $file = $request->file('document');
        $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $path = $file->storeAs('documents', $fileName, 'public');

        $document = new DocumentModel();
        $document->owner_type = $request->input('owner_type', 'task');
        $document->owner_id = $request->input('owner_id', 0);
        $document->title = $request->input('title');
        $document->file_name = $fileName;
        $document->file_path = $path;
        $document->file_type = $file->getClientMimeType();
        $document->file_size = $file->getSize();
        $document->uploaded_by = $request->input('uploaded_by');
        $document->status = $request->input('status', 1);

        try {
            $result = $document->save();
            if ($result) return response()->json(['status' => 200, 'data' => $document], 200);
            return response()->json(['status' => 400, 'error' => 'Save returned false.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function getalldocuments(Request $request)
    {
        $data = [];
        if ($request->has('offset') && $request->filled('offset')) $data['offset'] = $request->input('offset');
        if ($request->has('limit') && $request->filled('limit')) $data['limit'] = $request->input('limit');
        if ($request->has('search') && $request->filled('search')) $data['search'] = $request->input('search');
        if ($request->has('is_admin') && $request->filled('is_admin')) $data['is_admin'] = $request->input('is_admin');

        $document = new DocumentModel();
        $rows = $document->getalldocuments($data);
        if (isset($rows['total_count'])) return response()->json(['status' => 200, 'count' => $rows['total_count'], 'data' => $rows['data']], 200);
        return response()->json(['status' => 200, 'count' => count($rows), 'data' => $rows], 200);
    }

    public function updatedocument(Request $request)
    {
        $valid = Validator::make($request->all(), ['id' => 'required|numeric']);
        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        $update = $request->except(['id']);
        try {
            $result = DocumentModel::where('id', $request->input('id'))->update($update);
            if ($result) return response()->json(['status' => 200, 'data' => $result], 200);
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function deletedocument(Request $request)
    {
        $valid = Validator::make($request->all(), ['id' => 'required|numeric']);
        if ($valid->fails()) return response()->json(['status' => 400, 'error' => $valid->errors()], 400);

        try {
            $result = DocumentModel::where('id', $request->input('id'))->update(['status' => 0]);
            if ($result) return response()->json(['status' => 200, 'data' => $request->all()], 200);
            return response()->json(['status' => 400, 'error' => 'No rows updated or something went wrong.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
