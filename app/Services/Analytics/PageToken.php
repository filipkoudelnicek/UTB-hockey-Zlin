<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Jednorázový šifrovaný token vydaný serverem spolu s veřejnou stránkou.
 * Brání odesílání libovolných URL do analytického endpointu.
 */
class PageToken
{
    public static function issue(string $path, ?string $viewableType, ?int $viewableId): string
    {
        return Crypt::encryptString(json_encode([
            'p' => $path,
            't' => $viewableType,
            'i' => $viewableId,
            'n' => Str::random(24),
            'e' => now()->addMinutes((int) config('analytics.token_ttl_minutes'))->getTimestamp(),
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @return array{path: string, type: ?string, id: ?int}|null
     */
    public static function consume(string $token): ?array
    {
        try {
            $data = json_decode(Crypt::decryptString($token), true, 4, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }

        if (! is_array($data) || ! isset($data['p'], $data['n'], $data['e']) || ! is_string($data['p'])) {
            return null;
        }

        $remaining = (int) $data['e'] - now()->getTimestamp();

        if ($remaining <= 0 || ! Cache::add('analytics:nonce:'.$data['n'], 1, $remaining)) {
            return null;
        }

        return [
            'path' => $data['p'],
            'type' => $data['t'] ?? null,
            'id' => isset($data['i']) ? (int) $data['i'] : null,
        ];
    }
}
