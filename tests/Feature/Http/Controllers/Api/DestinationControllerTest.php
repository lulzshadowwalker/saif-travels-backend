<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Destination;
use App\Models\Package;
use App\Enums\PackageStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class DestinationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Collection $destinations;
    protected Collection $packages;

    public function setUp(): void
    {
        parent::setUp();

        // Create destinations
        $this->destinations = collect([
            Destination::factory()->create([
                "name" => [
                    "en" => "Dubai",
                    "ar" => "دبي",
                ],
                "slug" => "dubai",
            ]),
            Destination::factory()->create([
                "name" => [
                    "en" => "Abu Dhabi",
                    "ar" => "أبو ظبي",
                ],
                "slug" => "abu-dhabi",
            ]),
            Destination::factory()->create([
                "name" => [
                    "en" => "Sharjah",
                    "ar" => "الشارقة",
                ],
                "slug" => "sharjah",
            ]),
        ]);

        // Create packages with different statuses
        $this->packages = collect([
            Package::factory()->create([
                "status" => PackageStatus::active,
                "name" => [
                    "en" => "Active Package 1",
                    "ar" => "الباقة النشطة 1",
                ],
            ]),
            Package::factory()->create([
                "status" => PackageStatus::active,
                "name" => [
                    "en" => "Active Package 2",
                    "ar" => "الباقة النشطة 2",
                ],
            ]),
            Package::factory()->create([
                "status" => PackageStatus::inactive,
                "name" => [
                    "en" => "Inactive Package",
                    "ar" => "الباقة غير النشطة",
                ],
            ]),
        ]);

        // Attach packages to destinations
        $this->destinations[0]->packages()->attach([
            $this->packages[0]->id,
            $this->packages[2]->id, // inactive
        ]);
        $this->destinations[1]->packages()->attach([$this->packages[1]->id]);
    }

    /** @test */
    public function it_lists_all_destinations(): void
    {
        $response = $this->getJson(route("api.destinations.index"));

        $response
            ->assertOk()
            ->assertJsonCount(3, "data")
            ->assertJsonStructure([
                "data" => [
                    "*" => [
                        "type",
                        "id",
                        "attributes" => [
                            "name",
                            "slug",
                            "createdAt",
                            "updatedAt",
                            "createdAtForHumans",
                            "updatedAtForHumans",
                        ],
                        "meta" => ["packagesCount"],
                        "links" => ["self"],
                    ],
                ],
                "meta",
                "links",
            ]);
    }

    /** @test */
    public function it_orders_destinations_by_name(): void
    {
        $response = $this->getJson(route("api.destinations.index"));

        $response->assertOk();

        $names = collect($response->json("data"))
            ->pluck("attributes.name")
            ->toArray();

        $this->assertEquals(["Abu Dhabi", "Dubai", "Sharjah"], $names);
    }

    /** @test */
    public function it_paginates_destinations_correctly(): void
    {
        // Create more destinations for pagination testing
        Destination::factory()->count(20)->create();

        $response = $this->getJson(
            route("api.destinations.index", ["per_page" => 5])
        );

        $response
            ->assertOk()
            ->assertJsonCount(5, "data")
            ->assertJsonPath("meta.per_page", 5)
            ->assertJsonPath("meta.total", 23); // 3 from setUp + 20 new ones
    }

    /** @test */
    public function it_includes_packages_count(): void
    {
        $response = $this->getJson(route("api.destinations.index"));

        $response->assertOk();

        $destinationData = collect($response->json("data"));

        $dubai = $destinationData->firstWhere("attributes.slug", "dubai");
        $abuDhabi = $destinationData->firstWhere(
            "attributes.slug",
            "abu-dhabi"
        );
        $sharjah = $destinationData->firstWhere("attributes.slug", "sharjah");

        $this->assertEquals(2, $dubai["meta"]["packagesCount"]);
        $this->assertEquals(1, $abuDhabi["meta"]["packagesCount"]);
        $this->assertEquals(0, $sharjah["meta"]["packagesCount"]);
    }

    /** @test */
    public function it_shows_destination_with_details(): void
    {
        $destination = $this->destinations[0];

        $response = $this->getJson(
            route("api.destinations.show", [
                "destination" => $destination->slug,
            ])
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                    "type",
                    "id",
                    "attributes" => [
                        "name",
                        "slug",
                        "createdAt",
                        "updatedAt",
                        "createdAtForHumans",
                        "updatedAtForHumans",
                    ],
                    "relationships" => ["packages", "media" => ["images"]],
                    "meta" => ["packagesCount"],
                    "links" => ["self"],
                ],
            ])
            ->assertJsonPath("data.id", $destination->id)
            ->assertJsonPath("data.attributes.slug", "dubai");
    }

    /** @test */
    public function it_shows_only_active_packages_in_destination(): void
    {
        $destination = $this->destinations[0]; // Dubai with 1 active, 1 inactive package

        $response = $this->getJson(
            route("api.destinations.show", [
                "destination" => $destination->slug,
            ])
        );

        $response->assertOk();

        // Should only return the active package
        $packages = $response->json("data.relationships.packages");
        $this->assertCount(1, $packages);
        $this->assertTrue($packages[0]["attributes"]["isActive"]);
        $this->assertEquals(1, $response->json("data.meta.packagesCount"));
    }

    /** @test */
    public function it_returns_404_for_non_existent_destination(): void
    {
        $response = $this->getJson(
            route("api.destinations.show", [
                "destination" => "non-existent-slug",
            ])
        );

        $response->assertNotFound();
    }

    /** @test */
    public function it_includes_human_readable_dates(): void
    {
        $response = $this->getJson(route("api.destinations.index"));

        $response->assertOk();

        $firstDestination = collect($response->json("data"))->first();

        $this->assertArrayHasKey(
            "createdAtForHumans",
            $firstDestination["attributes"]
        );
        $this->assertArrayHasKey(
            "updatedAtForHumans",
            $firstDestination["attributes"]
        );
        $this->assertStringContainsString(
            "ago",
            $firstDestination["attributes"]["createdAtForHumans"]
        );
    }

    /** @test */
    public function it_returns_packages_with_minimal_data(): void
    {
        $destination = $this->destinations[1]; // Abu Dhabi with 1 active package

        $response = $this->getJson(
            route("api.destinations.show", [
                "destination" => $destination->slug,
            ])
        );

        $response->assertOk();

        $package = $response->json("data.relationships.packages.0");

        // Verify minimal package data structure
        $this->assertArrayHasKey("type", $package);
        $this->assertArrayHasKey("id", $package);
        $this->assertArrayHasKey("attributes", $package);
        $this->assertArrayHasKey("links", $package);

        // Verify essential attributes
        $attributes = $package["attributes"];
        $this->assertArrayHasKey("name", $attributes);
        $this->assertArrayHasKey("slug", $attributes);
        $this->assertArrayHasKey("durations", $attributes);
        $this->assertArrayHasKey("durationsDays", $attributes);
        $this->assertArrayHasKey("tags", $attributes);
        $this->assertArrayHasKey("status", $attributes);
        $this->assertArrayHasKey("isActive", $attributes);

        // Verify no circular references (shouldn't include destinations)
        $this->assertArrayNotHasKey("destinations", $attributes);
        $this->assertArrayNotHasKey("relationships", $package);
    }

    /** @test */
    public function it_handles_destinations_without_packages(): void
    {
        $sharjah = $this->destinations[2]; // No packages attached

        $response = $this->getJson(
            route("api.destinations.show", ["destination" => $sharjah->slug])
        );

        $response
            ->assertOk()
            ->assertJsonPath("data.relationships.packages", [])
            ->assertJsonPath("data.meta.packagesCount", 0);
    }

    /** @test */
    public function it_includes_self_link(): void
    {
        $destination = $this->destinations[0];

        $response = $this->getJson(
            route("api.destinations.show", [
                "destination" => $destination->slug,
            ])
        );

        $response->assertOk();

        $selfLink = $response->json("data.links.self");

        $this->assertEquals(
            route("api.destinations.show", [
                "destination" => $destination->slug,
            ]),
            $selfLink
        );
    }

    /** @test */
    public function it_includes_package_tags_as_array(): void
    {
        $destination = $this->destinations[1]; // Abu Dhabi with active package

        $response = $this->getJson(
            route("api.destinations.show", [
                "destination" => $destination->slug,
            ])
        );

        $response->assertOk();

        $package = $response->json("data.relationships.packages.0");
        $tags = $package["attributes"]["tags"];

        $this->assertIsArray($tags);
        $this->assertNotEmpty($tags);
    }

    /** @test */
    public function it_includes_package_status_details(): void
    {
        $destination = $this->destinations[1];

        $response = $this->getJson(
            route("api.destinations.show", [
                "destination" => $destination->slug,
            ])
        );

        $response->assertOk();

        $packageStatus = $response->json(
            "data.relationships.packages.0.attributes.status"
        );

        $this->assertArrayHasKey("value", $packageStatus);
        $this->assertArrayHasKey("label", $packageStatus);
        $this->assertArrayHasKey("color", $packageStatus);
        $this->assertEquals("active", $packageStatus["value"]);
        $this->assertEquals("Active", $packageStatus["label"]);
        $this->assertEquals("success", $packageStatus["color"]);
    }
}
