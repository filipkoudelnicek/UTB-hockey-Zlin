<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\MultiFactor\Pages\SetUpRequiredMultiFactorAuthentication;
use Filament\Facades\Filament;

class RootSetUpRequiredMultiFactorAuthentication extends SetUpRequiredMultiFactorAuthentication
{
    public function mount(): void
    {
        $user = Filament::auth()->user();

        if (! $user instanceof User || ! $user->isRoot()) {
            redirect()->intended(Filament::getUrl());

            return;
        }

        parent::mount();
    }
}