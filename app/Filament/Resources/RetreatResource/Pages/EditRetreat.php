<?php

namespace App\Filament\Resources\RetreatResource\Pages;

use App\Filament\Resources\RetreatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRetreat extends EditRecord
{
    protected static string $resource = RetreatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
