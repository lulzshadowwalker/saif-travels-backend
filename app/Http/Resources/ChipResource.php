<?php

namespace App\Http\Resources;

use App\Enums\PackageChip;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Handle both string and enum instances
        $chip =
            $this->resource instanceof PackageChip
                ? $this->resource
                : PackageChip::tryFrom($this->resource);

        if (!$chip) {
            return [
                "value" => $this->resource,
                "label" => ucfirst($this->resource),
                "color" => "default",
                "icon" => "heroicon-o-tag",
            ];
        }

        return [
            "value" => $chip->value,
            "label" => $chip->getLabel(),
            "color" => $chip->getColor(),
            "icon" => $chip->getIcon(),
            "hexColor" => $chip->getHexColor(),
            "backgroundColor" => $chip->getBackgroundColor(),
        ];
    }
}
