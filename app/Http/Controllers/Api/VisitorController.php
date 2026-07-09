<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\VisitorLog;

class VisitorController extends Controller
{
    /**
     * Authenticate and track a visitor.
     */
    public function track(Request $request)
    {
        $licenseKey = $request->header('X-FutureCloud-License');

        if (!$licenseKey) {
            return response()->json(['error' => 'Missing License Key'], 401);
        }

        $client = Client::where('license_key', $licenseKey)->first();

        if (!$client) {
            return response()->json(['error' => 'Invalid License Key'], 401);
        }

        if ($client->status !== 'active') {
            return response()->json(['error' => 'Subscription is inactive'], 403);
        }

        $log = VisitorLog::create([
            'client_id' => $client->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device' => $request->input('device'),
            'browser' => $request->input('browser'),
            'os' => $request->input('os'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'page_url' => $request->input('page_url'),
            'session_id' => $request->input('session_id'),
            'visited_at' => now(),
        ]);

        return response()->json([
            'message' => 'Visitor tracked successfully',
            'status' => 'success'
        ], 200);
    }
}
