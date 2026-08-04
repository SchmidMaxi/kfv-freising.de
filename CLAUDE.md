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

Website for **KFV Freising** (Kreisfeuerwehrverband Freising - Fire Department District Association) built with TYPO3 13 LTS. Features fire department maps, organizational charts, news, events, and general content management.

## Tech Stack

| Component | Technology | Version |
|-----------|------------|---------|
| CMS | TYPO3 | 13.4 LTS |
| PHP | PHP | 8.4 (CI: 8.3) |
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
| Feuerwehren-Karte | `FireStationsMap.tsx` | ✅ TYPO3 Plugin vorhanden |
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
| D10 | Beim visuellen Vergleich gefundene Bugs (neu) | Mehrere über den ursprünglichen Tracker-Scope hinausgehende, aber gravierende Bugs gefunden und behoben: Footer zeigte nur 2 von 3 Spalten (Seite 25 hatte doktype=Shortcut, fiel aus dem Footer-Menü-Query), `/verband` war eine komplett leere Landingpage (verwaistes `news_pi1`-Element von 2025 zeigte nur "No news available", keine echten Inhalte), `/aktuelles` hatte **gar keine** Content-Elemente (leere Hauptseite), `/termine`-Terminliste zeigte "There are no events" trotz vorhandener Termine (fehlendes `persistence.storagePid` im Plugin-Flexform), Suchen-Button zeigte englisches "Search" statt "Suchen". Alle behoben, siehe `OVERNIGHT_RUN_REPORT.md` für Details. **Zusätzlich entdeckt, nicht verändert:** `/termine`-Seite bindet einen externen Kalender-iFrame (open-web-calendar) ein, dessen Konfiguration auf ein Gist mit dem Titel „FFWGammelsdorfCalendar" verweist — sieht nach Daten/Copy-Paste aus einem anderen Projekt (`feuerwehr-gammelsdorf.de`) aus und gehört vermutlich nicht auf diese Seite; menschliche Prüfung nötig | ⚠️ neu, teilw. behoben |
| D11 | Farb-Token-Audit (Lovable `primary`=dark-gray-900 vs. Bootstrap `$primary`=Rot) + Dark-Mode-Pass | 2026-08-04 Follow-up-Run, ausgelöst durch gemeldeten Bug "Kommende Termine"-Widget rot statt dunkel. Root Cause (`.frame-primary-card` nutzte `var(--bs-primary)` statt festem `$gray-900`) behoben. Beim systematischen Rest-Audit zusätzlich gefunden und behoben: (1) `section`-Container-Farboptionen `bg-white`/`bg-muted`/`bg-secondary` waren im Dark Mode nicht themefähig (statische Bootstrap-Utilities, `!important`) → Überschriften auf diesen Sektionen fast unlesbar im Dark Mode; `bg-muted` war zusätzlich komplett wirkungslos (keine solche CSS-Klasse existierte). Fix: neue theme-aware `.section.bg-*`-Regeln in `_frame.scss`. (2) Calendarize-Pagination auf `/termine` rendere rohes HTML als sichtbaren Text (fehlendes `f:format.raw` bei `contentAs`-Capture) — behoben. (3) Kontaktformular-Button zeigte englisches "Submit" statt "Absenden" (fehlende Form-Framework-Übersetzung, gleiche Bug-Klasse wie der `/suche`-Fund der Vorsession) — behoben via `renderingOptions.submitButtonLabel`. Alle übrigen `bg-primary`/`text-primary`-Vorkommen sitejweit gegen Lovable-Quelle geprüft — durchweg korrekte Akzent-Rot-Verwendung, keine Änderung nötig. Details siehe `E2E_COMPARISON.md` ("Follow-up-Run 2026-08-04") und `OVERNIGHT_RUN_REPORT.md` | ✅ 2026-08-04 |
| D12 | Subpage-Hero-Banner (Nachtrag zu D11) + weitere Farb-Token-Funde | 2026-08-04: neuer ContentBlock `sitepackage/page-banner` (dunkles Vollbild-Banner, `bg-surface-dark`, H1 + Untertitel + optionaler Badge) gebaut und auf Downloads/Kontakt/Service/Verband als erstes Content-Element eingefügt; jeweils vorhandene, dadurch redundante `section-header`-Elemente ausgeblendet (hidden, nicht gelöscht). Feuerwehr- und Person-Detailseiten (`Templates/Feuerwehr/Show.html`, `Templates/Person/Show.html`) um eigenes dunkles Banner-Markup (Zurück-Link, Icon, H1, Untertitel) ergänzt. **Zusätzlich gefunden:** (1) `quick-actions`-ContentBlock, Variante „Primär (Dunkel)" nutzte ebenfalls `bg-primary` (= Rot in diesem Theme) statt Dunkel — gleiche Bug-Klasse wie D11, behoben (→ `bg-surface-dark`). (2) Downloads-Seite (uid 47) hatte durch offenbar doppelten Seed-Lauf **jedes Content-Element doppelt** (2× section-header, 2× jede card-group, 2× accordion) — komplett unbemerkt bisher; ein Satz ausgeblendet (hidden), Duplikat-Ursache (`SeedPhaseDPagesCommand`) nicht weiter untersucht, ggf. idempotent machen. **Hinweis:** `f:link.typolink` akzeptiert kein `style`-Attribut (strikte ViewHelper-Argumentliste) — beim ersten Versuch der Banner-Links zu 500-Fehler geführt, behoben durch inneres `<span style="...">`. Kein Rebuild von Vite/SCSS nötig (nur bestehende Utilities + Fluid). Keine Commits | ✅ 2026-08-04 |

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
