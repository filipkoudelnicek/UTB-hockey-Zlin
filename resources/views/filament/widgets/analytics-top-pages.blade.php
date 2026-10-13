<x-filament-widgets::widget>
    <x-filament::section heading="Nejnavštěvovanější URL">
        @if ($pages->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">V tomto období zatím nejsou žádná data.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-start text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="py-2 pe-4 text-start font-semibold">#</th>
                            <th class="py-2 pe-4 text-start font-semibold">URL</th>
                            <th class="py-2 pe-4 text-end font-semibold">Zobrazení</th>
                            <th class="py-2 text-end font-semibold">Unikátní návštěvníci</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr class="border-b border-gray-100 dark:border-white/5">
                                <td class="py-2 pe-4">{{ $loop->iteration }}</td>
                                <td class="py-2 pe-4 break-all">{{ $page->path }}</td>
                                <td class="py-2 pe-4 text-end">{{ number_format($page->views, 0, ',', ' ') }}</td>
                                <td class="py-2 text-end">{{ number_format($page->visitors, 0, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
