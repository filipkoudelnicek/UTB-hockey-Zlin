<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use Filament\Actions;

class EditArticle extends EditRecordWithSaveAndBack
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
