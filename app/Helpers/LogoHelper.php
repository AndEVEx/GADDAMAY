<?php

namespace App\Helpers;

class LogoHelper
{
    private static ?string $cachedBase64 = null;

    public static function getBase64(): string
    {
        if (self::$cachedBase64 === null) {
            $path = public_path('icons/logo-sekolah.png');
            if (file_exists($path)) {
                self::$cachedBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
            } else {
                self::$cachedBase64 = '';
            }
        }
        return self::$cachedBase64;
    }
}
