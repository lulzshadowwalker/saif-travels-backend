<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HasTimestamps;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
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
            "type" => "faqs",
            "id" => $this->id,
            "attributes" => [
                "question" => $this->question,
                "answer" => $this->answer,
                ...$this->timestamps(),
            ],
        ];
    }
}
