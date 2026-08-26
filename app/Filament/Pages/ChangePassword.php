<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;
    protected static ?string $navigationLabel = 'Změnit heslo';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Změnit heslo';
    protected static ?string $slug = 'change-password';

    public function getView(): string
    {
        return 'filament.pages.change-password';
    }

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Aktuální heslo')
                    ->password()
                    ->revealable()
                    ->required(),

                TextInput::make('new_password')
                    ->label('Nové heslo')
                    ->password()
                    ->revealable()
                    ->required()
                    ->minLength(8),

                TextInput::make('new_password_confirmation')
                    ->label('Potvrzení nového hesla')
                    ->password()
                    ->revealable()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.current_password' => 'Aktuální heslo není správné.',
            ]);
        }

        if ($data['new_password'] !== $data['new_password_confirmation']) {
            throw ValidationException::withMessages([
                'data.new_password_confirmation' => 'Nová hesla se neshodují.',
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        $this->form->fill();

        Notification::make()
            ->title('Heslo bylo úspěšně změněno.')
            ->success()
            ->send();
    }
}
