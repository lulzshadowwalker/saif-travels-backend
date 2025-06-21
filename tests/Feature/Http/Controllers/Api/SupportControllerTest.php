<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Support;
use App\Enums\SupportStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_new_support_request(): void
    {
        $data = [
            "name" => "John Doe",
            "email" => "john@example.com",
            "phone" => "+971501234567",
        ];

        $response = $this->postJson(route("api.support.store"), $data);

        $response->assertCreated()->assertJsonStructure([
            "data" => [
                "type",
                "id",
                "attributes" => [
                    "name",
                    "email",
                    "phone",
                    "status" => ["value", "label", "color"],
                    "createdAt",
                    "updatedAt",
                    "createdAtForHumans",
                    "updatedAtForHumans",
                ],
            ],
        ]);

        $this->assertDatabaseHas("supports", [
            "name" => "John Doe",
            "email" => "john@example.com",
            "phone" => "+971501234567",
            "status" => SupportStatus::open->value,
        ]);
    }

    /** @test */
    public function it_returns_correct_data_structure_for_support_request(): void
    {
        $data = [
            "name" => "Jane Smith",
            "email" => "jane@example.com",
            "phone" => "+971509876543",
        ];

        $response = $this->postJson(route("api.support.store"), $data);

        $response->assertCreated();

        $responseData = $response->json("data");

        $this->assertEquals("support-requests", $responseData["type"]);
        $this->assertIsInt($responseData["id"]);
        $this->assertEquals("Jane Smith", $responseData["attributes"]["name"]);
        $this->assertEquals(
            "jane@example.com",
            $responseData["attributes"]["email"]
        );
        $this->assertEquals(
            "+971509876543",
            $responseData["attributes"]["phone"]
        );
    }

    /** @test */
    public function it_sets_default_status_to_open(): void
    {
        $data = [
            "name" => "Test User",
            "email" => "test@example.com",
            "phone" => "+971501111111",
        ];

        $response = $this->postJson(route("api.support.store"), $data);

        $response->assertCreated();

        $status = $response->json("data.attributes.status");

        $this->assertEquals("open", $status["value"]);
        $this->assertEquals("Open", $status["label"]);
        $this->assertEquals("primary", $status["color"]);
    }

    /** @test */
    public function it_validates_required_fields(): void
    {
        $response = $this->postJson(route("api.support.store"), []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(["name", "email", "phone"]);
    }

    /** @test */
    public function it_validates_name_field(): void
    {
        $data = [
            "email" => "test@example.com",
            "phone" => "+971501234567",
        ];

        // Test missing name
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertJsonValidationErrors(["name"]);

        // Test empty name
        $data["name"] = "";
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertJsonValidationErrors(["name"]);

        // Test name too long
        $data["name"] = str_repeat("a", 256);
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertJsonValidationErrors(["name"]);

        // Test valid name
        $data["name"] = "Valid Name";
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertCreated();
    }

    /** @test */
    public function it_validates_email_field(): void
    {
        $data = [
            "name" => "Test User",
            "phone" => "+971501234567",
        ];

        // Test missing email
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertJsonValidationErrors(["email"]);

        // Test invalid email format
        $data["email"] = "invalid-email";
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertJsonValidationErrors(["email"]);

        // Test email too long
        $data["email"] = str_repeat("a", 250) . "@test.com";
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertJsonValidationErrors(["email"]);

        // Test valid email
        $data["email"] = "valid@example.com";
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertCreated();
    }

    /** @test */
    public function it_validates_phone_field(): void
    {
        $data = [
            "name" => "Test User",
            "email" => "test@example.com",
        ];

        // Test missing phone
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertJsonValidationErrors(["phone"]);

        // Test empty phone
        $data["phone"] = "";
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertJsonValidationErrors(["phone"]);

        // Test phone too long
        $data["phone"] = str_repeat("1", 256);
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertJsonValidationErrors(["phone"]);

        // Test valid phone
        $data["phone"] = "+971501234567";
        $response = $this->postJson(route("api.support.store"), $data);
        $response->assertCreated();
    }

    /** @test */
    public function it_accepts_various_phone_formats(): void
    {
        $phoneFormats = [
            "+971501234567",
            "971501234567",
            "0501234567",
            "+1-555-123-4567",
            "(555) 123-4567",
            "555.123.4567",
        ];

        foreach ($phoneFormats as $phone) {
            $response = $this->postJson(route("api.support.store"), [
                "name" => "Test User",
                "email" => "test@example.com",
                "phone" => $phone,
            ]);

            $response->assertCreated();
            $this->assertEquals(
                $phone,
                $response->json("data.attributes.phone")
            );
        }
    }

    /** @test */
    public function it_strips_extra_whitespace_from_string_fields(): void
    {
        $response = $this->postJson(route("api.support.store"), [
            "name" => "  John Doe  ",
            "email" => "  john@example.com  ",
            "phone" => "  +971501234567  ",
        ]);

        $response->assertCreated();

        $support = Support::latest()->first();

        // Laravel's validation automatically trims input values
        $this->assertEquals("John Doe", $support->name);
        $this->assertEquals("john@example.com", $support->email);
        $this->assertEquals("+971501234567", $support->phone);
    }

    /** @test */
    public function it_handles_special_characters_in_fields(): void
    {
        $data = [
            "name" => "John O'Brien & Sons",
            "email" => "john.obrien+support@example.com",
            "phone" => "+971-50-123-4567",
        ];

        $response = $this->postJson(route("api.support.store"), $data);

        $response->assertCreated();

        $attributes = $response->json("data.attributes");

        $this->assertEquals($data["name"], $attributes["name"]);
        $this->assertEquals($data["email"], $attributes["email"]);
        $this->assertEquals($data["phone"], $attributes["phone"]);
    }

    /** @test */
    public function it_returns_created_support_request_id(): void
    {
        $response = $this->postJson(route("api.support.store"), [
            "name" => "Test User",
            "email" => "test@example.com",
            "phone" => "+971501234567",
        ]);

        $response->assertCreated();

        $supportId = $response->json("data.id");

        $this->assertIsInt($supportId);
        $this->assertDatabaseHas("supports", [
            "id" => $supportId,
            "email" => "test@example.com",
        ]);
    }
}
