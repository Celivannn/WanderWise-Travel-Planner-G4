<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    protected WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Handle the weather request and delegate to the service.
     */
    public function getWeather(Request $request)
    {
        $city = $request->query('city', 'Manila');
        return $this->weatherService->getWeather($city);
    }
}
