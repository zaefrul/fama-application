@php
    use App\Domain\LotDisposition;
    use App\Support\TraceCopy;

    $t = TraceCopy::forPage($lang, (bool) $application?->isLivestock());
    $htmlLang = match ($lang) {
        'en' => 'en',
        'zh' => 'zh',
        default => 'ms',
    };
    $langUrl = fn (string $code) => url('/trace/'.$qrCode).'?lang='.$code;
    $produceName = trim((string) ($application?->produceType?->name ?? ''));
    if ($produceName === '') {
        $produceName = $t['productName'];
    }
    $variety = trim((string) ($application?->variety ?? ''));
    $unit = trim((string) ($application?->quantity_unit ?? ''));
    $fmt = fn ($amount) => $amount === null ? '' : number_format((int) $amount);
    $roleOf = function ($node) use ($lang, $t): string {
        if ($node->disposition === LotDisposition::Sold && $node->holder_company_id === null) {
            return TraceCopy::roleLabel($lang, $t['soldToPublic']);
        }
        $label = $node->holder?->partyLabel()
            ?? $node->pendingHolder?->partyLabel()
            ?? $t['soldToPublic'];

        return TraceCopy::roleLabel($lang, $label);
    };
    $nameOf = function ($node) use ($t): string {
        return $node->holder?->displayName()
            ?? $node->pendingHolder?->displayName()
            ?? $t['soldToPublic'];
    };
    $labelRole = fn (string $role): string => $lang === 'bm' ? mb_strtolower($role, 'UTF-8') : $role;
    $withLabel = function (?string $value, string $label): ?string {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        return mb_stripos($value, $label) === 0 ? $value : $label.' '.$value;
    };
    $parentLot = ($journey ?? collect())->count() > 1 ? $journey[$journey->count() - 2] : null;
    $currentQty = $qr?->quantity ?? $application?->quantity;
    $parentQty = $parentLot?->quantity;
    $parentRole = $parentLot ? $roleOf($parentLot) : '';
    $currentRole = $qr ? $roleOf($qr) : '';
    $showSplit = $parentLot !== null && $parentQty !== null && $currentQty !== null;
    $relatedLabel = ($journey ?? collect())->count() === 1
        ? $t['relatedCountOne']
        : str_replace(':count', (string) ($journey ?? collect())->count(), $t['relatedCount']);
    $farmName = trim((string) ($application?->farm_name ?? ''));
    $farmLocation = trim((string) ($application?->farm_location ?? ''));
    $farmOrigin = $farmLocation !== '' ? $farmLocation : $farmName;
    $destination = trim((string) ($application?->destination_country ?? ''));
    $companyName = trim((string) ($application?->company?->displayName() ?? ''));
    $holderName = trim((string) ($qr?->holder?->displayName() ?? $qr?->pendingHolder?->displayName() ?? ''));
    if ($holderName === '' && $qr?->disposition === LotDisposition::Sold) {
        $holderName = $t['soldToPublic'];
    }
    $showHolder = $holderName !== '' && mb_strtolower($holderName) !== mb_strtolower($companyName);
    $address = trim((string) ($application?->company?->address ?? ''));
    $exportDate = TraceCopy::formatDate($application?->export_date, $lang);
    $slaughterDate = $application?->isLivestock() ? TraceCopy::formatDate($application->slaughter_date, $lang) : '';
    $mapUrl = null;
    if ($application?->hasFarmCoordinates()) {
        $mapLat = $application->farm_lat;
        $mapLng = $application->farm_lng;
        $mapUrl = 'https://www.openstreetmap.org/?mlat='.$mapLat.'&mlon='.$mapLng.'#map=14/'.$mapLat.'/'.$mapLng;
    }
    $gradeText = $application?->isLivestock() ? null : $withLabel($application?->grade, $t['gradeLabel']);
    $sizeText = $application?->isLivestock() ? null : $withLabel($application?->size, $t['sizeLabel']);
    $gradeSize = collect([$application?->grade, $application?->size])->filter(fn ($value) => filled($value))->implode(' / ');
    $detailRows = [];
    $pushDetail = function (string $label, mixed $value, bool $mono = false) use (&$detailRows): void {
        $text = trim((string) $value);
        if ($text !== '') {
            $detailRows[] = ['label' => $label, 'value' => $text, 'mono' => $mono];
        }
    };
    $pushDetail($t['fruitType'], $application?->produceType?->name);
    if ($application?->isLivestock()) {
        $pushDetail($t['breed'], $application->variety);
        $pushDetail($t['headCount'], $application->head_count ? $application->head_count.' '.$t['headUnit'] : null);
        $pushDetail($t['weight'], $currentQty !== null ? trim($fmt($currentQty).' '.$unit) : null);
        $pushDetail($t['abattoir'], $application->abattoir_name);
    } elseif ($gradeSize !== '') {
        $pushDetail($t['gradeSize'], $gradeSize);
    }
    $pushDetail($t['company'], $companyName);
    if ($showHolder) {
        $pushDetail($t['currentHolder'], $holderName);
    }
    $pushDetail($t['lot'], $application?->lot_no, true);
    $pushDetail($t['destination'], $destination);
    $qrSrc = $publicUrl !== '' ? url('/api/qr').'?data='.urlencode($publicUrl).'&size=360&v=3' : '';
    $showOrigin = $farmName !== '' || $farmLocation !== '' || $mapUrl || $destination !== '' || $exportDate !== '' || $address !== '' || $slaughterDate !== '';
@endphp
<!DOCTYPE html>
<html lang="{{ $htmlLang }}" class="trace-html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jejak GPL · {{ $active ? $produceName.' · ' : '' }}{{ $qrCode }}</title>
    <x-assets />
</head>
<body class="trace-root">
<div class="trace-page">
    <a class="skip-link" href="#gpl-main">{{ $t['skip'] }}</a>
    <header class="gpl-header">
        <div class="page-shell header-bar">
            <div class="brand-cluster">
                <img class="jata-logo" src="{{ asset('logos/logo-jata-negara.png') }}" alt="{{ $t['agencyMalaysia'] }}" width="61" height="46">
                <span class="brand-divider" aria-hidden="true"></span>
                <img class="gpl-logo" src="{{ asset('logos/logo-fama.png') }}" alt="{{ $t['agencyFama'] }}" width="54" height="54">
                <div class="brand-block">
                    <div class="brand-title">jejak<span class="brand-title-light">GPL</span><span class="brand-dot">.</span></div>
                    <div class="brand-subtitle">{{ $t['knowYourProduce'] }}</div>
                </div>
                <p class="header-description">{{ $t['agencyName'] }}</p>
            </div>
            <nav class="language" aria-label="{{ $t['langLabel'] }}">
                <a href="{{ $langUrl('bm') }}" lang="ms" @if ($lang === 'bm') aria-current="true" @endif>BM</a>
                <a href="{{ $langUrl('en') }}" lang="en" @if ($lang === 'en') aria-current="true" @endif>EN</a>
                <a href="{{ $langUrl('zh') }}" lang="zh" @if ($lang === 'zh') aria-current="true" @endif>中文</a>
            </nav>
        </div>
    </header>

    @if (! $qr || ! $active)
        <main class="page-shell" id="gpl-main">
            <section class="product-panel status-panel">
                <h1>{{ $qr ? ($awaitingAcceptance ? $t['pendingTitle'] : $t['inactiveTitle']) : $t['invalid'] }}</h1>
                @if ($qr)
                    <p>{{ $awaitingAcceptance ? $t['pendingBody'] : $t['inactiveBody'] }}</p>
                    <p class="qr-id">{{ $qr->qr_code }}</p>
                @endif
            </section>
        </main>
    @else
        <main class="page-shell" id="gpl-main">
            <div class="breadcrumb-row">
                <div class="breadcrumb-trail">
                    <x-trace-icon name="scan-line" />
                    <span>{{ $t['breadcrumb'] }}</span>
                    <span class="text-forest-200" aria-hidden="true">/</span>
                    <span class="font-mono text-forest-700 breadcrumb-code">{{ $qr->qr_code }}</span>
                </div>
            </div>

            <article class="hero-grid" id="gpl-overview" aria-labelledby="gpl-product-title">
                <div class="hero-image {{ $heroIllustrated ? 'is-illustration' : '' }}">
                    @if ($heroImage)
                        <img src="{{ $heroImage }}" alt="{{ $heroIllustrated ? $t['conceptAlt'] : $produceName }}" width="1448" height="1086">
                    @else
                        <div class="empty-photo">
                            <x-trace-icon name="leaf" />
                            <p>{{ $t['noImage'] }}</p>
                        </div>
                    @endif
                    <span class="image-origin"><x-trace-icon name="sprout" style="width:14px;height:14px" /> {{ $t['originShort'] }}</span>
                    @if ($heroIllustrated)
                        <span class="image-caption">{{ $t['conceptCaption'] }}</span>
                    @endif
                </div>
                <div class="hero-content">
                    <div class="hero-kicker">
                        <span class="eyebrow">{{ $t['profileTitle'] }}</span>
                        <x-trace-icon name="leaf" class="icon text-forest-500" />
                    </div>
                    <h1 class="product-title" id="gpl-product-title">
                        {{ $produceName }}
                        @if ($variety !== '')
                            <br><span>{{ $variety }}</span>
                        @endif
                    </h1>
                    <div class="pill-row">
                        @if ($gradeText)
                            <span class="pill bg-gold-50 text-gold-600"><x-trace-icon name="sparkles" style="width:12px;height:12px" /> {{ $gradeText }}</span>
                        @endif
                        @if ($sizeText)
                            <span class="pill bg-paper text-gpl-muted">{{ $sizeText }}</span>
                        @endif
                        @if ($application?->isLivestock() && $application->head_count)
                            <span class="pill bg-paper text-gpl-muted">{{ $application->head_count }} {{ $t['headUnit'] }}</span>
                        @endif
                        <span class="pill bg-forest-50 text-forest-700"><x-trace-icon name="globe" style="width:12px;height:12px" /> {{ $t['malaysia'] }}</span>
                    </div>
                    <dl class="hero-meta">
                        @if ($farmOrigin !== '')
                            <div>
                                <dt class="meta-label">{{ $t['farmOrigin'] }}</dt>
                                <dd class="meta-value">{{ $farmOrigin }}</dd>
                            </div>
                        @endif
                        @if ($destination !== '')
                            <div>
                                <dt class="meta-label">{{ $t['exportDestination'] }}</dt>
                                <dd class="meta-value">{{ $destination }}</dd>
                            </div>
                        @endif
                        @if ($showSplit)
                            <div>
                                <dt class="meta-label">{{ str_replace(':role', $labelRole($parentRole), $t['parentLot']) }}</dt>
                                <dd class="meta-value">{{ $fmt($parentQty) }} @if ($unit !== '')<span class="font-medium text-gpl-muted">{{ $unit }}</span>@endif</dd>
                            </div>
                            <div>
                                <dt class="meta-label">{{ $t['thisPortion'] }}</dt>
                                <dd class="meta-value">{{ $fmt($currentQty) }} @if ($unit !== '')<span class="font-medium text-gpl-muted">{{ $unit }}</span>@endif @if ($currentRole !== '')<span class="ml-1.5 text-[9px] font-medium text-gpl-muted">· {{ $currentRole }}</span>@endif</dd>
                            </div>
                        @elseif ($currentQty !== null)
                            <div>
                                <dt class="meta-label">{{ $t['lotQuantity'] }}</dt>
                                <dd class="meta-value">{{ $fmt($currentQty) }} @if ($unit !== '')<span class="font-medium text-gpl-muted">{{ $unit }}</span>@endif</dd>
                            </div>
                        @endif
                    </dl>
                    <div class="hero-actions">
                        <a href="#gpl-journey" class="primary-action">
                            <x-trace-icon name="route" />
                            <span>{{ $t['viewJourney'] }}</span>
                            <x-trace-icon name="arrow-down" style="width:14px;height:14px" />
                        </a>
                        <button class="secondary-action" type="button" data-open="share" aria-haspopup="dialog" aria-label="{{ $t['shareRecord'] }}">
                            <x-trace-icon name="share-2" />
                            <span>{{ $t['share'] }}</span>
                        </button>
                    </div>
                </div>
            </article>

            <nav class="section-nav" aria-label="{{ $t['sectionsLabel'] }}">
                <a href="#gpl-overview" class="active"><x-trace-icon name="layout-grid" style="width:14px;height:14px" /> {{ $t['navSummary'] }}</a>
                <a href="#gpl-journey"><x-trace-icon name="route" style="width:14px;height:14px" /> {{ $t['navJourney'] }}</a>
                <a href="#gpl-details"><x-trace-icon name="list-filter" style="width:14px;height:14px" /> {{ $t['navMore'] }}</a>
            </nav>

            <div class="content-grid">
                <div class="main-stack">
                    <section class="product-panel" id="gpl-journey" aria-labelledby="gpl-journey-title">
                        <div class="section-heading">
                            <div>
                                <h2 id="gpl-journey-title">{{ $t['journeyTitle'] }}</h2>
                                <p>{{ $t['journeySubtitle'] }}</p>
                            </div>
                            <span class="journey-count pill bg-forest-50 text-forest-700"><x-trace-icon name="git-branch" style="width:12px;height:12px" /> {{ $relatedLabel }}</span>
                        </div>
                        <div class="journey-scroll">
                            <ol class="journey-nodes {{ $journey->count() === 1 ? 'is-single' : '' }}" aria-label="{{ $t['journeyLabel'] }}">
                                @foreach ($journey as $index => $node)
                                    @if ($index > 0)
                                        <li class="journey-link" aria-hidden="true"><x-trace-icon name="arrow-right" /></li>
                                    @endif
                                    @php
                                        $nodeCurrent = (string) $node->id === (string) $qr->id;
                                    @endphp
                                    <li class="journey-node {{ $nodeCurrent ? 'current' : '' }}" @if ($nodeCurrent) aria-current="step" @endif>
                                        <p class="node-role {{ $nodeCurrent ? 'text-forest-700' : '' }}">{{ $roleOf($node) }}</p>
                                        <p class="node-name">{{ $nameOf($node) }}</p>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </section>

                    <section class="product-panel" id="gpl-details" aria-labelledby="gpl-details-title">
                        <div class="section-heading is-tight">
                            <div>
                                <h2 id="gpl-details-title">{{ $t['product'] }}</h2>
                                <p>{{ $t['productSubtitle'] }}</p>
                            </div>
                            <x-trace-icon name="package-search" class="icon text-forest-500" />
                        </div>
                        <dl class="detail-grid">
                            @foreach ($detailRows as $row)
                                <div>
                                    <dt>{{ $row['label'] }}</dt>
                                    <dd @if ($row['mono']) class="font-mono" style="font-size:10px" @endif>{{ $row['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    @if (count($nutrition))
                        <details class="product-panel nutrition" id="gpl-nutrition">
                            <summary>
                                <x-trace-icon name="wheat" class="icon text-forest-500" />
                                {{ $t['nutrition'] }}
                                <x-trace-icon name="chevron-down" class="icon nutrition-arrow ml-auto" />
                            </summary>
                            <p class="mt-3 text-[11px] text-gpl-muted">{{ $t['nutritionNote'] }}</p>
                            <table class="nutrition-table">
                                <caption class="sr-only">{{ $t['nutrition'] }}</caption>
                                <tbody>
                                    @foreach ($nutrition as $row)
                                        <tr>
                                            <td>{{ $row['name'] }}</td>
                                            <td>{{ $row['amount'] }}</td>
                                            @if (($row['dailyPercent'] ?? '') !== '')
                                                <td class="text-gpl-muted">{{ $row['dailyPercent'] }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </details>
                    @endif
                </div>

                <aside class="side-stack" aria-label="{{ $t['originAside'] }}">
                    @if ($showOrigin)
                        <section class="product-panel origin-panel" aria-labelledby="gpl-origin-title">
                            <div class="origin-heading">
                                <h2 id="gpl-origin-title">{{ $application?->isDomestic() ? $t['originOnly'] : $t['originTitle'] }}</h2>
                                <x-trace-icon name="sprout" class="icon text-forest-500" style="width:22px;height:22px" />
                            </div>
                            @if ($farmName !== '' || $farmLocation !== '')
                                <div class="origin-item">
                                    <x-trace-icon name="trees" />
                                    <div>
                                        <p class="origin-label">{{ $t['farm'] }}</p>
                                        <p class="origin-value">
                                            {{ $farmName !== '' ? $farmName : $farmLocation }}
                                            @if ($farmLocation !== '' && $farmLocation !== $farmName)
                                                <br><span class="font-medium text-gpl-muted">{{ $farmLocation }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if ($mapUrl)
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="map-link">
                                    {{ $t['viewFarm'] }}
                                    <x-trace-icon name="arrow-up-right" style="width:12px;height:12px" />
                                </a>
                            @endif
                            @if ($destination !== '' || $exportDate !== '' || $address !== '' || $slaughterDate !== '')
                                <div class="origin-section">
                                    @if ($destination !== '')
                                        <div class="route-line">
                                            <span>{{ $t['malaysia'] }}</span>
                                            <span class="flex-1 h-px bg-forest-200"></span>
                                            <x-trace-icon name="plane" style="width:15px;height:15px" />
                                            <span class="flex-1 h-px bg-forest-200"></span>
                                            <span>{{ $destination }}</span>
                                        </div>
                                    @endif
                                    @if ($exportDate !== '')
                                        <div class="origin-item">
                                            <x-trace-icon name="calendar-days" />
                                            <div>
                                                <p class="origin-label">{{ $t['exportDate'] }}</p>
                                                <p class="origin-value">{{ $exportDate }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($slaughterDate !== '')
                                        <div class="origin-item">
                                            <x-trace-icon name="calendar-days" />
                                            <div>
                                                <p class="origin-label">{{ $t['slaughterDate'] }}</p>
                                                <p class="origin-value">{{ $slaughterDate }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($address !== '')
                                        <div class="origin-item">
                                            <x-trace-icon name="building-2" />
                                            <div>
                                                <p class="origin-label">{{ $t['exporterAddress'] }}</p>
                                                <p class="origin-value">{!! nl2br(e($address)) !!}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </section>
                    @endif

                    <details class="product-panel qr-details" open>
                        <summary>
                            <span class="flex items-center gap-2"><x-trace-icon name="qr-code" class="icon text-forest-500" /> {{ $t['qrTitle'] }}</span>
                            <x-trace-icon name="chevron-down" class="icon qr-chevron" />
                        </summary>
                        <div class="qr-body">
                            <img src="{{ $qrSrc }}" class="qr-image" alt="{{ $t['qrTitle'] }} {{ $qr->qr_code }}" width="143" height="143">
                            <p class="qr-id">{{ $qr->qr_code }}</p>
                            <p class="qr-copy mt-2 text-[10px] text-gpl-muted">{{ $t['qrHint'] }}</p>
                            <button class="secondary-action w-full mt-4" data-open="share" type="button" aria-haspopup="dialog">
                                <x-trace-icon name="share-2" style="width:14px;height:14px" />
                                {{ $t['shareRecord'] }}
                            </button>
                        </div>
                    </details>

                    <section class="help-panel">
                        <x-trace-icon name="circle-help" class="icon text-forest-500 mt-0.5" />
                        <div>
                            <p class="text-[11px] font-bold text-forest-800">{{ $t['helpTitle'] }}</p>
                            <a href="https://www.fama.gov.my" target="_blank" rel="noopener noreferrer" class="text-[10px] text-gpl-muted inline-flex items-center gap-1.5 mt-1 underline underline-offset-4 decoration-line">
                                {{ $t['helpLink'] }}
                                <x-trace-icon name="arrow-up-right" style="width:11px;height:11px" />
                            </a>
                            <p class="text-[10px] text-gpl-muted mt-1">www.fama.gov.my</p>
                        </div>
                    </section>
                </aside>
            </div>
        </main>

        <dialog class="gpl-dialog" id="gpl-share-dialog" aria-labelledby="gpl-share-title">
            <div class="dialog-inner">
                <div class="dialog-top">
                    <span class="eyebrow">{{ $t['shareEyebrow'] }}</span>
                    <button type="button" class="dialog-close" data-close aria-label="{{ $t['close'] }}"><x-trace-icon name="x" /></button>
                </div>
                <h2 class="dialog-title" id="gpl-share-title">{{ $t['shareTitle'] }}</h2>
                <p class="dialog-text">{{ str_replace(':code', $qr->qr_code, $t['shareBody']) }}</p>
                <img src="{{ $qrSrc }}" alt="{{ $t['qrTitle'] }} {{ $qr->qr_code }}" width="180" height="180" class="share-qr">
                <p class="font-mono text-center text-[12px] text-forest-700">{{ $qr->qr_code }}</p>
                <div class="dialog-actions">
                    <button type="button" class="primary-action flex-1" id="gpl-copy-link" data-url="{{ $publicUrl }}" data-copied="{{ $t['copied'] }}">
                        <x-trace-icon name="copy" />
                        {{ $t['copyLink'] }}
                    </button>
                    <a href="{{ $publicUrl }}" class="secondary-action" target="_blank" rel="noopener noreferrer">{{ $t['openRecord'] }} <x-trace-icon name="arrow-up-right" /></a>
                </div>
                <p id="gpl-copy-status" role="status" class="text-[11px] text-forest-700 mt-3"></p>
            </div>
        </dialog>
    @endif

    <footer class="gpl-footer">
        <div class="page-shell footer-grid">
            <div class="footer-brand">
                <img class="footer-logo" src="{{ asset('logos/logo-fama.png') }}" alt="{{ $t['agencyFama'] }}" width="36" height="36">
                <div>
                    <p class="text-[11px] font-bold text-forest-800">{{ $t['agencyName'] }}</p>
                    <p class="text-[9px] text-gpl-muted mt-0.5">{{ $t['ministry'] }}</p>
                </div>
            </div>
            <div class="text-[9px] text-gpl-muted">
                <p>&copy; {{ date('Y') }} FAMA. {{ $t['rights'] }}</p>
            </div>
        </div>
    </footer>
</div>
@if ($active)
<script>
(() => {
    const root = document.querySelector('.trace-page');
    if (!root) return;
    const openDialog = (dialog) => { if (dialog && typeof dialog.showModal === 'function' && !dialog.open) dialog.showModal(); };
    root.querySelectorAll('[data-open="share"]').forEach((button) => {
        button.addEventListener('click', () => openDialog(root.querySelector('#gpl-share-dialog')));
    });
    root.querySelectorAll('[data-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('dialog')?.close());
    });
    root.querySelectorAll('dialog').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target !== dialog) return;
            const rect = dialog.getBoundingClientRect();
            if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close();
        });
    });
    const copyButton = root.querySelector('#gpl-copy-link');
    copyButton?.addEventListener('click', async () => {
        const status = root.querySelector('#gpl-copy-status');
        const url = copyButton.dataset.url || '';
        try {
            await navigator.clipboard.writeText(url);
            status.textContent = copyButton.dataset.copied || '';
        } catch (error) {
            status.textContent = url;
        }
    });
    const nav = root.querySelector('.section-nav');
    if (nav) {
        const links = [...nav.querySelectorAll('a')];
        let lock = false;
        const activate = (link) => links.forEach((item) => item.classList.toggle('active', item === link));
        links.forEach((link) => link.addEventListener('click', () => {
            lock = true;
            activate(link);
            window.setTimeout(() => { lock = false; }, 700);
        }));
        const sections = links.map((link) => document.getElementById((link.getAttribute('href') || '').slice(1))).filter(Boolean);
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                if (lock) return;
                const visible = entries.filter((entry) => entry.isIntersecting).sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
                if (!visible) return;
                const match = links.find((link) => link.getAttribute('href') === '#' + visible.target.id);
                if (match) activate(match);
            }, { rootMargin: '-20% 0px -55% 0px', threshold: [0.15, 0.4] });
            sections.forEach((section) => observer.observe(section));
        }
    }
})();
</script>
@endif
</body>
</html>
