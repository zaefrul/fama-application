<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class UploadService
{
    public static function maxBytes(): int
    {
        $limits = array_values(array_filter(
            [
                self::iniToBytes((string) ini_get('upload_max_filesize')),
                self::iniToBytes((string) ini_get('post_max_size')),
            ],
            fn (int $bytes) => $bytes > 0,
        ));

        return $limits === [] ? 0 : min($limits);
    }

    public static function maxLabel(): string
    {
        $bytes = self::maxBytes();
        if ($bytes <= 0) {
            return 'tiada had';
        }
        if ($bytes % (1024 * 1024) === 0) {
            return ($bytes / (1024 * 1024)).'MB';
        }
        if ($bytes > 1024 * 1024) {
            return rtrim(rtrim(number_format($bytes / (1024 * 1024), 1, '.', ''), '0'), '.').'MB';
        }

        return (int) ceil($bytes / 1024).'KB';
    }

    public static function iniToBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '' || $value === '-1') {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }

    public function save(?UploadedFile $file, string $folder, bool $allowPdf = false, bool $required = false): ?string
    {
        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            if ($required) {
                throw new RuntimeException('Sila muat naik fail');
            }

            return null;
        }

        if (! $file->isValid()) {
            throw new RuntimeException($this->invalidUploadMessage($file));
        }

        $limit = self::maxBytes();
        if ($limit > 0 && $file->getSize() > $limit) {
            throw new RuntimeException('Fail melebihi had pelayan ('.self::maxLabel().')');
        }

        $allowed = $allowPdf
            ? ['image/jpeg', 'image/png', 'image/webp', 'application/pdf', 'application/x-pdf']
            : ['image/jpeg', 'image/png', 'image/webp'];

        $mime = $file->getMimeType() ?: $file->getClientMimeType();
        if (! in_array($mime, $allowed, true)) {
            throw new RuntimeException(
                $allowPdf
                    ? 'Format dibenarkan: JPG, PNG, WEBP atau PDF'
                    : 'Format dibenarkan: JPG, PNG atau WEBP'
            );
        }

        $directory = public_path('storage/'.$folder);
        if (! is_dir($directory) && ! @mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Folder muat naik tidak dapat dicipta. Sila semak kebenaran public/storage.');
        }

        try {
            $path = $file->store($folder, 'public');
        } catch (\Throwable) {
            throw new RuntimeException('Fail tidak dapat disimpan. Sila semak folder public/storage.');
        }

        if (! $path) {
            throw new RuntimeException('Fail tidak dapat disimpan. Sila semak folder public/storage.');
        }

        return '/storage/'.$path;
    }

    private function invalidUploadMessage(UploadedFile $file): string
    {
        $limit = self::maxLabel();

        return match ($file->getError()) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Fail melebihi had pelayan ('.$limit.')',
            UPLOAD_ERR_PARTIAL => 'Muat naik tidak lengkap. Sila cuba lagi.',
            default => 'Fail tidak dapat dimuat naik',
        };
    }

    public static function isPdf(string $path): bool
    {
        return str_ends_with(strtolower($path), '.pdf');
    }
}
