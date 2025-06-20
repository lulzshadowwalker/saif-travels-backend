<?php

namespace Database\Factories;

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
            "chips" => $this->localized(fn(): array => $this->faker->words(2)),
            "goal" => $this->localized(fn(): array => [$this->faker->sentence]),
            "durations" => $this->faker->numberBetween(1, 14),
            "program" => $this->localized(
                fn(): array => [$this->faker->sentence, $this->faker->sentence]
            ),
            "activities" => $this->localized(
                fn(): array => [$this->faker->word, $this->faker->word]
            ),
            "stay" => $this->localized(fn(): array => [$this->faker->company]),
            "iv_drips" => $this->localized(
                fn(): array => [$this->faker->word, $this->faker->word]
            ),
            "status" => $this->faker->randomElement(
                \App\Enums\PackageStatus::cases()
            )->value,
        ];
    }
}
