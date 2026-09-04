<?php

namespace App\Filament\Pages;

use App\Models\Article;
use App\Models\Language;
use App\Models\Navigation;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page as FilamentPage;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;

class MenuManager extends FilamentPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;
    protected static ?string  $navigationLabel = 'Správa menu';
    protected static ?string  $title           = 'Správa menu';
    protected static string|\UnitEnum|null $navigationGroup = 'Obsah';
    protected static ?int     $navigationSort  = 6;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermission('content.menu') ?? false;
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString(
            '<span class="text-gray-500 dark:text-gray-300 font-semibold tracking-tight">Správa menu</span>'
        );
    }

    public function getView(): string
    {
        return 'filament.pages.menu-manager';
    }

    // ── State ─────────────────────────────────────────────────────────────

    public ?string $locale       = null;
    public ?int    $navigationId = null;
    public array   $items        = [];
    public array   $savedItems   = [];

    // Custom-link panel
    public string $customLabel  = '';
    public string $customUrl    = '';
    public string $customTarget = '_self';

    // Inline edit state
    public ?string $editingPath = null;
    public string  $editLabel   = '';
    public string  $editUrl     = '';
    public string  $editTarget  = '_self';

    // Left panel open/collapsed state
    public array $openPanels = ['pages'];

    // ── Boot ──────────────────────────────────────────────────────────────

    public function mount(): void
    {
        // Determine initial locale from default/first active language
        $defaultLang  = Language::where('active', true)->where('is_default', true)->first()
                     ?? Language::where('active', true)->orderBy('name')->first();
        $this->locale = $defaultLang?->locale;

        // Allow pre-selecting a navigation via ?nav=ID
        $navId = request()->query('nav');
        $nav   = $navId ? Navigation::find((int) $navId) : null;

        if ($nav) {
            $this->locale       = $nav->lang_locale ?? $this->locale;
            $this->navigationId = $nav->id;
        } else {
            $this->selectFirstNavigation();
        }

        $this->loadItems();
    }

    private function selectFirstNavigation(): void
    {
        $nav = Navigation::when($this->locale, fn ($q) => $q->where('lang_locale', $this->locale))
            ->orderBy('name')
            ->first();

        $this->navigationId = $nav?->id;
    }

    // ── Computed properties ───────────────────────────────────────────────

    #[Computed(cache: true)]
    public function languages(): array
    {
        return Language::where('active', true)
            ->orderBy('name')
            ->pluck('name', 'locale')
            ->toArray();
    }

    #[Computed(cache: true)]
    public function navigations(): array
    {
        return Navigation::when($this->locale, fn ($q) => $q->where('lang_locale', $this->locale))
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($n) => [$n->id => $n->name])
            ->toArray();
    }

    #[Computed(cache: true)]
    public function pages(): array
    {
        return Page::where('active', true)
            ->when($this->locale, fn ($q) => $q->where('lang_locale', $this->locale))
            ->orderBy('title')
            ->get(['id', 'title', 'full_slug', 'slug'])
            ->toArray();
    }

    #[Computed(cache: true)]
    public function articles(): array
    {
        return Article::where('active', true)
            ->when($this->locale, fn ($q) => $q->where('lang_locale', $this->locale))
            ->orderBy('title')
            ->get(['id', 'title'])
            ->toArray();
    }

    #[Computed]
    public function currentNavigation(): ?Navigation
    {
        return $this->navigationId ? Navigation::find($this->navigationId) : null;
    }

    #[Computed]
    public function flatItems(): array
    {
        return $this->flatten($this->items, '', 0);
    }

    public function isDirty(): bool
    {
        return $this->items !== $this->savedItems;
    }

    // ── Navigation change ─────────────────────────────────────────────────

    public function updatedLocale(): void
    {
        $this->editingPath = null;
        unset($this->navigations, $this->pages, $this->articles);
        $this->selectFirstNavigation();
        $this->loadItems();
    }

    public function updatedNavigationId(): void
    {
        $this->editingPath = null;
        $this->loadItems();
    }

    private function loadItems(): void
    {
        $nav         = Navigation::find($this->navigationId);
        $this->items = $nav?->items ?? [];
        $this->savedItems = $this->items;
    }

    // ── Save / Clear / Cancel ─────────────────────────────────────────────

    public function save(): void
    {
        if (! $this->navigationId) {
            Notification::make()->title('Nejprve vyberte nebo vytvořte menu')->warning()->send();
            return;
        }

        $nav = Navigation::find($this->navigationId);
        if (! $nav) {
            Notification::make()->title('Navigace nenalezena')->danger()->send();
            return;
        }

        $nav->update(['items' => $this->items]);
        $this->savedItems = $this->items;

        Notification::make()->title('Menu bylo uloženo')->success()->send();
    }

    public function clearMenu(): void
    {
        $this->items       = [];
        $this->editingPath = null;
    }

    public function cancelMenu(): void
    {
        $this->items       = $this->savedItems;
        $this->editingPath = null;
    }

    // ── Add items ─────────────────────────────────────────────────────────

    public function addCustomLink(): void
    {
        $label = trim($this->customLabel);
        $url   = trim($this->customUrl);

        if (! $label || ! $url) {
            Notification::make()->title('Vyplňte název i URL')->warning()->send();
            return;
        }

        $this->items[] = $this->makeItem($label, $url, $this->customTarget, 'custom');

        $this->customLabel  = '';
        $this->customUrl    = '';
        $this->customTarget = '_self';
    }

    public function addPage(int $id): void
    {
        $page = Page::find($id);
        if (! $page) return;

        $url = '/' . ltrim($page->full_slug ?? $page->slug, '/');
        $this->items[] = $this->makeItem($page->title, $url, '_self', 'page', $id);
    }

    public function addArticle(int $id): void
    {
        $article = Article::find($id);
        if (! $article) return;

        $this->items[] = $this->makeItem($article->plain_title, $article->url ?? '#', '_self', 'article', $id);
    }

    private function makeItem(string $label, string $url, string $target, string $type, ?int $modelId = null): array
    {
        return [
            'id'       => Str::uuid()->toString(),
            'label'    => $label,
            'url'      => $url,
            'target'   => $target,
            'type'     => $type,
            'model_id' => $modelId,
            'children' => [],
        ];
    }

    // ── Reorder ───────────────────────────────────────────────────────────

    public function moveUp(string $path): void
    {
        $this->items = $this->doMove($this->items, $path, -1);
    }

    public function moveDown(string $path): void
    {
        $this->items = $this->doMove($this->items, $path, 1);
    }

    public function indent(string $path): void
    {
        $this->items = $this->doIndent($this->items, $path);
    }

    public function outdent(string $path): void
    {
        $this->items = $this->doOutdent($this->items, $path);
    }

    // ── Edit ──────────────────────────────────────────────────────────────

    public function startEdit(string $path): void
    {
        $item = $this->getItem($this->items, $path);
        if (! $item) return;

        $this->editingPath = $path;
        $this->editLabel   = $item['label']  ?? '';
        $this->editUrl     = $item['url']    ?? '';
        $this->editTarget  = $item['target'] ?? '_self';
    }

    public function saveEdit(): void
    {
        if (! $this->editingPath) return;

        $this->items = $this->doUpdate($this->items, $this->editingPath, [
            'label'  => trim($this->editLabel),
            'url'    => trim($this->editUrl),
            'target' => $this->editTarget,
        ]);

        $this->editingPath = null;
    }

    public function cancelEdit(): void
    {
        $this->editingPath = null;
    }

    // ── Toggle left accordion panels ──────────────────────────────────────

    public function togglePanel(string $key): void
    {
        if (in_array($key, $this->openPanels, true)) {
            $this->openPanels = array_values(array_filter($this->openPanels, fn ($p) => $p !== $key));
        } else {
            $this->openPanels[] = $key;
        }
    }

    // ── Delete ────────────────────────────────────────────────────────────

    public function deleteItem(string $path): void
    {
        if ($this->editingPath === $path) {
            $this->editingPath = null;
        }
        $this->items = $this->doDelete($this->items, $path);
    }

    // ── Flatten tree for rendering ────────────────────────────────────────

    private function flatten(array $items, string $prefix, int $depth): array
    {
        $result = [];
        $count  = count($items);

        foreach ($items as $index => $item) {
            $path = ($prefix === '') ? (string) $index : $prefix . '.' . $index;

            $result[] = [
                'path'       => $path,
                'depth'      => $depth,
                'item'       => $item,
                'isFirst'    => $index === 0,
                'isLast'     => $index === $count - 1,
                'canIndent'  => $index > 0 && $depth < 2,
                'canOutdent' => $depth > 0,
            ];

            if (! empty($item['children'])) {
                $result = array_merge($result, $this->flatten($item['children'], $path, $depth + 1));
            }
        }

        return $result;
    }

    // ── Tree manipulation helpers ─────────────────────────────────────────

    private function getItem(array $items, string $path): ?array
    {
        $parts  = explode('.', $path);
        $cursor = $items;

        foreach ($parts as $i => $seg) {
            $idx = (int) $seg;
            if (! array_key_exists($idx, $cursor)) return null;
            if ($i === count($parts) - 1) return $cursor[$idx];
            $cursor = $cursor[$idx]['children'] ?? [];
        }

        return null;
    }

    private function doMove(array $items, string $path, int $delta): array
    {
        $parts  = explode('.', $path);
        $index  = (int) array_pop($parts);
        $parent = &$items;
        foreach ($parts as $seg) {
            $parent = &$parent[(int) $seg]['children'];
        }

        $newIndex = $index + $delta;
        if ($newIndex >= 0 && $newIndex < count($parent)) {
            [$parent[$index], $parent[$newIndex]] = [$parent[$newIndex], $parent[$index]];
        }

        unset($parent);
        return $items;
    }

    private function doIndent(array $items, string $path): array
    {
        $parts  = explode('.', $path);
        $index  = (int) array_pop($parts);
        $parent = &$items;
        foreach ($parts as $seg) {
            $parent = &$parent[(int) $seg]['children'];
        }

        if ($index > 0) {
            $item = $parent[$index];
            array_splice($parent, $index, 1);
            $parent[$index - 1]['children'][] = $item;
        }

        unset($parent);
        return $items;
    }

    private function doOutdent(array $items, string $path): array
    {
        $parts = explode('.', $path);
        if (count($parts) < 2) return $items;

        $index       = (int) array_pop($parts);
        $parentIndex = (int) array_pop($parts);
        $grandParent = &$items;
        foreach ($parts as $seg) {
            $grandParent = &$grandParent[(int) $seg]['children'];
        }

        $item = $grandParent[$parentIndex]['children'][$index];
        array_splice($grandParent[$parentIndex]['children'], $index, 1);
        array_splice($grandParent, $parentIndex + 1, 0, [$item]);

        unset($grandParent);
        return $items;
    }

    private function doDelete(array $items, string $path): array
    {
        $parts  = explode('.', $path);
        $index  = (int) array_pop($parts);
        $parent = &$items;
        foreach ($parts as $seg) {
            $parent = &$parent[(int) $seg]['children'];
        }

        array_splice($parent, $index, 1);

        unset($parent);
        return $items;
    }

    private function doUpdate(array $items, string $path, array $updates): array
    {
        $parts  = explode('.', $path);
        $index  = (int) array_pop($parts);
        $parent = &$items;
        foreach ($parts as $seg) {
            $parent = &$parent[(int) $seg]['children'];
        }

        $parent[$index] = array_merge($parent[$index], $updates);

        unset($parent);
        return $items;
    }

    // ── Header actions ────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createNavigation')
                ->label('Nové menu')
                ->color('gray')
                ->icon(Heroicon::OutlinedPlus)
                ->schema([
                    TextInput::make('name')
                        ->label('Název menu')
                        ->placeholder('Např. Hlavní menu')
                        ->required(),
                    Select::make('handle')
                        ->label('Identifikátor (handle)')
                        ->helperText('Slouží k načítání menu v šablonách: Navigation::getByHandle("main").')
                        ->options(function (callable $get) {
                            $locale = $get('lang_locale') ?? $this->locale;
                            $used   = Navigation::when($locale, fn ($q) => $q->where('lang_locale', $locale))
                                ->pluck('handle')
                                ->flip()
                                ->toArray();

                            $all = [
                                'main'    => 'main — Hlavní menu',
                                'footer'  => 'footer — Patička',
                                'mobile'  => 'mobile — Mobilní menu',
                            ];

                            return array_diff_key($all, $used);
                        })
                        ->searchable()
                        ->required()
                        ->rules(['unique:navigations,handle']),
                    Select::make('lang_locale')
                        ->label('Jazyk')
                        ->options(fn () => Language::activeOptions())
                        ->default(fn () => $this->locale ?? Language::defaultActiveLocale())
                        ->hidden(fn () => ! Language::hasMultipleActive())
                        ->dehydratedWhenHidden()
                        ->required(),
                ])
                ->modalHeading('Vytvořit nové menu')
                ->modalSubmitActionLabel('Vytvořit')
                ->action(function (array $data): void {
                    $nav = Navigation::create([
                        'name'        => $data['name'],
                        'handle'      => $data['handle'],
                        'lang_locale' => $data['lang_locale'],
                        'items'       => [],
                    ]);

                    if ($this->locale !== $data['lang_locale']) {
                        $this->locale = $data['lang_locale'];
                        unset($this->navigations, $this->pages, $this->articles);
                    } else {
                        unset($this->navigations);
                    }

                    $this->navigationId = $nav->id;
                    $this->loadItems();

                    Notification::make()->title('Menu „' . $nav->name . '" bylo vytvořeno')->success()->send();
                }),

            Action::make('save')
                ->label('Uložit změny')
                ->color('primary')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->action(fn () => $this->save()),

            Action::make('clear')
                ->label('Vyprázdnit menu')
                ->color('gray')
                ->icon(Heroicon::OutlinedTrash)
                ->requiresConfirmation()
                ->modalHeading('Vyprázdnit menu?')
                ->modalDescription('Odstraní všechny položky ze stromu. Změna se projeví až po uložení.')
                ->action(fn () => $this->clearMenu()),

            Action::make('cancel')
                ->label('Zahodit změny')
                ->color('gray')
                ->icon(Heroicon::OutlinedXCircle)
                ->action(fn () => $this->cancelMenu()),

            Action::make('deleteNavigation')
                ->label('Smazat menu')
                ->color('danger')
                ->icon(Heroicon::OutlinedTrash)
                ->requiresConfirmation()
                ->modalHeading('Smazat menu?')
                ->modalDescription(fn () => 'Opravdu chcete trvale smazat menu „' . ($this->currentNavigation?->name ?? '') . '"? Tato akce je nevratná.')
                ->modalSubmitActionLabel('Smazat')
                ->visible(fn () => $this->navigationId !== null)
                ->action(function (): void {
                    $nav = Navigation::find($this->navigationId);
                    if (! $nav) return;

                    $name = $nav->name;
                    $nav->delete();

                    unset($this->navigations);
                    $this->navigationId = null;
                    $this->items        = [];
                    $this->savedItems   = [];
                    $this->editingPath  = null;

                    $this->selectFirstNavigation();
                    $this->loadItems();

                    Notification::make()->title('Menu „' . $name . '" bylo smazáno')->success()->send();
                }),
        ];
    }
}
