<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = Location::query()
            ->when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                    ->orWhere('code', 'like', '%' . request('search') . '%');
            })
            ->when(request('type'), function ($query) {
                $query->where('type', request('type'));
            })
            ->when(request('is_active') !== null, function ($query) {
                $query->where('is_active', request('is_active'));
            })
            ->paginate(request('per_page', 15));

        return ResponseHelper::success(LocationResource::collection($locations), 'Locations retrieved successfully');
    }

    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = Location::create($request->validated());
        return ResponseHelper::success(new LocationResource($location), 'Location created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $location = Location::find($id);

        if (!$location) {
            return ResponseHelper::notFound('Location not found');
        }

        return ResponseHelper::success(new LocationResource($location), 'Location retrieved successfully');
    }

    public function update(StoreLocationRequest $request, int $id): JsonResponse
    {
        $location = Location::find($id);

        if (!$location) {
            return ResponseHelper::notFound('Location not found');
        }

        $location->update($request->validated());
        return ResponseHelper::success(new LocationResource($location), 'Location updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $location = Location::find($id);

        if (!$location) {
            return ResponseHelper::notFound('Location not found');
        }

        $location->delete();
        return ResponseHelper::success(null, 'Location deleted successfully');
    }
}
