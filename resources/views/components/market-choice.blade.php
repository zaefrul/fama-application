@props(['value' => 'EXPORT', 'readOnly' => false])
<fieldset class="grid gap-2">
    <legend class="text-sm font-medium text-ink">Pasaran <span class="text-danger">*</span></legend>
    <div class="grid grid-cols-2 gap-2">
        @foreach (\App\Domain\Market::cases() as $option)
            <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-border px-3 py-2.5 text-sm font-semibold has-[:checked]:border-brand has-[:checked]:text-brand {{ $readOnly ? 'pointer-events-none' : '' }}">
                <input
                    type="radio"
                    name="market"
                    value="{{ $option->value }}"
                    class="accent-brand"
                    @checked($value === $option->value)
                    @disabled($readOnly)
                    @unless ($readOnly) required @endunless
                >
                {{ $option->label() }}
            </label>
        @endforeach
    </div>
</fieldset>
