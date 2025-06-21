<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "url" => $this->resource->getUrl(),
            "thumbnailUrl" => $this->when(
                $this->resource->hasGeneratedConversion("thumb"),
                fn() => $this->resource->getUrl("thumb")
            ),
            "name" => $this->resource->name,
            "fileName" => $this->resource->file_name,
            "mimeType" => $this->resource->mime_type,
            "size" => $this->resource->size,
            "humanReadableSize" => $this->resource->human_readable_size,
            "order" => $this->resource->order_column,
            "createdAt" => $this->resource->created_at->toIso8601String(),
            "updatedAt" => $this->resource->updated_at->toIso8601String(),
        ];
    }
}
