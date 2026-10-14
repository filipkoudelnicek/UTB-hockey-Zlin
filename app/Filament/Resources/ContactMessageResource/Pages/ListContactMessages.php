<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Cache;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(): void
    {
        parent::mount();

        // Bez cronu: úklid se spustí nejvýše jednou denně při otevření seznamu.
        Cache::remember('contact-messages:cleanup', now()->addDay(), function () {
            ContactMessage::whereNotNull('ip_address')
                ->where('created_at', '<', now()->subDays(90))
                ->update(['ip_address' => null]);

            ContactMessage::where('created_at', '<', now()->subYear())->delete();

            return true;
        });
    }
}
