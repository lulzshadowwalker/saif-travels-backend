<?php

namespace App\Models;

use App\Enums\PackageChip;
use App\Enums\PackageStatus;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Observers\PackageObserver;
use App\Services\TagParser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy(PackageObserver::class)]
class Package extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\PackageFactory> */
    use HasFactory, HasTranslations, InteractsWithMedia;

    /**
     * The attributes that are translatable using Spatie Translatable.
     *
     * @var array<int, string>
     */
    public $translatable = [
        "name",
        "description",
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
            "chips" => "json",
            "goal" => "array",
            "durations" => "integer",
            "program" => "array",
            "activities" => "array",
            "stay" => "array",
            "iv_drips" => "array",
            "status" => PackageStatus::class,
        ];
    }

    public function destinations()
    {
        return $this->belongsToMany(Destination::class, "destination_package");
    }

    const MEDIA_COLLECTION_IMAGES = "package-images";

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_IMAGES);
    }

    /**
     * Get the tags as an array using TagParser.
     */
    protected function tagsArray(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            $parser = new TagParser();
            return $parser->parseSimple($attributes["tags"] ?? "");
        });
    }

    public function retreats(): BelongsToMany
    {
        return $this->belongsToMany(Retreat::class, "retreat_package");
    }
}
