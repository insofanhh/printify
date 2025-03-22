<?php

namespace App\Filament\Resources\PrintOptionResource\Pages;

use App\Filament\Resources\PrintOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrintOptions extends ListRecords
{
    protected static string $resource = PrintOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
