<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Package;
use App\Models\Destination;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("destination_package", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignIdFor(Package::class)
                ->constrained()
                ->cascadeOnDelete();
            $table
                ->foreignIdFor(Destination::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->unique(["package_id", "destination_id"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("destination_package");
    }
};
