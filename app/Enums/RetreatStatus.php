<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RetreatStatus: string implements HasLabel, HasColor, HasIcon
{
    case active = "active";
    case inactive = "inactive";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::active => "Active",
            self::inactive => "Inactive",
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::active => "success",
            self::inactive => "danger",
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::active => "heroicon-o-check-circle",
            self::inactive => "heroicon-o-x-circle",
        };
    }

    public static function values(): array
    {
        return array_map(
            fn(RetreatStatus $status) => $status->value,
            self::cases()
        );
    }
}
