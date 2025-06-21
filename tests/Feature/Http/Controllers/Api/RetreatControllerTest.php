<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Retreat;
use App\Models\Package;
use App\Enums\RetreatStatus;
use App\Enums\PackageStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RetreatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Collection $retreats;
    protected Collection $packages;

    public function setUp(): void
    {
        parent::setUp();

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

        // Create retreats and attach packages
        $this->retreats = collect([
            Retreat::factory()->create([
                "name" => [
                    "en" => "Wellness Retreat",
                    "ar" => "الملاذ الصحي",
                ],
                "status" => RetreatStatus::active,
            ]),
            Retreat::factory()->create([
                "name" => [
                    "en" => "Adventure Retreat",
                    "ar" => "ملاذ المغامرة",
                ],
                "status" => RetreatStatus::inactive,
            ]),
            Retreat::factory()->create([
                "name" => [
                    "en" => "Luxury Retreat",
                    "ar" => "ملاذ الفخامة",
                ],
                "status" => RetreatStatus::active,
            ]),
        ]);

        $this->retreats[0]->packages()->attach([
            $this->packages[0]->id,
            $this->packages[2]->id, // inactive
        ]);
        $this->retreats[1]->packages()->attach([$this->packages[1]->id]);
    }

    /** @test */
    public function it_lists_all_retreats(): void
    {
        $response = $this->getJson(route("api.retreats.index"));

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
                            "status",
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
    public function it_paginates_retreats_correctly(): void
    {
        // Create more retreats for pagination testing
        Retreat::factory()->count(20)->create();

        $response = $this->getJson(
            route("api.retreats.index", ["per_page" => 5])
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
        $response = $this->getJson(route("api.retreats.index"));

        $response->assertOk();

        $retreatData = collect($response->json("data"));

        $wellness = $retreatData->firstWhere(
            "attributes.name",
            "Wellness Retreat"
        );
        $this->assertNotNull(
            $wellness,
            "Wellness Retreat not found in response data"
        );
        $this->assertEquals(2, $wellness["meta"]["packagesCount"]);

        $adventure = $retreatData->firstWhere(
            "attributes.name",
            "Adventure Retreat"
        );
        $this->assertNotNull(
            $adventure,
            "Adventure Retreat not found in response data"
        );
        $this->assertEquals(1, $adventure["meta"]["packagesCount"]);

        $luxury = $retreatData->firstWhere("attributes.name", "Luxury Retreat");
        $this->assertNotNull(
            $luxury,
            "Luxury Retreat not found in response data"
        );
        $this->assertEquals(0, $luxury["meta"]["packagesCount"]);
    }

    /** @test */
    public function it_includes_human_readable_dates(): void
    {
        $response = $this->getJson(route("api.retreats.index"));

        $response->assertOk();

        $firstRetreat = collect($response->json("data"))->first();

        $this->assertArrayHasKey(
            "createdAtForHumans",
            $firstRetreat["attributes"]
        );
        $this->assertArrayHasKey(
            "updatedAtForHumans",
            $firstRetreat["attributes"]
        );
        $this->assertStringContainsString(
            "ago",
            $firstRetreat["attributes"]["createdAtForHumans"]
        );
    }

    /** @test */
    public function it_returns_empty_collection_when_no_retreats(): void
    {
        // Delete all retreats
        Retreat::query()->delete();

        $response = $this->getJson(route("api.retreats.index"));

        $response
            ->assertOk()
            ->assertJsonCount(0, "data")
            ->assertJsonPath("meta.total", 0);
    }

    /** @test */
    public function it_handles_pagination_parameters(): void
    {
        // Create total of 25 retreats
        Retreat::factory()->count(22)->create();

        // Test first page
        $response = $this->getJson(
            route("api.retreats.index", ["page" => 1, "per_page" => 10])
        );

        $response
            ->assertOk()
            ->assertJsonCount(10, "data")
            ->assertJsonPath("meta.current_page", 1)
            ->assertJsonPath("meta.last_page", 3)
            ->assertJsonPath("meta.total", 25);

        // Test last page
        $response = $this->getJson(
            route("api.retreats.index", ["page" => 3, "per_page" => 10])
        );

        $response
            ->assertOk()
            ->assertJsonCount(5, "data")
            ->assertJsonPath("meta.current_page", 3);
    }

    /** @test */
    public function it_uses_default_pagination_when_per_page_not_provided(): void
    {
        // Create enough retreats to exceed default pagination
        Retreat::factory()->count(17)->create();

        $response = $this->getJson(route("api.retreats.index"));

        $response
            ->assertOk()
            ->assertJsonCount(15, "data") // Default per_page is 15
            ->assertJsonPath("meta.per_page", 15)
            ->assertJsonPath("meta.total", 20); // 3 from setUp + 17 new ones
    }

    /** @test */
    public function it_includes_packages_relationship(): void
    {
        $retreat = $this->retreats[0];

        $response = $this->getJson(route("api.retreats.index"));

        $response->assertOk();

        $retreatData = collect($response->json("data"))->firstWhere(
            "id",
            $retreat->id
        );

        $this->assertArrayHasKey("relationships", $retreatData);
        $this->assertArrayHasKey("packages", $retreatData["relationships"]);
        $this->assertIsArray($retreatData["relationships"]["packages"]);
    }

    /** @test */
    public function it_shows_retreat_with_details(): void
    {
        $retreat = $this->retreats[0];

        $response = $this->getJson(
            route("api.retreats.show", ["retreat" => $retreat->id])
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                    "type",
                    "id",
                    "attributes" => [
                        "name",
                        "status",
                        "createdAt",
                        "updatedAt",
                        "createdAtForHumans",
                        "updatedAtForHumans",
                    ],
                    "relationships" => ["packages"],
                    "meta" => ["packagesCount"],
                    "links" => ["self"],
                ],
            ])
            ->assertJsonPath("data.id", $retreat->id);
    }

    /** @test */
    public function it_returns_packages_with_minimal_data(): void
    {
        $retreat = $this->retreats[1]; // Adventure Retreat with 1 package

        $response = $this->getJson(route("api.retreats.index"));

        $response->assertOk();

        $retreatData = collect($response->json("data"))->firstWhere(
            "id",
            $retreat->id
        );
        $package = $retreatData["relationships"]["packages"][0] ?? null;

        $this->assertNotNull($package);
        $this->assertArrayHasKey("type", $package);
        $this->assertArrayHasKey("id", $package);
        $this->assertArrayHasKey("attributes", $package);
        $this->assertArrayHasKey("links", $package);

        $attributes = $package["attributes"];
        $this->assertArrayHasKey("name", $attributes);
        $this->assertArrayHasKey("slug", $attributes);
        $this->assertArrayHasKey("durations", $attributes);
        $this->assertArrayHasKey("durationsDays", $attributes);
        $this->assertArrayHasKey("tags", $attributes);
        $this->assertArrayHasKey("status", $attributes);
        $this->assertArrayHasKey("isActive", $attributes);

        $this->assertArrayNotHasKey("retreats", $attributes);
        $this->assertArrayNotHasKey("relationships", $package);
    }
}
