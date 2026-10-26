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
                @if (session('status'))
                    <p class="text-sm font-semibold text-success">{{ session('status') }}</p>
                @endif
                <x-field label="Jenis Keluaran Pertanian" hint="Taip untuk cari. Jika tiada dalam senarai, tekan + untuk tambah.">
                    <div class="flex items-start gap-2">
                        <x-produce-type-field :types="$types" class="min-w-0 flex-1" />
                        <x-button type="submit">+ Tambah</x-button>
                    </div>
                </x-field>
            </form>
            <ul class="mt-4 space-y-3">
                @foreach ($produce as $row)
                    @php
                        $nutrients = \App\Support\ProduceNutrition::formRows($row->produceType?->nutrition);
                    @endphp
                    <li class="rounded-xl border border-border px-3 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-semibold">{{ $row->produceType?->name }}</span>
                            <form action="{{ url('/exporter/company/produce/delete') }}" method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{ $row->id }}">
                                <x-button type="submit" variant="danger">Buang</x-button>
                            </form>
                        </div>
                        <form action="{{ route('exporter.produce.nutrition') }}" method="post" class="mt-3 space-y-2">
                            @csrf
                            <input type="hidden" name="produceTypeId" value="{{ $row->produce_type_id }}">
                            <p class="text-xs font-semibold">Nutrisi bagi 100 g</p>
                            <p class="text-[11px] text-muted">Anggaran demo untuk halaman awam. Bukan nilai rasmi makmal.</p>
                            <div class="space-y-2">
                                @foreach ($nutrients as $index => $nutrient)
                                    <div class="grid grid-cols-3 items-center gap-2">
                                        <input type="hidden" name="nutrients[{{ $index }}][name]" value="{{ $nutrient['name'] }}">
                                        <span class="text-sm">{{ $nutrient['name'] }}</span>
                                        <x-input name="nutrients[{{ $index }}][amount]" :value="$nutrient['amount']" placeholder="0 g" aria-label="{{ $nutrient['name'] }} jumlah" />
                                        <x-input name="nutrients[{{ $index }}][dailyPercent]" :value="$nutrient['dailyPercent']" placeholder="0%" aria-label="{{ $nutrient['name'] }} peratus" />
                                    </div>
                                @endforeach
                            </div>
                            <x-button type="submit">Simpan nutrisi</x-button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </x-card>
    </div>
</x-layouts.exporter>
