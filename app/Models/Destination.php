<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
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
