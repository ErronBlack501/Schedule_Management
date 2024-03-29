<?php

namespace App\Filament\Resources\ProfesseurResource\Pages;

use App\Filament\Resources\ProfesseurResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProfesseur extends CreateRecord
{
    protected static string $resource = ProfesseurResource::class;

    /*protected function getCreatedNotification(): ?string
    {
        return 'Professor created';
    }*/
    // protected function getRedirectUrl(): string
    // {
    //     return $this->CreateClass::getRessource()::getUrl('index');
    // }
}
