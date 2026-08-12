# Overnight Run Report — 2026-08-03/04

Unattended session covering: (1) a real browser-based visual E2E comparison against the
Lovable prototype, (2) fixing the diffs found, (3) closing out tracker Phase D items, and
(4) adding clearly-labeled placeholder content per explicit standing instructions. No git
commits were made — everything below is uncommitted working-tree state for review.

## TL;DR

The visual comparison surfaced several **real, previously-unnoticed bugs** that the earlier
HTTP-only check couldn't catch (empty pages, a broken plugin config, an English button label,
a non-functional demo page). All were root-caused and fixed. Phase 3 tracker items (D4/D5/D8)
are done; D5 turned out to already be working. Phase 4 placeholders (D6/D7) are in place.

## Environment note (read this if screenshots/browser tools misbehave next time)

`chrome-devtools-mcp` was configured in `.mcp.json` but two things blocked it in this
environment:

1. The MCP server's own Chrome binary (`~/.cache/puppeteer/chrome`) failed to start at all —
   missing shared library `libasound.so.2`. Fixed locally (no root needed): downloaded
   `libasound2t64` via `apt-get download`, extracted with `dpkg-deb -x` into a user-writable
   directory, and replaced the `chrome` / `chrome-headless-shell` binaries under
   `~/.cache/puppeteer/` with tiny wrapper scripts that set `LD_LIBRARY_PATH` before exec'ing
   the real binary. This fix is local-machine-level (under `~/.cache`), not part of the repo.
2. `.mcp.json` didn't pass `--headless`, and Chrome has no display in this container, so even
   with the library fixed, the already-connected MCP session (frozen at session start) kept
   failing. I added `--headless --isolated --chrome-arg=--disable-gpu
   --chrome-arg=--disable-dev-shm-usage` to `.mcp.json` for future sessions. For *this*
   session, since the already-running connection couldn't pick up the new args, I drove a
   second, correctly-configured `chrome-devtools-mcp` process directly over its stdio
   JSON-RPC protocol from Bash to actually take the screenshots used below.

If `mcp__chrome-devtools__*` tools return `Target closed` again next time, check `.mcp.json`
was actually picked up (it now has `--headless`) — if it still fails, the library fix above may
need repeating if `~/.cache/puppeteer` was reset.

## What was compared

Real Lovable (`https://freising-fire-connect.lovable.app/`) vs. real TYPO3
(`https://kfv-freising.ddev.site`) screenshots at ~1440×900 and spot-checks at 390×844, plus
`list_console_messages` on every TYPO3 page visited. Full route-by-route results are in
`E2E_COMPARISON.md` (rewritten this session). No JavaScript console errors were found anywhere;
one harmless recurring warning (`software WebGL fallback`) is a headless-Chrome artifact, not a
real bug.

## Bugs found and fixed

| # | Where | Root cause | Fix |
|---|---|---|---|
| 1 | Footer (site-wide) | Footer "Service" nav column missing — `pages.uid=25` was `doktype=4` (Shortcut); TYPO3's `special=directory` menu processor drops such entries even though their children existed | Changed `doktype` to `1` (standard page). Footer now shows all 3 columns |
| 2 | `/aktuelles` | Page had **zero** content elements — completely blank main content | Added a `news_pi1` List plugin (all categories, sorted by date, paginated, header "Aktuelles") |
| 3 | `/verband` | Landing page showed only an unstyled English "No news available." from an orphaned `news_pi1` element (crdate ~Feb 2025), then nothing — no intro, no links to its 3 subpages | Hid the orphaned element; added a real intro paragraph + 3-card links (Über uns / Organigramm / Ansprechpartner), matching the existing `/service` hub pattern |
| 4 | `/termine` | Terminliste plugin showed "There are no events in the current view." despite events existing (visible in the calendar widget below it) — its flexform was missing `persistence.storagePid`, so it searched the wrong storage folder | Added `persistence.storagePid=60,58,59`, matching the working homepage sidebar widget's config. List now shows 10+ upcoming events with pagination |
| 5 | `/suche` | Search submit button showed English "Search" instead of German "Suchen" (`f:translate` resolving to the extension's English default, no German override installed) | Hardcoded "Suchen" in the Bootstrap form partial, consistent with the already-hardcoded German placeholder text right next to it |
| 6 | `/tabs` (Musterseite) | Tracker claimed this rendered correctly; it did not — the tabs container's `pi_flexform` was `NULL`, so the template (which requires `tab_N_title` to render anything) produced empty `<ul>`/`<div class="tab-content">` | Added a flexform with 4 tab titles/icons matching the 4 existing demo content elements. Tabs now switch correctly |

## Phase 3 (tracker Phase D) — code changes

- **D8** (map → detail link): `packages/feuerwehren/Resources/Public/JavaScript/map-feuerwehren.js`
  now links both the map marker popup and the sidebar list items to
  `FeuerwehrController::showAction` (`/inspektion/feuerwehren?tx_feuerwehren_karte[...]`).
  Added `config/system/additional.php` entry excluding those three GET parameters from cHash
  enforcement (standard, safe TYPO3 mechanism for this exact use case — cache key correctness
  is unaffected, only the cHash *requirement* is lifted). Also hardened the popup/list-item
  rendering to build DOM nodes instead of interpolating API data into `innerHTML` (flagged by
  a security hook during the edit; fixed rather than ignored).
- **D4** (dark-mode map): same JS file — `buildBaseStyle(theme)` now takes a light/dark color
  set (background/water/landcover/roads/boundary); a `MutationObserver` on
  `document.documentElement`'s `data-bs-theme` attribute calls `map.setStyle()` and reloads
  overlay/search data + layer-visibility toggle state on theme change. Verified live via
  screenshot (toggled theme on `/inspektion/feuerwehren`, background/water/roads all changed).
- **D5** (ICS scheduler task): turned out to be **already done** — the task type was already
  registered in `ext_localconf.php` and 3 task instances (Inspektion, Kreisjugendfeuerwehr,
  Leistungsabzeichen) already existed in the database. `scheduler:list` shows all three;
  `scheduler:run --task=1 --force` executed successfully with no errors. No code change was
  needed; the tracker was just out of date.
- **D1** (Einsätze page): re-verified, still renders correctly with the News-category
  approach. No change needed.

## Phase 4 — placeholder content

- **D6** (hero image): no code change was needed — `hero-static/templates/frontend.html`
  already falls back to the `.gradient-fire` utility when no image is set. **A real photo from
  a Landkreis Freising fire department is still needed** to replace the gradient.
- **D7** (Feuerwehr/Person detail placeholders): added visibly-marked placeholder sections to
  `packages/feuerwehren/Resources/Private/Templates/Feuerwehr/Show.html` (Mitglieder, Kontakt,
  Einsatzstatistik, Bildergalerie) and `.../Person/Show.html` (Werdegang, Auszeichnungen,
  Kontakt). Every placeholder value is shown as a dash/generic icon with a
  `text-bg-secondary` "Beispielwert — noch zu bestätigen" badge, wrapped in a new dashed-border
  `.placeholder-card` utility (added to `packages/sitepackage/Resources/Public/Scss/_custom.scss`
  alongside `.placeholder-badge`). No specific numbers are presented as fact anywhere.
  **Real data (member counts, contact info, statistics, photos) is still needed** from each
  fire department / person before these sections can show real content.

## Files touched (code/template/config)

- `packages/sitepackage/Resources/Public/Scss/_custom.scss` — new `.placeholder-card`/`.placeholder-badge` utilities
- `packages/feuerwehren/Resources/Private/Templates/Feuerwehr/Show.html` — placeholder sections (D7)
- `packages/feuerwehren/Resources/Private/Templates/Person/Show.html` — placeholder sections (D7)
- `packages/feuerwehren/Resources/Public/JavaScript/map-feuerwehren.js` — D8 detail links, D4 dark mode, XSS-safe DOM construction
- `packages/feuerwehren/Resources/Private/Templates/Feuerwehr/List.html` — bumped JS cache-busting query string
- `config/system/additional.php` — cHash `excludedParameters` for the Feuerwehr detail link params
- `packages/sitepackage/Resources/Private/Extensions/IndexedSearch/Partials/Form.html` — "Suchen" button label fix
- `.mcp.json` — added `--headless --isolated` etc. so future sessions don't need the stdio workaround
- `CLAUDE.md` — Phase D tracker table updated (D1, D3, D4, D5, D6, D7, D8, D9 statuses; new D10 for tonight's extra bug finds)
- `E2E_COMPARISON.md` — fully rewritten with the real visual-comparison results
- `OVERNIGHT_RUN_REPORT.md` — this file (new)

## Database content changes (not files — noted here for visibility, all via `ddev mysql`, cache flushed after each)

- `pages.uid=25` ("Service" footer folder): `doktype` changed from `4` (Shortcut) to `1` (Standard) — fixes footer bug #1 above.
- `tt_content.uid=35` (orphaned `news_pi1` on `/verband`, crdate ~Feb 2025): `hidden` set to `1`.
- `tt_content.uid=156` (calendarize list on `/termine`): `pi_flexform` updated to add `persistence.storagePid=60,58,59`.
- `tt_content.uid=142` (tabs container on `/tabs`): `pi_flexform` populated with 4 tab titles/icons (was `NULL`).
- **New row** `tt_content.uid=158` (CType `text`, pid 12 = `/verband`): intro paragraph + 3-card navigation to the Verband subpages.
- **New row** `tt_content.uid=159` (CType `news_pi1`, pid 10 = `/aktuelles`): full news list plugin, header "Aktuelles".

These are ordinary editorial content changes (the kind a TYPO3 backend editor would make) —
not schema changes — so no `database:updateschema` was needed. They're only listed here because
they don't show up in `git status`.

## Things I could not fully resolve / need your input

1. **Real hero photo (D6)** — the gradient placeholder is fine visually, but the hero still
   needs a real photo of a Landkreis Freising fire department to replace it. Please provide one
   (no stock photo was substituted, per your standing instruction).
2. **Real Feuerwehr/Person data (D7)** — member counts, contact details, statistics, and photos
   for individual fire departments/people are still placeholders (clearly labeled). Decision
   needed: gather real confirmed data, or keep as labeled examples permanently.
3. **`/termine` external calendar iframe** — embeds `open-web-calendar.hosted.quelltext.eu`
   pointed at a gist named `FFWGammelsdorfCalendar.yml`. This strongly looks like leftover
   content from a *different* project (there's a sibling `feuerwehr-gammelsdorf.de` project on
   this machine). I did not touch it since it's third-party/cross-project content I shouldn't
   guess about — please review and decide whether to remove/replace it.
4. **`/aktuelles/berichte-news`** — this subpage (linked from the "Aktuelles" nav dropdown) is
   still completely empty. Given I just added a full news list directly to `/aktuelles` itself,
   this subpage may now be redundant — worth deciding whether to hide it, redirect it, or give
   it distinct content (e.g. a curated "Berichte" subset vs. the full list).
5. **Search functionality** — only the button's label was fixed and verified. Submitting an
   actual search query and checking results/pagination/filters in the browser was not done this
   session (matches the caveat already in the previous `E2E_COMPARISON.md`).
6. Given the volume of DB-level fixes tonight (several unrelated pages found completely empty,
   plus one instance of what looks like cross-project data contamination), it may be worth a
   broader content audit of pages that were seeded/imported early in this project's life —
   there could be more orphaned/misconfigured elements like the ones found on `/verband` and
   `/aktuelles` that simply weren't hit during tonight's route sweep.

## Constraints respected

- Bootstrap 5 only — no Tailwind, no new JS libraries introduced.
- No fabricated facts about real, named people or fire departments — all placeholder values in
  D7 are visually marked as examples, never presented as real.
- No `git commit`, `git push`, or destructive git commands were run. Everything above remains
  as uncommitted working-tree changes (plus the ordinary DB content edits noted above) for you
  to review in the morning.

---

## Follow-up run — 2026-08-04: color-token audit + dark-mode pass

Triggered by your report that the homepage "Kommende Termine" widget rendered red instead of
dark. Full write-up with all details in `E2E_COMPARISON.md` ("Follow-up-Run 2026-08-04"
section) — this is the short version.

**Root cause of the reported bug** (already fixed at the start of this session): Lovable's
`--primary` token is dark gray-900 in this project's design, but Bootstrap's `$primary` is
mapped to the brand red (an intentional, correct choice for CTAs/buttons elsewhere). One
component — `.frame-primary-card` in `_frame.scss`, used by the homepage "Kommende Termine"
sidebar — had been wired to `var(--bs-primary)` (red) instead of a fixed dark gray. Fixed to
use `$gray-900` directly, matching the sibling `.frame-bg-surface-dark` rule. Verified correct
in both light and dark mode.

**Further bugs found and fixed while hunting for more instances of the same class of bug:**

1. **Dark-mode contrast bug in the `section` container's background-color options**
   (`bg-white`/`bg-muted`/`bg-secondary`) — Bootstrap's `.bg-white`/`.bg-secondary` utilities
   are static colors that don't change with `data-bs-theme`, while the text inside does. This
   made the homepage's "Aktuelles & Termine" heading nearly unreadable in dark mode (light text
   on a background stuck at pure white). `.bg-muted` turned out to be a complete no-op (no such
   CSS class existed at all). Fixed with new theme-aware `.section.bg-*` rules in `_frame.scss`,
   scoped narrowly so unrelated uses of the same Bootstrap utility classes elsewhere (translucent
   white badges on the hero, search icon field) keep their original static-white behavior.
2. **Broken pagination markup on `/termine`** — raw, unescaped HTML (`<span class="page-link">
   2</span>...`) was visible as literal text next to the pagination controls. Root cause: the
   Calendarize pagination partial captured link markup via Fluid's `contentAs` and echoed it
   back without `f:format.raw`, so Fluid's default auto-escaping turned the markup into visible
   text. Fixed by wrapping both `{content}` outputs in `f:format.raw`.
3. **English "Submit" button label on the contact form** (`/kontakt`) — same root cause class as
   last session's `/suche` "Search"-vs-"Suchen" fix (missing German translation for a
   `typo3/cms-form` default label). Fixed via `renderingOptions.submitButtonLabel: Absenden` on
   the form definition's root level.

**Checked and confirmed NOT a bug:** the contact form's submit button looked washed-out/pale in
a screenshot but `getComputedStyle` confirmed it's exactly `#cc0000` at full opacity — a
screenshot-rendering artifact, not a real issue. Also cross-checked every other `bg-primary`/
`text-primary` usage found across Homepage/Kontakt/Ausbildung/Aktuelles/Einsätze/Service/
Downloads/Slider against the Lovable source — all of them are legitimately using Lovable's
*accent* (fire-red) role, so Bootstrap's red `$primary` is the correct translation there; no
changes made.

**Structural gap flagged, not built:** Lovable renders a full-width dark "hero" banner
(`bg-primary py-16`, white heading) at the top of `Downloads.tsx`, `Kontakt.tsx`, `Service.tsx`,
`Verband.tsx`, `InspektionDetail.tsx`, and `FeuerwehrDetail.tsx`. None of the TYPO3 equivalents
have anything like it — they just show a plain white `<h1>`. This isn't a color bug (nothing is
mis-colored, the element simply doesn't exist), and building a new shared "page hero" mechanism
across 6 routes is a real feature decision, not a one-line fix, so it was deliberately left
alone. Tracked as new item **D11** in `CLAUDE.md`.

**Environment note:** neither `chrome-devtools-mcp` nor the Playwright MCP plugin
(`mcp__plugin_ecc_playwright__*`) could launch a browser directly in this session (`"chrome"
executable not found`, even after re-applying the `libasound.so.2` fix from last session to
both the puppeteer and ms-playwright Chromium binaries). Worked around it by driving Chrome
directly via the DevTools Protocol using Node's built-in `WebSocket` client (`cdp.js`/`cdp2.js`
in the scratchpad dir) — this also made it possible to toggle dark mode reliably (this
project's `colormode.js` does **not** auto-detect `prefers-color-scheme`; it only switches
based on a manually-set `kfv-ui-theme` localStorage key, so emulating the OS dark-mode
preference alone does nothing here) and to read exact `getComputedStyle` values instead of
guessing from screenshots.

Files changed this run: `packages/sitepackage/Resources/Public/Scss/_frame.scss`,
`packages/sitepackage/Resources/Private/Extensions/Calendarize/Partials/Pagination.html`,
`packages/sitepackage/Resources/Private/Form/Definitions/kontaktformular.form.yaml`, plus a
`npm run build` rebuild of the Vite assets. `E2E_COMPARISON.md` and `CLAUDE.md` updated with
full details. No git commits made.
