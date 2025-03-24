<?php

namespace App\Filament\Resources\PaperTypeResource\Pages;

use App\Filament\Resources\PaperTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaperType extends CreateRecord
{
    protected static string $resource = PaperTypeResource::class;

    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Paper type created!';
    }
}
