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

    public static function isPdf(string $path): bool
    {
        return str_ends_with(strtolower($path), '.pdf');
    }
}
