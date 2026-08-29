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
    $oldNew = trim((string) old($newNameField, ''));
    $selectedId = (string) old($selectName, $selected ?? '');
    $items = $types->map(fn ($type) => [
        'id' => (string) $type->id,
        'name' => (string) $type->name,
    ])->values();
    $selectedName = $oldNew;
    if ($selectedName === '') {
        $match = $items->firstWhere('id', $selectedId);
        $selectedName = $match['name'] ?? '';
    }
    $startCustom = $oldNew !== '';
@endphp
<div
    {{ $attributes->merge(['id' => $uid, 'class' => 'flex gap-2']) }}
    data-required="{{ $required ? '1' : '0' }}"
>
    <div class="relative min-w-0 flex-1">
        <input type="hidden" name="{{ $selectName }}" value="{{ $startCustom ? '' : $selectedId }}" data-role="id" @disabled($disabled)>
        <input type="hidden" name="{{ $newNameField }}" value="{{ $oldNew }}" data-role="new" @disabled($disabled)>
        <x-input
            type="text"
            role="combobox"
            data-role="query"
            value="{{ $selectedName }}"
            placeholder="Cari Jenis Keluaran Pertanian"
            maxlength="80"
            autocomplete="off"
            autocorrect="off"
            spellcheck="false"
            aria-autocomplete="list"
            aria-expanded="false"
            aria-controls="{{ $uid }}-list"
            aria-label="Cari Jenis Keluaran Pertanian"
            :disabled="$disabled"
            :readonly="$disabled"
            :required="$required"
        />
        @unless ($disabled)
            <ul
                id="{{ $uid }}-list"
                hidden
                role="listbox"
                data-role="list"
                class="produce-type-list"
            ></ul>
            <script type="application/json" data-role="items">@json($items)</script>
        @endunless
    </div>
    @unless ($disabled)
        <x-button
            type="button"
            variant="secondary"
            class="min-w-11 px-3"
            data-role="toggle"
            aria-label="Tambah Jenis Keluaran Pertanian"
            title="Tambah Jenis Keluaran Pertanian"
        >+</x-button>
        <script>
            (function () {
                var root = document.getElementById(@json($uid));
                if (!root) return;
                var idInput = root.querySelector('[data-role="id"]');
                var newInput = root.querySelector('[data-role="new"]');
                var query = root.querySelector('[data-role="query"]');
                var list = root.querySelector('[data-role="list"]');
                var toggle = root.querySelector('[data-role="toggle"]');
                var itemsNode = root.querySelector('[data-role="items"]');
                var items = [];
                try { items = JSON.parse(itemsNode.textContent || '[]'); } catch (e) { items = []; }
                var active = -1;
                var browseAll = true;

                function norm(value) {
                    return String(value || '').trim().toLowerCase();
                }
                function findExact(value) {
                    var q = norm(value);
                    if (!q) return null;
                    for (var i = 0; i < items.length; i++) {
                        if (norm(items[i].name) === q) return items[i];
                    }
                    return null;
                }
                function filtered() {
                    var q = norm(query.value);
                    if (!q || browseAll) return items.slice();
                    return items.filter(function (item) {
                        return norm(item.name).indexOf(q) !== -1;
                    });
                }
                function setExpanded(open) {
                    list.hidden = !open;
                    query.setAttribute('aria-expanded', open ? 'true' : 'false');
                    if (!open) {
                        active = -1;
                        query.removeAttribute('aria-activedescendant');
                    }
                }
                function paintActive() {
                    var options = list.querySelectorAll('[role="option"]');
                    options.forEach(function (option, index) {
                        var on = index === active;
                        option.classList.toggle('is-active', on);
                        if (on) {
                            query.setAttribute('aria-activedescendant', option.id);
                            option.scrollIntoView({ block: 'nearest' });
                        }
                    });
                }
                function render() {
                    var rows = filtered();
                    list.innerHTML = '';
                    active = rows.length ? 0 : -1;
                    if (!rows.length) {
                        var empty = document.createElement('li');
                        empty.className = 'produce-type-empty';
                        empty.textContent = 'Tiada padanan. Tekan + untuk tambah.';
                        list.appendChild(empty);
                        query.removeAttribute('aria-activedescendant');
                        return;
                    }
                    rows.forEach(function (item, index) {
                        var option = document.createElement('li');
                        option.id = root.id + '-opt-' + index;
                        option.setAttribute('role', 'option');
                        option.className = 'produce-type-option';
                        option.textContent = item.name;
                        option.addEventListener('mousedown', function (event) {
                            event.preventDefault();
                            selectItem(item);
                        });
                        list.appendChild(option);
                    });
                    paintActive();
                }
                function isAddingNew() {
                    return Boolean(String(newInput.value || '').trim());
                }
                function openList() {
                    if (isAddingNew()) {
                        setExpanded(false);
                        return;
                    }
                    render();
                    setExpanded(true);
                }
                function selectItem(item) {
                    idInput.value = item.id;
                    newInput.value = '';
                    query.value = item.name;
                    browseAll = true;
                    setExpanded(false);
                    toggle.textContent = '+';
                    toggle.setAttribute('aria-label', 'Tambah Jenis Keluaran Pertanian');
                    toggle.setAttribute('title', 'Tambah Jenis Keluaran Pertanian');
                }
                function commitTyped() {
                    var typed = String(query.value || '').trim();
                    var exact = findExact(typed);
                    if (exact) {
                        selectItem(exact);
                        return true;
                    }
                    if (!typed) {
                        idInput.value = '';
                        newInput.value = '';
                        return false;
                    }
                    idInput.value = '';
                    newInput.value = typed;
                    query.value = typed;
                    list.innerHTML = '';
                    setExpanded(false);
                    toggle.textContent = '×';
                    toggle.setAttribute('aria-label', 'Batal tambah Jenis Keluaran Pertanian');
                    toggle.setAttribute('title', 'Batal');
                    return true;
                }
                function cancelCustom() {
                    newInput.value = '';
                    idInput.value = '';
                    query.value = '';
                    toggle.textContent = '+';
                    toggle.setAttribute('aria-label', 'Tambah Jenis Keluaran Pertanian');
                    toggle.setAttribute('title', 'Tambah Jenis Keluaran Pertanian');
                    browseAll = true;
                    query.focus();
                    openList();
                }

                query.addEventListener('focus', function () {
                    if (isAddingNew()) {
                        setExpanded(false);
                        return;
                    }
                    browseAll = true;
                    query.select();
                    openList();
                });
                query.addEventListener('input', function () {
                    idInput.value = '';
                    newInput.value = '';
                    browseAll = false;
                    toggle.textContent = '+';
                    toggle.setAttribute('aria-label', 'Tambah Jenis Keluaran Pertanian');
                    toggle.setAttribute('title', 'Tambah Jenis Keluaran Pertanian');
                    openList();
                });
                query.addEventListener('keydown', function (event) {
                    var open = !list.hidden;
                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        if (!open) openList();
                        var count = list.querySelectorAll('[role="option"]').length;
                        if (!count) return;
                        active = active < count - 1 ? active + 1 : 0;
                        paintActive();
                    } else if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        if (!open) openList();
                        var total = list.querySelectorAll('[role="option"]').length;
                        if (!total) return;
                        active = active > 0 ? active - 1 : total - 1;
                        paintActive();
                    } else if (event.key === 'Enter') {
                        if (open) {
                            var options = list.querySelectorAll('[role="option"]');
                            if (options[active]) {
                                event.preventDefault();
                                var name = options[active].textContent;
                                var item = findExact(name);
                                if (item) selectItem(item);
                                return;
                            }
                        }
                        commitTyped();
                    } else if (event.key === 'Escape') {
                        setExpanded(false);
                    }
                });
                toggle.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                });
                toggle.addEventListener('click', function () {
                    if (isAddingNew()) {
                        cancelCustom();
                        return;
                    }
                    if (!String(query.value || '').trim()) {
                        query.focus();
                        openList();
                        return;
                    }
                    commitTyped();
                    setExpanded(false);
                });
                document.addEventListener('click', function (event) {
                    if (!root.contains(event.target)) setExpanded(false);
                });
                var form = root.closest('form');
                if (form) {
                    form.addEventListener('submit', function () {
                        if (idInput.value || newInput.value) return;
                        commitTyped();
                    });
                }
            })();
        </script>
    @endunless
</div>
