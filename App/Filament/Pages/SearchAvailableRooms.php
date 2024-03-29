<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SearchAvailableRooms extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    //protected static ?string $navigationLabel = 'Search for available rooms';
    public static function getNavigationLabel(): string
    {
        return __('Search for available rooms');
    }
    public function getTitle(): string
    {
        return __('Search available rooms');
    }
    protected static string $view = 'filament.pages.search-free-rooms';
}
