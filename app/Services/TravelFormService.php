<?php

namespace App\Services;

use App\Models\TravelForm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TravelFormService
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $travelForms = TravelForm::where('user_id', $userId)->get();

        return $this->successResponse($travelForms, 'Travel forms retrieved successfully');
    }

    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $form = TravelForm::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            return $this->successResponse($form, 'Travel form retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Travel form not found or access denied', 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'travel_date' => 'required|date',
            'budget' => 'required|numeric|min:0',
            'days' => 'required|integer|min:1',
            'currency' => 'required|string|max:3',
        ]);

        $validated['user_id'] = $request->user()->id;

        $form = TravelForm::create($validated);

        return $this->successResponse($form, 'Travel form created successfully', 201);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:100',
            'travel_date' => 'sometimes|date',
            'budget' => 'sometimes|numeric|min:0',
            'days' => 'sometimes|integer|min:1',
            'currency' => 'sometimes|string|max:3',
        ]);

        try {
            $form = TravelForm::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $form->update($validated);

            return $this->successResponse($form, 'Travel form updated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Travel form not found or access denied', 404);
        }
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $form = TravelForm::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $form->delete();

            return $this->successResponse(null, 'Travel form deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Travel form not found or access denied', 404);
        }
    }

    // JSON response helpers

    protected function successResponse($data, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(string $message = 'Error', int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }
}
