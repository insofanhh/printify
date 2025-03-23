<?php

namespace App\Filament\Resources\PriceRuleResource\Pages;

use App\Filament\Resources\PriceRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPriceRule extends EditRecord
{
    protected static string $resource = PriceRuleResource::class;

    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Price rule updated!';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
