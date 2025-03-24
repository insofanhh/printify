<?php

namespace App\Filament\Resources\PrintOptionResource\Pages;

use App\Filament\Resources\PrintOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrintOption extends CreateRecord
{
    protected static string $resource = PrintOptionResource::class;

    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Print option created!';
    }
}
