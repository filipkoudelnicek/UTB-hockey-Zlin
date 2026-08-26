<?php

namespace App\Filament\Resources\PageRouteResource\Pages;

use App\Filament\Resources\PageRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPageRoutes extends ListRecords
{
    protected static string $resource = PageRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
