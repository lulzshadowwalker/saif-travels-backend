<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Destination extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\DestinationFactory> */
    use HasFactory, HasTranslations, InteractsWithMedia;

    public $translatable = ["name"];

    protected $fillable = ["name"];

    protected function casts(): array
    {
        return [
            "name" => "array",
        ];
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, "destination_package");
    }

    const MEDIA_COLLECTION_IMAGES = "destination-images";

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_IMAGES);
    }
}
