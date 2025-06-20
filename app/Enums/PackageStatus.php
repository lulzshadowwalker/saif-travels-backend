<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PackageStatus: string implements HasLabel, HasColor
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

    public static function values(): array
    {
        return array_map(
            fn(PackageStatus $status) => $status->value,
            self::cases()
        );
    }
}
