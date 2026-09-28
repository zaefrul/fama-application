@props(['produceUrl', 'livestockUrl'])
<div class="grid gap-3">
    <a href="{{ $produceUrl }}" class="block rounded-2xl border border-border bg-white p-5 shadow-sm transition hover:border-brand">
        <p class="text-base font-bold text-brand">Keluaran Pertanian</p>
        <p class="mt-1 text-sm text-muted">Buah-buahan dan sayur-sayuran. Borang ini merekod varieti, gred, saiz, ladang dan sijil CoC.</p>
    </a>
    <a href="{{ $livestockUrl }}" class="block rounded-2xl border border-border bg-white p-5 shadow-sm transition hover:border-brand">
        <p class="text-base font-bold text-brand">Haiwan Ternakan</p>
        <p class="mt-1 text-sm text-muted">Baka, bilangan, premis, rumah sembelih dan sijil veterinar.</p>
    </a>
</div>
