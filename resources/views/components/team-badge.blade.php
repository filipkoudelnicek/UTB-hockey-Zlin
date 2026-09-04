@props(['team', 'large' => false])

@php
    $logo = $team?->logo_url;
    $abbr = $team?->short_name ?: collect(explode(' ', $team?->name ?? 'T'))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(3)->join('');
    $callerClass = (string) $attributes->get('class');
    // only fall back to defaults when the caller didn't already set their own (e.g. bg-white or rounded-full on homepage cards)
    $hasCustomBackground = str_contains($callerClass, 'bg-');
    $hasCustomRounding = str_contains($callerClass, 'rounded-');
    $hasCustomSize = (bool) preg_match('/\b[hw]-\S+/', $callerClass);
@endphp

<div {{ $attributes->class([
    ($large ? 'h-16 w-16' : 'h-12 w-12') => ! $hasCustomSize,
    ($large ? 'rounded-full' : 'rounded-lg') => ! $hasCustomRounding,
    'flex items-center justify-center overflow-hidden font-black text-black',
]) }}>
    @if($logo)
        <img
            src="{{ $logo }}"
            alt="{{ $team?->name }}"
            class="{{ $large ? 'h-[3rem] w-[3rem]' : 'h-12 w-12' }} block max-w-full object-contain "
        >
    @else
        <span class="text-12">{{ $abbr }}</span>
    @endif
</div>
