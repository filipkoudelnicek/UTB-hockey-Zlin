<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageTypeResource\Pages;
use App\Models\PageType;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageTypeResource extends AdminResource
{
    protected static ?string $model = PageType::class;
    protected static ?string $permissionKey = 'website.settings';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
    protected static ?string $navigationLabel   = 'Typy stránek';
    protected static ?string $modelLabel        = 'Typ stránky';
    protected static ?string $pluralModelLabel  = 'Typy stránek';
    protected static string|\UnitEnum|null $navigationGroup = 'Správa webu';
    protected static ?int $navigationSort       = 2;

    /**
     * Typy stránek propojují Blade šablony a PHP schémata. Jsou určeny pro
     * vývojáře; pro běžnou práci s obsahem slouží sekce Stránky.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Definice typu')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('handle')
                            ->label('Handle (identifikátor)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Malá písmena, pomlčky. Např. service_detail. Nelze měnit po vytvoření.')
                            ->disabledOn('edit')
                            ->dehydrated(),

                        TextInput::make('label')
                            ->label('Název')
                            ->required()
                            ->helperText('Zobrazuje se v selectu při vytváření stránky.'),
                    ]),

                    Grid::make(3)->schema([
                        Select::make('template')
                            ->label('Blade šablona')
                            ->options(PageType::availableTemplates())
                            ->searchable()
                            ->required()
                            ->helperText('Soubory z resources/views/pages/'),

                        Select::make('schema_class')
                            ->label('Třída schématu')
                            ->options(PageType::availableSchemaClasses())
                            ->searchable()
                            ->nullable()
                            ->helperText('Třídy z app/Filament/Modules/PageTypes/'),

                        Select::make('controller')
                            ->label('Controller')
                            ->options(PageType::availableControllers())
                            ->searchable()
                            ->nullable()
                            ->helperText('Výchozí: PageController'),
                    ]),
                ]),

            Section::make('URL Routes')
                ->description('URL adresy pro tento typ stránek se konfigurují v sekci Správa webu → Route Builder.')
                ->schema([]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('handle')
                    ->label('Handle')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Název')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('template')
                    ->label('Šablona')
                    ->badge()
                    ->color('info'),

                TextColumn::make('controller')
                    ->label('Controller')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '—')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('updated_at')
                    ->label('Upraveno')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('label')
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPageTypes::route('/'),
            'create' => Pages\CreatePageType::route('/create'),
            'edit'   => Pages\EditPageType::route('/{record}/edit'),
        ];
    }
}
