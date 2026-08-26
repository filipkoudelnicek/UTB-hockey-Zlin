<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Modules\SeoModule;
use App\Models\Article;
use App\Models\Language;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\Toggle;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;
    protected static ?string $navigationLabel = 'Články';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $modelLabel = 'Článek';
    protected static ?string $pluralModelLabel = 'Články';
    protected static string|\UnitEnum|null $navigationGroup = 'Obsah';

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Nastavení článku')
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
                                ->required(),
                            Select::make('user_id')
                                ->default(fn () => Auth::id())
                                ->options(fn () => User::all()->pluck('name', 'id'))
                                ->nullable()
                                ->label('Uživatel'),
                        ]),

                    Grid::make(2)
                        ->schema([
                            Toggle::make('active')
                                ->label('Aktivní článek'),
                            DateTimePicker::make('publish_time'),
                        ]),
                ]),

            Section::make('Obsah')
                ->schema([
                    Section::make()
                    ->schema([
                        CuratorPicker::make('content.banner')->label('Banner'),
                        CuratorPicker::make('content.thumbnail')->label('Thumbnail'),
                        RichEditor::make('content.body')->label('Obsah')
                            ->extraInputAttributes(['style' => 'min-height: 30rem;'])
                            ->columnSpanFull(),
                    ])->columns(2),
                ]),

            ...SeoModule::make(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->searchable()->label('Název'),
                TextColumn::make('slug')->label('Slug'),
                TextColumn::make('lang_locale')->sortable()->label('Jazyk'),
                TextColumn::make('publish_time')
                    ->label('Publikace')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('Ihned')
                    ->badge()
                    ->color(fn ($state) => $state && $state->isFuture() ? 'warning' : 'success')
                    ->tooltip(fn ($state) => $state && $state->isFuture() ? 'Naplánováno' : ($state ? 'Publikováno' : 'Publikováno ihned')),
                ToggleColumn::make('active')->sortable()->label('Aktivní'),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn (Article $record) => static::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Actions\Action::make('view')
                    ->label('Zobrazit')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('info')
                    ->url(function (Article $record) {
                        $defaultLocale = \App\Services\UrlService::getDefaultLocale();
                        $locale        = $record->lang_locale;
                        $url           = $record->url; // relative path from model
                        return rtrim(config('app.url'), '/') . $url;
                    }, shouldOpenInNewTab: true),
                Actions\EditAction::make(),
                Actions\Action::make('duplicate')
                    ->label('Duplikovat')
                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                    ->color('gray')
                    ->action(function (Article $record) {
                        $suffix = '-kopie-' . rand(100, 999);
                        $record->replicate()
                            ->fill([
                                'title'  => $record->title . ' (kopie)',
                                'slug'   => $record->slug . $suffix,
                                'active' => false,
                            ])
                            ->save();
                    })
                    ->successNotificationTitle('Článek byl duplikován.'),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
