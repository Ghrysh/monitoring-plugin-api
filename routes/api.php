<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VisitorController;
use App\Http\Controllers\Api\LicenseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/visitor/track', [VisitorController::class, 'track']);
    Route::post('/license/sync', [LicenseController::class, 'sync']);
});
