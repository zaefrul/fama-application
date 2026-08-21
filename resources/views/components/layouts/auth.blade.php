<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sistem Jejak GPL' }}</title>
    <x-assets />
</head>
<body class="min-h-dvh antialiased">
    <div class="flex min-h-dvh flex-col">
        <x-gov-masthead />
        <div class="flex flex-1 items-center justify-center bg-[linear-gradient(180deg,#e8f3ee_0%,#f3f5f4_42%)] px-4 py-8">
            <div class="w-full max-w-md">
                <div class="mb-6 flex flex-col items-center text-center">
                    <x-brand-logo variant="auth" />
                    <p class="mt-2 text-xs font-medium text-ink">Lembaga Pemasaran Pertanian Persekutuan</p>
                    <p class="mt-0.5 text-xs text-muted">Portal rasmi jejak eksport hasil pertanian</p>
                </div>
                {{ $slot }}
            </div>
        </div>
        <x-gov-footer />
    </div>
</body>
</html>
