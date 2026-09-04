<?php

namespace App\Support;

use Illuminate\Validation\Rules\Password;

final class PasswordRequirements
{
    public const MIN_LENGTH = 9;

    /**
     * @return array<int, Password|string>
     */
    public static function rules(): array
    {
        return [
            Password::min(self::MIN_LENGTH)
                ->mixedCase()
                ->numbers(),
            'regex:/[^\p{L}\p{N}\s]/u',
        ];
    }

    /**
     * @return array<int, array{label: string, valid: bool}>
     */
    public static function checks(?string $password, ?string $confirmation = null): array
    {
        $password ??= '';

        return [
            [
                'label' => 'Alespoň ' . self::MIN_LENGTH . ' znaků',
                'valid' => mb_strlen($password) >= self::MIN_LENGTH,
            ],
            [
                'label' => 'Malé písmeno',
                'valid' => (bool) preg_match('/\p{Ll}/u', $password),
            ],
            [
                'label' => 'Velké písmeno',
                'valid' => (bool) preg_match('/\p{Lu}/u', $password),
            ],
            [
                'label' => 'Číslice',
                'valid' => (bool) preg_match('/\p{N}/u', $password),
            ],
            [
                'label' => 'Speciální znak',
                'valid' => (bool) preg_match('/[^\p{L}\p{N}\s]/u', $password),
            ],
            [
                'label' => 'Hesla se shodují',
                'valid' => $password !== '' && hash_equals($password, $confirmation ?? ''),
            ],
        ];
    }
}
