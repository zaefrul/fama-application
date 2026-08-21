<x-layouts.exporter title="{{ $qr->qr_code }}">
    <div class="space-y-4">
        <x-page-title :title="$qr->qr_code" :subtitle="$application?->produceType?->name" />
        <x-status-badge :qr="$qr->status" />
        <x-card class="space-y-3">
            <x-qr-preview :value="$publicUrl" />
            <p class="text-sm">Produk: {{ $application?->produceType?->name }} {{ $application?->variety }}</p>
            <p class="text-sm">Gred: {{ $application?->grade }}</p>
            <p class="text-sm">Pengimport: {{ $application?->importer_name }}</p>
            <a href="{{ route('exporter.qr.download', $qr) }}"><x-button type="button" class="w-full">Muat Turun QR</x-button></a>
        </x-card>
    </div>
</x-layouts.exporter>
