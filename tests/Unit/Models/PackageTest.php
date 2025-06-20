<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_assigns_slug_on_create()
    {
        $package = \App\Models\Package::create([
            "name" => ["en" => "Test Package"],
            "description" => ["en" => "desc"],
            "chips" => ["en" => "chips"],
            "goal" => ["en" => "goal"],
            "program" => ["en" => "program"],
            "activities" => ["en" => "activities"],
            "stay" => ["en" => "stay"],
            "iv_drips" => ["en" => "iv drips"],
            "tags" => "tag1,tag2",
            "durations" => 5,
            "status" => \App\Enums\PackageStatus::active,
        ]);

        $this->assertNotNull($package->slug);
        $this->assertEquals("test-package", $package->slug);
    }

    /** @test */
    public function it_updates_slug_when_name_changes()
    {
        $package = \App\Models\Package::create([
            "name" => ["en" => "First Name"],
            "description" => ["en" => "desc"],
            "chips" => ["en" => "chips"],
            "goal" => ["en" => "goal"],
            "program" => ["en" => "program"],
            "activities" => ["en" => "activities"],
            "stay" => ["en" => "stay"],
            "iv_drips" => ["en" => "iv drips"],
            "tags" => "tag1,tag2",
            "durations" => 5,
            "status" => \App\Enums\PackageStatus::active,
        ]);

        $package->name = ["en" => "Second Name"];
        $package->save();

        $this->assertEquals("second-name", $package->slug);
    }

    /** @test */
    public function it_assigns_unique_slug_for_duplicate_names()
    {
        $package1 = \App\Models\Package::create([
            "name" => ["en" => "Duplicate Name"],
            "description" => ["en" => "desc"],
            "chips" => ["en" => "chips"],
            "goal" => ["en" => "goal"],
            "program" => ["en" => "program"],
            "activities" => ["en" => "activities"],
            "stay" => ["en" => "stay"],
            "iv_drips" => ["en" => "iv drips"],
            "tags" => "tag1,tag2",
            "durations" => 5,
            "status" => \App\Enums\PackageStatus::active,
        ]);
        $package2 = \App\Models\Package::create([
            "name" => ["en" => "Duplicate Name"],
            "description" => ["en" => "desc"],
            "chips" => ["en" => "chips"],
            "goal" => ["en" => "goal"],
            "program" => ["en" => "program"],
            "activities" => ["en" => "activities"],
            "stay" => ["en" => "stay"],
            "iv_drips" => ["en" => "iv drips"],
            "tags" => "tag1,tag2",
            "durations" => 5,
            "status" => \App\Enums\PackageStatus::active,
        ]);

        $this->assertEquals("duplicate-name", $package1->slug);
        $this->assertEquals("duplicate-name-1", $package2->slug);
    }
}
