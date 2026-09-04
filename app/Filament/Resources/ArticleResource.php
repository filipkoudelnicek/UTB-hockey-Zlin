<?php

namespace App\Filament\Resources;

use App\Filament\Modules\SeoModule;
use App\Filament\Forms\Components\HighlightedTextInput;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\Language;
use App\Models\User;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class ArticleResource extends AdminResource
{
    protected static ?string $model = Article::class;
    protected static ?string $permissionKey = 'content.articles';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Aktuality';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Článek';
    protected static ?string $pluralModelLabel = 'Aktuality';
    protected static string|\UnitEnum|null $navigationGroup = 'Obsah';

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Základní informace')->schema([
                Grid::make(2)->schema([
                    HighlightedTextInput::make('title')->label('Nadpis článku')->required()->live(debounce: 500)
                        ->afterStateUpdated(function ($operation, $state, $set) {
                            if ($operation === 'create') $set('slug', Str::slug(strip_tags((string) $state)));
                        }),
                    TextInput::make('slug')->label('Část URL adresy')->required()
                        ->helperText('Krátký název v URL. Vytvoří se automaticky podle nadpisu, můžete jej upravit.')
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, callable $get) {
                        return $rule->where('lang_locale', $get('lang_locale'))->where('slug', $get('slug'));
                    }),
                    Select::make('category')->label('Kategorie')->options(Article::categoryOptions())->default('team')->required(),
                    Select::make('lang_locale')
                        ->label('Jazyk')
                        ->options(fn () => Language::activeOptions())
                        ->default(fn () => Language::defaultActiveLocale() ?? 'cs')
                        ->hidden(fn () => ! Language::hasMultipleActive())
                        ->dehydratedWhenHidden()
                        ->required(),
                    Select::make('user_id')->label('Autor')->default(fn () => Auth::id())->options(fn () => User::pluck('name', 'id'))->nullable(),
                    Toggle::make('active')->label('Publikovaný')->default(true),
                    DateTimePicker::make('publish_time')->label('Publikovat od')->seconds(false),
                ]),
                Grid::make(2)->schema([
                    CuratorPicker::make('featured_media_id')->label('Úvodní obrázek')->helperText('Obrázek pro výpis aktualit a sdílení článku.')->columnSpan(1),
                    Textarea::make('excerpt')->label('Krátké shrnutí (perex)')->rows(3)->maxLength(500)->columnSpanFull(),
                ]),
            ])->columns(1),
            Section::make('Obsah')->schema([
                RichEditor::make('content.body')->label('Obsah článku')->columnSpanFull(),
            ])->columns(1),
            ...SeoModule::make(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('publish_time', 'desc')->columns([
            TextColumn::make('title')->label('Název')->formatStateUsing(fn (Article $record): string => $record->plain_title)->searchable()->sortable(),
            TextColumn::make('category')
                ->label('Kategorie')
                ->badge()
                ->formatStateUsing(fn (?string $state): string => Article::categoryLabel($state))
                ->color(fn (?string $state): string => Article::categoryColor($state)),
            TextColumn::make('publish_time')->label('Publikace')->dateTime('d.m.Y H:i')->placeholder('Ihned')->sortable(),
            TextColumn::make('user.name')->label('Autor'),
            ToggleColumn::make('active')->label('Aktivní'),
        ])
        ->recordUrl(fn (Article $record) => static::getUrl('edit', ['record' => $record]))
        ->recordActions([
            Actions\Action::make('view')
                ->label('Zobrazit')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->url(fn (Article $record) => url($record->url), true)
                ->disabled(fn (Article $record) => ! $record->active || ($record->publish_time && $record->publish_time->isFuture()))
                ->tooltip(fn (Article $record) => ! $record->active
                    ? 'Článek není publikovaný.'
                    : ($record->publish_time && $record->publish_time->isFuture()
                        ? 'Článek bude na webu dostupný od ' . $record->publish_time->format('d.m.Y H:i') . '.'
                        : null)),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ]);
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
