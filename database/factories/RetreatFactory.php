<?php

namespace Database\Factories;

use App\Enums\RetreatStatus;

class RetreatFactory extends BaseFactory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => $this->localized(
                fn(): string => $this->faker->words(2, true)
            ),
            "status" => $this->faker->randomElement(RetreatStatus::values()),
        ];
    }
}
