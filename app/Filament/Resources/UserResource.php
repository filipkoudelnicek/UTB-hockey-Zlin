<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Role;
use App\Support\PasswordRequirements;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;

class UserResource extends AdminResource
{
    protected static ?string $model = User::class;
    protected static ?string $permissionKey = 'settings.users';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;
    protected static ?string $navigationLabel = 'Uživatelé';
    protected static ?string $modelLabel = 'Uživatelé';
    protected static ?string $pluralModelLabel = 'Uživatelé';
    protected static string|\UnitEnum|null $navigationGroup = 'Nastavení';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Jméno')
                    ->required(),
                TextInput::make('email')->label('Email')
                    ->required()
                    ->email()->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Heslo')
                    ->password()
                    ->revealable()
                    ->live(debounce: 300)
                    ->rules(fn (string $operation, ?string $state): array => $operation === 'create' || filled($state)
                        ? PasswordRequirements::rules()
                        : [])
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->visible(fn (?User $record): bool => $record === null || (auth()->user()?->canChangePasswordOf($record) ?? false))
                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Ponechte prázdné, pokud nechcete heslo měnit.' : null),
                TextInput::make('password_confirmation')
                    ->label('Potvrzení hesla')
                    ->password()
                    ->revealable()
                    ->same('password')
                    ->live(debounce: 300)
                    ->dehydrated(false)
                    ->required(fn (Get $get, string $operation): bool => $operation === 'create' || filled($get('password')))
                    ->visible(fn (?User $record): bool => $record === null || (auth()->user()?->canChangePasswordOf($record) ?? false)),
                Html::make(fn (Get $get): HtmlString => new HtmlString(view(
                    'filament.forms.components.password-requirements',
                    [
                        'password' => $get('password'),
                        'confirmation' => $get('password_confirmation'),
                    ],
                )->render()))
                    ->columnSpanFull()
                    ->visible(fn (?User $record): bool => $record === null || (auth()->user()?->canChangePasswordOf($record) ?? false)),
                Select::make('role_id')
                    ->label('Role')
                    ->options(fn (?User $record): array => Role::query()
                        ->whereKey(static::assignableRoleIds($record))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Uživatel může mít právě jednu roli.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->label('Jméno'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('roles.name')->label('Role')->badge()->separator(', '),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->visible(fn (User $record): bool => static::canDelete($record))
                    ->action(function (User $record): void {
                        if (! static::canDelete($record)) {
                            return;
                        }

                        $record->delete();
                    }),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->authorizeIndividualRecords(fn (User $record): bool => static::canDelete($record)),
                ]),
            ]);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if ($user?->is($record)) {
            return $user->hasAnyPermissions();
        }

        return parent::canEdit($record)
            && $user?->canManageUser($record) === true;
    }

    public static function canDelete(Model $record): bool
    {
        return ! auth()->user()?->is($record)
            && parent::canDelete($record)
            && auth()->user()?->canManageUser($record) === true;
    }

    public static function assignableRoleIds(?User $record): array
    {
        $user = auth()->user();
        $target = $record ?? new User();

        if (! $user) {
            return [];
        }

        return Role::query()
            ->get()
            ->filter(fn (Role $role): bool => $user->canAssignRoleTo($target, $role))
            ->pluck('id')
            ->all();
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
