<?php

namespace App\Filament\Resources\UsersResource\Pages;

use App\Filament\Resources\UsersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsers extends EditRecord
{
    protected static string $resource = UsersResource::class;

    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'User updated!';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
