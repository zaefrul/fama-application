<x-layouts.fama title="Senarai Syarikat">
    <div class="space-y-4">
        <div class="flex items-start justify-between gap-3">
            <x-page-title title="Senarai Syarikat" />
            <a href="{{ route('fama.companies.create') }}"><x-button type="button">Daftar Vendor</x-button></a>
        </div>
        <x-input placeholder="Carian" name="q" readonly />
        <ul class="space-y-2">
            @foreach ($companies as $company)
                <li>
                    <a href="{{ route('fama.companies.show', $company) }}">
                        <x-card class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-muted">{{ $company->registration_no }}</p>
                                <p class="font-semibold">{{ $company->name }}</p>
                            </div>
                            <span class="text-brand">✎</span>
                        </x-card>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts.fama>
