<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_assigns_slug_on_create()
    {
        $destination = \App\Models\Destination::create([
            "name" => ["en" => "Test Destination"],
        ]);

        $this->assertNotNull($destination->slug);
        $this->assertEquals("test-destination", $destination->slug);
    }

    /** @test */
    public function it_updates_slug_when_name_changes()
    {
        $destination = \App\Models\Destination::create([
            "name" => ["en" => "First Place"],
        ]);

        $destination->name = ["en" => "Second Place"];
        $destination->save();

        $this->assertEquals("second-place", $destination->slug);
    }

    /** @test */
    public function it_assigns_unique_slug_for_duplicate_names()
    {
        $destination1 = \App\Models\Destination::create([
            "name" => ["en" => "Duplicate Place"],
        ]);
        $destination2 = \App\Models\Destination::create([
            "name" => ["en" => "Duplicate Place"],
        ]);

        $this->assertEquals("duplicate-place", $destination1->slug);
        $this->assertEquals("duplicate-place-1", $destination2->slug);
    }
}
