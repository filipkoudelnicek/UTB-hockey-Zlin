<div>
    @if($categories->isNotEmpty())
        <div class="mb-10 flex flex-wrap gap-2">
            <button
                class="inline-flex cursor-pointer items-center rounded-lg border-2 px-5 py-2.5 font-condensed text-sm font-black uppercase tracking-widest transition-all focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 {{ $category === 'all' ? 'border-wine bg-wine text-white' : 'border-control-line bg-transparent text-nav-ink' }}"
                type="button"
                wire:click="selectCategory('all')"
            >Vše</button>

            @foreach($categories as $filterCategory => $filterLabel)
                <button
                    class="inline-flex cursor-pointer items-center rounded-lg border-2 px-5 py-2.5 font-condensed text-sm font-black uppercase tracking-widest transition-all focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 {{ $category === $filterCategory ? 'border-wine bg-wine text-white' : 'border-control-line bg-transparent text-nav-ink' }}"
                    type="button"
                    wire:click="selectCategory(@js($filterCategory))"
                    wire:key="news-category-{{ $loop->index }}"
                >{{ $filterLabel }}</button>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        @forelse($articles as $article)
            <article wire:key="news-article-{{ $article->id }}" class="group {{ $loop->first ? 'overflow-hidden rounded-[28px] border border-line bg-white shadow-[0_5px_30px_rgba(0,0,0,.06)] md:col-span-2' : 'flex flex-col overflow-hidden rounded-3xl border border-line bg-white shadow-[0_3px_20px_rgba(0,0,0,.05)]' }}">
                @if($loop->first)
                    <a href="{{ $article->url }}" class="grid text-inherit no-underline md:grid-cols-[1.35fr_1fr]">
                        <div class="h-[300px] overflow-hidden md:h-[420px]">
                            <img alt="{{ $article->plain_title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]" src="{{ $article->featured_image_url ?: asset('assets/obrazky/article.webp') }}">
                        </div>
                        <div class="flex flex-col justify-center p-8 md:p-10">
                            <p class="mb-4 mt-0 text-10 font-extrabold uppercase tracking-meta text-muted">
                                {{ optional($article->publish_time)->format('d. m. Y') }} · {{ mb_strtoupper(\App\Models\Article::categoryLabel($article->category)) }}
                            </p>
                            <h2 class="m-0 font-condensed text-[clamp(2.2rem,4vw,4rem)] font-black uppercase leading-95 text-ink-css">{{ $article->plain_title }}</h2>
                            @if($article->excerpt)
                                <p class="mt-5 text-sm leading-relaxed text-ink/65">{{ $article->excerpt }}</p>
                            @endif
                            <span class="mt-6 inline-flex items-center gap-2 text-11 font-black uppercase tracking-meta text-orange [transition:color_0.2s_ease,gap_0.2s_ease] group-hover:gap-3 motion-reduce:!transition-none">
                                ČÍST ČLÁNEK <span>›</span>
                            </span>
                        </div>
                    </a>
                @else
                    <a href="{{ $article->url }}" class="flex h-full flex-col text-inherit no-underline">
                        <div class="h-[230px] overflow-hidden">
                            <img alt="{{ $article->plain_title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]" src="{{ $article->featured_image_url ?: asset('assets/obrazky/article.webp') }}">
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <p class="mb-3 mt-0 text-10 font-extrabold uppercase tracking-meta text-muted">
                                {{ optional($article->publish_time)->format('d. m. Y') }} · {{ mb_strtoupper(\App\Models\Article::categoryLabel($article->category)) }}
                            </p>
                            <h2 class="m-0 flex-1 font-condensed text-28 font-black uppercase leading-[1.05] text-ink-css">{{ $article->plain_title }}</h2>
                            <span class="mt-5 inline-flex items-center gap-2 text-11 font-black uppercase tracking-meta text-orange [transition:color_0.2s_ease,gap_0.2s_ease] group-hover:gap-3 motion-reduce:!transition-none">
                                ČÍST ČLÁNEK <span>›</span>
                            </span>
                        </div>
                    </a>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-line bg-white p-8 text-center text-sm text-muted md:col-span-2">{{ $emptyMessage }}</div>
        @endforelse
    </div>

    @if($articles->hasPages())
        <div class="mt-10">{{ $articles->links(data: ['scrollTo' => '[data-news-section]']) }}</div>
    @endif
</div>
