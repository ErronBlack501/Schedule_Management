<?php

namespace App\Filament\Resources\EmploidutempResource\Pages;

use App\Filament\Resources\EmploidutempResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmploidutemps extends ListRecords
{
    protected static string $resource = EmploidutempResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
