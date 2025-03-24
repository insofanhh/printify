<?php

namespace App\Filament\Resources\PrintOptionResource\Pages;

use App\Filament\Resources\PrintOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrintOption extends EditRecord
{
    protected static string $resource = PrintOptionResource::class;

    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Print option updated!';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
