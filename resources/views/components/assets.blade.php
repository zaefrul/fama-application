<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=source-sans-3:400,500,600,700" rel="stylesheet">
@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <style>
        :root {
            --brand-primary: #0f6b4c;
            --brand-primary-hover: #0c5840;
            --brand-primary-foreground: #ffffff;
            --surface: #ffffff;
            --surface-muted: #f3f5f4;
            --surface-dark: #0d3d2e;
            --border: #d7ddd9;
            --text-primary: #14241d;
            --text-secondary: #5b6b63;
            --status-success: #1a7a4c;
            --status-warning: #c99212;
            --status-danger: #c0392b;
            --status-info: #2b6cb0;
            --status-neutral: #64748b;
            --chart-1: #2b6cb0;
            --chart-2: #c99212;
            --chart-3: #7c3aed;
            --chart-4: #0e7490;
            --chart-5: #db2777;
            --chart-6: #ea580c;
            --chart-7: #1a7a4c;
            --chart-8: #4338ca;
            --chart-9: #c0392b;
            --chart-10: #64748b;
        }
        body { background: var(--surface-muted); color: var(--text-primary); font-family: "Source Sans 3", "Segoe UI", sans-serif; margin: 0; }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; height: auto; }
        .trace-fama-logo { width: 56px; height: 56px; max-width: 56px; max-height: 56px; object-fit: contain; }
        .trace-header-mark { width: 44px; height: 44px; max-width: 44px; max-height: 44px; object-fit: contain; }
        .farm-map { display: block; width: 100%; height: 192px; border: 0; }
        .trace-produce-photo { width: 96px; height: 96px; max-width: 96px; max-height: 96px; border-radius: 9999px; object-fit: cover; }
        .trace-produce-hero { width: 100%; height: 200px; max-height: 200px; object-fit: cover; display: block; }
        .trace-produce-portrait { position: absolute; inset: 0; width: 100%; height: 100%; max-width: none; max-height: none; object-fit: cover; display: block; }
        .trace-gov-logo { display: block; width: auto; height: 64px; max-width: 100%; margin: 0 auto; object-fit: contain; }
        .brand-logo-header { display: block; width: 36px; height: 36px; max-width: 36px; max-height: 36px; object-fit: contain; }
        .brand-logo-sidebar { display: block; width: auto; height: 48px; max-width: 180px; max-height: 48px; margin: 0 auto; object-fit: contain; }
        .brand-logo-auth { display: block; width: auto; height: 96px; max-width: min(280px, 70vw); max-height: 96px; margin: 0 auto; object-fit: contain; }
        .gallery-hero { display: block; width: 100%; height: 176px; max-height: 176px; object-fit: cover; }
        .company-logo { display: block; width: 64px; height: 64px; max-width: 64px; max-height: 64px; object-fit: contain; }
        .trace-pamphlet { background: #fffdf8; border: 2px solid #d4b45a; }
        .produce-type-list { position: absolute; left: 0; right: 0; top: 100%; z-index: 30; margin-top: 0.25rem; max-height: 15rem; overflow-y: auto; border: 1px solid var(--border); border-radius: 0.75rem; background: var(--surface); padding: 0.25rem 0; box-shadow: 0 8px 20px rgba(20, 36, 29, 0.08); }
        .produce-type-option { cursor: pointer; padding: 0.5rem 0.75rem; font-size: 0.875rem; color: var(--text-primary); }
        .produce-type-option:hover, .produce-type-option.is-active { background: var(--surface-muted); }
        .produce-type-option.is-active { font-weight: 600; }
        .produce-type-empty { padding: 0.5rem 0.75rem; font-size: 0.875rem; color: var(--text-secondary); }
    </style>
@endif
