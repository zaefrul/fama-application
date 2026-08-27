<p class="border-b border-warning/20 px-3 py-2 text-[11px] leading-5 text-muted">{{ $t['agenciesNote'] }}</p>
<div class="space-y-3 px-3 py-4">
    <div class="grid grid-cols-2 items-center gap-3">
        <img
            src="{{ asset('logos/logo-jata-negara.png') }}"
            alt="{{ $t['agencyMalaysia'] }}"
            width="160"
            height="114"
            class="trace-gov-logo"
            style="display:block;width:auto;height:64px;max-width:100%;margin:0 auto;object-fit:contain"
        >
        <img
            src="{{ asset('logos/logo-fama.png') }}"
            alt="{{ $t['agencyFama'] }}"
            width="80"
            height="80"
            class="trace-gov-logo"
            style="display:block;width:auto;height:64px;max-width:100%;margin:0 auto;object-fit:contain"
        >
    </div>
    <p class="text-center text-sm font-semibold">
        <a href="https://www.fama.gov.my" class="text-brand underline-offset-2 hover:underline" rel="noopener noreferrer" target="_blank">{{ $t['contactLink'] }}</a>
    </p>
</div>
