<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DestinationController extends Controller
{
    /**
     * Display a listing of destinations.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $destinations = Destination::query()
            ->with(["media"])
            ->withCount("packages")
            ->orderBy("name->en")
            ->paginate($request->input("per_page", 15));

        return DestinationResource::collection($destinations);
    }

    /**
     * Display the specified destination.
     */
    public function show(Destination $destination): JsonResponse
    {
        $destination
            ->loadMissing([
                "packages" => function ($query) {
                    $query->where("status", \App\Enums\PackageStatus::active);
                },
                "media",
            ])
            ->loadCount([
                "packages" => function ($query) {
                    $query->where("status", \App\Enums\PackageStatus::active);
                },
            ]);

        return DestinationResource::make($destination)
            ->response()
            ->setStatusCode(200);
    }
}
