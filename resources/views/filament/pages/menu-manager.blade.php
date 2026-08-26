<x-filament-panels::page>
    {{-- Top bar: Language + Navigation selectors --}}
    <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-4 shadow-sm">
        <div class="flex flex-wrap items-center gap-4">

            @if (count($this->languages) > 1)
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 shrink-0">Jazyk</label>
                    <select wire:model.live="locale"
                        class="rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        @foreach ($this->languages as $loc => $langName)
                            <option value="{{ $loc }}">{{ $langName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="h-6 w-px bg-gray-200 dark:bg-white/10"></div>
            @endif

            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 shrink-0">
                    Menu<span class="text-danger-500 ml-0.5">*</span>
                </label>
                @if (count($this->navigations) > 0)
                    <select wire:model.live="navigationId"
                        class="rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        @foreach ($this->navigations as $navId => $navName)
                            <option value="{{ $navId }}">{{ $navName }}</option>
                        @endforeach
                    </select>
                @else
                    <span class="text-sm text-gray-400 dark:text-gray-500 italic">
                        Pro tento jazyk zatim neexistuje zadne menu — vytvorte ho tlacitkem „Nove menu".
                    </span>
                @endif
            </div>

            @if ($this->isDirty())
                <span class="ml-auto inline-flex items-center gap-1.5 rounded-full bg-warning-50 dark:bg-warning-400/10 px-3 py-1.5 text-xs font-medium text-warning-600 dark:text-warning-400 ring-1 ring-warning-300/50 dark:ring-warning-400/20">
                    <span class="h-1.5 w-1.5 rounded-full bg-warning-400 shrink-0"></span>
                    Neuložené změny
                </span>
            @endif

        </div>
    </div>

    @if (! $navigationId)
        <div class="rounded-xl border border-dashed border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 flex flex-col items-center justify-center py-20 text-center px-6">
            <x-filament::icon icon="heroicon-o-bars-3" class="h-10 w-10 text-gray-300 dark:text-gray-600 mb-3" />
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Žádné menu není k dispozici.</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 mb-4">Klikněte na „Nové menu" v pravém horním rohu a vytvořte první menu.</p>
        </div>
    @else

    <div class="flex gap-4 items-start" wire:loading.class="opacity-60 pointer-events-none" wire:target="addPage,addArticle,addCustomLink,moveUp,moveDown,indent,outdent,deleteItem,saveEdit,save,clearMenu,cancelMenu">

        {{-- LEFT COLUMN --}}
        <div style="width: 320px; flex: none;" class="space-y-3">

            {{-- Stranky --}}
            @php $pageList = $this->pages; @endphp
            <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
                <button wire:click="togglePanel('pages')" type="button"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <span class="flex items-center gap-2">
                        Stránky
                        @if (count($pageList) > 0)
                            <span class="rounded-full bg-gray-100 dark:bg-white/10 px-1.5 py-0.5 text-[10px] font-semibold text-gray-500 dark:text-gray-400">{{ count($pageList) }}</span>
                        @endif
                    </span>
                    <x-filament::icon icon="heroicon-o-chevron-down"
                        class="h-4 w-4 text-gray-400 transition-transform duration-200 {{ in_array('pages', $openPanels) ? 'rotate-180' : '' }}" />
                </button>

                @if (in_array('pages', $openPanels))
                    @if (count($pageList) === 0)
                        <div class="border-t border-gray-100 dark:border-white/10 px-4 py-4 text-xs text-gray-400 dark:text-gray-500 italic">
                            Pro tento jazyk nebyly nalezeny žádné aktivní stránky.
                        </div>
                    @else
                        <div class="border-t border-gray-100 dark:border-white/10 divide-y divide-gray-100 dark:divide-white/5 max-h-72 overflow-y-auto">
                            @foreach ($pageList as $page)
                                <button wire:click="addPage({{ $page['id'] }})" type="button"
                                    wire:loading.attr="disabled" wire:target="addPage({{ $page['id'] }})"
                                    class="w-full flex items-center justify-between px-4 py-2 text-left hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group disabled:opacity-50">
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-200 truncate">{{ $page['title'] }}</span>
                                        <span class="block text-xs text-gray-400 dark:text-gray-500 truncate">/{{ ltrim($page['full_slug'] ?? $page['slug'] ?? '', '/') }}</span>
                                    </span>
                                    <x-filament::icon icon="heroicon-o-plus" class="h-4 w-4 text-gray-400 group-hover:text-primary-500 shrink-0 ml-2" />
                                </button>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

            {{-- Vlastni odkaz --}}
            <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
                <button wire:click="togglePanel('custom')" type="button"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <span>Vlastní odkaz</span>
                    <x-filament::icon icon="heroicon-o-chevron-down"
                        class="h-4 w-4 text-gray-400 transition-transform duration-200 {{ in_array('custom', $openPanels) ? 'rotate-180' : '' }}" />
                </button>

                @if (in_array('custom', $openPanels))
                    <div class="border-t border-gray-100 dark:border-white/10 px-4 pb-4 pt-3 space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Název položky</label>
                            <input type="text" wire:model="customLabel" placeholder="Např. O nás"
                                class="block w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">URL</label>
                            <input type="text" wire:model="customUrl" placeholder="https:// nebo /cesta"
                                class="block w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Otevřít v</label>
                            <select wire:model="customTarget"
                                class="block w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                <option value="_self">Stejném okně</option>
                                <option value="_blank">Novém okně / záložce</option>
                            </select>
                        </div>
                        <button wire:click="addCustomLink" type="button"
                            wire:loading.attr="disabled" wire:target="addCustomLink"
                            class="w-full rounded-lg bg-primary-600 hover:bg-primary-700 active:bg-primary-800 px-3 py-2 text-sm font-medium text-white shadow-sm transition-colors flex items-center justify-center gap-1.5 disabled:opacity-60">
                            <x-filament::icon icon="heroicon-o-plus" class="h-4 w-4" />
                            Přidat do menu
                        </button>
                    </div>
                @endif
            </div>

            {{-- Clanky --}}
            @php $articleList = $this->articles; @endphp
            <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
                <button wire:click="togglePanel('articles')" type="button"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <span class="flex items-center gap-2">
                        Články
                        @if (count($articleList) > 0)
                            <span class="rounded-full bg-gray-100 dark:bg-white/10 px-1.5 py-0.5 text-[10px] font-semibold text-gray-500 dark:text-gray-400">{{ count($articleList) }}</span>
                        @endif
                    </span>
                    <x-filament::icon icon="heroicon-o-chevron-down"
                        class="h-4 w-4 text-gray-400 transition-transform duration-200 {{ in_array('articles', $openPanels) ? 'rotate-180' : '' }}" />
                </button>

                @if (in_array('articles', $openPanels))
                    @if (count($articleList) === 0)
                        <div class="border-t border-gray-100 dark:border-white/10 px-4 py-4 text-xs text-gray-400 dark:text-gray-500 italic">
                            Pro tento jazyk nebyly nalezeny žádné aktivní články.
                        </div>
                    @else
                        <div class="border-t border-gray-100 dark:border-white/10 divide-y divide-gray-100 dark:divide-white/5 max-h-72 overflow-y-auto">
                            @foreach ($articleList as $article)
                                <button wire:click="addArticle({{ $article['id'] }})" type="button"
                                    wire:loading.attr="disabled" wire:target="addArticle({{ $article['id'] }})"
                                    class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors text-left group disabled:opacity-50">
                                    <span class="truncate">{{ $article['title'] }}</span>
                                    <x-filament::icon icon="heroicon-o-plus" class="h-4 w-4 text-gray-400 group-hover:text-primary-500 shrink-0 ml-2" />
                                </button>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

        </div>
        {{-- end LEFT COLUMN --}}

        {{-- RIGHT COLUMN: Menu tree --}}
        <div class="flex-1 min-w-0 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

            @php $flat = $this->flatItems; @endphp

            @if (empty($flat))
                <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                    <x-filament::icon icon="heroicon-o-bars-3" class="h-10 w-10 text-gray-300 dark:text-gray-600 mb-3" />
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Toto menu nemá žádné položky.</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Přidejte položky pomocí panelů vlevo.</p>
                </div>
            @else
                <div class="px-4 py-2.5 border-b border-gray-100 dark:border-white/10 bg-gray-100/60 dark:bg-white/5">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Kliknutím na <x-filament::icon icon="heroicon-o-pencil-square" class="h-3.5 w-3.5 inline" /> upravíte položku.
                        Šipky slouží k přesouvání, <span class="font-mono">← →</span> mění úroveň odsazení.
                    </p>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-white/5 pt-0.5">
                    @foreach ($flat as $row)
                        @php
                            $item      = $row['item'];
                            $path      = $row['path'];
                            $depth     = $row['depth'];
                            $isEditing = $editingPath === $path;

                            $typeMeta = match ($item['type'] ?? 'custom') {
                                'page'    => ['label' => 'stránka',  'cls' => 'text-blue-600 bg-blue-50 dark:bg-blue-500/10 dark:text-blue-400'],
                                'article' => ['label' => 'článek',   'cls' => 'text-green-600 bg-green-50 dark:bg-green-500/10 dark:text-green-400'],
                                default   => ['label' => 'vlastní',  'cls' => 'text-gray-500 bg-gray-100 dark:bg-white/10 dark:text-gray-400'],
                            };

                            $rowBg = $isEditing ? 'bg-primary-50 dark:bg-primary-500/10' : 'hover:bg-gray-50 dark:hover:bg-gray-800/40';
                        @endphp

                        <div @class([
                            'group flex items-start gap-2 pr-4 py-3 transition-colors',
                            'pl-4'  => $depth === 0,
                            'pl-12' => $depth === 1,
                            'pl-20' => $depth >= 2,
                            $rowBg,
                        ])>

                            {{-- Depth badge --}}
                            <div class="flex items-center gap-1 shrink-0 pt-0.5">
                                <span class="inline-flex items-center justify-center w-6 h-5 rounded text-[10px] font-bold
                                    {{ $depth === 0 ? 'bg-gray-200 dark:bg-white/10 text-gray-500 dark:text-gray-400' : 'bg-primary-100 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400' }}">
                                    T{{ $depth + 1 }}
                                </span>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                @if ($isEditing)
                                    <div class="my-2 rounded-xl border border-primary-200 dark:border-primary-500/30 bg-gray-100 dark:bg-gray-800 p-4 space-y-3">
                                        <div class="flex gap-3">
                                            <div class="flex-1 min-w-0">
                                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Název</label>
                                                <input type="text" wire:model="editLabel" placeholder="Název položky"
                                                    class="block w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
                                            </div>
                                            <div class="w-36 shrink-0">
                                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Otevřít v</label>
                                                <select wire:model="editTarget"
                                                    class="block w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 px-2 py-2 text-sm text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                                    <option value="_self">Stejném okně</option>
                                                    <option value="_blank">Novém okně</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">URL</label>
                                            <input type="text" wire:model="editUrl" placeholder="https:// nebo /cesta"
                                                class="block w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
                                        </div>
                                        <div class="flex items-center gap-2 pt-1">
                                            <button wire:click="saveEdit" type="button"
                                                class="rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors">
                                                Uložit
                                            </button>
                                            <button wire:click="cancelEdit" type="button"
                                                class="rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-white/5 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors">
                                                Zrušit
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $item['label'] }}</span>
                                        @if (! empty($item['children']))
                                            <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0">({{ count($item['children']) }})</span>
                                        @endif
                                        <span class="shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium {{ $typeMeta['cls'] }}">
                                            {{ $typeMeta['label'] }}
                                        </span>
                                    </div>
                                    @if (! empty($item['url']))
                                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">
                                            {{ $item['url'] }}{{ ($item['target'] ?? '_self') === '_blank' ? ' ↗' : '' }}
                                        </p>
                                    @endif
                                @endif
                            </div>

                            {{-- Action buttons --}}
                            @if (! $isEditing)
                                <div class="shrink-0 flex items-center gap-0.5 pt-0.5">
                                    <button wire:click="moveUp('{{ $path }}')" type="button"
                                        @disabled($row['isFirst'])
                                        wire:loading.attr="disabled" wire:target="moveUp('{{ $path }}')"
                                        title="Nahoru"
                                        class="rounded p-1 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors disabled:opacity-25 disabled:cursor-not-allowed">
                                        <x-filament::icon icon="heroicon-o-arrow-up" class="h-3.5 w-3.5" />
                                    </button>
                                    <button wire:click="moveDown('{{ $path }}')" type="button"
                                        @disabled($row['isLast'])
                                        wire:loading.attr="disabled" wire:target="moveDown('{{ $path }}')"
                                        title="Dolů"
                                        class="rounded p-1 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors disabled:opacity-25 disabled:cursor-not-allowed">
                                        <x-filament::icon icon="heroicon-o-arrow-down" class="h-3.5 w-3.5" />
                                    </button>
                                    <button wire:click="outdent('{{ $path }}')" type="button"
                                        @disabled(! $row['canOutdent'])
                                        wire:loading.attr="disabled" wire:target="outdent('{{ $path }}')"
                                        title="Odsadit vlevo"
                                        class="rounded p-1 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors disabled:opacity-25 disabled:cursor-not-allowed">
                                        <x-filament::icon icon="heroicon-o-arrow-left" class="h-3.5 w-3.5" />
                                    </button>
                                    <button wire:click="indent('{{ $path }}')" type="button"
                                        @disabled(! $row['canIndent'])
                                        wire:loading.attr="disabled" wire:target="indent('{{ $path }}')"
                                        title="Zanořit"
                                        class="rounded p-1 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors disabled:opacity-25 disabled:cursor-not-allowed">
                                        <x-filament::icon icon="heroicon-o-arrow-right" class="h-3.5 w-3.5" />
                                    </button>
                                    <div class="w-px h-4 bg-gray-200 dark:bg-white/10 mx-0.5"></div>
                                    <button wire:click="startEdit('{{ $path }}')" type="button"
                                        title="Upravit"
                                        class="rounded p-1 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition-colors">
                                        <x-filament::icon icon="heroicon-o-pencil-square" class="h-3.5 w-3.5" />
                                    </button>
                                    <button wire:click="deleteItem('{{ $path }}')" type="button"
                                        wire:confirm="Opravdu smazat tuto položku?"
                                        title="Smazat"
                                        class="rounded p-1 text-gray-400 hover:text-danger-600 dark:hover:text-danger-400 hover:bg-danger-50 dark:hover:bg-danger-500/10 transition-colors">
                                        <x-filament::icon icon="heroicon-o-trash" class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        {{-- end RIGHT COLUMN --}}

    </div>
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>
