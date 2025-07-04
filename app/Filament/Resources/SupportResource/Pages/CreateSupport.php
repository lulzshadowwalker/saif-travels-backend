<?php

namespace App\Filament\Resources\SupportResource\Pages;

use App\Filament\Resources\SupportResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSupport extends CreateRecord
{
    protected static string $resource = SupportResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl("index");
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return "Support request created successfully";
    }
}
