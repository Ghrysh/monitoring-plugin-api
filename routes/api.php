<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VisitorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Monitoring API endpoints for the Widget
Route::prefix('v1')->group(function () {
    Route::post('/visitor/track', [VisitorController::class, 'track']);
});
