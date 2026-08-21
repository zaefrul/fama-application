<x-layouts.exporter title="Muat Turun QR">
    <div class="space-y-4">
        <x-page-title title="Muat Turun QR" />
        <x-card class="space-y-4">
            <x-qr-preview :value="$publicUrl" />
            <form action="{{ route('qr.download', $qr) }}" method="get" class="space-y-3">
                <x-field label="Saiz QR">
                    <x-select name="size">
                        <option value="3">3 cm</option>
                        <option value="5" selected>5 cm</option>
                        <option value="8">Custom (8 cm)</option>
                    </x-select>
                </x-field>
                <x-field label="Format Muat Turun">
                    <x-select name="format">
                        <option value="png">PNG</option>
                        <option value="pdf">PDF</option>
                    </x-select>
                </x-field>
                <x-button type="submit" class="w-full">Muat Turun QR</x-button>
            </form>
        </x-card>
    </div>
</x-layouts.exporter>
