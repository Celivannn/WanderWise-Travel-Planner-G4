<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Fetch weather data for a given city using OpenWeather API.
     *
     * @param string $city
     * @return JsonResponse
     */
    public function getWeather(string $city = 'Manila'): JsonResponse
    {
        if (empty(trim($city))) {
            return response()->json(['error' => 'City name cannot be empty'], 400);
        }

        $apiKey = config('services.openweather.key');
        if (!$apiKey) {
            return response()->json(['error' => 'OpenWeather API key not configured'], 500);
        }

        try {
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric'
            ]);

            if (!$response->ok()) {
                return $this->handleErrorResponse($response);
            }

            $data = $response->json();
            Log::info('Weather API Response', $data);

            return response()->json([
                'city' => $data['name'] ?? $city,
                'temperature' => $data['main']['temp'] ?? null,
                'feels_like' => $data['main']['feels_like'] ?? null,
                'humidity' => $data['main']['humidity'] ?? null,
                'weather' => $data['weather'][0]['main'] ?? 'Unknown',
                'description' => $data['weather'][0]['description'] ?? 'No description',
                'wind_speed' => $data['wind']['speed'] ?? null,
                'timestamp' => now()->toDateTimeString()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Weather API Error', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle non-200 responses from OpenWeather API.
     */
    protected function handleErrorResponse($response): JsonResponse
    {
        return match ($response->status()) {
            400 => response()->json(['error' => 'Bad request: Invalid city name'], 400),
            401 => response()->json(['error' => 'Unauthorized: Invalid API key'], 401),
            404 => response()->json(['error' => 'City not found'], 404),
            default => response()->json([
                'error' => 'Failed to fetch weather data',
                'status' => $response->status()
            ], 500),
        };
    }
}
