<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sistem Jejak GPL' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh antialiased">
    <div class="flex min-h-dvh items-center justify-center bg-[linear-gradient(180deg,#e8f3ee_0%,#f3f5f4_42%)] px-4 py-8">
        <div class="w-full max-w-md">
            <div class="mb-6 flex flex-col items-center text-center">
                <img src="{{ asset('logos/jejak-gpl.png') }}" alt="Sistem Jejak GPL" class="h-16 w-auto max-w-[70vw] object-contain sm:h-24">
                <p class="mt-2 text-xs text-muted">Lembaga Pemasaran Pertanian Persekutuan</p>
            </div>
            {{ $slot }}
        </div>
    </div>
</body>
</html>
