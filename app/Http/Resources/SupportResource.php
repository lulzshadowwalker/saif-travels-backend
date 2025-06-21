<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HasTimestamps;
use App\Http\Resources\Concerns\HasStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportResource extends JsonResource
{
    use HasTimestamps, HasStatus;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "support-requests",
            "id" => $this->id,
            "attributes" => [
                "name" => $this->name,
                "email" => $this->email,
                "phone" => $this->phone,
                "status" => $this->formatStatus(),
                ...$this->timestamps(),
            ],
        ];
    }
}
