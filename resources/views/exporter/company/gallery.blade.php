<x-layouts.exporter title="Galeri">
    <div class="space-y-4">
        <x-page-title title="Galeri" subtitle="Muat naik gambar kebun, lot atau buah (JPG/PNG/WEBP, maksimum 5MB)." />
        <x-company-nav />
        <x-card>
            <form action="{{ route('exporter.gallery') }}" method="post" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-2">
                @csrf
                <x-field label="Kategori">
                    <x-select name="category">
                        <option value="KEBUN">KEBUN</option>
                        <option value="LOT_KEBUN">LOT KEBUN</option>
                        <option value="BUAH">BUAH</option>
                    </x-select>
                </x-field>
                <x-field label="Keterangan" required><x-input name="description" required /></x-field>
                <x-field label="Gambar" required>
                    <x-input name="image" type="file" accept="image/jpeg,image/png,image/webp" required />
                </x-field>
                <div class="flex items-end"><x-button type="submit">+ Gambar</x-button></div>
                <div class="md:col-span-2"><x-error-text>{{ $error }}</x-error-text></div>
            </form>
        </x-card>
        <ul class="space-y-2">
            @foreach ($items as $item)
                <li>
                    <x-card class="flex items-center gap-3">
                        <img
                            src="{{ $item->file_path }}"
                            alt="{{ $item->description }}"
                            width="64"
                            height="64"
                            class="company-logo rounded-lg"
                            style="display:block;width:64px;height:64px;max-width:64px;max-height:64px;object-fit:cover"
                        >
                        <div class="flex-1">
                            <p class="font-semibold">{{ $item->description }}</p>
                            <p class="text-xs text-muted">{{ $item->uploaded_at?->locale('ms')->translatedFormat('d/m/Y') }}</p>
                        </div>
                        <form action="{{ url('/exporter/company/gallery/delete') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <x-button variant="danger" type="submit">Buang</x-button>
                        </form>
                    </x-card>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts.exporter>
