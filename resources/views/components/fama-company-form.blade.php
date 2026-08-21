@props(['action', 'company' => null, 'error' => null, 'submitLabel'])
@php $famaSourced = ! $company || $company->external_source === 'FAMA'; @endphp
<form action="{{ $action }}" method="post" class="grid gap-3 md:grid-cols-2">
    @csrf
    <x-field label="No. Pendaftaran" required>
        <x-input name="registrationNo" :value="$company?->registration_no" :readonly="! $famaSourced" :required="$famaSourced" />
    </x-field>
    <x-field label="Nama Syarikat" required>
        <x-input name="name" :value="$company?->name" :readonly="! $famaSourced" :required="$famaSourced" />
    </x-field>
    <x-field label="Alamat" required>
        <x-input name="address" :value="$company?->address" required />
    </x-field>
    <x-field label="Negeri">
        <x-input name="state" :value="$company?->state" />
    </x-field>
    <x-field label="Daerah">
        <x-input name="district" :value="$company?->district" />
    </x-field>
    <x-field label="Poskod">
        <x-input name="postcode" :value="$company?->postcode" />
    </x-field>
    <x-field label="No. Telefon">
        <x-input name="phone" :value="$company?->phone" />
    </x-field>
    <x-field label="Emel">
        <x-input name="email" type="email" :value="$company?->email" />
    </x-field>
    <x-field label="Laman Web">
        <x-input name="website" :value="$company?->website" />
    </x-field>
    <div class="md:col-span-2">
        <x-error-text>{{ $error }}</x-error-text>
    </div>
    <div class="md:col-span-2">
        <x-button type="submit">{{ $submitLabel }}</x-button>
    </div>
</form>
