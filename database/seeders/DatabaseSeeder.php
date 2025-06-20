<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Package;
use App\Models\Destination;
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
        Package::factory(10)
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
    }
}
