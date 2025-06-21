<?php

use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\SupportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
    return $request->user();
});

// Package routes
Route::prefix("packages")
    ->name("api.packages.")
    ->group(function () {
        Route::get("/", [PackageController::class, "index"])->name("index");
        Route::get("/{package:slug}", [PackageController::class, "show"])->name(
            "show"
        );
    });

// Destination routes
Route::prefix("destinations")
    ->name("api.destinations.")
    ->group(function () {
        Route::get("/", [DestinationController::class, "index"])->name("index");
        Route::get("/{destination:slug}", [
            DestinationController::class,
            "show",
        ])->name("show");
    });

// FAQ routes
Route::prefix("faqs")
    ->name("api.faqs.")
    ->group(function () {
        Route::get("/", [FaqController::class, "index"])->name("index");
    });

// Support routes
Route::prefix("support")
    ->name("api.support.")
    ->group(function () {
        Route::post("/", [SupportController::class, "store"])->name("store");
    });

// Retreat routes
Route::prefix("retreats")
    ->name("api.retreats.")
    ->group(function () {
        Route::get("/", [
            \App\Http\Controllers\Api\RetreatController::class,
            "index",
        ])->name("index");
        Route::get("/{retreat}", [
            \App\Http\Controllers\Api\RetreatController::class,
            "show",
        ])->name("show");
    });
