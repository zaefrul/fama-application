@props([
    'types',
    'selected' => null,
    'disabled' => false,
    'required' => false,
    'selectName' => 'produceTypeId',
    'newNameField' => 'newProduceName',
])
@php
    $uid = 'produce-type-'.str_replace('.', '', uniqid('', true));
    $oldNew = old($newNameField, '');
    $startCustom = filled($oldNew);
    $selectedId = old($selectName, $selected);
@endphp
<div
    {{ $attributes->merge(['id' => $uid, 'class' => 'flex gap-2']) }}
    data-required="{{ $required ? '1' : '0' }}"
    data-custom="{{ $startCustom ? '1' : '0' }}"
>
    <div class="min-w-0 flex-1" data-role="select-wrap" @if ($startCustom) hidden @endif>
        <x-select
            name="{{ $selectName }}"
            :disabled="$disabled || $startCustom"
            :required="$required && ! $startCustom"
        >
            @foreach ($types as $type)
                <option value="{{ $type->id }}" @selected($selectedId === $type->id)>{{ $type->name }}</option>
            @endforeach
        </x-select>
    </div>
    <div class="min-w-0 flex-1" data-role="new-wrap" @unless ($startCustom) hidden @endunless>
        <x-input
            name="{{ $newNameField }}"
            value="{{ $oldNew }}"
            placeholder="Jenis Keluaran Pertanian baharu"
            maxlength="80"
            autocomplete="off"
            :disabled="$disabled || ! $startCustom"
            :required="$startCustom"
        />
    </div>
    @unless ($disabled)
        <x-button
            type="button"
            variant="secondary"
            class="min-w-11 px-3"
            data-role="toggle"
            aria-label="{{ $startCustom ? 'Batal tambah Jenis Keluaran Pertanian' : 'Tambah Jenis Keluaran Pertanian' }}"
            title="{{ $startCustom ? 'Batal' : 'Tambah Jenis Keluaran Pertanian' }}"
        >{{ $startCustom ? '×' : '+' }}</x-button>
    @endunless
    @unless ($disabled)
    <script>
        (function () {
            var root = document.getElementById(@json($uid));
            if (!root) return;
            var selectWrap = root.querySelector('[data-role="select-wrap"]');
            var newWrap = root.querySelector('[data-role="new-wrap"]');
            var select = selectWrap.querySelector('select');
            var input = newWrap.querySelector('input');
            var toggle = root.querySelector('[data-role="toggle"]');
            var required = root.dataset.required === '1';
            function setCustom(on) {
                selectWrap.hidden = on;
                newWrap.hidden = !on;
                select.disabled = on;
                select.required = required && !on;
                input.disabled = !on;
                input.required = on;
                if (!on) input.value = '';
                toggle.textContent = on ? '×' : '+';
                toggle.setAttribute('aria-label', on ? 'Batal tambah Jenis Keluaran Pertanian' : 'Tambah Jenis Keluaran Pertanian');
                toggle.setAttribute('title', on ? 'Batal' : 'Tambah Jenis Keluaran Pertanian');
                if (on) input.focus();
            }
            toggle.addEventListener('click', function () {
                setCustom(newWrap.hidden);
            });
        })();
    </script>
    @endunless
</div>
