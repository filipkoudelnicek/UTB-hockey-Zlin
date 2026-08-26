<div
    x-data="{ open: false }"
    @click.outside="open = false"
    class="relative"
>
    <button
        type="button"
        @click="open = !open"
        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors border border-gray-200"
        :aria-expanded="open"
    >
        <span>{{ $actual->name }}</span>
        <svg class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
        </svg>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 top-full mt-1 min-w-[8rem] rounded-xl bg-white shadow-lg ring-1 ring-gray-200 py-1 z-50"
        style="display:none"
    >
        @foreach ($langs as $item)
            <a
                href="#"
                wire:click.prevent="changeLang('{{ $item->locale }}', '{{ request()->getRequestUri() }}')"
                lang="{{ $item->locale }}"
                class="flex items-center gap-2 px-4 py-2 text-sm transition-colors
                    {{ app()->getLocale() === $item->locale
                        ? 'text-violet-600 font-semibold bg-violet-50'
                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
            >
                @if (app()->getLocale() === $item->locale)
                    <span class="h-1.5 w-1.5 rounded-full bg-violet-500 shrink-0"></span>
                @else
                    <span class="h-1.5 w-1.5 rounded-full bg-transparent shrink-0"></span>
                @endif
                {{ $item->name }}
            </a>
        @endforeach
    </div>
</div>
