<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\SupportStatus;

/**
 * Support messages
 */
class Support extends Model
{
    /** @use HasFactory<\Database\Factories\SupportFactory> */
    use HasFactory;

    protected $fillable = ["name", "email", "phone", "status"];

    protected function casts(): array
    {
        return [
            "status" => SupportStatus::class,
        ];
    }
}
