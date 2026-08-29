<x-layouts.exporter title="Keluaran">
    <div class="space-y-4">
        <x-page-title title="Maklumat Keluaran Pertanian" />
        <x-company-nav />
        <x-card>
            <form action="{{ route('exporter.produce') }}" method="post" class="space-y-3">
                @csrf
                @if (session('error'))
                    <x-error-text>{{ session('error') }}</x-error-text>
                @endif
                <x-field label="Jenis Keluaran Pertanian" hint="Taip untuk cari. Jika tiada dalam senarai, tekan + untuk tambah.">
                    <div class="flex items-start gap-2">
                        <x-produce-type-field :types="$types" class="min-w-0 flex-1" />
                        <x-button type="submit">+ Tambah</x-button>
                    </div>
                </x-field>
            </form>
            <ul class="mt-4 space-y-2">
                @foreach ($produce as $row)
                    <li class="flex items-center justify-between rounded-xl border border-border px-3 py-2">
                        <span class="font-semibold">{{ $row->produceType?->name }}</span>
                        <form action="{{ url('/exporter/company/produce/delete') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $row->id }}">
                            <x-button type="submit" variant="danger">Buang</x-button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </x-card>
    </div>
</x-layouts.exporter>
