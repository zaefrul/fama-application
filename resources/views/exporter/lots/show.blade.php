@php
    use App\Domain\LotDisposition;
    $open = $lot->status->value === 'ACTIVE'
        && $lot->disposition !== LotDisposition::Sold
        && (int) $lot->quantity_remaining > 0;
@endphp
<x-layouts.exporter :title="$lot->qr_code">
    <div class="space-y-4">
        <x-page-title :title="$lot->qr_code" :subtitle="$lot->rootApplication?->produceType?->name" />
        @if (session('status'))
            <x-card><p class="text-sm font-semibold text-brand">{{ session('status') }}</p></x-card>
        @endif
        @if (session('error'))
            <x-error-text>{{ session('error') }}</x-error-text>
        @endif

        <x-card class="space-y-2">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm">Pemegang: {{ $lot->holder?->name }} · {{ $lot->holder?->partyLabel() }}</p>
                    <p class="text-sm">Kuantiti: {{ number_format((int) $lot->quantity) }} {{ $unit }}</p>
                    <p class="text-sm font-semibold">Baki: {{ number_format((int) $lot->quantity_remaining) }} {{ $unit }}</p>
                    <p class="text-sm">Status lot: {{ $lot->disposition?->label() }}</p>
                </div>
                <x-status-badge :qr="$lot->status" />
            </div>
            <x-qr-preview :value="$publicUrl" />
            <a href="{{ route('exporter.qr.download', $lot) }}"><x-button type="button" class="w-full">Muat Turun QR</x-button></a>
        </x-card>

        <x-card>
            <h2 class="font-semibold">Jejak pecahan</h2>
            <x-lot-timeline
                class="is-inset"
                :nodes="$lineage"
                :current-id="$lot->id"
                :unit="$unit"
                :nested="true"
                :show-balance="true"
                :child-links="true"
            />
        </x-card>

        @if ($open)
            <x-card class="space-y-3">
                <h2 class="font-semibold">Pecahkan kepada syarikat</h2>
                <p class="text-xs text-muted">Jumlah kuantiti tidak boleh melebihi baki. Unit kekal {{ $unit }}.</p>
                <form method="post" action="{{ route('exporter.lots.split', $lot) }}" class="space-y-3">
                    @csrf
                    <div id="chunk-rows" class="space-y-3">
                        <div class="grid gap-2 sm:grid-cols-2">
                            <x-field label="Kuantiti">
                                <x-input name="chunks[0][quantity]" type="number" min="1" />
                            </x-field>
                            <x-field label="Penerima">
                                <x-select name="chunks[0][recipient_company_id]">
                                    <option value="">Pilih syarikat</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->partyLabel() }} · {{ $company->name }}</option>
                                    @endforeach
                                </x-select>
                            </x-field>
                        </div>
                    </div>
                    <x-button type="button" variant="secondary" onclick="addChunkRow()">Tambah pecahan</x-button>
                    <x-button type="submit" class="w-full">Simpan pecahan</x-button>
                </form>
            </x-card>

            <x-card class="space-y-3">
                <h2 class="font-semibold">Jual kepada pengguna</h2>
                <form method="post" action="{{ route('exporter.lots.sell', $lot) }}">
                    @csrf
                    <input type="hidden" name="entire" value="1">
                    <x-button type="submit" variant="secondary" class="w-full">Jual semua baki ({{ number_format((int) $lot->quantity_remaining) }} {{ $unit }})</x-button>
                </form>
                <form method="post" action="{{ route('exporter.lots.sell', $lot) }}" class="space-y-3">
                    @csrf
                    <div id="sell-rows" class="space-y-2">
                        <x-field label="Kuantiti pek">
                            <x-input name="quantities[]" type="number" min="1" />
                        </x-field>
                    </div>
                    <x-button type="button" variant="secondary" onclick="addSellRow()">Tambah pek</x-button>
                    <x-button type="submit" class="w-full">Jual pek</x-button>
                </form>
            </x-card>
            <template id="company-options">
                <option value="">Pilih syarikat</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->partyLabel() }} · {{ $company->name }}</option>
                @endforeach
            </template>
            <script>
                let chunkIndex = 1;

                function addChunkRow() {
                    const rows = document.getElementById('chunk-rows');
                    const wrap = document.createElement('div');
                    wrap.className = 'grid gap-2 sm:grid-cols-2';
                    const quantity = document.createElement('input');
                    quantity.type = 'number';
                    quantity.min = '1';
                    quantity.name = 'chunks[' + chunkIndex + '][quantity]';
                    quantity.className = 'w-full min-w-0 max-w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-brand';
                    const select = document.createElement('select');
                    select.name = 'chunks[' + chunkIndex + '][recipient_company_id]';
                    select.className = 'w-full min-w-0 max-w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-brand';
                    select.innerHTML = document.getElementById('company-options').innerHTML;
                    wrap.append(quantity, select);
                    rows.append(wrap);
                    chunkIndex += 1;
                }

                function addSellRow() {
                    const rows = document.getElementById('sell-rows');
                    const input = document.createElement('input');
                    input.type = 'number';
                    input.min = '1';
                    input.name = 'quantities[]';
                    input.className = 'w-full min-w-0 max-w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-brand';
                    rows.append(input);
                }
            </script>
        @endif
    </div>
</x-layouts.exporter>
