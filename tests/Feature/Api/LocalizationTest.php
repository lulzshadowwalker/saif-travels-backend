<?php

namespace Tests\Feature\Api;

use App\Models\Package;
use App\Models\Retreat;
use App\Models\Destination;
use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the API respects the Accept-Language header for English.
     */
    public function test_api_returns_english_content_with_en_header(): void
    {
        // Create a package with translations
        $package = Package::factory()->create([
            "name" => [
                "en" => "English Package Name",
                "ar" => "اسم الحزمة بالعربية",
            ],
            "description" => [
                "en" => "English description",
                "ar" => "وصف بالعربية",
            ],
            "status" => \App\Enums\PackageStatus::active,
        ]);

        $response = $this->withHeaders([
            "Accept-Language" => "en",
        ])->getJson("/api/packages");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "English Package Name"
        );
        $response->assertJsonPath(
            "data.0.attributes.description",
            "English description"
        );
    }

    /**
     * Test that the API respects the Accept-Language header for Arabic.
     */
    public function test_api_returns_arabic_content_with_ar_header(): void
    {
        // Create a package with translations
        $package = Package::factory()->create([
            "name" => [
                "en" => "English Package Name",
                "ar" => "اسم الحزمة بالعربية",
            ],
            "description" => [
                "en" => "English description",
                "ar" => "وصف بالعربية",
            ],
            "status" => \App\Enums\PackageStatus::active,
        ]);

        $response = $this->withHeaders([
            "Accept-Language" => "ar",
        ])->getJson("/api/packages");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "اسم الحزمة بالعربية"
        );
        $response->assertJsonPath(
            "data.0.attributes.description",
            "وصف بالعربية"
        );
    }

    /**
     * Test that the API defaults to English when no Accept-Language header is provided.
     */
    public function test_api_defaults_to_english_without_header(): void
    {
        // Create a package with translations
        $package = Package::factory()->create([
            "name" => [
                "en" => "English Package Name",
                "ar" => "اسم الحزمة بالعربية",
            ],
            "status" => \App\Enums\PackageStatus::active,
        ]);

        $response = $this->getJson("/api/packages");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "English Package Name"
        );
    }

    /**
     * Test that the API falls back to English for unsupported locales.
     */
    public function test_api_falls_back_to_english_for_unsupported_locale(): void
    {
        // Create a package with translations
        $package = Package::factory()->create([
            "name" => [
                "en" => "English Package Name",
                "ar" => "اسم الحزمة بالعربية",
            ],
            "status" => \App\Enums\PackageStatus::active,
        ]);

        $response = $this->withHeaders([
            "Accept-Language" => "fr",
        ])->getJson("/api/packages");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "English Package Name"
        );
    }

    /**
     * Test that the API handles complex Accept-Language headers with quality values.
     */
    public function test_api_handles_complex_accept_language_header(): void
    {
        // Create a package with translations
        $package = Package::factory()->create([
            "name" => [
                "en" => "English Package Name",
                "ar" => "اسم الحزمة بالعربية",
            ],
            "status" => \App\Enums\PackageStatus::active,
        ]);

        // Test with Arabic preferred over English
        $response = $this->withHeaders([
            "Accept-Language" => "ar;q=0.9,en;q=0.8",
        ])->getJson("/api/packages");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "اسم الحزمة بالعربية"
        );

        // Test with English preferred over Arabic
        $response = $this->withHeaders([
            "Accept-Language" => "en;q=0.9,ar;q=0.8",
        ])->getJson("/api/packages");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "English Package Name"
        );
    }

    /**
     * Test that the API handles regional language codes.
     */
    public function test_api_handles_regional_language_codes(): void
    {
        // Create a package with translations
        $package = Package::factory()->create([
            "name" => [
                "en" => "English Package Name",
                "ar" => "اسم الحزمة بالعربية",
            ],
            "status" => \App\Enums\PackageStatus::active,
        ]);

        // Test with en-US
        $response = $this->withHeaders([
            "Accept-Language" => "en-US",
        ])->getJson("/api/packages");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "English Package Name"
        );

        // Test with ar-SA
        $response = $this->withHeaders([
            "Accept-Language" => "ar-SA",
        ])->getJson("/api/packages");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "اسم الحزمة بالعربية"
        );
    }

    /**
     * Test localization works for retreats endpoint.
     */
    public function test_retreats_api_respects_locale(): void
    {
        // Create a retreat with translations
        $retreat = Retreat::factory()->create([
            "name" => [
                "en" => "English Retreat Name",
                "ar" => "اسم الخلوة بالعربية",
            ],
            "status" => \App\Enums\RetreatStatus::active,
        ]);

        // Test English
        $response = $this->withHeaders([
            "Accept-Language" => "en",
        ])->getJson("/api/retreats");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "English Retreat Name"
        );

        // Test Arabic
        $response = $this->withHeaders([
            "Accept-Language" => "ar",
        ])->getJson("/api/retreats");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "اسم الخلوة بالعربية"
        );
    }

    /**
     * Test localization works for destinations endpoint.
     */
    public function test_destinations_api_respects_locale(): void
    {
        // Create a destination with translations
        $destination = Destination::factory()->create([
            "name" => [
                "en" => "English Destination",
                "ar" => "وجهة بالعربية",
            ],
        ]);

        // Test English
        $response = $this->withHeaders([
            "Accept-Language" => "en",
        ])->getJson("/api/destinations");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.name",
            "English Destination"
        );

        // Test Arabic
        $response = $this->withHeaders([
            "Accept-Language" => "ar",
        ])->getJson("/api/destinations");

        $response->assertStatus(200);
        $response->assertJsonPath("data.0.attributes.name", "وجهة بالعربية");
    }

    /**
     * Test localization works for FAQs endpoint.
     */
    public function test_faqs_api_respects_locale(): void
    {
        // Create a FAQ with translations
        $faq = Faq::factory()->create([
            "question" => [
                "en" => "English Question?",
                "ar" => "سؤال بالعربية؟",
            ],
            "answer" => [
                "en" => "English Answer",
                "ar" => "إجابة بالعربية",
            ],
        ]);

        // Test English
        $response = $this->withHeaders([
            "Accept-Language" => "en",
        ])->getJson("/api/faqs");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.question",
            "English Question?"
        );
        $response->assertJsonPath("data.0.attributes.answer", "English Answer");

        // Test Arabic
        $response = $this->withHeaders([
            "Accept-Language" => "ar",
        ])->getJson("/api/faqs");

        $response->assertStatus(200);
        $response->assertJsonPath(
            "data.0.attributes.question",
            "سؤال بالعربية؟"
        );
        $response->assertJsonPath("data.0.attributes.answer", "إجابة بالعربية");
    }

    /**
     * Test that single resource endpoints also respect locale.
     */
    public function test_single_resource_endpoints_respect_locale(): void
    {
        // Create a package with translations
        $package = Package::factory()->create([
            "name" => [
                "en" => "English Package",
                "ar" => "حزمة بالعربية",
            ],
            "status" => \App\Enums\PackageStatus::active,
        ]);

        // Test single package in English
        $response = $this->withHeaders([
            "Accept-Language" => "en",
        ])->getJson("/api/packages/{$package->slug}");

        $response->assertStatus(200);
        $response->assertJsonPath("data.attributes.name", "English Package");

        // Test single package in Arabic
        $response = $this->withHeaders([
            "Accept-Language" => "ar",
        ])->getJson("/api/packages/{$package->slug}");

        $response->assertStatus(200);
        $response->assertJsonPath("data.attributes.name", "حزمة بالعربية");
    }
}
