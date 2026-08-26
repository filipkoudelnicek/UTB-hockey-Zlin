<header
    x-data="{ mobileOpen: false, dropdown: null }"
    class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100 shadow-sm"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ $homepageUrl }}" class="flex items-center gap-2 font-bold text-lg text-gray-900 hover:text-violet-600 transition-colors shrink-0">
                {{ config('app.name') }}
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-1">
                @foreach ($navItems as $item)
                    @if (!empty($item['children']))
                        {{-- Hover dropdown --}}
                        <div class="group relative">
                            <a
                                href="{{ $item['url'] }}"
                                target="{{ $item['target'] ?? '_self' }}"
                                class="flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                            >
                                {{ $item['label'] }}
                                <svg class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                                </svg>
                            </a>
                            {{-- Invisible bridge to prevent dropdown from closing --}}
                            <div class="absolute left-0 top-full h-2 w-full"></div>
                            <div class="absolute left-0 top-full mt-2 w-52 rounded-xl bg-white shadow-xl ring-1 ring-gray-200 py-1.5 z-50
                                        invisible opacity-0 translate-y-1
                                        group-hover:visible group-hover:opacity-100 group-hover:translate-y-0
                                        transition-all duration-200 ease-out">
                                @foreach ($item['children'] as $child)
                                    <a
                                        href="{{ $child['url'] }}"
                                        target="{{ $child['target'] ?? '_self' }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors"
                                    >{{ $child['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ $item['url'] }}"
                            target="{{ $item['target'] ?? '_self' }}"
                            class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                        >{{ $item['label'] }}</a>
                    @endif
                @endforeach
            </nav>

            {{-- Right side: language switcher + mobile toggle --}}
            <div class="flex items-center gap-3">
                <livewire:language-switcher />

                {{-- Mobile hamburger --}}
                <button
                    @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                    aria-label="Otevřít menu"
                >
                    <svg x-show="!mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden border-t border-gray-100 bg-white"
        style="display:none"
    >
        <nav class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-1">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['url'] }}"
                    target="{{ $item['target'] ?? '_self' }}"
                    @click="mobileOpen = false"
                    class="px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors"
                >{{ $item['label'] }}</a>
                @if (!empty($item['children']))
                    @foreach ($item['children'] as $child)
                        <a
                            href="{{ $child['url'] }}"
                            target="{{ $child['target'] ?? '_self' }}"
                            @click="mobileOpen = false"
                            class="pl-7 pr-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                        >{{ $child['label'] }}</a>
                    @endforeach
                @endif
            @endforeach
        </nav>
    </div>
</header>