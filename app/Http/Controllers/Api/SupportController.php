<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportResource;
use App\Models\Support;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Store a newly created support request.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "email", "max:255"],
            "phone" => ["required", "string", "max:255"],
        ]);

        $support = Support::create($validated);

        return SupportResource::make($support)->response()->setStatusCode(201);
    }
}
