<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class CurrencyService
{
    public function convert(string $from = 'USD', string $to = 'EUR', float $amount = 1): JsonResponse
    {
        if (!$this->isValidCurrencyCode($from) || !$this->isValidCurrencyCode($to)) {
            return response()->json([
                'error' => 'Invalid currency code provided',
                'supported_currencies' => ['USD', 'EUR', 'GBP', 'JPY', 'CAD'] // Example list or fetch dynamically
            ], 400);
        }

        if ($amount <= 0) {
            return response()->json([
                'error' => 'Amount must be greater than zero'
            ], 400);
        }

        $apiKey = env('CURRENCY_API_KEY');
        if (empty($apiKey)) {
            return response()->json([
                'error' => 'API key not configured'
            ], 500);
        }

        try {
            $response = Http::get("https://v6.exchangerate-api.com/v6/{$apiKey}/pair/{$from}/{$to}");

            if ($response->ok()) {
                $data = $response->json();

                if (!isset($data['conversion_rate'])) {
                    return response()->json([
                        'error' => 'Invalid response from currency API'
                    ], 500);
                }

                $rate = $data['conversion_rate'];
                $converted = round($rate * $amount, 4);

                return response()->json([
                    'from' => $from,
                    'to' => $to,
                    'amount' => $amount,
                    'rate' => $rate,
                    'converted' => $converted,
                    'timestamp' => now()->toDateTimeString()
                ], 200);
            }

            return match ($response->status()) {
                401 => response()->json(['error' => 'Unauthorized: Invalid API key'], 401),
                404 => response()->json(['error' => 'Currency pair not found'], 404),
                default => response()->json([
                    'error' => 'Failed to fetch currency data',
                    'status' => $response->status()
                ], 500),
            };
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function isValidCurrencyCode(string $code): bool
    {
        return preg_match('/^[A-Z]{3}$/', $code) === 1;
    }
}
