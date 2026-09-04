<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Při zapnutém přípravném režimu skryje veřejnou část webu.
     *
     * Administrace musí zůstat dostupná, aby šlo režim opět vypnout. Stejně
     * tak jsou pro administraci potřeba její Livewire požadavky. Přihlášený
     * uživatel administrace je rozpoznán podle oprávnění, nikoli jen podle
     * existence session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $this->isAdministrativeRequest($request)
            || $this->isAdministrator($request)
            || ! $this->isEnabled()
        ) {
            return $next($request);
        }

        return response()
            ->view('maintenance', [], 503)
            ->header('Cache-Control', 'no-store, private')
            ->header('Retry-After', '3600');
    }

    private function isAdministrativeRequest(Request $request): bool
    {
        return $request->is(
            'admin',
            'admin/*',
            'livewire',
            'livewire/*',
            'livewire-*',
            'livewire-*/*',
        )
            // Filament odesílá formuláře přes Livewire. Vedle aktualizací jde
            // například o upload i náhled dočasných souborů. Cesty Livewire 4
            // obsahují hash instalace, proto je rozpoznáváme také podle jména
            // routy a hlavičky.
            || $request->hasHeader('X-Livewire')
            || $request->routeIs(
                'filament.admin.*',
                'livewire.*',
                'default-livewire.*',
            );
    }

    private function isAdministrator(Request $request): bool
    {
        return $request->user()?->hasAnyPermissions() ?? false;
    }

    private function isEnabled(): bool
    {
        try {
            if (! Schema::hasTable('settings')) {
                return false;
            }

            return filter_var(
                Setting::get('maintenance_mode', false),
                FILTER_VALIDATE_BOOL,
            );
        } catch (\Throwable) {
            // Při instalaci nebo výpadku databáze neblokujeme možnost opravy.
            return false;
        }
    }
}
