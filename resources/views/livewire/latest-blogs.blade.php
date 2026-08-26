<div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
    @foreach ($latestArticles as $post)
        <article class="group flex flex-col rounded-2xl overflow-hidden border border-gray-100 bg-white shadow-sm hover:shadow-md transition-shadow">

            @isset($post->content['thumbnail'])
                <a href="{{ $this->getArticleUrl($post) }}" class="block overflow-hidden aspect-video bg-gray-100">
                    <x-curator-glider :media="$post->content['thumbnail']" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                </a>
            @endisset

            <div class="flex flex-col flex-1 p-6 gap-3">
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    @if ($post->publish_time)
                        <span>{{ $post->publish_time->format('j.n. Y') }}</span>
                        <span class="text-gray-200">&bull;</span>
                    @endif
                    <span>{{ $post->user->name }}</span>
                </div>

                <a href="{{ $this->getArticleUrl($post) }}" class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-violet-600 transition-colors leading-snug line-clamp-3">
                        {{ $post->title }}
                    </h3>
                </a>

                @isset($post->content['body'])
                    <p class="text-sm text-gray-500 leading-relaxed line-clamp-3">
                        {{ $this->getFirstParagraph($post->content['body']) }}
                    </p>
                @endisset

                <a href="{{ $this->getArticleUrl($post) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-violet-600 hover:text-violet-800 transition-colors mt-auto">
                    {{ __('Číst více') }}
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </article>
    @endforeach
</div>