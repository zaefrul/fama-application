@props(['readonly' => false])
<input {{ $attributes->merge(['class' => 'w-full min-w-0 max-w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-brand '.($readonly ? 'bg-surface-muted text-muted' : '')])->merge($readonly ? ['readonly' => true] : []) }}>
