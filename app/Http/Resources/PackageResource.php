<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HasTimestamps;
use App\Http\Resources\Concerns\HasStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            "type" => "packages",
            "id" => $this->id,
            "attributes" => [
                "name" => $this->name,
                "slug" => $this->slug,
                "description" => $this->description,
                "tags" => $this->tagsArray,
                "chips" => $this->chips
                    ? ChipResource::collection(collect($this->chips))
                    : [],
                "goal" => $this->goal,
                "durations" => $this->durations,
                "durationsDays" =>
                    $this->durations .
                    " " .
                    str("day")->plural($this->durations),
                "program" => $this->program,
                "activities" => $this->activities,
                "stay" => $this->stay,
                "ivDrips" => $this->iv_drips,
                "status" => $this->formatStatus(),
                "isActive" =>
                    $this->status === \App\Enums\PackageStatus::active,
                ...$this->timestamps(),
            ],
            "relationships" => [
                "destinations" => DestinationResource::collection(
                    $this->whenLoaded("destinations")
                ),
                "media" => [
                    "images" => $this->whenLoaded("media", function () {
                        return MediaResource::collection(
                            $this->resource->getMedia(
                                \App\Models\Package::MEDIA_COLLECTION_IMAGES
                            )
                        );
                    }),
                ],
            ],
            "links" => [
                "self" => route("api.packages.show", [
                    "package" => $this->slug,
                ]),
            ],
        ];
    }
}
