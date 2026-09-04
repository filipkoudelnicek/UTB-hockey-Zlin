@if ($paginator->hasPages())
    <nav aria-label="{{ __('Pagination Navigation') }}" class="mt-14 flex items-center justify-center gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#ddd7d0] bg-transparent font-condensed text-lg font-black text-wine/30">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#ddd7d0] bg-transparent font-condensed text-lg font-black text-wine no-underline transition-all hover:border-wine hover:bg-wine hover:text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none">‹</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="flex h-11 min-w-8 items-center justify-center text-sm font-bold text-muted">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="flex h-11 min-w-11 items-center justify-center rounded-full bg-wine px-4 font-condensed text-sm font-black text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="flex h-11 min-w-11 items-center justify-center rounded-full border border-[#ddd7d0] bg-transparent px-4 font-condensed text-sm font-black text-wine no-underline transition-all hover:border-wine hover:bg-wine hover:text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#ddd7d0] bg-transparent font-condensed text-lg font-black text-wine no-underline transition-all hover:border-wine hover:bg-wine hover:text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none">›</a>
        @else
            <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#ddd7d0] bg-transparent font-condensed text-lg font-black text-wine/30">›</span>
        @endif
    </nav>
@endif
