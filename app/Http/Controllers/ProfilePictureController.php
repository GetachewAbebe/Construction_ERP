<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class ProfilePictureController extends Controller
{
    /**
     * Serve profile picture directly from storage
     * This bypasses the need for public/storage symlink
     */
    public function show(string $filename): \Symfony\Component\HttpFoundation\Response
    {
        // Normalize slashes for the internal lookup
        $cleanPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filename);

        // Security filter
        if (! $cleanPath || str_contains($cleanPath, '..')) {
            return response()->json(['error' => 'Illegal asset access'], 400);
        }

        // Direct physical resolution
        // Try exact path first
        $fullPath = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$cleanPath);

        // Try absolute employees/ subdirectory as fallback
        if (! file_exists($fullPath)) {
            $baseName = basename($cleanPath);
            $altPath = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'employees'.DIRECTORY_SEPARATOR.$baseName);

            if (file_exists($altPath)) {
                $fullPath = $altPath;
            } else {
                \Log::warning('Profile asset lookup failed physically: '.$fullPath);

                return response()->json(['error' => 'Identity anchor point missing'], 404);
            }
        }

        // MIME Detection
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $contentTypes = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',
        ];
        $contentType = $contentTypes[$ext] ?? 'image/jpeg';

        return response()->file($fullPath, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'X-Identity-Resolved' => $filename,
        ]);
    }
}
