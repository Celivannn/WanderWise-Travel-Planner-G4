<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AviationstackService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.aviationstack.key');
        $this->baseUrl = rtrim(config('services.aviationstack.base_url'), '/');
    }

    public function searchFlights(string $from): array
    {
        $url = "{$this->baseUrl}/flights";

        $params = [
            'access_key' => $this->apiKey,
            'dep_iata' => $from,
        ];

        $response = Http::get($url, $params);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => 'Failed to fetch data from Aviationstack API',
            'details' => $response->body(),
        ];
    }
}
