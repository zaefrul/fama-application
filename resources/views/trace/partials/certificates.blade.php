@php
    $certCols = $certCols ?? 'grid-cols-2';
@endphp
<p class="border-b border-warning/20 px-3 py-2 text-[11px] leading-5 text-muted">{{ $t['certsNote'] }}</p>
<div class="grid {{ $certCols }} gap-2 p-3">
    @foreach ($certificates as $certificate)
        @php
            $certSrc = \App\Services\JejakService::certificatePreviewPath($certificate->type, $certificate->document_path);
        @endphp
        <a href="{{ $certSrc }}" target="_blank" rel="noreferrer" class="overflow-hidden rounded-sm border border-warning/40 bg-white/70">
            <div class="relative">
                <x-document-preview :src="$certSrc" :alt="'Contoh sijil '.$certificate->type" class="trace-cert-thumb h-36 w-full object-cover object-top" />
                <span class="absolute left-1.5 top-1.5 rounded-sm bg-danger px-1.5 py-0.5 text-[9px] font-bold tracking-wide text-white">{{ $t['sample'] }}</span>
            </div>
            <div class="p-2">
                <p class="text-xs font-semibold">SIJIL {{ $certificate->type }}</p>
                <p class="truncate text-[10px] text-muted">{{ $certificate->certificate_no }}</p>
            </div>
        </a>
    @endforeach
</div>
