<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    public function payment(string $provider, Request $request): Response
    {
        abort_unless($provider === 'midtrans' && app()->environment('production'), 404);
        abort_unless(config('services.midtrans.server_key'), 503);

        // Payment status must be verified by the provider's authenticated status API before transitions.
        return response()->json(['message' => 'Payment provider adapter is not configured.'], 503);
    }

    public function supplier(string $provider): Response
    {
        abort_unless($provider === 'digiflazz' && app()->environment('production'), 404);

        // Supplier updates must be authenticated, idempotent and reconciled before any fulfillment state change.
        return response()->json(['message' => 'Supplier adapter is not configured.'], 503);
    }
}
