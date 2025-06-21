<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RetreatResource;
use App\Models\Retreat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RetreatController extends Controller
{
    /**
     * Display a listing of retreats.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $retreats = Retreat::query()
            ->with(["packages"])
            ->withCount("packages")
            ->orderBy("name->en")
            ->paginate($request->input("per_page", 15));

        return RetreatResource::collection($retreats);
    }

    /**
     * Display the specified retreat.
     */
    public function show(Retreat $retreat): \Illuminate\Http\JsonResponse
    {
        $retreat->loadMissing(["packages"])->loadCount("packages");

        return RetreatResource::make($retreat)->response()->setStatusCode(200);
    }
}
