<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PackageController extends Controller
{
    /**
     * Display a listing of packages.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $packages = Package::query()
            ->where("status", \App\Enums\PackageStatus::active)
            ->with(["destinations", "media"])
            ->withCount("destinations")
            ->orderBy("created_at", "desc")
            ->paginate($request->input("per_page", 15));

        return PackageResource::collection($packages);
    }

    /**
     * Display the specified package.
     */
    public function show(Package $package): JsonResponse
    {
        // Only show active packages
        if ($package->status !== \App\Enums\PackageStatus::active) {
            abort(404);
        }

        $package
            ->loadMissing(["destinations", "media"])
            ->loadCount("destinations");

        return PackageResource::make($package)->response()->setStatusCode(200);
    }
}
