<?php

namespace App\Filament\Resources\PrintOptionResource\Pages;

use App\Filament\Resources\PrintOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrintOption extends EditRecord
{
    protected static string $resource = PrintOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
