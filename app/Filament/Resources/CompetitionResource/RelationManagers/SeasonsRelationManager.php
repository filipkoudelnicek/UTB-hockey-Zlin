<?php

namespace App\Filament\Resources\CompetitionResource\RelationManagers;

use App\Models\CompetitionSeason;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeasonsRelationManager extends RelationManager
{
    protected static string $relationship = 'competitionSeasons';

    protected static ?string $title = 'Ročníky soutěže';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Základní informace')->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Název ročníku')
                            ->placeholder('Např. ULLH 2027/2028')
                            ->required(),
                        Select::make('status')
                            ->label('Stav ročníku')
                            ->options([
                                'planned' => 'Plánovaná',
                                'active' => 'Aktivní',
                                'finished' => 'Ukončená',
                            ])
                            ->default('planned')
                            ->required(),
                        DatePicker::make('starts_at')->label('Začátek ročníku')->required(),
                        DatePicker::make('ends_at')->label('Konec ročníku')->required()->after('starts_at'),
                    ]),
                ]),
                Section::make('Účastníci')->schema([
                    Select::make('teams')
                        ->relationship('teams', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->label('Týmy v soutěži'),
                ]),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Ročník')->searchable(),
                TextColumn::make('status')->label('Stav')->badge(),
                TextColumn::make('starts_at')->label('Začátek')->date('d.m.Y')->placeholder('—'),
                TextColumn::make('ends_at')->label('Konec')->date('d.m.Y')->placeholder('—'),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Přidat ročník')
                    ->createAnother(false),
            ])
            ->recordActions([
                Actions\Action::make('duplicateRound')
                    ->label('Založit další ročník')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->schema([
                        TextInput::make('name')
                            ->label('Název nového ročníku')
                            ->required(),
                        DatePicker::make('starts_at')->label('Začátek nového ročníku')->required(),
                        DatePicker::make('ends_at')->label('Konec nového ročníku')->required()->after('starts_at'),
                    ])
                    ->fillForm(fn (CompetitionSeason $record): array => [
                        'name' => $record->name,
                    ])
                    ->action(function (Actions\Action $action, CompetitionSeason $record, array $data): void {
                        if (CompetitionSeason::query()
                            ->where('competition_id', $record->competition_id)
                            ->where('name', $data['name'])
                            ->exists()) {
                            Notification::make()
                                ->title('Ročník s tímto názvem už existuje.')
                                ->body('Otevřete jej v seznamu ročníků a upravte ho přímo.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }

                        $newRound = $record->replicate();
                        $newRound->fill([
                            'name' => $data['name'],
                            'status' => 'planned',
                            'starts_at' => $data['starts_at'],
                            'ends_at' => $data['ends_at'],
                        ]);
                        $newRound->save();

                        $newRound->teams()->sync($record->teams()->pluck('teams.id')->all());
                    })
                    ->successNotificationTitle('Nový ročník byl založen. Zkontrolujte jeho data a stav.'),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }
}
