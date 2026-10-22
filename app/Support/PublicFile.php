<?php

namespace App\Support;

final class PublicFile
{
    public static function url(?string $path): ?string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        $relative = ltrim(strtok($path, '?') ?: $path, '/');
        $url = '/'.$relative;
        $file = public_path($relative);
        if (is_file($file)) {
            $url .= '?v='.filemtime($file);
        }

        return $url;
    }
}
