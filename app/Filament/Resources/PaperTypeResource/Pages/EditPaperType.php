<?php

namespace App\Filament\Resources\PaperTypeResource\Pages;

use App\Filament\Resources\PaperTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaperType extends EditRecord
{
    protected static string $resource = PaperTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
