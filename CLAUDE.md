# KFV Freising - Claude Context

## Knowledge Graph

Unter `graphify-out/` liegt ein generierter Knowledge Graph des Projekts:

| Datei | Inhalt |
|---|---|
| `graphify-out/graph.html` | Interaktive Visualisierung (im Browser öffnen) |
| `graphify-out/GRAPH_REPORT.md` | Nodes, Edges, Communities, Hub-Konzepte — Einstiegspunkt bei unbekannten Bereichen |
| `graphify-out/graph.json` | Maschinenlesbarer Graph — Basis für alle `/graphify`-Abfragen |

### Befehle

**Graph abfragen (wenn `graphify-out/graph.json` existiert — bevorzugter Weg):**

```bash
# Architektur- und Datenflussfragen
/graphify query "Wie ist der hero-static Content Block aufgebaut und welche Felder hat er?"
/graphify query "Welche Templates überschreiben die News-Extension?"
/graphify query "Wie sind die b13/container-Elemente mit den Content Blocks verbunden?"

# Kürzester Pfad zwischen zwei Konzepten
/graphify path "hero-static" "ViteAssetCollector"
/graphify path "feuerwehren" "MapLibre"

# Verständliche Erklärung eines Knotens
/graphify explain "ContentBlock"
/graphify explain "LeidenCommunity"
```

**Graph aktualisieren (nach Code-Änderungen — nur geänderte Dateien):**

```bash
/graphify --update
```

**Vollständiger Neuaufbau (nach größerem Refactoring oder wenn Graph fehlt):**

```bash
/graphify .
```

### Workflow-Regeln für AI-Assistenten

1. **Einstieg in unbekannte Bereiche:** `GRAPH_REPORT.md` lesen → Community-Hubs zeigen die wichtigsten Cluster
2. **Konkrete Architektur-/Datenflussfrage:** `/graphify query "<Frage>"` — BFS-Traversal, ~70× komprimierter Kontext
3. **Abhängigkeit zwischen zwei Extensions/Modulen prüfen:** `/graphify path "A" "B"` — zeigt kürzesten Pfad
4. **Unbekanntes Konzept verstehen:** `/graphify explain "<Node>"` — plain-language Erklärung

**Faustregeln:**
- `graph.json` vorhanden → `/graphify query` statt manuellem Code-Lesen für Architektur-Fragen
- Erst neu bauen wenn: neue Dateien/Extensions hinzugekommen, größeres Refactoring, oder Graph fehlt

---

## Project Overview

Website for **KFV Freising** (Kreisfeuerwehrverband Freising - Fire Department District Association) built with TYPO3 14 LTS. Features fire department maps, organizational charts, news, events, and general content management.

## Tech Stack

| Component | Technology | Version |
|-----------|------------|---------|
| CMS | TYPO3 | 14.3 LTS |
| PHP | PHP | 8.5 |
| Database | MySQL | 8.0 |
| CSS Framework | Bootstrap | 5.3 (SCSS) |
| Icons | Bootstrap Icons | 1.11.3 |
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
│   ├── downloads/                # Downloads plugin extension
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
- Bootstrap 5 (SCSS) / Vite build pipeline

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

### 4. downloads (`schmid/downloads`)
Download plugin: lists fileadmin files grouped by category, filterable by category and keyword. No custom TCA/tables — reuses core `sys_category` + `sys_file_metadata.keywords`, editors tag files directly in Filelist.

**Key Features:**
- Plugin "Downloads" (FlexForm: pick one or more File Collections, optional file-extension filter)
- Frontend grouped by category, category-pill filter + client-side search box

**Path:** `packages/downloads/`
**Docs:** See `packages/downloads/CLAUDE.md`

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
# Start Vite dev server — MUSS im DDEV-Container laufen
ddev exec -d /var/www/html/packages/sitepackage npm run dev

# Access site
open https://kfv-freising.ddev.site
```

> **Wichtig:** `npm run dev` auf dem Host scheitert mit „Port 5173 is already in use". Port 5173
> ist DDEVs eigenes Port-Mapping (`web_environment: VITE_PRIMARY_PORT=5173`) und wird auf
> `127.0.0.1` gebunden; der Web-Container erreicht einen Host-Prozess dort nicht (Router
> antwortet mit 502). Der Dev-Server gehört deshalb in den Container.
>
> Läuft kein Dev-Server, liefert `praetorius/vite-asset-collector` im Development-Kontext
> trotzdem `<script src="https://kfv-freising.ddev.site:5173/…">` aus — die Seite rendert dann
> **komplett ungestylt**. Das ist kein CSS-Bug, sondern ein fehlender Dev-Server.

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

### CSS/Bootstrap Structure
```
packages/sitepackage/
├── Resources/Public/Scss/
│   ├── layout.scss                        # Haupt-Entry: Bootstrap-Imports + eigene Partials
│   ├── _variables.scss / _variables-dark.scss  # Bootstrap SASS-Variablen-Overrides (Farben, Dark Mode)
│   ├── _custom.scss                       # KFV-spezifische Anpassungen/Utilities
│   ├── contentblocks/                     # Styles je Content Block (cardslider, heroslider, ...)
│   ├── extensions/                        # Overrides für news, feuerwehr, slider, typo3
│   ├── forms/, helpers/, mixins/          # Bootstrap-Partials (aus Bootstrap-Source übernommen/angepasst)
│   └── icons.scss
└── Resources/Private/JavaScript/main.js   # importiert layout.scss + Bootstrap JS-Komponenten selektiv
```

**Bootstrap-Einbindung:**
- `bootstrap` npm-Paket (`^5.3.8`) als SASS-Quelle für `layout.scss` und als JS-Modul-Quelle
- `main.js` importiert nur einzelne Bootstrap-JS-Komponenten (`collapse`, `dropdown`, `offcanvas`, `tab`) statt des kompletten Bundles — jede Komponente registriert ihre `data-bs-toggle`-Data-API automatisch
- Dark Mode: `data-bs-theme="dark"` auf `<html>`, gesetzt via `colormode.js`
- Fonts: Oswald (heading) + Roboto (body) — lokal, kein Google Fonts

**Wichtig:** Alle Templates verwenden Standard-Bootstrap-Utility-Klassen (`.d-flex`, `.container`, `.row`/`.col-*`, `.vstack`, `.gap-*`, `.bg-body-secondary` etc.). Keine Tailwind-Klassen — ein früherer Tailwind-Migrationsversuch wurde verworfen, siehe [Historische Notiz](#hinweis-tailwind-migrationsversuch-verworfen) unten.

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

## Code Quality & Testing

**TYPO3 Coding Guidelines:** Alle PHP-Änderungen in `packages/*/Classes/`, `Configuration/` etc. müssen den [offiziellen TYPO3 Coding Guidelines](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/CodingGuidelines/Index.html) folgen (PSR-12/PER-CS-Basis, `declare(strict_types=1)`, PSR-4-Namespaces, moderne DI via Constructor-Promotion statt `inject*()`-Setter, keine `ObjectManager`-Nutzung).

**php-cs-fixer:** `typo3/coding-standards` ist als Dev-Dependency installiert (`.php-cs-fixer.dist.php` im Projekt-Root, scoped auf `packages/`). Vor jedem Commit mit PHP-Änderungen:
```bash
ddev php-cs-fixer fix --dry-run --diff   # zeigt nötige Fixes
ddev php-cs-fixer fix                    # wendet sie an
```

**Testen bei Anpassungen (verbindlich, unabhängig vom Layer):**
- **PHP:** `ddev php-cs-fixer fix --dry-run --diff` sauber; bei Schema-relevanten Änderungen (Content Blocks, TCA) zusätzlich `ddev exec vendor/bin/typo3 database:updateschema` + `ddev exec vendor/bin/typo3 cache:flush`
- **JS/Frontend:** betroffene Seite im Browser laden (Vite-Dev-Server muss laufen, siehe [Start Development](#start-development)), Konsole auf Fehler prüfen, Golden Path + Edge Cases der geänderten Funktion manuell durchklicken
- **TypoScript/Sets:** nach jeder Änderung `ddev exec vendor/bin/typo3 cache:flush`, dann betroffene Frontend-Seite(n) per curl oder Browser neu laden (TypoScript-Fehler werfen keine PHP-Exception, sondern rendern oft nur ein leeres/fehlerhaftes Fragment — stumme Fehler nicht mit "funktioniert" verwechseln)
- Nach größeren Refactorings: alle in diesem Dokument unter "Implementierungs-Tracker" gelisteten Kern-Seiten stichprobenartig gegenprüfen (HTTP-Status + kein sichtbarer Fehlertext)

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
| `hero-static` | ✅ Bootstrap | Vollbild-Hero: Bild+Gradient, Badge, H1 (2 Zeilen via `header_line2`), CTAs, Stats-Grid |
| `quick-actions` | ✅ Bootstrap | Icon-Karten-Raster (bis 4 Spalten), konfigurierbares Icon/Link/Variante |
| `section-header` | ✅ Bootstrap | Badge + H2 (mit optionalem Akzent-Span) + Einleitungstext + Link-Button |
| `feature-list` | ✅ Bootstrap | Icon-Feature-Liste (Bootstrap Icons + Titel + Beschreibung), Collection |
| `accordion` | ✅ Bootstrap | Akkordeon via `.accordion`/`data-bs-toggle="collapse"` (Bootstrap-Collapse-Komponente) |
| `card` | ✅ Bootstrap | Einzelne Karte mit `.card-color-{variant}` (default/light/dark) |
| `card-group` | ✅ Bootstrap | Responsive Grid (`.row`/`.col-*`) |
| `card-slider` | ✅ Bootstrap | Splide-Karussell in Bootstrap-Karten-Wrapper |
| `heroslider` | ✅ Bootstrap | Splide-Slider mit Bootstrap `.caption`-Overlay |

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

## Frontend-Vorschau (Design-Referenz)

**Lovable Prototype:** https://freising-fire-connect.lovable.app/
**Quellcode:** `/frontend/src/` (React/TypeScript, nur Prototyp — nicht produktiv)

### Seitenstruktur Startseite (Referenz → TYPO3 Umsetzung)

```
Startseite
├── Hero (fullbleed)                       → ContentBlock: hero-static
│     Hintergrundbild + Gradient, Badge,
│     H1 (2 Zeilen), Subheadline,
│     2 CTA-Buttons, Stats-Grid (3 Kacheln)
│
├── Quick Actions (bg-secondary)           → section-Container (bg-secondary)
│     4 Icon-Karten (Einsätze / Termine /   └── ContentBlock: quick-actions
│     Feuerwehr finden / Downloads)
│
├── Aktuelles & Termine (bg-background)   → section-Container (bg-white)
│     Links: News-Liste (2/3)              └── 2cols-Container
│     Rechts: Terminvorschau (1/3)              ├── News-Plugin (georgringer/news)
│                                               └── Calendarize / section mit Terminen
│
└── Über uns (bg-muted)                   → section-Container (bg-muted)
      Links: Text + Feature-Liste (1/2)    └── 2cols-Container (lg:w-6/12 | lg:w-6/12)
      Rechts: Karte / Placeholder (1/2)        ├── Textblock + feature-items
                                               └── Feuerwehren-Karte (Map-Plugin)
```

### Frontend-Komponenten → TYPO3 Mapping

| React-Komponente | TYPO3-Umsetzung | Status |
|------------------|-----------------|--------|
| `Hero.tsx` | ContentBlock `hero-static` | ✅ implementiert |
| `QuickActions.tsx` | ContentBlock `quick-actions` | ✅ implementiert |
| `News.tsx` (Karten) | `georgringer/news` List-Plugin | ✅ Template vorhanden |
| `News.tsx` (Termine-Sidebar) | `lochmueller/calendarize` | ✅ Template vorhanden |
| `AboutSection.tsx` (Kopfzeile) | ContentBlock `section-header` | ✅ implementiert |
| `AboutSection.tsx` (Feature-Items) | ContentBlock `feature-list` | ✅ implementiert |
| `AboutSection.tsx` (Karte) | Feuerwehren Map-Plugin | ✅ vorhanden |
| `Header.tsx` | Fluid Partial `Header.html` | ✅ implementiert |
| `Footer.tsx` | Fluid Partial `Footer.html` | ✅ implementiert |

### Container-Struktur (b13/container)

| Container | CType | Verwendung |
|-----------|-------|------------|
| Section | `section` | Sektionen mit Hintergrundfarbe + `py-16 md:py-24` Padding |
| 2 Spalten | `2cols` | Text + Bild, Text + Karte, News + Termine |
| 3 Spalten | `3cols` | Feature-Kacheln, 3-spaltige Layouts |
| 4 Spalten | `4cols` | Quick-Actions-Grid (alternativ zu quick-actions ContentBlock) |
| Tabs | `tabs` | Tab-Navigation (4 Tabs) |

**Section-Container Hintergrundfarben:**
- `bg-white` — weißer Hintergrund
- `bg-secondary` — hellgrau (wie QuickActions-Sektion)
- `bg-muted` — hellgrau/gedämpft (wie About-Sektion)
- `bg-surface-dark` — dunkelgrau (+ automatisch `text-surface-dark-foreground`)

**Wichtig: Verschachtelung von Containern**
Der `section`-Container rendert seine Kinder in einem `<div class="container py-16 md:py-24">`. Werden darin `2cols`/`3cols`/`4cols`-Container platziert, muss deren **Layout auf „Kein Layout" (frame_class=none)** gestellt werden, um doppeltes Containerizing zu vermeiden. Die `section-header`- und `feature-list`-ContentBlocks haben absichtlich KEINEN eigenen container-Wrapper und sind für die Verwendung innerhalb des section-Containers oder einer Spalte eines 2cols-Containers ausgelegt.

**ContentBlocks für Seitenstruktur (fullbleed — direkt auf der Seite platzieren):**
- `hero-static` — Vollbild-Hero (eigenes section-Element + container)
- `quick-actions` — Icon-Karten-Raster (eigenes section-Element + bg-secondary + container)

**ContentBlocks für Inhalt innerhalb von section/2cols:**
- `section-header` — Badge + H2 + optionaler Link-Button (kein eigener container)
- `feature-list` — Icon-Feature-Liste (kein eigener container)
- `accordion` — Akkordeon (kein eigener container)
- `card`, `card-group`, `card-slider` — Karten-Varianten

---

## Frontend-Redesign: Bootstrap-Umsetzung

Ein React/Tailwind-Frontend-Prototyp liegt unter `/frontend` (lovable.dev, Projekt `194228cc-7ee5-484d-b55d-ac4838948b2f`, "KFV Freising" / `freising-fire-connect`) als **reine Design-Referenz**. Ziel ist die Übernahme dieses Designs in die TYPO3 Sitepackage Extension — umgesetzt mit **Bootstrap 5**, nicht 1:1 mit Tailwind-Klassen.

**Grundsatzentscheidung (verbindlich):** Bootstrap CSS/SCSS + selektiv importierte Bootstrap-JS-Komponenten. `packages/sitepackage/package.json` führt `bootstrap ^5.3.8`, `main.js` importiert `bootstrap/js/dist/{collapse,dropdown,offcanvas,tab}`, Styling läuft über `Resources/Public/Scss/layout.scss` + Partials (siehe [CSS/Bootstrap Structure](#csbootstrap-structure)). Alle Content Blocks und Templates nutzen Standard-Bootstrap-Utility-Klassen (`.d-flex`, `.container`, `.row`/`.col-*`, `.accordion`, `data-bs-*` etc.).

<a id="hinweis-tailwind-migrationsversuch-verworfen"></a>
> **Historische Notiz:** Eine frühere Version dieser Datei dokumentierte eine "Tailwind-Migration" (Phasen 1–6, angeblich vollständig abgeschlossen: `tailwind.config.js`, `main.css` mit CSS Custom Properties, Bootstrap JS entfernt etc.). Diese Beschreibung entsprach nicht dem tatsächlichen Code-Stand — im Repo existiert kein `tailwind.config.js`/`postcss.config.js` in `sitepackage`, keine `main.css`, und `bootstrap` ist weiterhin aktive Abhängigkeit mit vollständiger SCSS-Struktur. Vermutlich wurde der Migrationsversuch verworfen/zurückgerollt, ohne die Doku nachzuziehen. Verbindlich ist der tatsächliche Code-Stand: **Bootstrap 5**, siehe oben.

### Bootstrap-Umsetzungsstand (Ist-Zustand, aus Code verifiziert)

Alle Content Blocks in `packages/sitepackage/ContentBlocks/ContentElements/` sind in Bootstrap implementiert (siehe [Content Blocks](#content-blocks)-Tabelle). Header/Footer, News-, Calendarize-, Feuerwehren- und Jubiläums-Templates laufen ebenfalls auf Bootstrap-Klassen. Offene Punkte zum Soll-Zustand (Lovable-Design 1:1) sind in [Implementierungs-Tracker](#implementierungs-tracker-startseiten-struktur-frontend--typo3) unten laufend zu pflegen — dort **Tailwind-Referenzen auf Bootstrap-Äquivalente umstellen** (z.B. `flex flex-col gap-6` → `d-flex flex-column gap-3`, `rounded-xl` → `rounded-3`, `gradient-fire` bleibt als eigene Utility-Klasse in `_custom.scss`).

---

## Implementierungs-Tracker: Startseiten-Struktur (Frontend → TYPO3)

> Status-Legende: ✅ Code fertig | 🔲 Content im Backend anlegen | ⚠️ Klärung nötig

### Phase A — ContentBlocks (Code)

| Schritt | Was | Status |
|---------|-----|--------|
| A1 | `hero-static`: `header_line2`-Feld (zweizeilige H1) | ✅ 2026-05-05 |
| A2 | `section`-Container: `py-16 md:py-24` + container-Wrapper | ✅ 2026-05-05 |
| A3 | `section`-Container: `bg-secondary` als Farboptionen | ✅ 2026-05-05 |
| A4 | ContentBlock `section-header` erstellt | ✅ 2026-05-05 |
| A5 | ContentBlock `feature-list` erstellt | ✅ 2026-05-05 |
| A6 | DB-Schema aktualisiert (alle neuen Felder/Tabellen) | ✅ 2026-05-05 |

### Phase B — Content im TYPO3-Backend anlegen

| Schritt | Was | Struktur | Status |
|---------|-----|----------|--------|
| B1 | Hero-Bereich | `hero-static` mit Hintergrundbild, H1-Zeile 1 + Zeile 2, Subheadline, 2 CTAs, 3 Stats | ✅ (Bild noch offen, siehe D6) |
| B2 | Quick-Actions-Sektion | `quick-actions` direkt auf Seite (4 Karten: Einsätze, Termine, Feuerwehr finden, Downloads) | ✅ 2026-07-24 (Links gefixt) |
| B3 | Aktuelles-Sektion (Wrapper) | `section`-Container mit `bg-white` | ✅ |
| B4 | Aktuelles-Sektion (Header) | `section-header` in B3: Badge „Neuigkeiten", H2 „Aktuelles & Termine", Link „Alle Nachrichten" | ✅ |
| B5 | Aktuelles-Sektion (2-Spalten) | `2cols`-Container in B3 mit **frame_class=none** (lg: 8/12 \| 4/12) | ✅ |
| B6 | News-Plugin | `georgringer/news` List-Plugin in linker Spalte von B5 | ✅ |
| B7 | Termine-Sidebar | `calendarize`-Plugin oder Textblock in rechter Spalte von B5 | ✅ |
| B8 | Über-uns-Sektion (Wrapper) | `section`-Container mit `bg-muted` | ✅ 2026-07-24 |
| B9 | Über-uns-Sektion (2-Spalten) | `2cols`-Container in B8 mit **frame_class=none** (lg: 6/12 \| 6/12) | ✅ 2026-07-24 |
| B10 | Über-uns (linke Spalte) | `section-header` (Badge „Über uns", H2 „Wir im Landkreis", Highlight „Freising") + Textblock + `feature-list` | ✅ 2026-07-24 |
| B11 | Über-uns (rechte Spalte) | Feuerwehren-Map-Plugin | ✅ 2026-07-24 (siehe D6: nutzt volle Filter-UI, nicht Lovables schlichtes Platzhalter-Design) |

### Phase C — Weitere Seiten (nach Priorität)

| Seite | Frontend-Referenz | Status |
|-------|-------------------|--------|
| Feuerwehren-Karte | `FireStationsMap.tsx` | ✅ 2026-09-09 auf den neuen Lovable-Stand (Filter-Sidebar + Areas) gebracht, siehe D13 |
| News-Liste | `Aktuelles.tsx` | ✅ Template vorhanden |
| News-Detail | `NewsDetail.tsx` | ✅ Template vorhanden |
| Termine | `Termine.tsx` | ✅ 2026-08-03: Jubiläen-Plugin (bestand bereits, Seite 13) + ergänzte Terminliste (`calendarize_list`) |
| Einsätze | `Einsaetze.tsx` | ✅ 2026-08-03: News-Kategorie "Einsatz" (Seite 32), 5 Beispieleinträge — Default, siehe D1 |
| Ausbildung | `Ausbildung.tsx` | ✅ Content vorhanden (Seite 58) |
| Service | `Service.tsx` | ✅ 2026-08-03: Hub-Content (Downloads/Formulare/Kontakt-Karten + FAQ), Seite 39 entsperrt |
| Downloads | `Downloads.tsx` | ✅ Vollständiger Katalog vorhanden (Seite 47) |
| Kontakt | `Kontakt.tsx` | ✅ Formular vorhanden (Seite 17); Hauptnav-Kontakt (Seite 63) 2026-08-03 als Shortcut auf 17 konfiguriert statt Duplikat |
| Verband | `Verband.tsx` | ✅ Landing + Subseiten (Über uns/Organigramm/Ansprechpartner) — IA bewusst abweichend vom Prototyp (eigene Unterseiten statt All-in-one) |
| Suche | `SearchResults.tsx` | ✅ 2026-08-03: neue Seite (`/suche`, `nav_hide=1`), `typo3/indexed-search` Set eingebunden, Bootstrap-Templates (`packages/sitepackage/Resources/Private/Extensions/IndexedSearch/`) — Live-Suche noch nicht im Browser verifiziert (siehe `E2E_COMPARISON.md`) |
| Musterseiten Tabs/Slider | `ShowcaseTabs.tsx`, `ShowcaseSlider.tsx` | ✅ 2026-08-03: neue Musterseiten unter Musterseiten-Ordner (`/tabs`, `/slider`) |
| Feuerwehr-Detail (`/feuerwehr/:id`) | `FeuerwehrDetail.tsx` | ⚠️ 2026-08-03: `FeuerwehrController::showAction` + Template gebaut, aber **nur echte DB-Felder** (Adresse, Gründungsjahr, Fahrzeugkategorien, Jubiläen) — Mitgliederzahlen/Statistik/Kontakt/Galerie aus dem Prototyp bewusst nicht übernommen (unbestätigte Fakten zu echten Feuerwehren), siehe D7. Kartenmarker verlinken noch nicht dorthin (JS, siehe D8) |
| Inspektion-Detail (`/inspektion/:id`) | `InspektionDetail.tsx` | ⚠️ Person-Show-Template unverändert — Bio/Auszeichnungen/Kontakt aus dem Prototyp bewusst nicht übernommen (unbestätigte Personendaten zu echten, namentlich genannten Funktionsträgern), siehe D7 |

### Phase D — Offene technische Punkte

| Punkt | Beschreibung | Status |
|-------|-------------|--------|
| D1 | Einsätze-Seite | Datenquelle: News-Kategorie "Einsatz" (manuell gepflegt) als Default gewählt 2026-08-03 — Anbindung an echte Quelle (Alamos/Fax-to-Web) bleibt offen. 2026-08-04: Seite erneut geprüft, rendert weiterhin korrekt, kein Code-Änderungsbedarf | ⚠️ |
| D2 | Kontaktformular | `typo3/cms-form` (Form-Framework) verwendet, `kontaktformular.form.yaml`, Mail-Finisher → info@kfv-freising.de | ✅ |
| D3 | Tabs ContentBlock (b13/container) | 2026-08-04: Visuell geprüft — Musterseite `/tabs` rendert entgegen der bisherigen Notiz **keine** Tabs (leeres `pi_flexform` auf Content-Element uid 142, Template bricht mangels `tab_1_title` etc. früh ab). Flexform mit 4 Tab-Titeln/Icons nachgetragen, rendert jetzt korrekt (Tab 1–4 mit Demo-Inhalten) | ✅ 2026-08-04 |
| D4 | Dark-Mode Feuerwehren-Karte | `map-feuerwehren.js`: `buildBaseStyle(theme)` mit Light-/Dark-Farbpalette (Hintergrund/Wasser/Landbedeckung/Straßen/Grenzen), `MutationObserver` auf `data-bs-theme` ruft bei Themewechsel `map.setStyle()` + lädt Overlays/Suchdaten neu. Per Screenshot verifiziert (Light→Dark-Toggle auf `/inspektion/feuerwehren`) | ✅ 2026-08-04 |
| D5 | ICS-Importer | Bei Prüfung festgestellt: Scheduler-Task-Typ war bereits in `ext_localconf.php` registriert **und** 3 Task-Instanzen (Inspektion/Kreisjugendfeuerwehr/Leistungsabzeichen) bereits in der DB angelegt. `scheduler:list` zeigt alle 3, `scheduler:run --task=1 --force` lief fehlerfrei durch. Kein Code-Änderungsbedarf — Tracker war veraltet | ✅ 2026-08-04 (bereits vorhanden, nur verifiziert) |
| D6 | Startseite Hero-Bild + Feuerwehren-Karte in B11 | Hero (uid 83) hat weiterhin kein Hintergrundbild — Code fällt bereits auf `.gradient-fire`-Utility zurück (kein Codeänderungsbedarf, war schon so implementiert), **echtes Feuerwehr-Foto von Nutzer noch ausständig**. Feuerwehren-Karte in der Über-uns-Sektion: beim visuellen Vergleich 2026-08-04 wirkte die kompakte Einbettung auf der Startseite deutlich unaufdringlicher als befürchtet (eigenes `compact`-Template ohne Filter-UI) — Einschätzung "wuchtiger als Referenz" damit relativiert | ⚠️ (nur Foto offen) |
| D7 | Feuerwehr-/Personen-Detaildaten | 2026-08-04 (explizite Nutzerentscheidung für diesen Lauf): Platzhalter-Abschnitte (Mitglieder, Kontakt, Einsatzstatistik, Bildergalerie bei Feuerwehr; Werdegang, Auszeichnungen, Kontakt bei Person) ergänzt — visuell klar als Platzhalter markiert (`.placeholder-card`, gestrichelter Rahmen, Badge „Beispielwert — noch zu bestätigen"), keine erfundenen Zahlen als Fakt dargestellt. Neue Utility-Klassen in `_custom.scss`. **Reale Daten weiterhin ausständig** | ✅ Platzhalter umgesetzt, ⚠️ echte Daten offen |
| D8 | Feuerwehren-Karte → Detailseite | `map-feuerwehren.js`: Marker-Klick (Popup) und Sidebar-Listeneinträge verlinken jetzt auf `Feuerwehr::showAction` (`/inspektion/feuerwehren?tx_feuerwehren_karte[...]`). cHash-Pflicht für diese Parameter-Kombination global deaktiviert (`config/system/additional.php`, `excludedParameters` — Cache-Key bleibt korrekt granular, nur die cHash-Prüfung entfällt). Popup/Listen-HTML auf sichere DOM-Konstruktion statt `innerHTML`-Interpolation umgestellt | ✅ 2026-08-04 |
| D9 | Visueller E2E-Vergleich | 2026-08-04 durchgeführt: `chrome-devtools-mcp` war im Environment zwar konfiguriert, aber die Chrome-Binary scheiterte an einer fehlenden Systembibliothek (`libasound.so.2`) und den fehlenden `--headless`-Flags in der zu Sessionbeginn eingefrorenen Server-Konfiguration. Lokal ohne root behoben (`.deb` per `apt-get download` entpackt, Chrome-Binaries auf Wrapper-Skripte mit `LD_LIBRARY_PATH` umgestellt) und ein zweiter, korrekt konfigurierter `chrome-devtools-mcp`-Prozess direkt per stdio/JSON-RPC angesteuert, um echte Screenshots zu erhalten. `.mcp.json` für künftige Sessions um `--headless --isolated` ergänzt. Ergebnisse siehe `E2E_COMPARISON.md` und `OVERNIGHT_RUN_REPORT.md` | ✅ 2026-08-04 |
| D10 | Beim visuellen Vergleich gefundene Bugs (neu) | Mehrere über den ursprünglichen Tracker-Scope hinausgehende, aber gravierende Bugs gefunden und behoben: Footer zeigte nur 2 von 3 Spalten (Seite 25 hatte doktype=Shortcut, fiel aus dem Footer-Menü-Query), `/verband` war eine komplett leere Landingpage (verwaistes `news_pi1`-Element von 2025 zeigte nur "No news available", keine echten Inhalte), `/aktuelles` hatte **gar keine** Content-Elemente (leere Hauptseite), `/termine`-Terminliste zeigte "There are no events" trotz vorhandener Termine (fehlendes `persistence.storagePid` im Plugin-Flexform), Suchen-Button zeigte englisches "Search" statt "Suchen". Alle behoben, siehe `OVERNIGHT_RUN_REPORT.md` für Details. **2026-08-05 abgeschlossen:** externes Kalender-iFrame (`tt_content` uid 44, Seite 13/Termine, CType `html`) bestätigt als Fremd-Content aus `feuerwehr-gammelsdorf.de` (Gist „FFWGammelsdorfCalendar") — ausgeblendet (`hidden=1`, nicht gelöscht). Seite nutzt bereits das korrekt konfigurierte `calendarize_list`-Plugin (uid 156, storagePid 60,58,59) als alleinige Terminquelle | ✅ 2026-08-05 |
| D11 | Farb-Token-Audit (Lovable `primary`=dark-gray-900 vs. Bootstrap `$primary`=Rot) + Dark-Mode-Pass | 2026-08-04 Follow-up-Run, ausgelöst durch gemeldeten Bug "Kommende Termine"-Widget rot statt dunkel. Root Cause (`.frame-primary-card` nutzte `var(--bs-primary)` statt festem `$gray-900`) behoben. Beim systematischen Rest-Audit zusätzlich gefunden und behoben: (1) `section`-Container-Farboptionen `bg-white`/`bg-muted`/`bg-secondary` waren im Dark Mode nicht themefähig (statische Bootstrap-Utilities, `!important`) → Überschriften auf diesen Sektionen fast unlesbar im Dark Mode; `bg-muted` war zusätzlich komplett wirkungslos (keine solche CSS-Klasse existierte). Fix: neue theme-aware `.section.bg-*`-Regeln in `_frame.scss`. (2) Calendarize-Pagination auf `/termine` rendere rohes HTML als sichtbaren Text (fehlendes `f:format.raw` bei `contentAs`-Capture) — behoben. (3) Kontaktformular-Button zeigte englisches "Submit" statt "Absenden" (fehlende Form-Framework-Übersetzung, gleiche Bug-Klasse wie der `/suche`-Fund der Vorsession) — behoben via `renderingOptions.submitButtonLabel`. Alle übrigen `bg-primary`/`text-primary`-Vorkommen sitejweit gegen Lovable-Quelle geprüft — durchweg korrekte Akzent-Rot-Verwendung, keine Änderung nötig. Details siehe `E2E_COMPARISON.md` ("Follow-up-Run 2026-08-04") und `OVERNIGHT_RUN_REPORT.md` | ✅ 2026-08-04 |
| D12 | Subpage-Hero-Banner (Nachtrag zu D11) + weitere Farb-Token-Funde | 2026-08-04: neuer ContentBlock `sitepackage/page-banner` (dunkles Vollbild-Banner, `bg-surface-dark`, H1 + Untertitel + optionaler Badge) gebaut und auf Downloads/Kontakt/Service/Verband als erstes Content-Element eingefügt; jeweils vorhandene, dadurch redundante `section-header`-Elemente ausgeblendet (hidden, nicht gelöscht). Feuerwehr- und Person-Detailseiten (`Templates/Feuerwehr/Show.html`, `Templates/Person/Show.html`) um eigenes dunkles Banner-Markup (Zurück-Link, Icon, H1, Untertitel) ergänzt. **Zusätzlich gefunden:** (1) `quick-actions`-ContentBlock, Variante „Primär (Dunkel)" nutzte ebenfalls `bg-primary` (= Rot in diesem Theme) statt Dunkel — gleiche Bug-Klasse wie D11, behoben (→ `bg-surface-dark`). (2) Downloads-Seite (uid 47) hatte durch offenbar doppelten Seed-Lauf **jedes Content-Element doppelt** (2× section-header, 2× jede card-group, 2× accordion) — komplett unbemerkt bisher; ein Satz ausgeblendet (hidden), Duplikat-Ursache (`SeedPhaseDPagesCommand`) nicht weiter untersucht, ggf. idempotent machen. **Hinweis:** `f:link.typolink` akzeptiert kein `style`-Attribut (strikte ViewHelper-Argumentliste) — beim ersten Versuch der Banner-Links zu 500-Fehler geführt, behoben durch inneres `<span style="...">`. Kein Rebuild von Vite/SCSS nötig (nur bestehende Utilities + Fluid). Keine Commits | ✅ 2026-08-04 |
| D13 | Feuerwehren-Kartensuche auf neuen Lovable-Stand (`FireStationsMap.tsx`, Commit `f5b77c2` „Feuerwehrenfilter & Areas") | 2026-09-09: `/frontend` war 16 Commits hinter `origin/main` (kein Submodul, nur nested Repo) — auf `origin/main` gesetzt, der eine lokale Commit `688b4c8` (3 Lint-Fixes) liegt auf Branch `backup-next-steps`. Umgesetzt: Layout auf Karte 2/3 + Sidebar 1/3 (Filter-Card mit Aktiv-Zähler, Fahrzeug-Checkboxen im 2-Spalten-Raster, Areas als `form-switch`, „Filter zurücksetzen"), sticky Listen-Card mit Suchfeld, Stats-Kacheln, Fahrzeug-Badges (max. 3 + „+n"), Legende und Klick-Hinweis als Karten-Overlays, Detailkarte unter der Karte statt Popup. **Suche läuft server-seitig landkreisweit** (neuer `q`-Parameter in `SearchService`/`FeuerwehrRepository`; bei gesetztem `q` wird die bbox verworfen, Karte zoomt per `fitBounds` auf die Treffer). **Neue DB-Felder** `mitglieder` (int) und `typ` (`''`/`stuetzpunkt`/`ort`) in Model/TCA/`ext_tables.sql` — bewusst leer gelassen, UI blendet Mitglieder-Kachel und Typ-Badge aus, solange nichts gepflegt ist (Marker dann blau „ohne Typangabe"). Markerfarben: Stützpunkt `$fire`, Ortsfeuerwehr `$gray-800` — Lovables neutrales `primary` ist hier **nicht** `$primary` (= Rot), gleiche Fehlerklasse wie D11. Abweichungen vom Prototyp: UND/ODER-Modus bleibt (Obermenge von Lovables ODER), KBM/KBI bleiben gegenseitig exklusiv, Gemeinden-Layer ist jetzt wie in Lovable per Default **aus**. Gefunden und behoben: `fitBounds` mit symmetrischem Padding schob Treffer hinter die Legende (Hallbergmoos projizierte auf (201,581), Legende y≈555–640) → asymmetrisches Padding. Verifiziert per Puppeteer-E2E (82 Einträge initial, Detailkarte, `q=moos` → 2 landkreisweite Treffer, Reset, Leerzustand, Dark Mode, keine Konsolenfehler) und API-Tests inkl. LIKE-Wildcard-Eingabe (`q=%` wird normalisiert, liefert keine Vollausgabe). Keine Commits | ✅ 2026-09-09 |
| D14 | Produktions-Content-Migration Phase 1–3 (Plan: `.claude/plans/partitioned-enchanting-bear.md`) | 2026-09-22: Content aus Prod (kfv-freising.de, altes Design) per Live-Crawl in neue Content Blocks übernommen. **Phase 1 Fachbereiche** (uid 76–90, `/fachbereiche` + 14 Unterseiten, `page-banner`+`section-header`+`card-group`/`textmedia`) — Slug-Bug gefunden+gefixt (14 Detailseiten landeten initial flach auf Root-Ebene statt unter `/fachbereiche/`, da Slug-Feld nur das letzte Segment statt vollem Pfad enthielt). **Phase 2 Personen/Organigramm**: Area/Person-Daten waren lokal größt. bereits vorhanden, nur Lücken gefüllt (Area uid 24 „Stadtbrandmeister" + Person uid 23), `/verband/ansprechpartner` (uid 44) mit Organigramm-Plugin befüllt, neue Seite „Schatzmeister" (uid 120). Bug-Fix `PersonController::showAction`/`Person/Show.html`: totes `{person.gemeinde}`-Binding → `{area.gemeinden}` (Area wird jetzt im Controller geladen und an View übergeben). **Phase 3 Ausbildung-Baum**: Realbrandausbildung + Leistungsabzeichen-Bewerbe-Baum (uid 121–135) neu unter Ausbildung, Stub uid 60 „Lehrgänge" befüllt, uid 59 „Jugendarbeit" von pid 58 nach pid 2 verschoben (war fälschlich unter Ausbildung verschachtelt) + befüllt + Kind „Kinderfeuerwehr" (uid 138), neue Top-Level-Seite „Feuerwehrsport" (uid 136). Alle drei Phasen als `fork`-Subagenten mit MCP-`typo3`-Tools (`WriteTable`) umgesetzt — Erkenntnis für künftige Runs: `WriteTable` schreibt in einen TYPO3-Workspace (`sys_workspace` uid 1), nicht live; Publish-Workflow: `sys_workspace.publish_time` per SQL auf Vergangenheit setzen → `ddev exec vendor/bin/typo3 workspace:autopublish` → `cache:flush`; neue Seiten starten zusätzlich mit `hidden=1`. Alle Ziel-URLs aller drei Phasen per `curl` auf HTTP 200 gegengeprüft (Koordinator-Stichprobe, nicht nur Subagent-Selbstauskunft). **Offen:** Bilder/Icons der migrierten Prod-Seiten (Fachbereiche-SVGs, Ausbildungs-Badges) wurden bewusst **nicht** heruntergeladen (Kostenabwägung während der Ausführung) — weicht von der ursprünglichen Nutzerentscheidung „Bilder automatisch übernehmen" ab, bei Bedarf nachträglich zu ergänzen; Namens-Widerspruch Fachbereich 1 (Landing-Karte „Neumair Johannes" vs. alte Detailseite „Melanie Walter", Landing-Wert übernommen) noch gegenzuprüfen; ASLB-Seite (uid 127) hat Platzhaltertext für Bronze-/Silber-Richtlinien-PDFs (URLs beim Crawl nicht auflösbar); Person-Kontaktfelder (Telefon/E-Mail) weiterhin nicht im Datenmodell, Platzhalter „folgt" bleibt. Phase 4 (Rand-/Sonderseiten: faz, mp-feuer, corona, stromausfall, wiki/anleitungen-lehrgangsportal etc.) bewusst nicht Teil dieses Durchlaufs, siehe Plan-Datei. **Bild-Nachtrag (selber Tag):** Überraschung — lokales `fileadmin` enthielt bereits den kompletten alten Bilder-/PDF-Bestand aus Produktion (vermutlich aus einem früheren `sync --with-files`-Lauf), kein erneuter Download nötig, nur Verknüpfung. 14 Fachbereiche-Icons (`sys_file` uid 578–590, 1305) an `sitepackage_cardgroup_items.image` gehängt; dafür `card-group`-Template (`quick-action`-Case) um `f:if` auf `item.image` erweitert (zeigt Bild statt Bootstrap-Icon, wenn gesetzt) — `packages/sitepackage/ContentBlocks/ContentElements/card-group/templates/frontend.html`. 9 Ausbildungs-Badges/-Fotos an `tt_content.assets` der jeweiligen `textmedia`-Elemente gehängt (Realbrandausbildung, ASLB, Löscheinsatz, Hilfeleistungseinsatz, BayJLSP, DJLSP, CTIF, Bundeswettbewerb, Deutsche Jugendflamme, Wissenstest, Kinderfeuerwehr-Logo). Schatzmeister und Feuerwehrsport: kein passendes Bild in Prod gefunden, bewusst ausgelassen statt erfunden. Alle per `curl` gegen die finalen Seiten verifiziert (echtes `<img>` mit erwartetem Dateinamen). Nebenfund: ASLB Bronze/Silber/Gold-Richtlinien-PDFs (von Phase 3 als „nicht auflösbar" vermerkt) liegen ebenfalls schon lokal vor (`sys_file` uid 2380–2383) — für späteren Feinschliff der ASLB-Seite nutzbar. Keine Commits (reine DB-Änderungen + ein Fluid-Template-Edit) | ⚠️ Feinschliff offen (Namens-Widerspruch FB1, Person-Kontaktfelder, Phase 4), Struktur+Text+Bilder live |
| D15 | Nav-Konsolidierung nach D14 (Nutzer-Feedback: „zu viele Seiten in der Navigation") | 2026-09-22: Root Cause geprüft — `Header.html` rendert nur 2 Menü-Ebenen (Top-Level + direkte Kinder, keine Rekursion in `child.children`), daher war der tief verschachtelte Ausbildung-Baum aus D14 nie im Dropdown sichtbar; echtes Problem war die 14 direkten Fachbereiche-Kinder (riesiges Dropdown) und die auf 9 gewachsene Top-Level-Nav. Nutzer hat für alle 3 Punkte die konsolidierende Option gewählt. **Fachbereiche:** 14 Einzelseiten (uid 77–90) gelöscht, Inhalte als 14-Item-`accordion` (layout `icon-badge`) auf Landing-Seite 76 (tt_content uid 249); bestehendes `card-group` (uid 201, mit den echten SVG-Icons aus D14) bleibt als visueller Index erhalten, Item-Links zeigen jetzt per Anker (`t3://page?uid=76#collapse249-N`) auf die passenden Accordion-Items statt auf gelöschte Seiten. **Leistungsabzeichen-Bewerbe:** 13 Einzelseiten (uid 123–135, inkl. der beiden Landing-Seiten „Erwachsene"/„Jugend") gelöscht, Inhalte als 1 gruppiertes `accordion` (layout `grouped`, 3 Gruppen „Allgemein"/„Erwachsene"/„Jugend", 11 Items) auf Seite 122 (tt_content uid 251), PDF-Link (Anmeldeformular) im Bodytext erhalten. **Top-Level-Nav:** Jugendarbeit (uid 59) + Feuerwehrsport (uid 136) von pid 2 nach pid 58 (Ausbildung) verschoben — Haupt-Nav wieder bei 7 Top-Level-Punkten (Aktuelles, Verband, Inspektion, Ausbildung, Service, Fachbereiche, Kontakt), Ausbildung-Dropdown jetzt 5 Kinder (Feuerwehrsport, Jugendarbeit, Lehrgänge, Realbrandausbildung, Leistungsabzeichen & Bewerbe), Fachbereiche-Dropdown entfällt (0 Kinder). **Technische Falle für künftige Accordion-Writes:** `WriteTable` schreibt verschachtelte Inline-Relationen (`sitepackage_accordion_groups[].group_items[]`, 2 Ebenen tief ab `tt_content`) beim ersten `create`-Aufruf still auf 0 Items statt Fehler zu werfen — Fix: Gruppen zuerst ohne `group_items` anlegen/publizieren, dann `group_items` in einem zweiten `update` direkt auf die Gruppen-Records nachliefern, und zwar mit vollen Record-Daten-Objekten statt UID-Arrays (`group_items` gilt als „embedded", nicht „independent" Relation — UID-Array-Schreibweise wirft `Validation error: Embedded inline relations must contain record data arrays`). Alle betroffenen und gelöschten URLs per `curl` gegengeprüft (200 bzw. erwartetes 404), Nav-Struktur per `GetPageTree` verifiziert. Keine Commits (reine DB-Änderungen) | ✅ 2026-09-22 |

| D16 | Seitenbaum auf Lovable-Nav ausgerichtet + Demo-Content gegen Prod-Content getauscht | 2026-09-24: **Hauptnav** jetzt Aktuelles · Verband · Feuerwehren · Ausbildung · Service · Kontakt (wie Lovable). Termine (13) → unter Aktuelles; Feuerwehren-Karte (45) → Top-Level, Slug `/feuerwehren` (`DETAIL_BASE_PATH` in `map-feuerwehren.js` angepasst, sysfolder 46 → `/erweiterungen/feuerwehren`); Inspektion (11) + Fachbereiche (76) → unter Verband; Berichte & News (18) = Shortcut auf Aktuelles; Service/Downloads (61) = Shortcut auf 47; Lehrgänge (60) wieder in Nav; Schatzmeister (120) `nav_hide`. **Neu aus Prod gecrawlt**: Vorstandschaft (169), Satzung & Ehrenordnung (170), Gründungsfeste (171, unter Termine), Über uns (42 = Geschichte), Spenden (23), Inspektion-Landing, Mitglieder (43), Ehrenkreisbrandinspektion (57), Kreiseinheiten (40) + ABC-Zug (41) + UG-ÖEL (56). **Demo-Fakten korrigiert**: Hero-Stats/-Text + Feature-Liste (Prod: 82 FF, 44 JF, 3.722 Helfer statt „42 Feuerwehren“), Kontakt-Telefon/-Adresse (Header-/Impressum-Werte), Öffnungszeiten ausgeblendet. **Prüf-Ordner `Zu prüfen` (uid 168, alles hidden, LIES-MICH-Seite 182)**: Lovable-Demo-CEs (Ausbildung, Downloads-Karten, FAQ, Verband-Intro, Kreiseinheiten-Karten), Duplikate (Organigramm-Plugin ex Inspektion, Einsätze-Liste ex Mitglieder, Formulare 14, FAQ-Stub 62), Fremd-Content FF Gammelsdorf (ex Über uns/Ansprechpartner), Phase-4-Seiten aus Prod (FAZ, MP-Feuer, Brandschutzdienststelle, Vertreter Kommandanten, Keine Gewalt gegen Retter, Stromausfall, Corona). Redirects (301) für `/inspektion/feuerwehren`, `/verband/geschichte`, `/verband/ehrennadel`, `/verband/spenden`. Neues Tool: `vendor/bin/typo3 sitepackage:datahandler:apply <json>` (DataHandler live, statt MCP-Workspace-Umweg). **Fallen:** neue CEs mit positiver pid landen oben (sorting danach setzen); `sys_file_reference`-NEW-IDs in `assets` wurden nicht remappt → Referenzen im 2. Lauf per bestehender uid zuweisen; `section-header` ohne umgebenden Container klebt am Rand. Keine Commits | ✅ 2026-09-24 |

| D17 | Typografie-Abgleich + Kartenansicht auf Lovable `9a112b2` („Overlay auf Karte") | 2026-09-28: Lovable-Preview braucht Login → Referenz lokal aus `/frontend` gestartet (`npx vite --port 8080`, vorher `npm ci` wegen fehlendem `@lovable.dev/mcp-js`), per Puppeteer Computed Styles verglichen. **Typo global** (`_variables.scss`/`_reboot.scss`): Nav-Links 14px/500 in Body-Farbe statt 16px/400 mit 65 % Deckkraft, `$gray-900` → `#253141` (Lovable-Foreground), `$headings-line-height` 1 → 1.2 (h4–h6: 1.4, h5/h6 jetzt semibold), Buttons 500 + Lovable-Maße (h-10 / lg h-11 px-8), Inputs 14px/h-10, Badges semibold. **Header**: Nav zentriert zwischen Logo und Icons, Hover als Fläche, Chevron statt Caret, Logo md 3.5rem. **Hero**: H1 36/48/60px, Lead 20px/85 %, CTAs in Oswald (`.font-heading`). **Footer**: Titel h5, Links 16px mit Lovable-Abstand, Meta-Links hatten gar keinen Abstand („ImpressumDatenschutz…") → `column-gap`. **Karte**: Seitenkopf (Zurück-Link, Badge, `.page-title`-H1, Intro), Detailkarte jetzt Overlay unten rechts auf der Karte mit Schließen-Button + „Details ansehen", Filter-Card per Bootstrap-Collapse standardmäßig zu (Zähler + „Aktiv: …"-Zeile im zugeklappten Zustand), Listen-Card mit Header „Feuerwehren", Legende/Hinweis in 12px. Abweichungen: Hinweis bleibt oben links (MapLibre-Zoom oben rechts), Listeneinträge behalten die Adresse. Per Puppeteer verifiziert (Light/Dark, Mobile 390px, Filter auf/zu/Reset, keine Konsolenfehler). **Offen/außerhalb Scope**: Kontaktseite hat unabhängig davon ein kaputtes Layout (Formular vollbreit, fehlende Abstände). Keine Commits | ✅ 2026-09-28 |

| D18 | Sonderfall Stadt Freising: Abschnittsfläche fehlte auf der Karte | 2026-09-28: Freising ist Kreisstadt, der Kommandant der FF Freising ist zugleich SBI und hat **keinen KBM** unter sich. Die Gemeinde Freising (uid 9) hängt deshalb per `kbm_area` direkt am KBI-Area uid 22 statt an einem KBM. `OverlaysService` baute KBI-Flächen aber nur aus den Gemeinden der Kind-KBMs → für den Abschnitt entstand keine Fläche, sichtbar war nur die normale Gemeindegrenze. Fix in `packages/feuerwehren/Classes/Service/Api/OverlaysService.php`: KBI-Fläche = direkt zugeordnete Gemeinden + Gemeinden der KBMs (neuer Helper `collectGemeindeUids()`); KBI-Bereiche mit direkten Gemeinden erscheinen zusätzlich in der KBM-Ansicht, damit dort keine Lücke im Landkreis bleibt. TCA `tx_feuerwehren_domain_model_area`: Feld `gemeinden` jetzt auch bei `type=kbi` sichtbar (`displayCond FIELD:type:IN:kbm,kbi` + kbi-`showitem`). **Daten** (per `sitepackage:datahandler:apply`, weich gelöscht): Area 22 umbenannt „Stadt Freising“ → „Abschnitt 6 (Stadt Freising)“; Area 24 „Stadtbrandmeister“ (KBM unter 22, bei D14 angelegt, fachlich falsch) und die nur dort verknüpfte Person uid 23 „Christian Schäffler“ gelöscht. Verifiziert über `/api/feuerwehren/overlays` (Abschnitt 6 in KBI- und KBM-Features) und `/verband/ansprechpartner` (HTTP 200, kein Stadtbrandmeister mehr), `php-cs-fixer` sauber. Nicht im Browser angesehen. **Offen:** es gibt keinen „Abschnitt 1“ (nur 2–6) — beim Nutzer nachgefragt, noch keine Antwort. Keine Commits | ✅ 2026-09-28 |

### Technische Hinweise für die Weiterarbeit

**Bootstrap-Klassen in neuen Templates:**
- Standard-Bootstrap-5-Utility-Klassen verwenden (`.row`, `.col-*`, `.card`, `.btn`, `.badge`, `.d-flex`, `.gap-*` etc.)
- Farben/Design-Tokens über SASS-Variablen-Overrides in `_variables.scss`/`_variables-dark.scss` (nicht CSS Custom Properties à la Tailwind)
- Dark Mode via `data-bs-theme="dark"` auf `<html>` (Bootstrap-5.3-natives Attribut, gesetzt durch `colormode.js`) — Storage-Key `kfv-ui-theme`, Toggle via `data-theme-toggle`-Attribut
- Eigene Utilities (`.gradient-fire` etc.) in `_custom.scss` außerhalb der Bootstrap-Quelle ergänzen, nicht überschreiben

**Fluid-Template-Besonderheiten:**
- Content Block Felder werden im Template mit dem Identifier (ohne Präfix) angesprochen: `{data.fieldname}`, `{item.fieldname}`
- Links als `type: Link` in config.yaml → `<f:link.typolink parameter="{data.link}">` im Template
- Hintergrundbilder: `style="background-image: url({f:uri.image(image: data.image.0)})"`
- Fluid inline `f:if` in Klassen: `class="{f:if(condition: '{item.variant} == accent', then: 'gradient-fire', else: 'bg-primary')}"`

**Bootstrap Icons:**
- Installiert als npm-Paket (`bootstrap-icons ^1.11.3`)
- Verwendung: `<i class="bi bi-{iconname}"></i>` — alle Icons verfügbar
- Theme-Toggle: `<button data-theme-toggle>` + zwei Icons `bi-sun-fill`/`.show-light-mode` und `bi-moon-stars-fill`/`.show-dark-mode`, Ein-/Ausblenden über `[data-bs-theme]`-Selektoren in `_header.scss` (kein Tailwind `dark:`-Modifier)
