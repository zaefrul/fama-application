<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class UploadService
{
    public function save(?UploadedFile $file, string $folder, bool $allowPdf = false, bool $required = false): ?string
    {
        if (! $file || ! $file->isValid()) {
            if ($required) {
                throw new RuntimeException('Sila muat naik fail');
            }

            return null;
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new RuntimeException('Fail melebihi 5MB');
        }

        $allowed = $allowPdf
            ? ['image/jpeg', 'image/png', 'image/webp', 'application/pdf']
            : ['image/jpeg', 'image/png', 'image/webp'];

        if (! in_array($file->getMimeType(), $allowed, true)) {
            throw new RuntimeException(
                $allowPdf
                    ? 'Format dibenarkan: JPG, PNG, WEBP atau PDF'
                    : 'Format dibenarkan: JPG, PNG atau WEBP'
            );
        }

        $path = $file->store($folder, 'public');

        return '/storage/'.$path;
    }

    public static function isPdf(string $path): bool
    {
        return str_ends_with(strtolower($path), '.pdf');
    }
}
