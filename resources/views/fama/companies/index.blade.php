<x-layouts.fama title="Senarai Syarikat">
    <div class="space-y-4">
        <div class="flex items-start justify-between gap-3">
            <x-page-title title="Senarai Syarikat" />
            <a href="{{ route('fama.companies.create') }}"><x-button type="button">Daftar Vendor</x-button></a>
        </div>
        <form method="get" action="{{ route('fama.companies') }}" class="flex items-start gap-2" data-role="company-search" data-debounce="300">
            <x-input
                name="q"
                value="{{ $q }}"
                placeholder="Carian nama syarikat, no. pendaftaran atau no. akaun"
                class="flex-1"
                autocomplete="off"
            />
            <x-button type="submit" variant="secondary">Cari</x-button>
        </form>
        <div data-role="company-results">
            @if ($companies->isEmpty())
                <p class="text-sm text-muted">{{ $q !== '' ? 'Tiada syarikat dijumpai untuk carian ini.' : 'Tiada syarikat lagi.' }}</p>
            @else
                <ul class="space-y-2">
                    @foreach ($companies as $company)
                        <li>
                            <a href="{{ route('fama.companies.show', $company) }}">
                                <x-card class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-muted">{{ $company->registration_no }}</p>
                                        <p class="font-semibold">{{ $company->displayName() }}</p>
                                    </div>
                                    <span class="text-brand">✎</span>
                                </x-card>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    <script>
        (function () {
            var form = document.querySelector('[data-role="company-search"]');
            if (!form) return;
            var input = form.querySelector('[name="q"]');
            var delay = parseInt(form.getAttribute('data-debounce') || '300', 10);
            var timer = null;
            var request = null;

            function resultsBox() {
                return document.querySelector('[data-role="company-results"]');
            }

            function searchUrl(value) {
                var url = new URL(form.getAttribute('action'), window.location.origin);
                var q = String(value || '').trim();
                if (q) url.searchParams.set('q', q);
                return url;
            }

            function runSearch() {
                var url = searchUrl(input.value);
                if (request) request.abort();
                request = new AbortController();
                fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    signal: request.signal,
                }).then(function (response) {
                    return response.text();
                }).then(function (html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var next = doc.querySelector('[data-role="company-results"]');
                    var current = resultsBox();
                    if (next && current) current.replaceWith(next);
                    history.replaceState({}, '', url);
                }).catch(function (error) {
                    if (error && error.name === 'AbortError') return;
                });
            }

            function scheduleSearch() {
                clearTimeout(timer);
                timer = setTimeout(runSearch, delay);
            }

            input.addEventListener('input', scheduleSearch);
            input.addEventListener('keyup', scheduleSearch);
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                clearTimeout(timer);
                runSearch();
            });
        })();
    </script>
</x-layouts.fama>
