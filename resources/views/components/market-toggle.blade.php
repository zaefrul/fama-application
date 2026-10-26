@props(['readOnly' => false])
@unless ($readOnly)
    <script>
    (() => {
        const form = document.currentScript?.closest('form');
        if (!form) return;
        const section = form.querySelector('[data-export-section]');
        const apply = () => {
            const domestic = form.querySelector('input[name="market"]:checked')?.value === 'DOMESTIC';
            if (!section) return;
            section.hidden = domestic;
            section.querySelectorAll('input, textarea, select').forEach((field) => {
                field.disabled = domestic;
                if (field.hasAttribute('data-export-required')) field.required = !domestic;
            });
        };
        form.querySelectorAll('input[name="market"]').forEach((field) => field.addEventListener('change', apply));
        apply();
    })();
    </script>
@endunless
