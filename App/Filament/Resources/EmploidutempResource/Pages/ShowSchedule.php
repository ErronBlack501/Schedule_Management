<?php

namespace App\Filament\Resources\EmploidutempResource\Pages;

use App\Filament\Resources\EmploidutempResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ShowSchedule extends Page
{
    protected static string $resource = EmploidutempResource::class;

    protected static string $view = 'filament.resources.emploidutemp-resource.pages.show-edt';

    public function getTitle(): string
    {
        return __('Show schedule');
    }
    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make(),
    //     ];
    // }
}
