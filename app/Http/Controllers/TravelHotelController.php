<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TravelHotelService;

class TravelHotelController extends Controller
{
    protected $travelHotelService;

    public function __construct(TravelHotelService $travelHotelService)
    {
        $this->travelHotelService = $travelHotelService;
    }

    public function searchHotels(Request $request)
    {
        $city = $request->input('city');
        $checkInDate = $request->input('check_in');
        $checkOutDate = $request->input('check_out');

        return $this->travelHotelService->searchHotels($city, $checkInDate, $checkOutDate);
    }
}
