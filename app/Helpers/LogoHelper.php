<?php

namespace App\Helpers;

class LogoHelper
{
    private static ?string $cachedBase64 = null;

    public static function getBase64(): string
    {
        if (self::$cachedBase64 === null) {
            $paths = [
                public_path('pwa-icons/logosekolah.png'),
                public_path('pwa-icons/logo-sekolah.png'),
                public_path('icons/logo-sekolah.png'),
                public_path('icons/logosekolah.png'),
                public_path('icons/icon-512.png'),
            ];

            foreach ($paths as $path) {
                if (file_exists($path)) {
                    self::$cachedBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
                    break;
                }
            }

            if (self::$cachedBase64 === null) {
                self::$cachedBase64 = '';
            }
        }
        return self::$cachedBase64;
    }
}
