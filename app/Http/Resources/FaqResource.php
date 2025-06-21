<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "faqs",
            "id" => $this->id,
            "attributes" => [
                "question" => $this->question,
                "answer" => $this->answer,
                "createdAt" => $this->created_at->toIso8601String(),
                "updatedAt" => $this->updated_at->toIso8601String(),
                "createdAtForHumans" => $this->created_at->diffForHumans(),
                "updatedAtForHumans" => $this->updated_at->diffForHumans(),
            ],
        ];
    }
}
