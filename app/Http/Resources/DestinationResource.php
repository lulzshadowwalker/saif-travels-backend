<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
{
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
                "name" => $this->getTranslations("name"),
                "slug" => $this->slug,
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
                                    "name" => $package->getTranslations("name"),
                                    "slug" => $package->slug,
                                    "durations" => $package->durations,
                                    "durationsDays" =>
                                        $package->durations .
                                        " " .
                                        str("day")->plural($package->durations),
                                    "tags" => $package->tagsArray,
                                    "status" => [
                                        "value" => $package->status->value,
                                        "label" => $package->status->getLabel(),
                                        "color" => $package->status->getColor(),
                                    ],
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
                "media" => [
                    "images" => $this->when(
                        $this->relationLoaded("media"),
                        fn() => $this->getMedia(
                            \App\Models\Destination::MEDIA_COLLECTION_IMAGES
                        )->map(
                            fn($media) => [
                                "id" => $media->id,
                                "url" => $media->getUrl(),
                                "thumbnailUrl" => $media->getUrl("thumb"),
                                "name" => $media->name,
                                "fileName" => $media->file_name,
                                "mimeType" => $media->mime_type,
                                "size" => $media->size,
                                "humanReadableSize" =>
                                    $media->human_readable_size,
                                "order" => $media->order_column,
                            ]
                        )
                    ),
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
