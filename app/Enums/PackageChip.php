<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PackageChip: string implements HasLabel, HasColor, HasIcon
{
    case yoga = "yoga";
    case nature = "nature";
    case meditation = "meditation";
    case adventure = "adventure";
    case explore = "explore";
    case honeymoon = "honeymoon";

    public function getLabel(): string
    {
        return match ($this) {
            self::yoga => "Yoga",
            self::nature => "Nature",
            self::meditation => "Meditation",
            self::adventure => "Adventure",
            self::explore => "Explore",
            self::honeymoon => "Honeymoon",
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::yoga => "primary",
            self::nature => "success",
            self::meditation => "warning",
            self::adventure => "info",
            self::explore => "info",
            self::honeymoon => "danger",
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::yoga => "heroicon-o-sparkles",
            self::nature => "heroicon-o-sun",
            self::meditation => "heroicon-o-academic-cap",
            self::adventure => "heroicon-o-map",
            self::explore => "heroicon-o-globe-alt",
            self::honeymoon => "heroicon-o-heart",
        };
    }

    public function getHexColor(): string
    {
        return match ($this) {
            self::yoga => "#FFFFFF",
            self::nature => "#5BC73A",
            self::meditation => "#E58A21",
            self::adventure => "#1E90FF",
            self::explore => "#197CC3",
            self::honeymoon => "#D3388D",
        };
    }

    public function getBackgroundColor(): string
    {
        return match ($this) {
            self::yoga => "#FFC107",
            self::nature => "#E0FFC8",
            self::meditation => "#FFF2D5",
            self::adventure => "#D5EFFF",
            self::explore => "#B1E4FF",
            self::honeymoon => "#FFB5D3",
        };
    }

    public static function values(): array
    {
        return array_map(fn($value) => $value->value, self::cases());
    }
}
