<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class LicenseController extends Controller
{
    /**
     * Sinkronisasi lisensi dari sistem utama (FutureCloud)
     */
    public function sync(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'license_key' => 'required|string',
        ]);

        $client = Client::updateOrCreate(
            ['license_key' => $request->license_key],
            [
                'name' => $request->name,
                'email' => $request->email,
                'status' => 'active',
                'subscription_expires_at' => now()->addYear(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'License synced successfully'
        ]);
    }

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

    public function install(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string'
        ]);

        $client = Client::where('license_key', $request->license_key)->first();
        
        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'License not found'
            ], 404);
        }

        $client->update(['is_installed' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Plugin marked as installed successfully'
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'status' => 'required|string',
        ]);
        
        $client = Client::where('license_key', $request->license_key)->first();
        if ($client) {
            $client->update(['status' => $request->status]);
            return response()->json(['message' => 'Status updated']);
        }
        return response()->json(['message' => 'Not found'], 404);
    }

    public function destroy($licenseKey)
    {
        $client = Client::where('license_key', $licenseKey)->first();
        if ($client) {
            $client->delete();
            return response()->json(['message' => 'License deleted']);
        }
        return response()->json(['message' => 'Not found'], 404);
    }
}