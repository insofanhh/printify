<?php

namespace App\Filament\Resources\OrderStatusResource\Pages;

use App\Filament\Resources\OrderStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderStatus extends EditRecord
{
    protected static string $resource = OrderStatusResource::class;

    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Status updated!';
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
