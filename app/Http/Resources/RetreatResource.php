<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RetreatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "retreats",
            "id" => $this->id,
            "attributes" => [
                "name" => $this->name,
                "status" => $this->status,
                "createdAt" => $this->created_at->toIso8601String(),
                "updatedAt" => $this->updated_at->toIso8601String(),
                "createdAtForHumans" => $this->created_at->diffForHumans(),
                "updatedAtForHumans" => $this->updated_at->diffForHumans(),
            ],
            "relationships" => [
                "packages" => $this->when(
                    $this->relationLoaded("packages"),
                    function () {
                        return $this->packages->map(function ($package) {
                            return [
                                "type" => "packages",
                                "id" => $package->id,
                                "attributes" => [
                                    "name" => $package->name,
                                    "slug" => $package->slug,
                                    "durations" => $package->durations,
                                    "durationsDays" =>
                                        $package->durations .
                                        " " .
                                        str("day")->plural($package->durations),
                                    "tags" => $package->tagsArray,
                                    "status" => $package->status,
                                    "isActive" =>
                                        $package->status ===
                                        \App\Enums\PackageStatus::active,
                                ],
                                "links" => [
                                    "self" => route("api.packages.show", [
                                        "package" => $package->slug,
                                    ]),
                                ],
                            ];
                        });
                    }
                ),
            ],
            "meta" => [
                "packagesCount" => $this->whenCounted("packages"),
            ],
            "links" => [
                "self" => route("api.retreats.show", [
                    "retreat" => $this->id,
                ]),
            ],
        ];
    }
}
