@if ($paginator->hasPages())
    <div class="tmp-pagination-area-next-pev mt--50">
        
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button disabled aria-label="Předchozí stránka"><i class="fa-regular fa-chevron-left"></i></button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" aria-label="Předchozí stránka">
                <button aria-label="Předchozí stránka"><i class="fa-regular fa-chevron-left"></i></button>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <button disabled>{{ $element }}</button>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        {{-- Aktivní stránka - bez odkazu --}}
                        <button class="active">{{ $page }}</button>
                    @else
                        {{-- Ostatní stránky - s odkazem --}}
                        <a href="{{ $url }}">
                            <button>{{ $page }}</button>
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" aria-label="Další stránka">
                <button aria-label="Další stránka"><i class="fa-sharp fa-regular fa-chevron-right"></i></button>
            </a>
        @else
            <button disabled aria-label="Další stránka"><i class="fa-sharp fa-regular fa-chevron-right"></i></button>
        @endif

    </div>
@endif
