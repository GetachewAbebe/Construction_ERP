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
    public function show(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $path = $request->get('path');
        
        // Security: Prevent directory traversal attacks
        if (!$path || str_contains($path, '..')) {
             abort(404);
        }

        // Use Storage facade to check existence and get path (Server-Agnostic)
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Image not found in storage');
        }

        $fullPath = Storage::disk('public')->path($path);

        // Terminate if physical file is missing despite Storage saying yes (race condition/cache)
        if (!file_exists($fullPath)) {
            abort(404, 'Physical file missing');
        }

        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
