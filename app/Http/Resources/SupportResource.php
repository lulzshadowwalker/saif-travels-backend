<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->refresh();

        return [
            "type" => "support-requests",
            "id" => $this->id,
            "attributes" => [
                "name" => $this->name,
                "email" => $this->email,
                "phone" => $this->phone,
                "status" => [
                    "value" => $this->status->value,
                    "label" => $this->status->getLabel(),
                    "color" => $this->status->getColor(),
                ],
                "createdAt" => $this->created_at->toIso8601String(),
                "updatedAt" => $this->updated_at->toIso8601String(),
                "createdAtForHumans" => $this->created_at->diffForHumans(),
                "updatedAtForHumans" => $this->updated_at->diffForHumans(),
            ],
        ];
    }
}
