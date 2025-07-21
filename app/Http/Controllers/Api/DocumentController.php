<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    use ApiResponse;

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'imageMultipartFile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'document' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $documentData = json_decode($request->document, true);

        $file = $request->file('imageMultipartFile');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('uploads/images', $filename, 'public');

        // Store record in DB
        $document = Document::create([
            'file_name' => $filename,
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'entity_type' => $documentData['entity_type'] ?? null,
            'entity_id' => $documentData['entity_id'] ?? null,
        ]);

        $encId = $this->shortEncrypt($document->id);
        $document->user_id = $encId;
        $document->save();

        return response()->json([
            'status' => true,
            'message' => 'Image uploaded and saved successfully',
            'id' => $encId,
            'file_url' => Storage::url($filePath),
        ]);
    }

    public function getDocumentsByIds(Request $request)
    {
        $docs = $request->input('entity_ids_list', "");
        $docIds = explode(",", $docs);

        // Validate input
        if (!is_array($docIds) || empty($docIds)) {
            return response()->json(['message' => 'Invalid or empty doc_ids'], 422);
        }

        // Fetch documents from DB
        $documents = Document::whereIn('user_id', $docIds)->get();

        $GET_URL = 'http://192.168.1.3:8000/storage/';
        $UPDATE_URL = 'https://cdn.openai.com';

        $response = $documents->map(function ($doc) use ($GET_URL, $UPDATE_URL) {
            return [
                'id' => $doc->user_id,
                'doc_url' => $GET_URL.$doc->file_path,
                'document_name' => $doc->file_name,
                'media_data_type' => $doc->mime_type === 'image/jpeg' || str_contains($doc->mime_type, 'image') ? 'images' : 'pdf',
            ];
        });

        return response()->json($response);
    }
}
