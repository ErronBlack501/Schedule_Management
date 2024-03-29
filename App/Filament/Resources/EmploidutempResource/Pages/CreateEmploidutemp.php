<?php

namespace App\Filament\Resources\EmploidutempResource\Pages;

use App\Filament\Resources\EmploidutempResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmploidutemp extends CreateRecord
{
    protected static string $resource = EmploidutempResource::class;

    /*protected function beforeCreate(): void
    {
        $debCours = $this->getRecord()->Emploidutemp->DebutCours;
        $recentFinCours = $this->record['']

    }*/
    // protected function getRedirectUrl(): string
    // {
    //     return $this->CreateClass::getRessource()::getUrl('index');
    // }
}
