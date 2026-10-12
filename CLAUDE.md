# KFV Freising - Claude Context

Website for **KFV Freising** (Kreisfeuerwehrverband Freising - Fire Department District Association) built with TYPO3 14 LTS. Features fire department maps, organizational charts, news, events, and general content management.

Custom extensions live in `packages/` (`sitepackage`, `feuerwehren`, `downloads`, `icsimporter`); `feuerwehren` and `downloads` have their own `CLAUDE.md`.

**Status, Historie und Fallen der Lovable-Umsetzung und Prod-Content-Migration (Phasen A–D, D1–D19):** siehe [`docs/IMPLEMENTIERUNGS-TRACKER.md`](docs/IMPLEMENTIERUNGS-TRACKER.md) — vor Content-/Migrationsarbeit lesen.

## Development Environment

### Setup
```bash
ddev start
ddev composer install
cd packages/sitepackage && npm ci

# Import database (optional)
ddev sync staging
```

`ddev sync staging|production` braucht `.env.db-sync` (aus `.env.db-sync.example` kopieren, SSH-Credentials eintragen).

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

## Styling

- `main.js` importiert nur einzelne Bootstrap-JS-Komponenten (`collapse`, `dropdown`, `offcanvas`, `tab`) statt des kompletten Bundles — jede Komponente registriert ihre `data-bs-toggle`-Data-API automatisch. Neue Bootstrap-JS-Komponente → dort ergänzen.
- Fonts: Oswald (heading) + Roboto (body) — lokal, kein Google Fonts

**Wichtig:** Alle Templates verwenden Standard-Bootstrap-Utility-Klassen (`.d-flex`, `.container`, `.row`/`.col-*`, `.vstack`, `.gap-*`, `.bg-body-secondary` etc.). Keine Tailwind-Klassen — ein früherer Tailwind-Migrationsversuch wurde verworfen, siehe [Historische Notiz](#hinweis-tailwind-migrationsversuch-verworfen) unten.

## Deployment

### Branches
- `main`: Production
- `develop`: Development
- `staging`: Staging environment

### CI/CD
GitHub Actions automatically deploy:
- Push to `staging` → Deploy to staging server
- Push to `production` → Deploy to production server

Manuell: `dep deploy staging|production` (Deployer, `deploy.php`). Bei Fehlern: SSH-Key im Agent? Serverzugang `ssh -p 222 kfvvfr@www416.your-server.de` prüfen, GitHub-Actions-Logs ansehen.

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
- Nach größeren Refactorings: alle im [Implementierungs-Tracker](docs/IMPLEMENTIERUNGS-TRACKER.md) gelisteten Kern-Seiten stichprobenartig gegenprüfen (HTTP-Status + kein sichtbarer Fehlertext)

## Content Blocks

Custom content elements in `packages/sitepackage/ContentBlocks/ContentElements/`.

Struktur je Block: `config.yaml` + `templates/frontend.html` + `templates/backend-preview.html` + `language/labels.xlf`

**Collection-Felder** in `config.yaml` erzeugen eigene DB-Tabellen. Nach Änderungen immer:
```bash
ddev exec vendor/bin/typo3 database:updateschema
ddev exec vendor/bin/typo3 cache:flush
```

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

**Grundsatzentscheidung (verbindlich):** Bootstrap CSS/SCSS + selektiv importierte Bootstrap-JS-Komponenten. `packages/sitepackage/package.json` führt `bootstrap ^5.3.8`, `main.js` importiert `bootstrap/js/dist/{collapse,dropdown,offcanvas,tab}`, Styling läuft über `Resources/Public/Scss/layout.scss` + Partials. Alle Content Blocks und Templates nutzen Standard-Bootstrap-Utility-Klassen (`.d-flex`, `.container`, `.row`/`.col-*`, `.accordion`, `data-bs-*` etc.).

<a id="hinweis-tailwind-migrationsversuch-verworfen"></a>
> **Historische Notiz:** Eine frühere Version dieser Datei dokumentierte eine "Tailwind-Migration" (Phasen 1–6, angeblich vollständig abgeschlossen: `tailwind.config.js`, `main.css` mit CSS Custom Properties, Bootstrap JS entfernt etc.). Diese Beschreibung entsprach nicht dem tatsächlichen Code-Stand — im Repo existiert kein `tailwind.config.js`/`postcss.config.js` in `sitepackage`, keine `main.css`, und `bootstrap` ist weiterhin aktive Abhängigkeit mit vollständiger SCSS-Struktur. Vermutlich wurde der Migrationsversuch verworfen/zurückgerollt, ohne die Doku nachzuziehen. Verbindlich ist der tatsächliche Code-Stand: **Bootstrap 5**, siehe oben.

Offene Punkte zum Soll-Zustand (Lovable-Design 1:1) sind im [Implementierungs-Tracker](docs/IMPLEMENTIERUNGS-TRACKER.md) laufend zu pflegen — dort **Tailwind-Referenzen auf Bootstrap-Äquivalente umstellen** (z.B. `flex flex-col gap-6` → `d-flex flex-column gap-3`, `rounded-xl` → `rounded-3`, `gradient-fire` bleibt als eigene Utility-Klasse in `_custom.scss`).

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
