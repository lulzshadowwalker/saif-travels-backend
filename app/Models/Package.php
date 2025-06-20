<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Package extends Model implements HasTranslations
{
    /** @use HasFactory<\Database\Factories\PackageFactory> */
    use HasFactory;

    /**
     * The attributes that are translatable using Spatie Translatable.
     *
     * @var array<int, string>
     */
    public $translatable = [
        "name",
        "description",
        "chips",
        "goal",
        "program",
        "activities",
        "stay",
        "iv_drips",
    ];

    protected $fillable = [
        "name",
        "slug",
        "description",
        "tags",
        "chips",
        "goal",
        "durations",
        "program",
        "activities",
        "stay",
        "iv_drips",
        "status",
    ];

    protected function casts(): array
    {
        return [
            "name" => "array",
            "description" => "array",
            "tags" => "string",
            "chips" => "array",
            "goal" => "array",
            "durations" => "integer",
            "program" => "array",
            "activities" => "array",
            "stay" => "array",
            "iv_drips" => "array",
            "status" => \App\Enums\PackageStatus::class,
        ];
    }

    public function destinations()
    {
        return $this->belongsToMany(Destination::class, "destination_package");
    }
}
