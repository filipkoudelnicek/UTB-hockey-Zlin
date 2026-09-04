@php
    $scrollTo ??= 'body';
    $scrollIntoViewJsSnippet = $scrollTo !== false
        ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
        : '';
@endphp

@if ($paginator->hasPages())
    <nav aria-label="Stránkování aktualit" class="mt-14 flex items-center justify-center gap-2">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="Předchozí stránka" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#ddd7d0] bg-transparent font-condensed text-lg font-black text-wine/30">‹</span>
        @else
            <button
                type="button"
                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                wire:loading.attr="disabled"
                aria-label="Předchozí stránka"
                class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-full border border-[#ddd7d0] bg-transparent font-condensed text-lg font-black text-wine transition-all hover:border-wine hover:bg-wine hover:text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 disabled:cursor-wait disabled:opacity-60 motion-reduce:!transition-none"
            >‹</button>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span aria-hidden="true" class="flex h-11 min-w-8 items-center justify-center text-sm font-bold text-muted">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page === $paginator->currentPage())
                        <span aria-current="page" class="flex h-11 min-w-11 items-center justify-center rounded-full bg-wine px-4 font-condensed text-sm font-black text-white">{{ $page }}</span>
                    @else
                        <button
                            type="button"
                            wire:key="news-pagination-{{ $paginator->getPageName() }}-{{ $page }}"
                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            aria-label="Přejít na stránku {{ $page }}"
                            class="flex h-11 min-w-11 cursor-pointer items-center justify-center rounded-full border border-[#ddd7d0] bg-transparent px-4 font-condensed text-sm font-black text-wine transition-all hover:border-wine hover:bg-wine hover:text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 disabled:cursor-wait disabled:opacity-60 motion-reduce:!transition-none"
                        >{{ $page }}</button>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <button
                type="button"
                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                wire:loading.attr="disabled"
                aria-label="Další stránka"
                class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-full border border-[#ddd7d0] bg-transparent font-condensed text-lg font-black text-wine transition-all hover:border-wine hover:bg-wine hover:text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 disabled:cursor-wait disabled:opacity-60 motion-reduce:!transition-none"
            >›</button>
        @else
            <span aria-disabled="true" aria-label="Další stránka" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#ddd7d0] bg-transparent font-condensed text-lg font-black text-wine/30">›</span>
        @endif
    </nav>
@endif
