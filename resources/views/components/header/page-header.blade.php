<div class="bg-gray-50 border-b border-gray-100"
    @if(!empty($background)) style="background-image:url('{{ $background }}');background-size:cover;background-position:center;" @endif
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16"
        @if(!empty($background)) style="background:rgba(0,0,0,0.45);" @endif
    >
        @isset($title)
            <h1 class="text-3xl md:text-4xl font-bold {{ !empty($background) ? 'text-white' : 'text-gray-900' }}">
                {{ $title }}
            </h1>
        @endisset
        @if (!empty($author) || !empty($publishedAt))
            <div class="flex items-center gap-3 mt-3 text-sm {{ !empty($background) ? 'text-gray-300' : 'text-gray-500' }}">
                @if (!empty($publishedAt))
                    <span>{{ $publishedAt }}</span>
                @endif
                @if (!empty($author) && !empty($publishedAt))
                    <span class="text-gray-400">&mdash;</span>
                @endif
                @if (!empty($author))
                    <span>{{ $author }}</span>
                @endif
            </div>
        @endif
    </div>
</div>