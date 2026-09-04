<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedirectResource\Pages;
use App\Models\Redirect;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class RedirectResource extends AdminResource
{
    protected static ?string $model = Redirect::class;
    protected static ?string $permissionKey = 'website.redirects';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTopRightOnSquare;
    protected static ?string $navigationLabel    = 'Přesměrování';
    protected static ?string $modelLabel         = 'Přesměrování';
    protected static ?string $pluralModelLabel   = 'Přesměrování';
    protected static string|\UnitEnum|null $navigationGroup    = 'Správa webu';
    protected static ?int $navigationSort           = 3;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            TextInput::make('from_url')
                ->label('Z URL (from)')
                ->placeholder('/stara-stranka')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Vždy začínejte lomítkem: /stara-stranka'),

            TextInput::make('to_url')
                ->label('Na URL (to)')
                ->placeholder('/nova-stranka')
                ->required()
                ->helperText('Může být relativní (/nova) nebo absolutní (https://...).') 
                ->rules([
                    fn (callable $get) => function (string $attribute, mixed $value, Closure $fail) use ($get) {
                        $fromUrl = $get('from_url');
                        if (!$fromUrl || !$value) {
                            return;
                        }
                        if ($value === $fromUrl) {
                            $fail('Cílová URL je stejná jako zdrojová — vznikla by smyčka.');
                            return;
                        }
                        // Sleduj řetěz přesměrování a zkontroluj cyklus
                        $visited = [$fromUrl];
                        $current = $value;
                        $maxSteps = 10;
                        while ($maxSteps-- > 0) {
                            $next = Redirect::where('from_url', $current)->where('active', true)->value('to_url');
                            if (!$next) {
                                break;
                            }
                            if (in_array($next, $visited)) {
                                $fail('Toto přesměrování vytvoří smyčku (řetěz přesměrování vede zpět na zdrojovou URL).');
                                return;
                            }
                            $visited[] = $next;
                            $current = $next;
                        }
                    },
                ]),

            Select::make('http_code')
                ->label('Typ přesměrování')
                ->options([
                    301 => '301 – Trvalé (SEO doporučeno)',
                    302 => '302 – Dočasné',
                ])
                ->default(301)
                ->required(),

            Toggle::make('active')
                ->label('Aktivní')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_url')->label('Z URL')->searchable()->sortable(),
                TextColumn::make('to_url')->label('Na URL')->searchable(),
                TextColumn::make('http_code')->label('Kód')->badge()
                    ->color(fn ($state) => $state === 301 ? 'success' : 'warning'),
                ToggleColumn::make('active')->label('Aktivní'),
                TextColumn::make('updated_at')->label('Upraveno')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('from_url')
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit'   => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}
