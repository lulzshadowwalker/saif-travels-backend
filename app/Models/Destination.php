<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Destination extends Model implements HasTranslations
{
    /** @use HasFactory<\Database\Factories\DestinationFactory> */
    use HasFactory;

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
}
