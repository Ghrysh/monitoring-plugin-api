<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class LicenseController extends Controller
{
    /**
     * Verify a client license key.
     */
    public function verify(Request $request)
    {
        $licenseKey = $request->input('license_key') ?? $request->header('X-FutureCloud-License');

        if (!$licenseKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing License Key'
            ], 400);
        }

        $client = Client::where('license_key', $licenseKey)->first();

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid License Key'
            ], 404);
        }

        if ($client->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Subscription is inactive'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'License is valid and active',
            'data' => [
                'client_name' => $client->name,
                'status' => $client->status
            ]
        ], 200);
    }
}
