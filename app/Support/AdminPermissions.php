<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Competition;
use App\Models\CompetitionStanding;
use App\Models\GameMatch;
use App\Models\Language;
use App\Models\MatchPlayerStat;
use App\Models\Page;
use App\Models\PageRoute;
use App\Models\PageType;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Redirect;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;

class AdminPermissions
{
    public const DEFINITIONS = [
        'content.articles' => ['label' => 'Aktuality', 'group' => 'Obsah'],
        'content.pages' => ['label' => 'Stránky', 'group' => 'Obsah'],
        'content.partners' => ['label' => 'Partneři', 'group' => 'Obsah'],
        'content.menu' => ['label' => 'Správa menu', 'group' => 'Obsah'],
        'sport.matches' => ['label' => 'Zápasy', 'group' => 'Běžná správa'],
        'sport.players' => ['label' => 'Hráči', 'group' => 'Běžná správa'],
        'sport.settings' => ['label' => 'Soutěže, týmy a stadiony', 'group' => 'Sportovní nastavení'],
        'reports.view' => ['label' => 'Tabulka a statistiky', 'group' => 'Přehledy'],
        'website.settings' => ['label' => 'Nastavení webu a typy stránek', 'group' => 'Nastavení'],
        'website.languages' => ['label' => 'Jazyky', 'group' => 'Správa webu'],
        'website.page_routes' => ['label' => 'Page Routes', 'group' => 'Správa webu'],
        'website.redirects' => ['label' => 'Přesměrování', 'group' => 'Správa webu'],
        'settings.users' => ['label' => 'Uživatelé', 'group' => 'Nastavení'],
        'settings.roles' => ['label' => 'Role a oprávnění', 'group' => 'Nastavení'],
    ];

    public static function permissionForModel(string $model): ?string
    {
        return match ($model) {
            Article::class => 'content.articles',
            Page::class => 'content.pages',
            Partner::class => 'content.partners',
            GameMatch::class => 'sport.matches',
            Player::class => 'sport.players',
            Competition::class, Team::class, Venue::class => 'sport.settings',
            CompetitionStanding::class, MatchPlayerStat::class => 'reports.view',
            PageType::class => 'website.settings',
            Language::class => 'website.languages',
            PageRoute::class => 'website.page_routes',
            Redirect::class => 'website.redirects',
            User::class => 'settings.users',
            Role::class => 'settings.roles',
            default => null,
        };
    }
}
