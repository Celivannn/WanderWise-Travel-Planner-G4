<?php

namespace App\Http\Controllers;

use App\Services\TravelFormService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TravelFormController extends Controller
{
    protected $travelFormService;

    public function __construct(TravelFormService $travelFormService)
    {
        $this->travelFormService = $travelFormService;
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        return $this->travelFormService->index($request);
    }

    public function show(Request $request, $id): JsonResponse
    {
        return $this->travelFormService->show($id, $request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->travelFormService->store($request);
    }

    public function update(Request $request, $id): JsonResponse
    {
        return $this->travelFormService->update($id, $request);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        return $this->travelFormService->destroy($id, $request);
    }
}
