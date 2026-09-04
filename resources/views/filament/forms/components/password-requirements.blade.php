<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900" aria-live="polite">
    <p class="mb-3 text-sm font-semibold text-gray-950 dark:text-white">Heslo musí splňovat:</p>

    <ul class="space-y-2 text-sm">
        @foreach (\App\Support\PasswordRequirements::checks($password, $confirmation) as $requirement)
            <li @class([
                'flex items-center gap-2 transition-colors duration-200',
                'text-success-600 dark:text-success-400' => $requirement['valid'],
                'text-danger-600 dark:text-danger-400' => ! $requirement['valid'],
            ])>
                <span @class([
                    'flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-black',
                    'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-300' => $requirement['valid'],
                    'bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-300' => ! $requirement['valid'],
                ]) aria-hidden="true">{{ $requirement['valid'] ? '✓' : '×' }}</span>
                <span>{{ $requirement['label'] }}</span>
                <span class="sr-only">{{ $requirement['valid'] ? 'splněno' : 'nesplněno' }}</span>
            </li>
        @endforeach
    </ul>
</div>
