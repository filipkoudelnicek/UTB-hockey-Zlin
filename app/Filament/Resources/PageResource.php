<?php

namespace App\Filament\Resources;

use App\Filament\Modules\SeoModule;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageType;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
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

class PageResource extends AdminResource
{
    protected static ?string $model = Page::class;
    protected static ?string $permissionKey = 'content.pages';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Stránky';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $modelLabel = 'Stránky';
    protected static ?string $pluralModelLabel = 'Stránky';
    protected static string|\UnitEnum|null $navigationGroup = 'Obsah';

    public static function form(Schema $form): Schema
    {
        $hasMultipleLanguages = Language::hasMultipleActive();

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

        $languageField = $hasMultipleLanguages
            ? Select::make('lang_locale')->label('Jazyk')
                ->options(fn () => Language::activeOptions())
                ->default(fn () => Language::defaultActiveLocale() ?? 'cs')
                ->required()
                ->live()
                ->afterStateUpdated(function ($get, $set) use ($computePreview) {
                    $computePreview($get, $set);
                })
            : \Filament\Forms\Components\Hidden::make('lang_locale')
                ->default(fn () => Language::defaultActiveLocale() ?? 'cs')
                ->dehydrated();

        return $form->schema([
            Section::make('Nastavení stránky')
                ->schema([
                    Grid::make($hasMultipleLanguages ? 4 : 3)
                        ->schema([
                            TextInput::make('title')->label('Název stránky')
                                ->helperText('Tento název se zobrazuje v administraci a může se použít v navigaci.')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($operation, $state, $set){
                                    if ($operation === 'edit'){
                                        return;
                                    }
                                    $set('slug', Str::slug($state));
                                }),
                            TextInput::make('slug')->label('Část URL adresy')
                                ->helperText('Krátký název v URL, například „kontakt“. Nepoužívejte mezery ani diakritiku.')
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
                            $languageField,
                            Select::make('type')->label('Šablona stránky')
                                ->helperText('Určuje, jaké obsahové bloky budete na stránce upravovat. Po vytvoření ji nelze změnit.')
                                ->options(fn () => PageType::allAsOptions())
                                ->live()
                                ->required()
                                ->afterStateUpdated(function ($get, $set) use ($computePreview) {
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
                            Toggle::make('active')->label('Zobrazit stránku na webu')
                                ->helperText('Vypnutá stránka zůstane uložená, ale návštěvníci ji neuvidí.'),
                        ]),
                ])->columns(1),

            Section::make('Obsah stránky')
                ->description('Všechny části stránky jsou otevřené. Upravujte jen bloky, které chcete zobrazit na webu.')
                ->schema([
                    Group::make(function ($get) {
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

                    RichEditor::make('content.text')
                        ->label('Obsah')
                        ->visible(fn ($get): bool => $get('type') === 'text')
                        ->columnSpanFull(),
                ]),

            ...SeoModule::make(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->searchable()->label('Název'),
                TextColumn::make('pageType.admin_label')->label('Šablona')->badge()->color('gray'),
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
