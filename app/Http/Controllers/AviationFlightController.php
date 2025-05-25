<?php

namespace App\Http\Controllers; // ✅ Only this line should exist

use App\Services\AviationstackService;
use Illuminate\Http\Request;

class AviationFlightController extends Controller
{
    protected AviationstackService $aviationstack;

    public function __construct(AviationstackService $aviationstack)
    {
        $this->aviationstack = $aviationstack;
    }

    public function search(Request $request)
    {
        $from = $request->input('from');

        if (!$from) {
            return response()->json(['error' => 'Missing "from" parameter'], 400);
        }

        $result = $this->aviationstack->searchFlights($from);

        return response()->json($result);
    }
}
