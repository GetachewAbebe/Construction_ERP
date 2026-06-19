<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/up', function () {
    return response()->json([
        'status' => 'up',
        'timestamp' => now()->toIso8601String(),
    ]);
});
