<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VerifyShilappSignature
{
    public function handle(Request $request, Closure $next)
    {
        $clientId = $request->header('X-CLIENT-ID');
        $timestamp = $request->header('X-TIMESTAMP');
        $nonce = $request->header('X-NONCE');
        $signature = $request->header('X-SIGNATURE');

        if (!$clientId || !$timestamp || !$nonce || !$signature) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing authentication headers',
            ], 401);
        }

        if (!hash_equals(config('services.shilapp.client_id'), $clientId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid client id',
            ], 401);
        }

        if (!ctype_digit((string) $timestamp)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid timestamp',
            ], 401);
        }

        if (abs(now()->timestamp - (int) $timestamp) > 300) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request expired',
            ], 401);
        }

        $allowedIps = array_filter(array_map('trim', explode(',', config('services.shilapp.allowed_ips', ''))));

        if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'IP not allowed',
            ], 403);
        }

        $nonceCacheKey = 'shilapp_api_nonce_' . $clientId . '_' . $nonce;

        if (Cache::has($nonceCacheKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Duplicate request',
            ], 409);
        }

        Cache::put($nonceCacheKey, true, now()->addMinutes(10));

        $method = strtoupper($request->method());
        $path = '/' . ltrim($request->path(), '/');

        $signaturePayload = $method
            . '|' . $path
            . '|' . $timestamp
            . '|' . $nonce;

        $expectedSignature = hash_hmac(
            'sha256',
            $signaturePayload,
            config('services.shilapp.client_secret')
        );



        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature',
            ], 401);
        }

        return $next($request);
    }
}
