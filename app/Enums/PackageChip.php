<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PackageChip: string implements HasLabel, HasColor, HasIcon
{
    case yoga = "yoga";

    public function getLabel(): string
    {
        return match ($this) {
            self::yoga => "Yoga",
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::yoga => "primary",
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::yoga => "heroicon-o-globe-alt",
        };
    }

    public static function values(): array
    {
        return array_map(fn($value) => $value->value, self::cases());
    }
}
