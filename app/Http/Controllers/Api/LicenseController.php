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
}
