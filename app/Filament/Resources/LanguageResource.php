<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LanguageResource\Pages;
use App\Models\Language;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LanguageResource extends AdminResource
{
    protected static ?string $model = Language::class;
    protected static ?string $permissionKey = 'website.languages';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;
    protected static ?string $navigationLabel = 'Jazyky';
    protected static ?string $modelLabel = 'Jazyky';
    protected static ?string $pluralModelLabel = 'Jazyky';
    protected static string|\UnitEnum|null $navigationGroup = 'Správa webu';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Název jazyka'),
                Select::make('locale')
                    ->required()
                    ->label('Jazyk')
                    ->options([
                        'cs' => '🇨🇿 Čeština (cs)',
                        'sk' => '🇸🇰 Slovenčina (sk)',
                        'en' => '🇬🇧 English (en)',
                        'de' => '🇩🇪 Deutsch (de)',
                        'pl' => '🇵🇱 Polski (pl)',
                        'fr' => '🇫🇷 Français (fr)',
                        'it' => '🇮🇹 Italiano (it)',
                        'es' => '🇪🇸 Español (es)',
                        'pt' => '🇵🇹 Português (pt)',
                    ])
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $names = [
                            'cs' => 'Čeština', 'sk' => 'Slovenčina', 'en' => 'English',
                            'de' => 'Deutsch', 'pl' => 'Polski', 'fr' => 'Français',
                            'it' => 'Italiano', 'es' => 'Español', 'pt' => 'Português',
                        ];
                        if ($state && isset($names[$state])) {
                            $set('name', $names[$state]);
                        }
                    })
                    ->disabled(fn ($record) => $record !== null)
                    ->helperText(fn ($record) => $record !== null ? 'Zkratku jazyka nelze po vytvoření měnit.' : null),
                Toggle::make('active')->label('Aktivní jazyk'),
                Toggle::make('is_default')
                    ->label('Výchozí jazyk')
                    ->helperText('Výchozí jazyk může být pouze jeden. Zapnutím tohoto přepínače se automaticky odznačí předchozí výchozí jazyk.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->label('Název'),
                TextColumn::make('locale')->sortable()->label('Zkratka'),
                CheckboxColumn::make('active')->sortable()->label('Aktivní'),
                CheckboxColumn::make('is_default')->sortable()->label('Výchozí')->disabled(),
            ])
            ->filters([
                //
            ])
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}
