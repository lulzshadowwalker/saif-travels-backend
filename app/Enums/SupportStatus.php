<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SupportStatus: string implements HasLabel, HasColor, HasIcon
{
    case open = "open";
    case resolved = "resolved";
    case closed = "closed";

    public function getLabel(): string
    {
        return match ($this) {
            self::open => "Open",
            self::resolved => "Resolved",
            self::closed => "Closed",
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::open => "primary",
            self::resolved => "success",
            self::closed => "danger",
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::open => "heroicon-o-circle-stack",
            self::resolved => "heroicon-o-check-circle",
            self::closed => "heroicon-o-x-circle",
        };
    }

    public static function values(): array
    {
        return array_map(
            fn(SupportStatus $status) => $status->value,
            self::cases()
        );
    }
}
