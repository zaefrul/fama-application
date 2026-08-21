<x-layouts.fama title="Jejak Audit">
    <div class="space-y-4">
        <x-page-title title="Jejak Audit" />
        <ul class="space-y-2">
            @foreach ($logs as $log)
                <li>
                    <x-card>
                        <p class="text-sm font-semibold">{{ $log->action }}</p>
                        <p class="text-xs text-muted">{{ $log->actor_role }} · {{ $log->object_type }} · {{ $log->created_at?->locale('ms')->translatedFormat('d/m/Y H:i') }}</p>
                        @if ($log->remarks)<p class="text-sm">{{ $log->remarks }}</p>@endif
                    </x-card>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts.fama>
