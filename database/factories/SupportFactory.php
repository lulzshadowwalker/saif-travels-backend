<?php

namespace Database\Factories;

use App\Enums\SupportStatus;

class SupportFactory extends BaseFactory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => $this->faker->name(),
            "email" => $this->faker->email(),
            "phone" => $this->faker->phoneNumber(),
            "message" => $this->faker->paragraph(3),
            "status" => $this->faker->randomElement(SupportStatus::values()),
        ];
    }
}
