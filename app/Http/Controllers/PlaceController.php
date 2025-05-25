<?php

namespace App\Http\Controllers;

use App\Services\PlaceService;

class PlaceController extends Controller
{
    protected PlaceService $placeService;

    public function __construct(PlaceService $placeService)
    {
        $this->placeService = $placeService;
    }

    public function getPlacesByCity(string $city)
    {
        return $this->placeService->getPlacesByCity($city);
    }
}
