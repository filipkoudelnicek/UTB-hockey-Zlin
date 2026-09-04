<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Permission;
use App\Models\Role;
use Filament\Actions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RoleResource extends AdminResource
{
    protected static ?string $model = Role::class;
    protected static ?string $permissionKey = 'settings.roles';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|\UnitEnum|null $navigationGroup = 'Nastavení';
    protected static ?string $navigationLabel = 'Role a oprávnění';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $pluralModelLabel = 'Role a oprávnění';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Základní informace')->schema([
                TextInput::make('name')->label('Název role')->required()->unique(ignoreRecord: true),
                Textarea::make('description')->label('Popis pro kolegy')->rows(2)->maxLength(300),
            ])->columns(1),
            Section::make('Oprávnění')
                ->description('Zaškrtněte části administrace, které mohou uživatelé s touto rolí spravovat.')
                ->schema([
                    CheckboxList::make('permissions')
                        ->relationship('permissions', 'label', fn ($query) => $query->orderBy('group')->orderBy('label'))
                        ->getOptionLabelFromRecordUsing(fn (Permission $permission): string => "{$permission->group} — {$permission->label}")
                        ->columns(2)
                        ->searchable()
                        ->bulkToggleable()
                        ->disabled(fn (?Role $record): bool => (bool) $record?->is_administrator)
                        ->helperText(fn (?Role $record): ?string => $record?->is_administrator
                            ? 'Role root má vždy úplný přístup.'
                            : null),
                ]),
            Section::make('Správa rolí')
                ->description('Vyberte role, které mohou uživatelé s touto rolí upravovat a přiřazovat.')
                ->schema([
                    CheckboxList::make('manageableRoles')
                        ->label('Spravované role')
                        ->relationship('manageableRoles', 'name')
                        ->options(fn (): array => Role::query()
                            ->get()
                            ->filter(fn (Role $role): bool => auth()->user()?->canManageRole($role) ?? false)
                            ->sortBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->disabled(fn (?Role $record): bool => (bool) $record?->is_administrator)
                        ->helperText(fn (?Role $record): ?string => $record?->is_administrator
                            ? 'Role root může spravovat všechny role.'
                            : null),
                ]),
        ]);
    }

    public static function canEdit(Model $record): bool
    {
        return parent::canEdit($record)
            && auth()->user()?->canManageRole($record) === true;
    }

    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record)
            && auth()->user()?->canManageRole($record) === true;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Role')->searchable()->sortable(),
            TextColumn::make('description')->label('Popis')->limit(60)->placeholder('—'),
            TextColumn::make('permissions_count')->label('Oprávnění')->counts('permissions'),
            TextColumn::make('users_count')->label('Uživatelé')->counts('users'),
            IconColumn::make('is_administrator')->label('Úplný přístup')->boolean(),
        ])->recordActions([
            Actions\EditAction::make(),
            Actions\DeleteAction::make()->visible(fn (Role $record): bool => ! $record->is_administrator),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
