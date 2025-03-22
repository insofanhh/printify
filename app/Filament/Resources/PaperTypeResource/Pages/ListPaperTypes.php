<?php

namespace App\Filament\Resources\PaperTypeResource\Pages;

use App\Filament\Resources\PaperTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaperTypes extends ListRecords
{
    protected static string $resource = PaperTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
