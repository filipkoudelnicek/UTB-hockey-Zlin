# Filament CMS

Moderní, vícejazyčný CMS systém postavený na **Laravel 13**, **Filament 5** a **Livewire 4**. Poskytuje kompletní správu obsahu — od stránek a článků přes navigace až po dynamický Route Builder — vše přes přehledné administrační rozhraní.

---

## Obsah

- [Funkce](#funkce)
- [Požadavky](#požadavky)
- [Instalace](#instalace)
- [Struktura projektu](#struktura-projektu)
- [Administrace](#administrace)
- [Livewire komponenty](#livewire-komponenty)
- [Licence](#licence)

---

## Funkce

| Oblast | Popis |
|---|---|
| **Stránky** | Správa stránek se slugy, blocky obsahu, typy stránek a vícejazyčnou podporou |
| **Články / Blog** | Správa článků s plánovaným publikováním, přiřazením autora a URL generováním |
| **Jazyky** | Vícejazyčný obsah, nastavení výchozího jazyka |
| **Route Builder** | Dynamické URL trasy uložené v databázi, přiřazení šablony a controlleru |
| **Menu Manager** | Drag-and-drop správa navigačních menu pro každý jazyk |
| **Přesměrování** | Správa URL redirectů (301 / 302) přímo z adminu |
| **Globální nastavení** | Název webu, slogan, kontaktní údaje, sociální sítě |
| **Sitemap** | Automatická generace `sitemap.xml` přes Spatie Sitemap |
| **Media** | Správa médií pomocí Filament Curator |
| **Uživatelé** | Správa uživatelů s přihlášením a změnou hesla |

---

## Požadavky

- PHP **8.3+**
- Composer
- Node.js & npm
- MySQL / PostgreSQL / SQLite

---

## Instalace

```bash
# 1. Klonování repozitáře
git clone https://github.com/filipkoudelnicek/UTB-hockey-Zlin.git
cd UTB-hockey-Zlin

# 2. Instalace PHP závislostí
composer install

# 3. Instalace JS závislostí
npm install

# 4. Konfigurace prostředí
cp .env.example .env
php artisan key:generate

# 5. Nastavte připojení k databázi v souboru .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=filament_cms
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Migrace databáze (+ seed)
php artisan migrate --seed

# 7. Vytvoření admin uživatele
php artisan make:filament-user

# 8. Vygenerování bezpečnostního tokenu pro Curator (správa médií)
# Token se automaticky zapíše do .env jako CURATOR_GLIDE_TOKEN
php artisan curator:token

# 9. Sestavení front-endu
npm run build

# 10. Spuštění vývojového serveru
composer run dev
```

Po instalaci je administrace dostupná na adrese: **`/admin`**
- [Laravel 13](https://laravel.com)
---

- [Tailwind CSS 4](https://tailwindcss.com)
- [Vite 6](https://vite.dev)
```
app/
├── Filament/
│   ├── Pages/
│   │   ├── ChangePassword.php      # Změna hesla přihlášeného uživatele
│   │   ├── GlobalSettings.php      # Globální nastavení webu
│   │   └── MenuManager.php         # Drag-and-drop Menu Manager
│   └── Resources/
│       ├── ArticleResource.php     # Správa článků
│       ├── LanguageResource.php    # Správa jazyků
│       ├── PageResource.php        # Správa stránek
│       ├── PageRouteResource.php   # Route Builder
│       ├── PageTypeResource.php    # Typy stránek
│       ├── RedirectResource.php    # URL přesměrování
│       └── UserResource.php        # Správa uživatelů
├── Livewire/
│   ├── ArticleList.php             # Výpis článků s filtrováním
│   ├── ContactForm.php             # Kontaktní formulář
│   ├── LanguageSwitcher.php        # Přepínač jazyků
│   └── LatestBlogs.php             # Widget posledních článků
├── Models/
│   ├── Article.php
│   ├── Language.php
│   ├── Navigation.php
│   ├── Page.php
│   ├── PageRoute.php
│   ├── PageType.php
│   ├── Redirect.php
│   ├── Setting.php
│   └── User.php
└── Services/
    ├── ArticleService.php
    ├── LanguageService.php
    ├── MediaService.php
    ├── PageService.php
    └── UrlService.php
```

---

## Administrace

Administrace je dostupná na **/admin** a obsahuje:

### Obsah
- **Stránky** — vytváření, úprava a mazání stránek s vícejazyčnou podporou
- **Články** — správa blogu s plánovaným publikováním a přiřazením autora
- **Média** — nahrávání a správa souborů (Filament Curator)

### Navigace
- **Menu Manager** — vizuální drag-and-drop editor navigačních menu per jazyk

### Správa webu
- **Page Routes** — mapování URL cest na kontrolery/šablony přímo z databáze
- **Typy stránek** — definice vlastních typů stránek (např. `blog`, `landing`)
- **Přesměrování** — HTTP 301/302 redirecty bez zásahu do kódu

### Nastavení
- **Jazyky** — správa aktivních jazyků, nastavení výchozího jazyka
- **Nastavení webu** — informace o webu, sociální sítě, analytika, GDPR; možnost ručně spustit generaci sitemap

### Administrace
- **Uživatelé** — správa admin účtů

---

## Livewire komponenty

Komponenty lze vkládat do Blade šablon pomocí standardní Livewire syntaxe:

```blade
{{-- Výpis článků --}}
<livewire:article-list />

{{-- Posledních N blogových příspěvků --}}
<livewire:latest-blogs />

{{-- Kontaktní formulář --}}
<livewire:contact-form />

{{-- Přepínač jazyků --}}
<livewire:language-switcher />
```

---

## Tech Stack

- [Laravel 13](https://laravel.com)
- [Filament 5](https://filamentphp.com)
- [Livewire 4](https://livewire.laravel.com)
- [Tailwind CSS 4](https://tailwindcss.com)
- [Vite 6](https://vite.dev)
- [Filament Curator](https://github.com/awcodes/filament-curator) — správa médií
- [Spatie Laravel Sitemap](https://github.com/spatie/laravel-sitemap) — generace sitemap

---

## Licence

Tento projekt je licencován pod licencí [MIT](https://opensource.org/licenses/MIT).

---

## UTB RedBricks – rozšíření CMS

Frontend v `_FRONTEND` je v projektu převedený na Blade/Tailwind šablony bez nahrazení původního CMS routingu.

### Stránky a šablony

Původní princip CMS zůstává zachovaný:

- `Page` = konkrétní stránka, její slug a redakční obsah
- `PageType` = typ stránky, Blade šablona a Filament schema
- `PageRoute` = dynamická URL struktura spravovaná přes Route Builder
- `Menu Manager` = navigace navázaná na vytvořené stránky

RedBricks přidává typy/šablony:

- Homepage (`pages.homepage`)
- Zápasy (`pages.matches`)
- Tým (`pages.team`)
- Aktuality (`pages.blog`)
- Klub (`pages.about`)
- Kontakt (`pages.contact`)
- původní Textová stránka zůstává dostupná

Redakční obsah jednotlivých pohledů se upravuje ve **Stránky**. URL se nadále řídí slugem stránky a pravidly v **Page Routes**; nejsou napevno definované v `routes/web.php`.

### Globální data mimo stránky

Filament má navíc oddělené moduly:

**Sport**
- Zápasy
- Hráči
- Soupisky
- Týmy
- Soutěže
- Sezóny
- Ročníky soutěží
- Stadiony
- Tabulka (read-only, dopočítaná ze zápasů)
- Statistiky hráčů (read-only přehled; editace zápasových statistik probíhá u zápasu)

**Klub**
- Partneři – globální, protože se používají na více stránkách

FAQ, milníky, hodnoty a vedení klubu nejsou samostatné globální entity. Jsou součástí obsahu odpovídající stránky přes její PageType schema.

### Sportovní logika

- ligový zápas může patřit do `CompetitionSeason`
- přátelský/přípravný zápas může mít `competition_season_id = null`
- přátelské zápasy se nikdy nepočítají do ligové tabulky
- tabulka se rekonstruuje ze zápasů podle bodování konkrétního ročníku soutěže
- sezónní statistiky hráčů se odvozují z `MatchPlayerStat`
- změna výsledku ligového zápasu přepočítá standings
- `source` + `external_id` připravují sportovní entity na budoucí API synchronizaci

### Dynamické URL

Detail článku používá Route Builder route s vazbou na stránku typu `blog` a detail hráče obdobně na stránku typu `team`. Změna slugu hlavní stránky se tak promítne do veřejných URL bez hardcodování `/aktuality` nebo `/tym` ve frontendových šablonách.
