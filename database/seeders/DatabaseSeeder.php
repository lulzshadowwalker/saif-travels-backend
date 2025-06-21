<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Package;
use App\Models\Destination;
use App\Models\Support;
use App\Models\Retreat;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            "name" => "John Doe",
            "email" => "admin@example.com",
        ]);

        // Create 5 destinations
        $destinations = Destination::factory(5)->create();

        // Create 10 packages and attach random destinations to each
        $packages = Package::factory(10)
            ->create()
            ->each(function ($package) use ($destinations) {
                $package
                    ->destinations()
                    ->attach(
                        $destinations
                            ->random(rand(1, 3))
                            ->pluck("id")
                            ->toArray()
                    );
            });

        Support::factory()->count(5)->create();

        // Create 3 retreats and attach random packages to each
        Retreat::factory(3)
            ->create()
            ->each(function ($retreat) use ($packages) {
                $retreat
                    ->packages()
                    ->attach(
                        $packages->random(rand(2, 5))->pluck("id")->toArray()
                    );
            });
    }
}
