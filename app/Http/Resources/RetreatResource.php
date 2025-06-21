<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HasTimestamps;
use App\Http\Resources\Concerns\HasStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RetreatResource extends JsonResource
{
    use HasTimestamps, HasStatus;
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
                "status" => $this->formatStatus(),
                ...$this->timestamps(),
            ],
            "relationships" => [
                "packages" => PackageResource::collection(
                    $this->whenLoaded("packages")
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
