<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilePictureController extends Controller
{
    /**
     * Serve profile picture directly from storage
     * This bypasses the need for public/storage symlink
     */
    public function show(Request $request): StreamedResponse
    {
        $path = $request->get('path');
        
        // Security: Prevent directory traversal attacks
        if (str_contains($path, '..') || str_starts_with($path, '/')) {
            abort(403, 'Invalid path');
        }

        // Check if file exists in storage
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            abort(404, 'Image not found');
        }

        // Determine MIME type
        $mimeType = mime_content_type($fullPath);
        
        // Stream the file directly
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
        ]);
    }
}
