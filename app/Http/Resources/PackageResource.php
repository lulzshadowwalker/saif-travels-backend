<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
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
                    ? array_map(
                        fn($chip) => [
                            "value" => $chip,
                            "label" =>
                                \App\Enums\PackageChip::tryFrom(
                                    $chip
                                )?->getLabel() ?? $chip,
                            "color" =>
                                \App\Enums\PackageChip::tryFrom(
                                    $chip
                                )?->getColor() ?? "primary",
                            "icon" =>
                                \App\Enums\PackageChip::tryFrom(
                                    $chip
                                )?->getIcon() ?? null,
                        ],
                        $this->chips
                    )
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
                "status" => [
                    "value" => $this->status->value,
                    "label" => $this->status->getLabel(),
                    "color" => $this->status->getColor(),
                    "icon" => $this->status->getIcon(),
                ],
                "isActive" =>
                    $this->status === \App\Enums\PackageStatus::active,
                "createdAt" => $this->created_at->toIso8601String(),
                "updatedAt" => $this->updated_at->toIso8601String(),
                "createdAtForHumans" => $this->created_at->diffForHumans(),
                "updatedAtForHumans" => $this->updated_at->diffForHumans(),
            ],
            "relationships" => [
                "destinations" => [
                    "data" => $this->whenLoaded(
                        "destinations",
                        function () {
                            return $this->destinations->map(function (
                                $destination
                            ) {
                                return [
                                    "type" => "destinations",
                                    "id" => $destination->id,
                                    "attributes" => [
                                        "name" => $destination->name,
                                        "slug" => $destination->slug,
                                    ],
                                ];
                            });
                        },
                        []
                    ),
                ],
                "media" => [
                    "images" => $this->when(
                        $this->relationLoaded("media"),
                        fn() => $this->getMedia(
                            \App\Models\Package::MEDIA_COLLECTION_IMAGES
                        )->map(
                            fn($media) => [
                                "id" => $media->id,
                                "url" => $media->getUrl(),
                                // "thumbnailUrl" => $media->getUrl("thumb"),
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
            "links" => [
                "self" => route("api.packages.show", [
                    "package" => $this->slug,
                ]),
            ],
        ];
    }
}
