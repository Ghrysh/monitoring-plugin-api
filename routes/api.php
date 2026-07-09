<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VisitorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/visitor/track', [App\Http\Controllers\Api\VisitorController::class, 'track']);
    Route::post('/verify-license', [App\Http\Controllers\Api\LicenseController::class, 'verify']);
    Route::post('/webhook/payment', [App\Http\Controllers\Api\PaymentWebhookController::class, 'handle']);
});
