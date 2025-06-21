<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FaqControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Collection $faqs;

    public function setUp(): void
    {
        parent::setUp();

        // Create FAQs with translations
        $this->faqs = collect([
            Faq::factory()->create([
                "question" => [
                    "en" => "What are your business hours?",
                    "ar" => "ما هي ساعات العمل؟",
                ],
                "answer" => [
                    "en" =>
                        "We are open from 9 AM to 6 PM, Sunday to Thursday.",
                    "ar" =>
                        "نحن مفتوحون من 9 صباحًا إلى 6 مساءً، من الأحد إلى الخميس.",
                ],
                "created_at" => now()->subDays(3),
            ]),
            Faq::factory()->create([
                "question" => [
                    "en" => "How can I book a package?",
                    "ar" => "كيف يمكنني حجز باقة؟",
                ],
                "answer" => [
                    "en" =>
                        "You can book a package through our website or by calling our office.",
                    "ar" =>
                        "يمكنك حجز باقة من خلال موقعنا الإلكتروني أو بالاتصال بمكتبنا.",
                ],
                "created_at" => now()->subDays(2),
            ]),
            Faq::factory()->create([
                "question" => [
                    "en" => "What is your cancellation policy?",
                    "ar" => "ما هي سياسة الإلغاء لديكم؟",
                ],
                "answer" => [
                    "en" =>
                        "Cancellations must be made at least 48 hours before the trip.",
                    "ar" =>
                        "يجب إجراء الإلغاء قبل 48 ساعة على الأقل من الرحلة.",
                ],
                "created_at" => now()->subDay(),
            ]),
        ]);
    }

    /** @test */
    public function it_lists_all_faqs(): void
    {
        $response = $this->getJson(route("api.faqs.index"));

        $response
            ->assertOk()
            ->assertJsonCount(3, "data")
            ->assertJsonStructure([
                "data" => [
                    "*" => [
                        "type",
                        "id",
                        "attributes" => [
                            "question",
                            "answer",
                            "createdAt",
                            "updatedAt",
                            "createdAtForHumans",
                            "updatedAtForHumans",
                        ],
                    ],
                ],
                "meta",
                "links",
            ]);
    }

    /** @test */
    public function it_returns_faqs_with_correct_data_structure(): void
    {
        $response = $this->getJson(route("api.faqs.index"));

        $response->assertOk();

        $firstFaq = $response->json("data.0");

        $this->assertEquals("faqs", $firstFaq["type"]);
        $this->assertEquals($this->faqs[2]->id, $firstFaq["id"]); // Most recent first
        $this->assertIsArray($firstFaq["attributes"]["question"]);
        $this->assertIsArray($firstFaq["attributes"]["answer"]);
        $this->assertArrayHasKey("en", $firstFaq["attributes"]["question"]);
        $this->assertArrayHasKey("ar", $firstFaq["attributes"]["question"]);
        $this->assertArrayHasKey("en", $firstFaq["attributes"]["answer"]);
        $this->assertArrayHasKey("ar", $firstFaq["attributes"]["answer"]);
    }

    /** @test */
    public function it_orders_faqs_by_created_at_desc(): void
    {
        $response = $this->getJson(route("api.faqs.index"));

        $response->assertOk();

        $faqIds = collect($response->json("data"))->pluck("id")->toArray();

        // Most recent FAQ should be first
        $this->assertEquals(
            [$this->faqs[2]->id, $this->faqs[1]->id, $this->faqs[0]->id],
            $faqIds
        );
    }

    /** @test */
    public function it_paginates_faqs_correctly(): void
    {
        // Create more FAQs for pagination testing
        Faq::factory()->count(20)->create();

        $response = $this->getJson(route("api.faqs.index", ["per_page" => 5]));

        $response
            ->assertOk()
            ->assertJsonCount(5, "data")
            ->assertJsonPath("meta.per_page", 5)
            ->assertJsonPath("meta.total", 23); // 3 from setUp + 20 new ones
    }

    /** @test */
    public function it_includes_human_readable_dates(): void
    {
        $response = $this->getJson(route("api.faqs.index"));

        $response->assertOk();

        $firstFaq = $response->json("data.0");

        $this->assertArrayHasKey("createdAtForHumans", $firstFaq["attributes"]);
        $this->assertArrayHasKey("updatedAtForHumans", $firstFaq["attributes"]);
        $this->assertStringContainsString(
            "ago",
            $firstFaq["attributes"]["createdAtForHumans"]
        );
    }

    /** @test */
    public function it_returns_translations_for_all_fields(): void
    {
        $response = $this->getJson(route("api.faqs.index"));

        $response->assertOk();

        $faq = $response->json("data.0");

        // Check English translations
        $this->assertEquals(
            "What is your cancellation policy?",
            $faq["attributes"]["question"]["en"]
        );
        $this->assertEquals(
            "Cancellations must be made at least 48 hours before the trip.",
            $faq["attributes"]["answer"]["en"]
        );

        // Check Arabic translations
        $this->assertEquals(
            "ما هي سياسة الإلغاء لديكم؟",
            $faq["attributes"]["question"]["ar"]
        );
        $this->assertEquals(
            "يجب إجراء الإلغاء قبل 48 ساعة على الأقل من الرحلة.",
            $faq["attributes"]["answer"]["ar"]
        );
    }

    /** @test */
    public function it_returns_empty_collection_when_no_faqs(): void
    {
        // Delete all FAQs
        Faq::query()->delete();

        $response = $this->getJson(route("api.faqs.index"));

        $response
            ->assertOk()
            ->assertJsonCount(0, "data")
            ->assertJsonPath("meta.total", 0);
    }

    /** @test */
    public function it_handles_pagination_parameters(): void
    {
        // Create total of 25 FAQs
        Faq::factory()->count(22)->create();

        // Test first page
        $response = $this->getJson(
            route("api.faqs.index", ["page" => 1, "per_page" => 10])
        );

        $response
            ->assertOk()
            ->assertJsonCount(10, "data")
            ->assertJsonPath("meta.current_page", 1)
            ->assertJsonPath("meta.last_page", 3)
            ->assertJsonPath("meta.total", 25);

        // Test last page
        $response = $this->getJson(
            route("api.faqs.index", ["page" => 3, "per_page" => 10])
        );

        $response
            ->assertOk()
            ->assertJsonCount(5, "data")
            ->assertJsonPath("meta.current_page", 3);
    }

    /** @test */
    public function it_uses_default_pagination_when_per_page_not_provided(): void
    {
        // Create enough FAQs to exceed default pagination
        Faq::factory()->count(17)->create();

        $response = $this->getJson(route("api.faqs.index"));

        $response
            ->assertOk()
            ->assertJsonCount(15, "data") // Default per_page is 15
            ->assertJsonPath("meta.per_page", 15)
            ->assertJsonPath("meta.total", 20); // 3 from setUp + 17 new ones
    }
}
