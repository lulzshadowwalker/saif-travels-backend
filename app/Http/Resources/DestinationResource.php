<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HasTimestamps;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
{
    use HasTimestamps;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "destinations",
            "id" => $this->id,
            "attributes" => [
                "name" => $this->name,
                "slug" => $this->slug,
                ...$this->timestamps(),
            ],
            "relationships" => [
                "packages" => PackageResource::collection(
                    $this->whenLoaded("packages")
                ),
                "media" => [
                    "images" => $this->whenLoaded("media", function () {
                        return MediaResource::collection(
                            $this->resource->getMedia(
                                \App\Models\Destination::MEDIA_COLLECTION_IMAGES
                            )
                        );
                    }),
                ],
            ],
            "meta" => [
                "packagesCount" => $this->whenCounted("packages"),
            ],
            "links" => [
                "self" => route("api.destinations.show", [
                    "destination" => $this->slug,
                ]),
            ],
        ];
    }
}
