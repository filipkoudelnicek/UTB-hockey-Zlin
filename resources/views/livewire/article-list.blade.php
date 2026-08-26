<div class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if ($articles->isEmpty())
            <p class="text-center text-gray-400 py-20">{{ __('Žádné články nebyly nalezeny.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($articles as $article)
                    <article class="group flex flex-col rounded-2xl overflow-hidden border border-gray-100 bg-white shadow-sm hover:shadow-md transition-shadow">

                        @isset($article->content['thumbnail'])
                            <a href="{{ $this->getArticleUrl($article) }}" class="block overflow-hidden aspect-video bg-gray-100">
                                <x-curator-glider :media="$article->content['thumbnail']" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            </a>
                        @endisset

                        <div class="flex flex-col flex-1 p-6 gap-3">
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                @if ($article->publish_time)
                                    <span>{{ $article->publish_time->format('j.n. Y') }}</span>
                                    <span class="text-gray-200">&bull;</span>
                                @endif
                                <span>{{ $article->user->name }}</span>
                            </div>

                            <a href="{{ $this->getArticleUrl($article) }}" class="flex-1">
                                <h2 class="text-lg font-semibold text-gray-900 group-hover:text-violet-600 transition-colors leading-snug line-clamp-3">
                                    {{ $article->title }}
                                </h2>
                            </a>

                            <a href="{{ $this->getArticleUrl($article) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-violet-600 hover:text-violet-800 transition-colors mt-auto">
                                {{ __('Číst více') }}
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $articles->links() }}
            </div>
        @endif

    </div>
</div>