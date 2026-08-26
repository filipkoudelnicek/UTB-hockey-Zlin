<?php

namespace App\Livewire;

use App\Services\LanguageService;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public function changeLang(string $selectLocale, string $actualUrl)
    {
        $redirectUrl = LanguageService::changeLanguage($selectLocale, $actualUrl);
        
        return $this->redirect($redirectUrl);
    }

    public function render()
    {
        $langs = LanguageService::getActiveLanguages();
        $actual = LanguageService::getCurrentLanguage();

        return view('livewire.language-switcher', [
            'langs' => $langs,
            'actual' => $actual
        ]);
    }
}
