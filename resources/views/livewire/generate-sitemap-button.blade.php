<div style="display: contents;">
    @if(auth()->user()?->hasPermission('website.settings'))
        {{ $this->generateSitemapAction() }}

        <x-filament-actions::modals />
    @endif
</div>
