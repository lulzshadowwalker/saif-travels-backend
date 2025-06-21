<?php

namespace App\Http\Resources\Concerns;

trait HasTimestamps
{
    /**
     * Get formatted timestamps for the resource.
     *
     * @return array<string, string>
     */
    protected function timestamps(): array
    {
        return [
            "createdAt" => $this->resource->created_at->toIso8601String(),
            "updatedAt" => $this->resource->updated_at->toIso8601String(),
            "createdAtForHumans" => $this->resource->created_at->diffForHumans(),
            "updatedAtForHumans" => $this->resource->updated_at->diffForHumans(),
        ];
    }
}
