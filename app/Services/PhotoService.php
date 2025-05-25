<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class PhotoService
{
    public function getPhotos(string $city = 'Manila'): JsonResponse
    {
        $city = trim($city);
        if ($city === '') {
            return response()->json([
                'error' => 'City name cannot be empty'
            ], 400);
        }

        $apiKey = config('services.unsplash.key');
        if (empty($apiKey)) {
            return response()->json([
                'error' => 'Unsplash API key not configured'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID ' . $apiKey,
            ])->get('https://api.unsplash.com/search/photos', [
                'query' => $city,
                'per_page' => 5,
            ]);

            if ($response->ok()) {
                $data = $response->json();

                if (!isset($data['results'])) {
                    return response()->json([
                        'error' => 'Invalid response from Unsplash API'
                    ], 500);
                }

                $photos = array_map(fn($photo) => [
                    'id' => $photo['id'],
                    'url' => $photo['urls']['regular'],
                    'description' => $photo['description'] ?? 'No description available',
                    'user' => $photo['user']['name'],
                    'link' => $photo['links']['html'],
                ], $data['results']);

                return response()->json([
                    'city' => $city,
                    'photos' => $photos,
                    'total' => $data['total'] ?? count($photos),
                    'timestamp' => now()->toDateTimeString(),
                ], 200);
            }

            return match ($response->status()) {
                400 => response()->json(['error' => 'Bad request: Invalid query parameters'], 400),
                401 => response()->json(['error' => 'Unauthorized: Invalid API key'], 401),
                404 => response()->json(['error' => 'Resource not found'], 404),
                default => response()->json([
                    'error' => 'Failed to fetch photos',
                    'status' => $response->status(),
                ], 500),
            };
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
