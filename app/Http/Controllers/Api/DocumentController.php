<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
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

        return response()->json([
            'status' => true,
            'message' => 'Image uploaded and saved successfully',
            'id' => 'doc_'.$document->id,
            'file_url' => Storage::url($filePath),
        ]);
    }
}
