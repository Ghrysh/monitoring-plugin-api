<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\VisitorLog;

class VisitorController extends Controller
{
    /**
     * Authenticate and track a visitor session journey.
     */
    public function track(Request $request)
    {
        $licenseKey = $request->header('X-FutureCloud-License');

        if (!$licenseKey) {
            return response()->json(['error' => 'Missing License Key'], 401);
        }

        $client = Client::where('license_key', $licenseKey)->first();

        if (!$client || $client->status !== 'active') {
            return response()->json(['error' => 'Invalid or inactive License Key'], 403);
        }

        $sessionId = $request->input('session_id');
        $date = now()->toDateString();
        $ip = $request->ip();

        // Cari sesi hari ini, atau buat baru
        $log = VisitorLog::firstOrCreate(
            ['client_id' => $client->id, 'session_id' => $sessionId, 'date' => $date],
            [
                'ip_address' => $ip, 
                'user_agent' => $request->userAgent(),
                'device' => $request->input('device'),
                'browser' => $request->input('browser'),
                'os' => $request->input('os'),
                'country' => $request->input('country'),
                'city' => $request->input('city'),
                'page_journey' => []
            ]
        );

        $path = $request->input('page_url', '/');
        $journey = $log->page_journey ?? [];
        
        $lastVisit = end($journey);
        if (!$lastVisit || $lastVisit['path'] !== $path) {
            $journey[] = [
                'path' => $path, 
                'time' => now()->format('H:i')
            ];
            $log->page_journey = $journey;
            $log->save();
        }

        return response()->json([
            'message' => 'Visitor journey tracked successfully',
            'status' => 'success'
        ], 200);
    }
}
