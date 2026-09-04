<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnerResource extends AdminResource
{
    protected static ?string $model = Partner::class;
    protected static ?string $permissionKey = 'content.partners';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static string|\UnitEnum|null $navigationGroup = 'Obsah';
    protected static ?string $navigationLabel = 'Partneři';
    protected static ?string $modelLabel = 'Partner';
    protected static ?string $pluralModelLabel = 'Partneři';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Základní informace')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->label('Název partnera')->required(),
                    CuratorPicker::make('logo_media_id')
                        ->label('Logo partnera')
                        ->directory('media/partneri')
                        ->limitToDirectory()
                        ->required()
                        ->helperText('Loga se ukládají do Media Library / Partneři.'),
                    TextInput::make('website')->label('Web partnera')->url(),
                    Toggle::make('is_active')->label('Zobrazit na webu')->default(true),
                ]),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('logo')
                ->label('')
                ->getStateUsing(fn (Partner $record) => $record->logo_url ? url($record->logo_url) : null)
                ->imageSize(32)
                ->square()
                ->extraImgAttributes(['class' => 'object-contain']),
            TextColumn::make('name')->label('Partner')->searchable(),
            TextColumn::make('website')->label('Web')->limit(35),
            IconColumn::make('is_active')->boolean()->label('Aktivní'),
        ])->recordActions([
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
