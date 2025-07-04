<?php

namespace Database\Factories;

use App\Enums\PackageChip;

class PackageFactory extends BaseFactory
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
            "slug" => $this->faker->unique()->slug,
            "description" => $this->localized(
                fn(): string => $this->faker->paragraph
            ),
            "tags" => implode(",", $this->faker->words(3)),
            "chips" => $this->faker->randomElements(
                PackageChip::cases(),
                rand(1, min(count(PackageChip::cases()), 3))
            ),
            "goal" => $this->localized(fn() => $this->faker->sentence),
            "durations" => $this->faker->numberBetween(1, 14),
            "program" => $this->localized(
                fn() => $this->faker->sentence,
                $this->faker->sentence
            ),
            "activities" => $this->localized(
                fn() => $this->faker->word,
                $this->faker->word
            ),
            "stay" => $this->localized(fn() => $this->faker->company),
            "iv_drips" => $this->localized(
                fn() => $this->faker->word,
                $this->faker->word
            ),
            "status" => $this->faker->randomElement(
                \App\Enums\PackageStatus::values()
            ),
        ];
    }
}
