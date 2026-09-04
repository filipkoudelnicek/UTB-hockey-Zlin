<?php

namespace App\Filament\Resources;

use App\Enums\CaptainRole;
use App\Enums\PlayerPosition;
use App\Filament\Resources\PlayerResource\Pages;
use App\Models\Player;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlayerResource extends AdminResource
{
    protected static ?string $model = Player::class;
    protected static ?string $permissionKey = 'sport.players';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user';
    protected static string|\UnitEnum|null $navigationGroup = 'Běžná správa';
    protected static ?string $navigationLabel = 'Hráči';
    protected static ?string $modelLabel = 'Hráč';
    protected static ?string $pluralModelLabel = 'Hráči';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Základní informace')->schema([
                Grid::make(2)->schema([
                    TextInput::make('first_name')->label('Jméno')->required(),
                    TextInput::make('last_name')
                        ->label('Příjmení')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($operation, $state, $set, $get): void {
                            if ($operation === 'create') {
                                $set('slug', Str::slug(trim(($get('first_name') ?? '').' '.$state)));
                            }
                        }),
                    DatePicker::make('date_of_birth')->label('Datum narození'),
                    TextInput::make('height')->label('Výška (cm)')->numeric(),
                    TextInput::make('weight')->label('Hmotnost (kg)')->numeric(),
                    Select::make('stick_side')->label('Hůl')->options(['left' => 'Levá', 'right' => 'Pravá']),
                    Select::make('faculty')->label('Fakulta')->options([
                        'FAI' => 'FAI — Fakulta aplikované informatiky',
                        'FAME' => 'FAME — Fakulta managementu a ekonomiky',
                        'FHS' => 'FHS — Fakulta humanitních studií',
                        'FLKŘ' => 'FLKŘ — Fakulta logistiky a krizového řízení',
                        'FMK' => 'FMK — Fakulta multimediálních komunikací',
                        'FT' => 'FT — Fakulta technologická',
                    ])->searchable(),
                ]),
                Grid::make(2)->schema([
                    CuratorPicker::make('portrait_media_id')
                        ->label('Portrét hráče')
                        ->directory('media/hraci')
                        ->limitToDirectory()
                        ->helperText('Obrázky se ukládají do Media Library / Hráči.'),
                    CuratorPicker::make('video_media_id')
                        ->label('Video z knihovny médií')
                        ->acceptedFileTypes(['video/mp4', 'video/webm'])
                        ->directory('media/hraci')
                        ->limitToDirectory()
                        ->helperText('Videa se ukládají do Media Library / Hráči.'),
                ]),
            ]),
            Section::make('Zařazení v týmu')
                ->description('Tyto údaje se zobrazují přímo na kartě a detailu hráče.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('jersey_number')->label('Číslo dresu')->numeric()->minValue(0)->maxValue(99),
                        Select::make('position')->label('Herní post')->options(PlayerPosition::options()),
                        Select::make('captain_role')->label('Role kapitána')->options(CaptainRole::options())->default(CaptainRole::None->value),
                        Toggle::make('is_active')->label('Zobrazovat hráče na webu')->default(true),
                    ]),
                ]),
            Section::make('Profil hráče')->schema([
                TextInput::make('profile_heading')->label('Nadpis profilu'),
                RichEditor::make('bio')->label('Medailonek')->columnSpanFull(),
                Textarea::make('quote')->label('Citace')->rows(3),
            ]),
            Section::make('SEO a sdílení')
                ->description('Volitelné nastavení pro vyhledávače a náhled při sdílení profilu.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('seo_title')->label('Titulek ve vyhledávačích'),
                        CuratorPicker::make('seo_og_media_id')
                            ->label('Obrázek náhledu')
                            ->directory('media/hraci')
                            ->limitToDirectory(),
                    ]),
                    Textarea::make('seo_description')->label('Popis ve vyhledávačích')->rows(3)->columnSpanFull(),
                ])
                ->collapsed(),
            Section::make('Pokročilé nastavení')
                ->description('Identifikátory využívá web a případné importy dat.')
                ->schema([
                    TextInput::make('slug')
                        ->label('Identifikátor v URL')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Vytvoří se automaticky ze jména.'),
                ])
                ->collapsed(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('full_name')->label('Hráč')->searchable(['first_name', 'last_name'])->sortable(['last_name']),
            TextColumn::make('jersey_number')->label('#'),
            TextColumn::make('position')->label('Post')->formatStateUsing(fn ($state) => $state?->label() ?? $state),
            TextColumn::make('captain_role')->label('Role')->formatStateUsing(fn ($state) => $state?->label() ?? $state),
            TextColumn::make('faculty')->label('Fakulta'),
            IconColumn::make('is_active')->boolean()->label('Aktivní'),
        ])->recordActions([
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayers::route('/'),
            'create' => Pages\CreatePlayer::route('/create'),
            'edit' => Pages\EditPlayer::route('/{record}/edit'),
        ];
    }
}
