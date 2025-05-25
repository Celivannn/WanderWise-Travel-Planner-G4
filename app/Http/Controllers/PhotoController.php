<?php

namespace App\Http\Controllers;

use App\Services\PhotoService;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    protected PhotoService $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    public function getPhotos(Request $request)
    {
        $city = $request->query('city', 'Manila');

        return $this->photoService->getPhotos($city);
    }
}
