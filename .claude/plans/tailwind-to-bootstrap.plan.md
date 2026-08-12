# Plan: Tailwind → Bootstrap 5 Reversal (sitepackage Frontend)

**Complexity**: Large (multi-session, 7 phases)

## Summary
Convert all Fluid templates/partials in `packages/sitepackage` from Tailwind utility classes to Bootstrap 5 standard markup (cards, accordion, navbar, offcanvas, dropdown, pagination, forms, badges). `/frontend` (lovable.dev prototype) is a visual/structural reference only, not a source of class names. Sliders stay on Splide (never Bootstrap Carousel), configured via `data-*` attributes. Reactivate the existing but currently-dead Bootstrap SCSS scaffold in `Resources/Public/Scss/`. Incremental cutover: Bootstrap and Tailwind coexist during migration, Tailwind removed only at the end (Phase 7).

## Key Findings
- `Resources/Public/Scss/layout.scss` already imports a full Bootstrap 5 partial set but is not wired into Vite/`main.js` — dead code, ready to activate.
- `_variables.scss` already maps `$primary: $fire` (#cc0000) and full `$red-100..900` scale — brand colors ready.
- `_variables-dark.scss` targets Bootstrap 5.3's native `[data-bs-theme="dark"]`, but `colormode.js` currently toggles `class="dark"` (Tailwind convention) — needs realignment in Phase 1.
- `TCEFORM.tsconfig` already defines a `frame_class` field with custom items; several ContentBlocks (hero-static, quick-actions, section-header, feature-list) already render `frame-{data.frame_class}` in their wrapper — pattern to extend to the rest.
- `card-slider`/`heroslider` already configure Splide via `data-splide='{"type":...}'` JSON attribute — exactly the pattern requested; extend to generic `slider` block.
- Extensions in scope per composer.json: `georgringer/news`, `lochmueller/calendarize`, `mediadreams/md_fullcalendar`, `reelworx/rx-shariff` (override exists), `schmid/feuerwehren` (own templates/CLAUDE.md). `schmid/icsimporter` and `causal/oidc` are backend-only.

## Patterns to Mirror
| Category | Source | Pattern |
|---|---|---|
| Slider config | `card-slider/templates/frontend.html:9` | `data-splide='{...json...}'` built from ContentBlock fields via `<f:format.raw>` |
| Frame wrapper | `hero-static/templates/frontend.html:3` | `class="... frame frame-{data.frame_class} frame-type-{data.CType} frame-layout-{data.layout} ..."` |
| Backend option lists | `TCEFORM.tsconfig:9-23` | `addItems` / `types.<CType>.addItems` blocks |
| Extension overrides | `Resources/Private/Extensions/{News,Calendarize,MdFullcalendar}/` | Existing override folder structure |

## Phases

### Phase 1 — Foundation: Bootstrap Build Activation
- `package.json`: add `bootstrap` npm package back; keep `@splidejs/splide`, `glightbox`; keep Tailwind deps until Phase 7.
- `main.js`: import `../../Public/Scss/layout.scss` after existing `../Css/main.css`; selective Bootstrap JS import (`Collapse`, `Dropdown`, `Offcanvas`, `Tab`).
- `colormode.js`: switch to `data-bs-theme` attribute (keep `kfv-ui-theme` storage key).
- New `_custom.scss` (imported at end of `layout.scss`): recreate `.gradient-fire` / surface-dark utilities as plain Bootstrap-flavored classes; port `@font-face` rules out of `Css/main.css`.
- Extend `_variables.scss`/`_variables-dark.scss` only for confirmed gaps.
- **Validate**: `npm run build`; Bootstrap `.btn.btn-primary` renders in brand red; theme toggle flips `data-bs-theme`.

### Phase 2 — Header & Footer
- `PageView/Partials/Header.html`: `navbar navbar-expand-lg` + `offcanvas` (mobile menu, remove old toggle JS from `main.js`) + `dropdown` (desktop submenus).
- `PageView/Partials/Footer.html`: Bootstrap grid instead of `grid grid-cols-*`.
- Reference `/frontend/src/components/Header.tsx`, `Footer.tsx` for structure/copy only.

### Phase 3 — Structural ContentBlocks
`hero-static`, `quick-actions`, `section-header`, `feature-list` → Bootstrap grid/`.btn`/`.badge`/`.list-group`. Add missing `frame-{data.frame_class}` wrapper where absent. Reference `/frontend/src/components/Hero.tsx`, `QuickActions.tsx`, `AboutSection.tsx`.

### Phase 4 — Card / Accordion / Slider ContentBlocks
- `accordion`: real `.accordion`/`.accordion-item`/`.accordion-button` + `data-bs-toggle="collapse"` (drop `<details>`/`<summary>`), 4 layout variants as SCSS modifiers on same markup.
- `card`, `card-group`: `.card` + `.row.row-cols-*` (replaces Tailwind grid-cols safelist hack).
- `card-slider`, `heroslider`, `slider`: keep Splide + `data-splide`; Bootstrap card/caption wrapper classes.
- Reference `/frontend/src/pages/ShowcaseCards.tsx`, `ShowcaseSlider.tsx`, `ShowcaseAccordions.tsx`.

### Phase 5 — Extension Overrides
- News: `List.html`, `Partials/List/Item.html`, `Detail.html`, `Category/Items.html`, `Pagination.html`, `SearchForm.html`, `Detail/MediaImage.html`/`MediaVideo.html`, `Detail/Shariff.html`.
- Calendarize: `Partials/Event/ListItem.html`, `Detail.html`, `Partials/Pagination.html`, `Templates/Calendar/Detail.html`, `Search.html`. Homepage padding/rounded-corners wrapper → `frame_class` option (Phase 6), not hardcoded.
- md_fullcalendar: `Cal/Show.html`, `Cal/Detail.html` — Bootstrap-styled classes on existing native `<dialog>`.
- feuerwehren (`packages/feuerwehren/Resources/Private/Templates/`): `Feuerwehr/List.html` (filter pills → `.btn-check`+`.btn-outline-*`), `Person/List.html`/`Show.html`, `Jubilaeum/List.html`.

### Phase 6 — `frame_class` Background System
- Extend `TCEFORM.tsconfig` `frame_class.addItems` with Bootstrap-semantic background options.
- New `_frame.scss`: `.frame-bg-*` rules (background + padding + border-radius), covers Calendarize case generically.
- Ensure all ContentBlocks render `frame-{data.frame_class}` wrapper.
- Verify plugin output (News/Calendarize) renders through core `Frame.html` partial so `frame_class` applies without extra Fluid work.

### Phase 7 — Cutover & Tailwind Removal
- Remove Tailwind from `main.js`, delete `tailwind.config.js`/`postcss.config.js`/`Css/main.css`, drop Tailwind deps from `package.json`.
- Replace tabs ContentBlock JS with Bootstrap `Tab` component.
- Final build + full manual QA (all page types, dark mode, mobile).

## Risks
| Risk | Likelihood | Mitigation |
|---|---|---|
| `.container` class collision during migration | Medium (cosmetic) | Bootstrap SCSS loaded after Tailwind CSS; re-verify in Phase 7 |
| `frame_class` doesn't reach plugin output via core Frame.html | Low-Medium | Verify per-extension in Phase 6; wrap plugin render manually only if needed |
| Accordion layout variants don't map 1:1 to Bootstrap markup | Medium | SCSS modifiers on one shared Bootstrap DOM structure |
| Large scope / long branch life | Medium | Phase-by-phase, `develop` stays deployable throughout |
| Dark-mode convention switch breaks something | Low | Single storage key reused; swept per phase |

## Validation
```bash
cd packages/sitepackage && npm run build
ddev exec vendor/bin/typo3 cache:flush
```
Manual browser QA per phase (desktop + mobile, light + dark).

## Acceptance
- [ ] All 7 phases complete
- [ ] `npm run build` passes with Tailwind fully removed
- [ ] All 10 ContentBlocks, Header/Footer, News/Calendarize/md_fullcalendar/feuerwehren converted
- [ ] frame_class background options work on containers, ContentBlocks, and plugin output
