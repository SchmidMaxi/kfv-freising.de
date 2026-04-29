# KFV Freising - Claude Context

## Project Overview

Website for **KFV Freising** (Kreisfeuerwehrverband Freising - Fire Department District Association) built with TYPO3 13 LTS. Features fire department maps, organizational charts, news, events, and general content management.

## Tech Stack

| Component | Technology | Version |
|-----------|------------|---------|
| CMS | TYPO3 | 13.4 LTS |
| PHP | PHP | 8.4 (CI: 8.3) |
| Database | MySQL | 8.0 |
| CSS Framework | Tailwind CSS | 3.4 |
| JS (legacy) | Bootstrap JS | 5.3.3 (nur JS, kein CSS) |
| Build Tool | Vite | 6.0 |
| Dev Environment | DDEV | Latest |
| Deployment | Deployer | 7.4 |

## Project Structure

```
kfv-freising.de/
├── config/
│   └── system/
│       ├── settings.php          # TYPO3 system config
│       └── additional.php        # Additional config
├── packages/
│   ├── sitepackage/              # Main site package
│   ├── feuerwehren/              # Fire department extension
│   └── icsimporter/              # Calendar ICS importer
├── public/                        # TYPO3 webroot
│   ├── fileadmin/                # User uploads
│   ├── typo3/                    # TYPO3 backend
│   └── _vt/                      # Vector tiles cache
├── var/                           # TYPO3 var (logs, cache)
├── vendor/                        # Composer dependencies
├── .ddev/                         # DDEV configuration
├── .github/workflows/             # CI/CD workflows
├── deploy.php                     # Deployer config
└── composer.json
```

## Custom Extensions

### 1. sitepackage (`schmid/sitepackage`)
Main site configuration, templates, and styling.

**Key Features:**
- Page templates and layouts
- Content blocks (accordion, cards, sliders, hero, quick-actions)
- News extension overrides
- Calendar integration
- Tailwind CSS / Vite build pipeline

**Path:** `packages/sitepackage/`

### 2. feuerwehren (`schmid/feuerwehren`)
Fire department management with interactive maps.

**Key Features:**
- MapLibre GL vector tile maps
- Fire department search API
- Organizational chart
- Jubilee management

**Path:** `packages/feuerwehren/`
**Docs:** See `packages/feuerwehren/CLAUDE.md`

### 3. icsimporter (`schmid/icsimporter`)
Automated calendar event import from ICS feeds.

**Path:** `packages/icsimporter/`

## Development Environment

### Prerequisites
- Docker Desktop
- DDEV CLI
- Node.js 22+

### Setup
```bash
# Clone and start
git clone <repo-url> kfv-freising.de
cd kfv-freising.de
ddev start

# Install dependencies
ddev composer install
cd packages/sitepackage && npm ci

# Import database (optional)
ddev sync staging
```

### Start Development
```bash
# Start Vite dev server
cd packages/sitepackage
npm run dev

# Access site
open https://kfv-freising.ddev.site
```

### URLs
- **Frontend**: https://kfv-freising.ddev.site
- **Backend**: https://kfv-freising.ddev.site/typo3
- **Vite Dev**: https://kfv-freising.ddev.site:5173

## Build & Assets

### Vite Configuration
Entry point: `packages/sitepackage/Resources/Private/JavaScript/main.js`

```bash
# Development (with HMR)
npm run dev

# Production build
npm run build
```

**Output:** `packages/sitepackage/Resources/Public/Vite/`

### CSS/Tailwind Structure
```
packages/sitepackage/
├── tailwind.config.js                     # Design System: Farben, Fonts, Schatten, Animationen
├── postcss.config.js                      # tailwindcss + autoprefixer
├── Resources/Private/Css/main.css        # Tailwind-Directives + CSS Custom Properties (HSL)
└── Resources/Public/Scss_backup/         # Backup der alten SCSS-Dateien (52 Dateien)
```

**Design Tokens** (CSS Custom Properties in `main.css`):
- Farben: `--fire-red`, `--fire-red-light`, `--surface-dark`, `--gray-{100|200|300|500|900}`
- Semantik: `--background`, `--foreground`, `--card`, `--accent`, `--muted`, `--border` etc.
- Dark Mode: `[data-bs-theme="dark"]` — kompatibel mit `colormode.js`
- Fonts: Oswald (heading) + Roboto (body) — lokal, kein Google Fonts

**Tailwind content-Pfade** (werden für JIT-Scan verwendet):
- `./Resources/Private/**/*.html`
- `./Resources/Private/**/*.js`
- `./ContentBlocks/**/*.html`

**Wichtig:** Eigene Utility-Klassen (`.gradient-fire`, `.bg-surface-dark` etc.) stehen als plain CSS **außerhalb von `@layer`** in `main.css` — nur so werden sie nicht vom JIT-Purger entfernt.

## Database Sync

Sync database from remote environment:

```bash
# Create .env.db-sync from template
cp .env.db-sync.example .env.db-sync
# Edit with SSH credentials

# Sync from staging
ddev sync staging

# Sync from production
ddev sync production
```

## Deployment

### Branches
- `main`: Production
- `develop`: Development
- `staging`: Staging environment

### Manual Deploy
```bash
# Deploy to staging
dep deploy staging

# Deploy to production
dep deploy production
```

### CI/CD
GitHub Actions automatically deploy:
- Push to `staging` → Deploy to staging server
- Push to `production` → Deploy to production server

### Server Details
- **Host**: www416.your-server.de
- **Port**: 222
- **User**: kfvvfr

## CLI Commands

### TYPO3 Console
```bash
# Clear all caches
ddev exec vendor/bin/typo3 cache:flush

# Update database schema
ddev exec vendor/bin/typo3 database:updateschema

# Scheduler run
ddev exec vendor/bin/typo3 scheduler:run
```

### Feuerwehren Commands
```bash
# Prefetch vector tiles
ddev exec vendor/bin/typo3 feuerwehren:tiles:prefetch --preset=freising --zooms=8-14

# Download MBTiles
ddev exec vendor/bin/typo3 feuerwehren:mbtiles:fetch --url=<url> --dest=fileadmin/tiles/osm.mbtiles
```

## Third-Party Extensions

| Extension | Purpose |
|-----------|---------|
| `georgringer/news` | News management |
| `lochmueller/calendarize` | Event calendar |
| `mediadreams/md_fullcalendar` | Calendar UI |
| `friendsoftypo3/content-blocks` | Custom content elements |
| `b13/container` | Backend container elements |
| `reelworx/rx-shariff` | Social sharing |
| `praetorius/vite-asset-collector` | Vite integration |

## Content Blocks

Custom content elements in `packages/sitepackage/ContentBlocks/ContentElements/`.

Struktur je Block: `config.yaml` + `templates/frontend.html` + `templates/backend-preview.html` + `language/labels.xlf`

| Block | Status | Beschreibung |
|-------|--------|--------------|
| `hero-static` | ✅ Tailwind | Vollbild-Hero: Bild+Gradient, Headline, CTAs, Stats-Grid |
| `quick-actions` | ✅ Tailwind | Icon-Karten-Raster (bis 4 Spalten), konfigurierbares Icon/Link/Variante |
| `accordion` | ✅ Tailwind | Akkordeon via `<details>`/`<summary>`, chevron-Animation mit `group-open:rotate-180` |
| `card` | ✅ Tailwind | Einzelne Karte mit `.card-color-{variant}` (default/light/dark) |
| `card-group` | ✅ Tailwind | Responsive Grid (`grid-cols-*` mit safelist für dynamische Werte) |
| `card-slider` | ✅ Tailwind | Splide-Karussell, Bootstrap-Wrapper ersetzt durch Tailwind-Karten |
| `heroslider` | ✅ Tailwind | Splide-Slider, Bootstrap `.caption` ersetzt durch Tailwind-Overlay |

**Collection-Felder** in `config.yaml` erzeugen eigene DB-Tabellen. Nach Änderungen immer:
```bash
ddev exec vendor/bin/typo3 database:updateschema
ddev exec vendor/bin/typo3 cache:flush
```

## Configuration Files

| File | Purpose |
|------|---------|
| `config/system/settings.php` | TYPO3 system configuration |
| `config/sites/*/config.yaml` | Site configuration |
| `.ddev/config.yaml` | DDEV configuration |
| `deploy.php` | Deployer configuration |
| `composer.json` | PHP dependencies |
| `packages/sitepackage/package.json` | Node dependencies |
| `packages/sitepackage/vite.config.js` | Vite configuration |

## Environment Variables

### DDEV (.ddev/config.yaml)
```yaml
web_environment:
  - VITE_PRIMARY_PORT=5173
```

### Database Sync (.env.db-sync)
```env
REMOTE_SSH_HOST=www416.your-server.de
REMOTE_SSH_PORT=222
REMOTE_SSH_USER=kfvvfr
REMOTE_DB_NAME=kfvvfr_typo3
REMOTE_DB_USER=kfvvfr_typo3
REMOTE_DB_PASSWORD=***
REMOTE_DB_HOST=localhost
```

## Common Tasks

### Add a New Page Template
1. Create layout in `packages/sitepackage/Resources/Private/PageView/Layouts/`
2. Create template in `packages/sitepackage/Resources/Private/PageView/Templates/`
3. Register in `Configuration/Sets/SitePackage/PageTsConfig/BackendLayouts/`

### Add a New Content Block
1. Create folder in `packages/sitepackage/ContentBlocks/ContentElements/<name>/`
2. Add `config.yaml`, `templates/frontend.html`, `templates/backend-preview.html`, `language/labels.xlf`
3. Run `ddev exec vendor/bin/typo3 database:updateschema`
4. Run `ddev exec vendor/bin/typo3 cache:flush`

### Modify News Templates
Override in `packages/sitepackage/Resources/Private/Extensions/news/`

### Update Dependencies
```bash
# PHP
ddev composer update

# Node
cd packages/sitepackage && npm update
```

## Troubleshooting

### Vite HMR not working
1. Check Vite dev server is running
2. Verify port 5173 is accessible
3. Check browser console for connection errors

### Database connection issues
```bash
ddev restart
ddev describe  # Check DB credentials
```

### Cache issues
```bash
ddev exec vendor/bin/typo3 cache:flush
rm -rf var/cache/*
```

### Deployment failures
1. Check SSH key is added to agent
2. Verify server access: `ssh -p 222 kfvvfr@www416.your-server.de`
3. Check GitHub Actions logs

## Git Workflow

```bash
# Feature development
git checkout develop
git checkout -b feature/my-feature
# ... make changes ...
git commit -m "Add feature"
git push -u origin feature/my-feature
# Create PR to develop

# Release to staging
git checkout staging
git merge develop
git push

# Release to production
git checkout main
git merge staging
git push
```

## Important Notes

- Always run `ddev composer install` after pulling changes
- Run `npm run build` before deploying (CI does this automatically)
- Never commit `.env.db-sync` or `config/system/additional.local.php`
- MBTiles files are not in Git (download via CLI command)
- TYPO3 13 uses Site Sets instead of ext_typoscript_setup.txt

---

## Frontend-Redesign: Tailwind Migration

Ein neues React/Tailwind-Frontend-Prototyp liegt unter `/frontend`. Ziel ist die vollständige Übernahme des Designs in die TYPO3 Sitepackage Extension.

**Grundsatzentscheidung:** Tailwind CSS statt Bootstrap CSS. Bootstrap JS bleibt temporär erhalten (für colormode.js und etwaige Legacy-Komponenten) und wird in Phase 4 vollständig entfernt.

### Abgeschlossene Phasen

#### ✅ Phase 1 — Design System
- Backup aller SCSS-Dateien nach `Resources/Public/Scss_backup/` (52 Dateien)
- `tailwind.config.js` mit vollständigem Design System (Farben, Fonts, Schatten, Animationen, dark mode via `[data-bs-theme="dark"]`)
- `postcss.config.js` (tailwindcss + autoprefixer)
- `Resources/Private/Css/main.css` — CSS Custom Properties (HSL), lokale Fonts, Tailwind-Directives, eigene Utilities
- `package.json` — Bootstrap CSS + Sass entfernt, Tailwind + bootstrap-icons hinzugefügt
- `main.js` — importiert neue main.css, Bootstrap Icons, Bootstrap JS (temporär), colormode.js

#### ✅ Phase 2 — Header & Footer
- `PageView/Partials/Header.html` — vollständig neu in Tailwind:
  - Preheader (dunkel, nur Desktop) mit Telefon/E-Mail/Notruf 112
  - Desktop-Nav mit CSS-`group`/`group-hover:block`-Dropdowns (kein JS)
  - Mobile-Menü mit Hamburger-Toggle (vanilla JS in main.js) und `<details>`-Sub-Navigation (kein JS)
  - Theme-Toggle (Dropdown mit SVG-Sprites, `data-bs-theme-value`)
- `PageView/Partials/Footer.html` — vollständig neu in Tailwind:
  - 4-Spalten-Grid: KFV-Brand + dynamische Nav-Spalten (`f:for each="{footer}"`) + Kontakt
  - Social Icons (Facebook/Instagram/YouTube als Inline-SVG)
  - Untere Leiste mit Copyright (`lib.copyright`) und Meta-Navigation

#### ✅ Phase 3 — Hero, Quick Actions & News-Liste
- **Content Block `hero-static`** — Vollbild-Hero:
  - Hintergrundbild mit fire-red Gradient-Overlay (oder reiner Gradient ohne Bild)
  - Badge, Headline (h1), Untertitel, 2 CTA-Buttons, Stats-Grid (Collection mit Icon/Wert/Label)
- **Content Block `quick-actions`** — Aktionskarten-Raster:
  - Collection aus Icon (Bootstrap Icons Name), Titel, Beschreibung, Link, Variante (accent/primary)
  - 1→2→4 Spalten responsiv, Hover-Lift-Animation
- **News-Templates** modernisiert:
  - `Extensions/News/Templates/News/List.html` — Bootstrap-Grid → `flex flex-col gap-6`
  - `Extensions/News/Partials/List/Item.html` — horizontales Karten-Layout: Bild links (`sm:w-52`), `gradient-fire`-Kategorie-Badge, Hover-Effekte

### Abgeschlossene Phasen (Fortsetzung)

#### ✅ Phase 4 — Bestehende Content Blocks
- `accordion` — `data-bs-toggle="collapse"` → `<details>`/`<summary>` (kein JS), chevron mit `group-open:rotate-180`
- `card` + `card-group` — Bootstrap-Card → Tailwind, `.card-color-{variant}` Utilities in `main.css`
- `card-slider` — Splide bleibt, Bootstrap-Wrapper → Tailwind-Karten
- `heroslider` — Splide bleibt, `.caption` (Bootstrap) → `absolute inset-0 bg-gradient-to-r`
- `tailwind.config.js` — `safelist` für `grid-cols-[1-4]` mit `md:`/`xl:` Varianten (dynamische Spaltenzahl)
- **Bootstrap JS** bleibt vorerst in `main.js` (colormode.js-Abhängigkeit)

#### ✅ Phase 5 — Seiten & Extensions
- **News Detail** (`Extensions/News/Templates/News/Detail.html`) — Hero-Header, Article-Card mit `-mt-12 border-t-[5px] border-accent`, Related-News/Files/Links als Tailwind-Karten, Prev/Next-Nav
- **News Partials** — `Category/Items.html` (gradient-fire Badges), `List/Pagination.html` (Tailwind-Pagination), `Detail/MediaImage.html`, `Detail/MediaVideo.html` (figcaption)
- **News SearchForm** (`Templates/News/SearchForm.html`) — Tailwind form-inputs + submit
- **Feuerwehren Karte** (`Templates/Feuerwehr/List.html`) — Tailwind flex-Layout (1/4 Liste + 3/4 Karte), peer-checked Filter-Pills, accent-accent Checkboxen
- **Feuerwehren Organigramm** (`Templates/Person/List.html`, `Show.html`, `Partials/Person/AreaItem.html`) — Tailwind Karten-Hierarchy, f:debug entfernt
- **Jubiläen** (`Templates/Jubilaeum/List.html`) — Tailwind-Tabelle mit hover, gradient-fire Datum-Badges
- **feuerwehren-map.css** — `#map` und `#fwList` Höhen (600px desktop / 450px/300px mobile)
- **tailwind.config.js** — `../feuerwehren/Resources/Private/**/*.html` zu content-Pfaden hinzugefügt

### Offene Punkte

#### 🔲 Noch ausstehend (Reihenfolge)

1. **Bootstrap JS entfernen** aus `main.js` — Bootstrap-Import + colormode.js-Abhängigkeit prüfen, ggf. colormode.js direkt einbinden
2. **Kalender/calendarize-Overrides** — Template-Overrides für `lochmueller/calendarize` und `mediadreams/md_fullcalendar` erstellen (aktuell keine vorhanden, Extension rendert mit eigenen Templates)
3. **404-Seite** — neues Template + TYPO3-Fehlerseiten-Konfiguration (aktuell kein Override)

### Technische Hinweise für die Weiterarbeit

**Tailwind-Klassen in neuen Templates:**
- Keine Bootstrap-CSS-Klassen mehr verwenden (`.row`, `.col-*`, `.card`, `.btn`, `.badge` etc.)
- Tailwind-Farben über CSS Custom Properties: `bg-accent`, `text-foreground`, `bg-surface-dark` etc.
- Dark Mode via `[data-bs-theme="dark"]` — Tailwind-Selektoren mit `dark:` funktionieren automatisch
- Eigene Utilities (`.gradient-fire`, `.bg-surface-dark`) stehen immer zur Verfügung

**Fluid-Template-Besonderheiten:**
- Content Block Felder werden im Template mit dem Identifier (ohne Präfix) angesprochen: `{data.fieldname}`, `{item.fieldname}`
- Links als `type: Link` in config.yaml → `<f:link.typolink parameter="{data.link}">` im Template
- Hintergrundbilder: `style="background-image: url({f:uri.image(image: data.image.0)})"`
- Fluid inline `f:if` in Klassen: `class="{f:if(condition: '{item.variant} == accent', then: 'gradient-fire', else: 'bg-primary')}"`

**Bootstrap Icons:**
- Installiert als npm-Paket (`bootstrap-icons ^1.11.3`)
- Verwendung: `<i class="bi bi-{iconname}"></i>` — alle Icons verfügbar
- SVG-Inline für Theme-Toggle: `<svg class="bi theme-icon"><use href="#circle-half"></use></svg>` + Sprite-Block im Header
