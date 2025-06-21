<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RetreatStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("retreats", function (Blueprint $table) {
            $table->id();
            $table->json("name");
            $table
                ->enum("status", RetreatStatus::values())
                ->default(RetreatStatus::active);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("retreats");
    }
};
