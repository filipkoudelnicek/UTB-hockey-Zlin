<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMessageResource extends AdminResource
{
    protected static ?string $model = ContactMessage::class;
    protected static ?string $permissionKey = 'website.contact_messages';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static ?string $navigationLabel = 'Přijaté dotazy';
    protected static ?string $modelLabel = 'Dotaz';
    protected static ?string $pluralModelLabel = 'Přijaté dotazy';
    protected static string|\UnitEnum|null $navigationGroup = 'Správa webu';
    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool { return false; }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool { return false; }

    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::whereNull('read_at')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            TextInput::make('name')->label('Jméno'),
            TextInput::make('email')->label('E-mail'),
            TextInput::make('subject')->label('Předmět'),
            Textarea::make('message')->label('Zpráva')->rows(10)->columnSpanFull(),
            TextInput::make('created_at')->label('Přijato')
                ->formatStateUsing(fn ($state) => $state ? \Illuminate\Support\Carbon::parse($state)->format('d.m.Y H:i') : null),
            TextInput::make('ip_address')->label('IP adresa'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('read_at')->label('Přečteno')->boolean()
                    ->getStateUsing(fn (ContactMessage $record) => $record->read_at !== null),
                TextColumn::make('created_at')->label('Přijato')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('name')->label('Jméno')->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('subject')->label('Předmět')->badge()->searchable(),
                TextColumn::make('message')->label('Zpráva')->limit(60),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Actions\ViewAction::make(),
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
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
