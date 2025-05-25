<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class TravelHotelService
{
    /**
     * Search hotels for a given city and date range using Travelpayouts API.
     *
     * @param string $city City to search hotels for
     * @param string $checkInDate Check-in date (format: YYYY-MM-DD)
     * @param string $checkOutDate Check-out date (format: YYYY-MM-DD)
     * @return JsonResponse
     */
    public function searchHotels(string $city, string $checkInDate, string $checkOutDate): JsonResponse
    {
        // Validate inputs
        if (empty(trim($city))) {
            return response()->json([
                'error' => 'City name cannot be empty'
            ], 400);
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkInDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkOutDate)) {
            return response()->json([
                'error' => 'Invalid date format. Use YYYY-MM-DD'
            ], 400);
        }

        // Check if API token is configured
        $apiToken = config('services.travelpayouts.api_token');
        $unsplashKey = config('services.unsplash.access_key');

        if (empty($apiToken)) {
            return response()->json([
                'error' => 'Travelpayouts API token not configured'
            ], 500);
        }

        try {
            // Fetch hotels from Travelpayouts
            $hotelResponse = Http::withHeaders([
                'Authorization' => 'Token ' . $apiToken,
            ])->get('https://engine.hotellook.com/api/v2/cache.json', [
                'location' => $city,
                'checkIn' => $checkInDate,
                'checkOut' => $checkOutDate,
                'currency' => 'USD',
                'limit' => 10,
            ]);

            if (!$hotelResponse->successful()) {
                return response()->json(['error' => 'Failed to fetch hotels'], 500);
            }

            $hotels = $hotelResponse->json();

            // Attach images using Unsplash
            $hotelsWithImages = array_map(function ($hotel) use ($unsplashKey) {
                $image = 'https://via.placeholder.com/300x200?text=No+Image';

                if (!empty($hotel['hotelName'])) {
                    $imageResponse = Http::get('https://api.unsplash.com/search/photos', [
                        'query' => $hotel['hotelName'],
                        'client_id' => $unsplashKey,
                        'per_page' => 1,
                        'orientation' => 'landscape',
                    ]);

                    if ($imageResponse->successful() && isset($imageResponse['results'][0]['urls']['regular'])) {
                        $image = $imageResponse['results'][0]['urls']['regular'];
                    }
                }

                return [
                    'hotel_id' => $hotel['hotelId'] ?? null,
                    'name' => $hotel['hotelName'] ?? 'Unnamed hotel',
                    'price' => $hotel['price'] ?? 0,
                    'stars' => $hotel['stars'] ?? 0,
                    'location' => $hotel['location'] ?? ['lat' => null, 'lon' => null],
                    'url' => $hotel['url'] ?? null,
                    'image' => $image,
                ];
            }, $hotels);

            return response()->json([
                'city' => $city,
                'check_in' => $checkInDate,
                'check_out' => $checkOutDate,
                'hotels' => $hotelsWithImages,
                'total' => count($hotelsWithImages),
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
