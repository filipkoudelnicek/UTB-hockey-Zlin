<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Illuminate\Contracts\Auth\Authenticatable;

class RootAppAuthentication extends AppAuthentication
{
    public function isEnabled(Authenticatable $user): bool
    {
        return $user instanceof User
            && $user->isRoot()
            && parent::isEnabled($user);
    }
}