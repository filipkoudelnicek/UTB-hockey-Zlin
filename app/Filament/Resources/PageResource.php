<?php

namespace App\Filament\Resources;

use App\Filament\Modules\SeoModule;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\Toggle;
use App\Filament\Resources\PageResource\Pages;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Stránky';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $modelLabel = 'Stránky';
    protected static ?string $pluralModelLabel = 'Stránky';
    protected static string|\UnitEnum|null $navigationGroup = 'Obsah';

    public static function form(Schema $form): Schema
    {
        $computePreview = function (callable $get, callable $set): void {
            $slug   = trim((string) $get('slug'), '/');
            $type   = $get('type');
            $locale = $get('lang_locale');

            if (!$slug && !$type) {
                return;
            }

            try {
                $pageType = $type ? PageType::findByHandle($type) : null;
                if ($pageType && $locale) {
                    $route = \App\Models\PageRoute::activeForTypeAndLocale($pageType->id, $locale);
                    if ($route) {
                        $cleanSlug = trim($slug, '/');
                        $resolved  = str_contains($route->path, '{slug}')
                            ? ltrim(preg_replace('#/{2,}#', '/', str_replace('{slug}', $cleanSlug, $route->path)), '/')
                            : ltrim($route->path, '/');
                        $set('full_slug', $resolved === '' ? '/' : $resolved);
                        return;
                    }
                }
            } catch (\Throwable) {}

            $set('full_slug', $slug === '' ? '/' : $slug);
        };

        return $form->schema([
            Section::make('Nastavení stránky')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('title')->label('Název')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($operation, $state, $set){
                                    if ($operation === 'edit'){
                                        return;
                                    }
                                    $set('slug', Str::slug($state));
                                }),
                            TextInput::make('slug')->label('Slug')
                                ->required()
                                ->minLength(1)
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($get, $set) use ($computePreview) {
                                    $computePreview($get, $set);
                                })
                                ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, callable $get) {
                                    return $rule
                                        ->where('lang_locale', $get('lang_locale'))
                                        ->where('slug', $get('slug'));
                                }),
                        ]),

                    Grid::make(2)
                        ->schema([
                            Select::make('lang_locale')->label('Jazyk')
                                ->options(Language::where('active', 1)->pluck('name', 'locale'))
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($get, $set) use ($computePreview) {
                                    $computePreview($get, $set);
                                }),
                            Select::make('type')->label('Typ stránky')
                                ->options(fn () => PageType::allAsOptions())
                                ->live()
                                ->required()
                                ->afterStateUpdated(function (Select $component, $get, $set) use ($computePreview) {
                                    if ($section = $component->getContainer()->getComponent('pageTypes')) {
                                        $section->getChildSchema()->fill();
                                    }
                                    $computePreview($get, $set);
                                })
                                ->disabledOn('edit')
                                ->dehydrated(),
                        ]),

                    Grid::make(2)
                        ->schema([
                            TextInput::make('full_slug')
                                ->label('Výsledná URL cesta')
                                ->disabled()
                                ->dehydrated(false)
                                ->formatStateUsing(fn ($state) => $state === '' || $state === null ? '/' : $state)
                                ->placeholder('Vyberte typ a vyplňte slug...')
                                ->helperText('Automaticky generováno z Route Builderu.'),

                            Toggle::make('active')->label('Aktivní stránka'),
                        ]),
                ]),

            Section::make('Obsah')
                ->schema([
                    Section::make()
                        ->schema(function ($get) {
                            $type = $get('type');
                            if (!$type) return [];

                            try {
                                $pageType = PageType::findByHandle($type);
                                $class = $pageType?->schema_class;

                                if ($class && class_exists($class) && method_exists($class, 'getSchema')) {
                                    return $class::getSchema();
                                }
                            } catch (\Throwable $e) {
                                // ignoruj
                            }

                            return [];
                        })
                        ->key('pageTypes'),
                ]),

            ...SeoModule::make(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->searchable()->label('Název'),
                TextColumn::make('pageType.template')->label('Šablona')->badge()->color('gray'),
                TextColumn::make('full_slug')->label('URL cesta')
                    ->getStateUsing(fn ($record) => ($record->full_slug === '' || $record->full_slug === null) ? '/' : $record->full_slug)
                    ->searchable(),
                TextColumn::make('lang_locale')->sortable()->label('Jazyk'),
                ToggleColumn::make('active')->sortable()->label('Aktivní'),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn (Page $record) => static::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Actions\Action::make('view')
                    ->label('Zobrazit')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('info')
                    ->url(function (Page $record) {
                        $fullSlug      = ltrim($record->full_slug ?: $record->computeFullSlug(), '/');
                        $defaultLocale = \App\Services\UrlService::getDefaultLocale();
                        $locale        = $record->lang_locale;
                        $path = ($locale && $locale !== $defaultLocale)
                            ? '/' . $locale . '/' . $fullSlug
                            : '/' . $fullSlug;
                        return rtrim(config('app.url'), '/') . $path;
                    }, shouldOpenInNewTab: true),
                Actions\EditAction::make(),
                Actions\Action::make('duplicate')
                    ->label('Duplikovat')
                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                    ->color('gray')
                    ->action(function (Page $record) {
                        $suffix = '-kopie-' . rand(100, 999);
                        $record->replicate()
                            ->fill([
                                'title'    => $record->title . ' (kopie)',
                                'slug'     => $record->slug . $suffix,
                                'active'   => false,
                            ])
                            ->save();
                    })
                    ->successNotificationTitle('Stránka byla duplikována.'),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
