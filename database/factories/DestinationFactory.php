<?php

namespace Database\Factories;

class DestinationFactory extends BaseFactory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => $this->localized(fn(): string => $this->faker->city),
        ];
    }
}
