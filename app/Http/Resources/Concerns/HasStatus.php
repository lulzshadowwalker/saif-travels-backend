<?php

namespace App\Http\Resources\Concerns;

trait HasStatus
{
    /**
     * Get formatted status for the resource.
     *
     * @return array<string, mixed>
     */
    protected function formatStatus(): array
    {
        return [
            "value" => $this->resource->status->value,
            "label" => $this->resource->status->getLabel(),
            "color" => $this->resource->status->getColor(),
        ];
    }
}
