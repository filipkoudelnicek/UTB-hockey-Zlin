<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleCategoryResource\Pages;
use App\Models\ArticleCategory;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticleCategoryResource extends AdminResource
{
    protected static ?string $model = ArticleCategory::class;

    protected static ?string $permissionKey = 'content.articles';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Obsah';

    protected static ?string $navigationLabel = 'Kategorie článků';

    protected static ?string $modelLabel = 'Kategorie';

    protected static ?string $pluralModelLabel = 'Kategorie článků';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Kategorie')->schema([
                Grid::make(1)->schema([
                    TextInput::make('name')
                        ->label('Název')
                        ->required()
                        ->maxLength(255),
                    Toggle::make('is_active')
                        ->label('Aktivní')
                        ->default(true)
                        ->helperText('Neaktivní kategorii nelze přiřadit novým článkům. Stávající přiřazení zůstávají.'),
                    Toggle::make('is_filterable')
                        ->label('Použít ve filtru')
                        ->default(true)
                        ->helperText('Po vypnutí se kategorie nezobrazí ve filtru, ani když ji mají přiřazené publikované články.'),
                ]),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Kategorie')->searchable()->sortable(),
            IconColumn::make('is_active')->label('Aktivní')->boolean(),
            IconColumn::make('is_filterable')->label('Ve filtru')->boolean(),
            TextColumn::make('articles_count')->counts('articles')->label('Počet článků')->sortable(),
        ])->recordActions([
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticleCategories::route('/'),
            'create' => Pages\CreateArticleCategory::route('/create'),
            'edit' => Pages\EditArticleCategory::route('/{record}/edit'),
        ];
    }
}
