@props(['player', 'compact' => true])

@php
    $p = $player;
    $img = $p->portrait_url ?: asset('assets/obrazky/player.webp');
    $age = $p->date_of_birth?->age;
    $label = $player->position?->label() ?? '';
@endphp

<a
    class="group block text-inherit no-underline focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3"
    href="{{ route('player.show', ['playerSlug' => $p->slug]) }}"
>
    <div class="relative overflow-hidden rounded-2xl border border-line bg-white shadow-sm transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-lg motion-reduce:!transition-none {{ $compact ? 'min-h-[325px]' : 'min-h-[350px]' }}">
        <div class="relative flex items-end justify-center overflow-hidden bg-white pt-6 {{ $compact ? 'h-[225px]' : 'h-[250px]' }}">
            <img
                alt="{{ $p->full_name }}"
                class="block h-full w-full max-w-full object-contain object-bottom transition-transform duration-300 group-hover:scale-[1.025] motion-reduce:!transition-none"
                src="{{ $img }}"
            >
            @if($player->jersey_number !== null)
                <span class="pointer-events-none absolute right-3 top-3 z-20 font-condensed font-black leading-none text-orange-css/58 [-webkit-text-stroke:1px_#6a1b21] {{ $compact ? 'text-86' : 'text-94' }}">
                    {{ $player->jersey_number }}
                </span>
            @endif
        </div>

        <div class="relative z-10 bg-white px-5 pb-5 pt-4 text-center">
            <p class="m-0 font-bold uppercase tracking-stat text-muted {{ $compact ? 'text-10' : 'text-11' }}">
                {{ mb_strtoupper($label) }}{{ $age ? ' · ' . $age . ' LET' : '' }}@if($player->captain_role?->value === 'captain') · C @elseif($player->captain_role?->value === 'assistant') · A @endif
            </p>
            <h3 class="m-0 mt-1.5 font-condensed font-black uppercase leading-tight text-ink {{ $compact ? 'text-23' : 'text-25' }}">
                {{ $p->full_name }}
            </h3>
        </div>
    </div>
</a>
