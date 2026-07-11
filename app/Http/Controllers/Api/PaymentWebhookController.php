<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class PaymentWebhookController extends Controller
{
    /**
     * Handle incoming payment webhooks (e.g., to lock API access).
     */
    public function handle(Request $request)
    {
        $event = $request->input('event');
        $licenseKey = $request->input('data.license_key');

        if (!$event || !$licenseKey) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        }

        $client = Client::where('license_key', $licenseKey)->first();

        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Client not found'], 404);
        }

        if (in_array($event, ['subscription.expired', 'payment.failed'])) {
            $client->status = 'expired';
            $client->save();
        } elseif (in_array($event, ['subscription.renewed', 'payment.success'])) {
            $client->status = 'active';
            $client->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Webhook processed successfully'], 200);
    }
}
