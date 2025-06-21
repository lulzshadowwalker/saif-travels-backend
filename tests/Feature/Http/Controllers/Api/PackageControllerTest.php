<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\PackageResource;
use App\Models\Package;
use App\Models\Destination;
use App\Enums\PackageStatus;
use App\Enums\PackageChip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PackageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Collection $packages;
    protected Collection $destinations;

    public function setUp(): void
    {
        parent::setUp();

        // Create destinations
        $this->destinations = Destination::factory()->count(3)->create();

        // Create packages with different statuses
        $this->packages = collect([
            Package::factory()->create([
                "name" => [
                    "en" => "Active Package 1",
                    "ar" => "الباقة النشطة 1",
                ],
                "status" => PackageStatus::active,
                "tags" => "wellness, spa, luxury",
                "chips" => [PackageChip::yoga->value],
                "durations" => 7,
                "created_at" => now()->subDays(3),
            ]),
            Package::factory()->create([
                "name" => [
                    "en" => "Active Package 2",
                    "ar" => "الباقة النشطة 2",
                ],
                "status" => PackageStatus::active,
                "tags" => "health, retreat",
                "chips" => [PackageChip::yoga->value],
                "durations" => 14,
                "created_at" => now()->subDays(1),
            ]),
            Package::factory()->create([
                "name" => [
                    "en" => "Inactive Package",
                    "ar" => "الباقة غير النشطة",
                ],
                "status" => PackageStatus::inactive,
                "tags" => "test",
                "chips" => [],
                "durations" => 5,
                "created_at" => now()->subDays(2),
            ]),
        ]);

        // Attach destinations to packages
        $this->packages[0]
            ->destinations()
            ->attach([$this->destinations[0]->id, $this->destinations[1]->id]);
        $this->packages[1]
            ->destinations()
            ->attach([$this->destinations[2]->id]);
    }

    /** @test */
    public function it_paginates_packages_correctly(): void
    {
        // Create more packages for pagination testing
        Package::factory()
            ->count(20)
            ->create(["status" => PackageStatus::active]);

        $response = $this->getJson(
            route("api.packages.index", ["per_page" => 5])
        );

        $response
            ->assertOk()
            ->assertJsonCount(5, "data")
            ->assertJsonPath("meta.per_page", 5)
            ->assertJsonPath("meta.total", 22); // 2 from setUp + 20 new ones
    }

    /** @test */
    public function it_returns_packages_in_correct_order(): void
    {
        $response = $this->getJson(route("api.packages.index"));

        $response->assertOk();

        $createdDates = collect($response->json("data"))
            ->pluck("attributes.createdAt")
            ->map(fn($date) => \Carbon\Carbon::parse($date));

        // Verify descending order
        $sortedDates = $createdDates->sort()->reverse()->values();
        $this->assertEquals($sortedDates, $createdDates);
    }

    /** @test */
    public function it_includes_parsed_tags_array(): void
    {
        $response = $this->getJson(route("api.packages.index"));

        $response->assertOk();

        // The second package (index 1) was created 1 day ago, so it comes first due to DESC ordering
        $firstPackage = collect($response->json("data"))->first();
        $this->assertIsArray($firstPackage["attributes"]["tags"]);
        $this->assertEquals(
            ["health", "retreat"],
            $firstPackage["attributes"]["tags"]
        );
    }

    /** @test */
    public function it_shows_active_package_with_details(): void
    {
        $package = $this->packages[0];
        $package->load(["destinations", "media"]);

        $response = $this->getJson(
            route("api.packages.show", ["package" => $package->slug])
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                    "type",
                    "id",
                    "attributes",
                    "relationships",
                    "links",
                ],
            ])
            ->assertJsonPath("data.id", $package->id)
            ->assertJsonPath("data.attributes.slug", $package->slug)
            ->assertJsonPath("data.attributes.tags", [
                "wellness",
                "spa",
                "luxury",
            ])
            ->assertJsonPath("data.attributes.durationsDays", "7 days")
            ->assertJsonPath("data.attributes.isActive", true);

        // Verify relationships are loaded
        $destinationsData = $response->json(
            "data.relationships.destinations.data"
        );
        $this->assertIsArray($destinationsData);
        $this->assertCount(2, $destinationsData);
    }

    /** @test */
    public function it_returns_404_for_inactive_package(): void
    {
        $inactivePackage = $this->packages[2];

        $response = $this->getJson(
            route("api.packages.show", ["package" => $inactivePackage->slug])
        );

        $response->assertNotFound();
    }

    /** @test */
    public function it_returns_404_for_non_existent_package(): void
    {
        $response = $this->getJson(
            route("api.packages.show", ["package" => "non-existent-slug"])
        );

        $response->assertNotFound();
    }

    /** @test */
    public function it_formats_chips_with_enum_details(): void
    {
        $response = $this->getJson(route("api.packages.index"));

        $response->assertOk();

        $firstPackage = collect($response->json("data"))->first();
        $chips = $firstPackage["attributes"]["chips"];

        $this->assertCount(1, $chips);
        $this->assertEquals("yoga", $chips[0]);
    }

    /** @test */
    public function it_includes_human_readable_dates(): void
    {
        $response = $this->getJson(route("api.packages.index"));

        $response->assertOk();

        $firstPackage = collect($response->json("data"))->first();

        $this->assertArrayHasKey(
            "createdAtForHumans",
            $firstPackage["attributes"]
        );
        $this->assertArrayHasKey(
            "updatedAtForHumans",
            $firstPackage["attributes"]
        );
        $this->assertStringContainsString(
            "ago",
            $firstPackage["attributes"]["createdAtForHumans"]
        );
    }

    /** @test */
    public function it_includes_status_details(): void
    {
        $response = $this->getJson(route("api.packages.index"));

        $response->assertOk();

        $firstPackage = collect($response->json("data"))->first();
        $status = $firstPackage["attributes"]["status"];

        $this->assertEquals("active", $status);
    }

    /** @test */
    public function it_handles_packages_without_destinations(): void
    {
        $packageWithoutDestinations = Package::factory()->create([
            "status" => PackageStatus::active,
            "chips" => [],
        ]);

        $response = $this->getJson(
            route("api.packages.show", [
                "package" => $packageWithoutDestinations->slug,
            ])
        );

        $response->assertOk();

        $destinationsData = $response->json(
            "data.relationships.destinations.data"
        );
        $this->assertIsArray($destinationsData);
        $this->assertCount(0, $destinationsData);
    }
}
