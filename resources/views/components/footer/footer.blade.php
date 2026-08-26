<footer class="bg-gray-900 text-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

            {{-- Brand --}}
            <div class="flex flex-col gap-3">
                <a href="{{ $homepageUrl }}" class="text-white font-bold text-lg hover:text-violet-400 transition-colors">
                    {{ config('app.name') }}
                </a>
                <p class="text-sm text-gray-400 leading-relaxed">
                    &copy; {{ date('Y') }} {{ config('app.name') }}.<br>Všechna práva vyhrazena.
                </p>
            </div>

            {{-- Footer nav --}}
            @if (count($navItems) > 0)
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-4">Navigace</h3>
                    <ul class="flex flex-col gap-2">
                        @foreach ($navItems as $item)
                            <li>
                                <a
                                    href="{{ $item['url'] }}"
                                    target="{{ $item['target'] ?? '_self' }}"
                                    class="text-sm text-gray-400 hover:text-white transition-colors"
                                >{{ $item['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Latest articles --}}
            @if ($latestArticles && $latestArticles->count() > 0)
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-4">Nejnovější články</h3>
                    <ul class="flex flex-col gap-4">
                        @foreach ($latestArticles as $article)
                            <li>
                                <a href="{{ $getArticleUrl($article) }}" class="group flex flex-col gap-1">
                                    <span class="text-sm font-medium text-gray-300 group-hover:text-white transition-colors line-clamp-2 leading-snug">
                                        {{ $article->title }}
                                    </span>
                                    @if (!empty($article->published_at))
                                        <span class="text-xs text-gray-600">
                                            {{ \Carbon\Carbon::parse($article->published_at)->format('d.m.Y') }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-gray-600">
            {{ config('app.name') }} &mdash; {{ date('Y') }}
        </div>
    </div>
</footer>