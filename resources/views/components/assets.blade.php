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
        }
        body { background: var(--surface-muted); color: var(--text-primary); font-family: "Source Sans 3", "Segoe UI", sans-serif; margin: 0; }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; }
    </style>
@endif
