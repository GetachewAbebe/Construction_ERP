
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (! Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /** @var \App\Models\User $user */
    $user = $request->user();

    // Optional: add abilities like ['read', 'write'] instead of ['*']
    $token = $user->createToken('api-login', ['*'])->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
        ],
    ]);
});

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return [
        'id' => $request->user()->id,
        'name' => $request->user()->name,
        'email' => $request->user()->email,
        'roles' => $request->user()->getRoleNames(),
    ];
});
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    /** @var \App\Models\User $user */
    $user = $request->user();

    // Revoke all tokens...
    $user->tokens()->delete();

    return response()->json(['message' => 'Logged out']);
});
