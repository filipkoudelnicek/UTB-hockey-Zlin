<x-filament-panels::page>
    <x-filament::modal
        id="maintenance-command-result"
        :heading="$commandResultTitle"
        width="4xl"
    >
        <pre class="max-h-[60vh] overflow-auto whitespace-pre-wrap break-words rounded-lg bg-gray-50 p-4 text-sm text-gray-950 dark:bg-gray-950 dark:text-gray-100">{{ $commandResultOutput !== '' ? $commandResultOutput : 'Příkaz nevrátil žádné další informace.' }}</pre>
    </x-filament::modal>

    <x-filament-actions::modals />
</x-filament-panels::page>
