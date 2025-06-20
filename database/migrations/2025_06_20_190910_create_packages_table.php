<?php

use App\Enums\PackageStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("packages", function (Blueprint $table) {
            $table->id();
            $table->json("name");
            $table->string("slug");
            $table->json("description");
            $table->text("tags");
            $table->json("chips");
            $table->json("goal");
            $table->integer("durations")->comment("days");
            $table->json("program");
            $table->json("activities");
            $table
                ->json("stay")
                ->comment(
                    "represents the residence options available to the guests"
                );
            $table
                ->json("iv_drips")
                ->comment(
                    "represents the IV drip options available to the guests"
                );
            $table
                ->enum("status", PackageStatus::values())
                ->default(PackageStatus::active);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("packages");
    }
};
