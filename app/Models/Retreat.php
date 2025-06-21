<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\RetreatStatus;

/*
 * Retreats represnts collections of packages
 */
class Retreat extends Model
{
    /** @use HasFactory<\Database\Factories\RetreatFactory> */
    use HasFactory, HasTranslations;

    protected $fillable = ["name", "status"];

    protected $translatable = ["name"];

    protected function casts(): array
    {
        return [
            "status" => RetreatStatus::class,
        ];
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class);
    }
}
