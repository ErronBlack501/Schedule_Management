<?php

namespace App\Filament\Resources\SalleResource\Pages;

use App\Filament\Resources\SalleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSalle extends CreateRecord
{
    protected static string $resource = SalleResource::class;
    // protected function getRedirectUrl(): string
    // {
    //     return $this->CreateClass::getRessource()::getUrl('index');
    // }
}


