@props(['label', 'hint' => null, 'required' => false])
<label class="block space-y-1.5">
    <span class="text-sm font-medium text-ink">
        {{ $label }}
        @if ($required)<span class="text-danger"> *</span>@endif
    </span>
    {{ $slot }}
    @if ($hint)<span class="text-xs text-muted">{{ $hint }}</span>@endif
</label>
