<?php

namespace App\Domain;

use Illuminate\Support\Str;

final class Ids
{
    public static function create(string $prefix): string
    {
        return $prefix.'_'.Str::uuid()->toString();
    }

    /**
     * @param  list<string>  $existing
     */
    public static function nextApplicationNo(array $existing): string
    {
        return self::nextSerial($existing, 'FAMA-2026-');
    }

    /**
     * @param  list<string>  $existing
     */
    public static function nextQrCode(array $existing): string
    {
        return self::nextSerial($existing, 'GPL-QR-');
    }

    /**
     * @param  list<string>  $existing
     */
    private static function nextSerial(array $existing, string $prefix): string
    {
        $numbers = [];
        foreach ($existing as $value) {
            $suffix = Str::afterLast($value, '-');
            if (preg_match('/^\d{1,6}$/', $suffix) === 1) {
                $numbers[] = (int) $suffix;
            }
        }

        $next = ($numbers === [] ? 0 : max($numbers)) + 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
