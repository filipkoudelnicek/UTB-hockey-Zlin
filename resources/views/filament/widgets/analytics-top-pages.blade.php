<x-filament-widgets::widget>
    <x-filament::section heading="Nejnavštěvovanější URL">
        <div class="mb-4 flex flex-wrap gap-x-6 gap-y-2 text-sm">
            @foreach (['pages' => 'Stránky', 'articles' => 'Články', 'players' => 'Hráči'] as $key => $label)
                <label class="flex items-center gap-2">
                    <input type="checkbox" value="{{ $key }}" wire:model.live="types"
                        class="fi-checkbox-input rounded border-gray-300 dark:border-white/20" />
                    {{ $label }}
                </label>
            @endforeach
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Hledat v URL…"
                class="ms-auto w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm sm:w-64 dark:border-white/20" />
        </div>

        @if ($pages->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">Pro zvolený filtr nejsou žádná data.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-start text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="py-2 pe-4 text-start font-semibold">#</th>
                            @foreach (['path' => ['URL', 'text-start pe-4'], 'views' => ['Zobrazení', 'text-end pe-4'], 'visitors' => ['Unikátní návštěvníci', 'text-end']] as $col => [$label, $cls])
                                <th class="py-2 {{ $cls }} font-semibold">
                                    <button type="button" wire:click="sortBy('{{ $col }}')" class="inline-flex items-center gap-1">
                                        {{ $label }}
                                        @if ($sortColumn === $col)
                                            <span>{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </button>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr class="border-b border-gray-100 dark:border-white/5">
                                <td class="py-2 pe-4">{{ $pages->firstItem() + $loop->index }}</td>
                                <td class="py-2 pe-4 break-all">{{ $page->path }}</td>
                                <td class="py-2 pe-4 text-end">{{ number_format($page->views, 0, ',', ' ') }}</td>
                                <td class="py-2 text-end">{{ number_format($page->visitors, 0, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm">
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 dark:text-gray-400">Na stránce</span>
                    <select wire:model.live="perPage" class="rounded-lg border border-gray-300 bg-transparent py-1 text-sm dark:border-white/20 dark:bg-gray-900">
                        @foreach ([20, 50, 100] as $n)
                            <option value="{{ $n }}">{{ $n }}</option>
                        @endforeach
                    </select>
                    <span class="text-gray-500 dark:text-gray-400">
                        {{ $pages->firstItem() }}–{{ $pages->lastItem() }} z {{ number_format($pages->total(), 0, ',', ' ') }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="previousPage" @disabled($pages->onFirstPage())
                        class="rounded-lg border border-gray-300 px-3 py-1 disabled:opacity-40 dark:border-white/20">Předchozí</button>
                    <span>{{ $pages->currentPage() }} / {{ $pages->lastPage() }}</span>
                    <button type="button" wire:click="nextPage" @disabled(! $pages->hasMorePages())
                        class="rounded-lg border border-gray-300 px-3 py-1 disabled:opacity-40 dark:border-white/20">Další</button>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

