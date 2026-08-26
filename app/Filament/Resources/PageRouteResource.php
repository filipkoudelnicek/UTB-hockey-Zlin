<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageRouteResource\Pages;
use App\Models\Language;
use App\Models\PageRoute;
use App\Models\PageType;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PageRouteResource extends Resource
{
    protected static ?string $model = PageRoute::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;
    protected static ?string $navigationLabel  = 'Page Routes';
    protected static ?string $modelLabel       = 'Route';
    protected static ?string $pluralModelLabel = 'Page Routes';
    protected static string|\UnitEnum|null $navigationGroup = 'Správa webu';
    protected static ?int $navigationSort      = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Nastavení route')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Route name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('homepage, article.show, form.contact...')
                            ->helperText('Unikátní identifikátor, používaný v šablonách jako route(\'...\').'),

                        TextInput::make('path')
                            ->label('Route path')
                            ->required()
                            ->placeholder('/, /{slug}, /{page:blog}/{articleSlug}')
                            ->helperText('Bez jazykového prefixu — ten se přidá automaticky dle pole Jazyk. Použij {page:handle} pro dynamický slug jiné stránky, např. {page:blog} → slug stránky typu blog.'),
                    ]),

                    Grid::make(2)->schema([
                        Select::make('method')
                            ->label('Route method')
                            ->options([
                                'GET'    => 'GET',
                                'POST'   => 'POST',
                                'PUT'    => 'PUT',
                                'PATCH'  => 'PATCH',
                                'DELETE' => 'DELETE',
                            ])
                            ->default('GET')
                            ->required(),

                        Select::make('page_type_id')
                            ->label('Asociovaný typ stránky')
                            ->options(fn () => \App\Models\PageType::allAsIdOptions())
                            ->nullable()
                            ->searchable()
                            ->placeholder('Žádný (obecná route)')
                            ->helperText('Volitelné. Pokud nastavíte, tento URL vzor se použije pro výpočet full_slug stránek daného typu.'),
                    ]),

                    Grid::make(2)->schema([
                        Select::make('controller')
                            ->label('Controller')
                            ->options(fn () => PageRoute::availableControllers())
                            ->searchable()
                            ->required()
                            ->live()
                            ->placeholder('Zvolte controller'),

                        Select::make('action')
                            ->label('Akce')
                            ->options(fn ($get) => PageRoute::getAvailableActions($get('controller')))
                            ->required()
                            ->live()
                            ->placeholder('Zvolte některou z možností')
                            ->helperText('Metody controlleru se načtou po výběru controlleru.'),
                    ]),

                    Grid::make(3)->schema([
                        Select::make('lang_locale')
                            ->label('Jazyk')
                            ->options(fn () => Language::where('active', true)->pluck('name', 'locale'))
                            ->nullable()
                            ->searchable()
                            ->placeholder('Výchozí jazyk')
                            ->helperText('Prázdné = výchozí jazyk (bez prefixu v URL).'),

                        Toggle::make('is_active')
                            ->label('Aktivní')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('auto_generate')
                            ->label('Jen při existenci obsahu')
                            ->default(false)
                            ->helperText('Route se zaregistruje pouze pokud existuje alespoň jedna aktivní stránka přiřazeného typu a jazyka.')
                            ->inline(false),
                    ]),
                ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Route name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('path')
                    ->label('Route path')
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('method')
                    ->label('Method')
                    ->badge()
                    ->color(fn ($state) => PageRoute::methodColor($state ?? 'GET')),

                TextColumn::make('controller')
                    ->label('Controller')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '—')
                    ->badge()
                    ->color('info'),

                TextColumn::make('action')
                    ->label('Akce')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('lang_locale')
                    ->label('Jazyk')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => $state ? strtoupper($state) : 'výchozí'),

                ToggleColumn::make('is_active')
                    ->label('Aktivní')
                    ->sortable(),
            ])
            ->defaultSort('id')
            ->filters([])
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPageRoutes::route('/'),
            'create' => Pages\CreatePageRoute::route('/create'),
            'edit'   => Pages\EditPageRoute::route('/{record}/edit'),
        ];
    }
}
