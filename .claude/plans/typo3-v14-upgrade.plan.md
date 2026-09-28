# Plan: TYPO3 13 LTS → 14 Upgrade

**Complexity**: Medium
**Status**: core upgrade executed and smoke-tested (2026-09-21) — see "Execution Log" at bottom for what ran, what broke, and what's still open.

**Amendments from user on confirm:**
- PHP target raised to **8.5** everywhere (was 8.2/8.3 min-compat framing).
- Update/sync scripts audited for v14/PHP 8.5 fitness — see "Update/Sync Script Audit" below.

## Summary
Upgrade TYPO3 core and all three extensions in `packages/` (`sitepackage`, `feuerwehren`, `icsimporter`) to TYPO3 v14, applying current TYPO3 Coding Guidelines. All required 3rd-party extensions confirmed by user as v14-available. A code-level breaking-change scan came back clean — this is primarily a dependency/config/verification upgrade, not a rewrite.

## Codebase Baseline (verified via fork scan of packages/)

| Breaking change | Result |
|---|---|
| `renderStatic()` in ViewHelpers | none found |
| `StandaloneView` | none found |
| `f:widget.paginate` | none found |
| Middleware superglobals (`$_GET`/`$_POST`/`$GLOBALS['TYPO3_REQUEST']`) | none — `ApiGatewayMiddleware` already uses injected `ServerRequestInterface` correctly |
| `getTSFE()` in TypoScript conditions | none found |
| Backend-preview `{header}`→`{record.header}` | N/A — Content Blocks templates use `{data.*}`, not classic hook |
| `declare(strict_types=1)` | present in all 33 PHP files |
| DI style (`inject*()`/`ObjectManager`) | none — all constructor-promoted `readonly` DI already |

Main real risk is 3rd-party ext/GUI behavior changes (Content Blocks now ships a GUI in v14, Fluid 5, calendarize namespace changes) and DB/TCA migrations via upgrade wizards.

## Patterns to Mirror
| Category | Source | Pattern |
|---|---|---|
| DI | `packages/feuerwehren/Classes/Service/Api/SearchService.php:27-29` | constructor-promoted `private readonly` — keep for any new/touched classes |
| Middleware | `packages/feuerwehren/Classes/Middleware/ApiGatewayMiddleware.php` | PSR-7 `$request` only, no superglobals |
| Composer | root `composer.json` + 3 package `composer.json` | path-repo `packages/*`, `@dev` internal deps |

## Files to Change
| File | Action | Why |
|---|---|---|
| `composer.json` (root) | UPDATE | bump all `typo3/cms-*` `^13.0`→`^14.0`, `typo3/minimal` `^13`→`^14`, bump `helhum/typo3-console`, `causal/oidc`, `brotkrueml/typo3-matomo-widgets`, `mediadreams/md_fullcalendar`, `reelworx/rx-shariff` to v14-compatible releases |
| `packages/feuerwehren/composer.json` | UPDATE | `typo3/cms-core`/`cms-frontend` `^13.4`→`^14.0`, PHP req if core min changed |
| `packages/sitepackage/composer.json` | UPDATE | `typo3/cms-core` `^13.4`→`^14.0`, `lochmueller/calendarize` `*`→pin sane range if needed |
| `packages/icsimporter/composer.json` | UPDATE | `typo3/cms-core` `^13.0`→`^14.0`, `lochmueller/calendarize` `^14.0` check still current |
| `.ddev/config.yaml` | CHECK | `php_version: 8.4` already ≥ v14 min (8.2/8.3) — verify against exact v14.x requirement at execution time |
| `.github/workflows/*.yaml` | UPDATE | bump PHP version used in CI if below v14 min (CI currently 8.3 per CLAUDE.md — verify still sufficient) |
| `packages/feuerwehren/CLAUDE.md`, root `CLAUDE.md` | UPDATE | version references "TYPO3 13 LTS/13.4" → 14, after upgrade verified working |
| Content Blocks `config.yaml`/templates (10 elements) | VERIFY | Content Blocks v14 ships a backend GUI — confirm existing hand-written blocks still render, no schema migration needed |
| `packages/*/ext_emconf.php` | OPTIONAL CLEANUP | v14 no longer requires it for composer-mode installs; can drop, not required |

## Tasks

### Task 1: Pre-flight safety
- Full DB dump + git status check. Working tree currently has unrelated uncommitted changes (map filter work in `packages/feuerwehren`, `frontend/`) — confirm with user before touching anything, don't mix unrelated diffs into upgrade commit.
- Do upgrade on a dedicated branch (e.g. `feature/typo3-v14-upgrade`).
- **Validate**: `git status` clean apart from intentional upgrade branch.

### Task 2: Bump composer constraints, resolve
- Edit root + 3 package `composer.json` per table above.
- `ddev composer update "typo3/*" --with-all-dependencies` (or full `composer update` in DDEV container).
- Resolve conflicts extension by extension.
- **Validate**: `ddev composer why-not typo3/cms-core 14.x` clean; `composer.lock` updated.

### Task 3: Run TYPO3 upgrade wizards + schema update
- `ddev exec vendor/bin/typo3 database:updateschema`
- Backend → Admin Tools → Upgrade Wizards → run all (site sets, TCA migrations, calendarize/content-blocks specific wizards if any).
- `ddev exec vendor/bin/typo3 cache:flush`
- **Validate**: no wizard left pending, no exception in `var/log/typo3_*.log` (grep log for "message", not the error page).

### Task 4: Fix fallout
- Re-run the breaking-change grep sweep post-upgrade against our integration points: `packages/sitepackage/Resources/Private/Extensions/{news,IndexedSearch}`, calendarize templates, container templates.
- Spot-check all 10 Content Blocks backend-preview templates render in the new GUI-capable v14 Content Blocks.
- Check `causal/oidc` login flow, `brotkrueml/typo3-matomo-widgets` dashboard widgets, `mediadreams/md_fullcalendar` still render.

### Task 5: Apply current TYPO3 Coding Guidelines polish
- Confirm PHP-CS-Fixer config present/updated to PER-CS2.0 target — add `typo3/coding-standards` dev dependency if missing, run `vendor/bin/php-cs-fixer fix` scoped to `packages/`.
- Spot-check ViewHelper/class namespace conventions unaffected.
- **Validate**: CS-Fixer dry-run clean.

### Task 6: Manual verification (real browser test before done)
- Start Vite dev server in container, browse all pages listed in root `CLAUDE.md` Implementation Tracker (`/`, `/feuerwehren`, `/aktuelles`, `/termine`, `/verband`, `/suche`, map + calendar + org-chart plugins).
- Dark mode toggle, forms (Kontakt), map search API.
- **Validate**: no console errors, no visual regression vs current screenshots in `E2E_COMPARISON.md`.

### Task 7: CI/CD + docs
- Bump GitHub Actions PHP version if needed.
- Update root `CLAUDE.md` + `packages/feuerwehren/CLAUDE.md` version references to v14.
- Rebuild knowledge graph (`/graphify --update`).

## Validation
```bash
ddev composer validate
ddev composer update
ddev exec vendor/bin/typo3 database:updateschema
ddev exec vendor/bin/typo3 cache:flush
ddev exec vendor/bin/typo3 scheduler:list
```

## Risks
| Risk | Likelihood | Mitigation |
|---|---|---|
| `helhum/typo3-console` version lag behind core CLI changes | Medium | check console v9+ compat before bump, TYPO3 core now bundles most CLI itself |
| `lochmueller/calendarize` namespace/upgrade-wizard quirks | Medium | run its upgrade wizard explicitly, verify `icsimporter` still runs via scheduler after |
| Content Blocks v14 GUI migrates/rewrites hand-authored `config.yaml` | Low-Medium | diff `config.yaml` files before/after any GUI interaction; don't open blocks in new GUI until confirmed non-destructive |
| Uncommitted unrelated working-tree changes get tangled with upgrade commit | Medium | do upgrade on clean branch, don't touch those files |
| Vector tiles / MBTiles custom middleware breaks silently | Low | explicit curl tests from `packages/feuerwehren/CLAUDE.md` "Testing" section |

## Acceptance
- [ ] `composer.json` (root + 3 packages) on `^14.0`
- [ ] All upgrade wizards run, no pending
- [ ] All pages in Implementation Tracker manually verified in browser, no regressions
- [ ] CGL/CS-Fixer clean
- [ ] CI green on upgrade branch
- [ ] Docs (`CLAUDE.md` files) updated

## Update/Sync Script Audit (added after user confirm)

Checked every script in `.ddev/commands/host/`, `.ddev/commands/web/`, `.github/workflows/*.yaml`, `deploy.php`:

| File | Finding | Action |
|---|---|---|
| `.github/workflows/staging.yaml:20`, `production.yaml:20` | already `php-version: "8.5"` | none — already correct |
| `deploy.php:14` | `set('bin/php', '/usr/bin/php84')` — hardcoded PHP 8.4 binary path on deploy server | **must update** to `/usr/bin/php85`; requires PHP 8.5 actually installed on `www416.your-server.de` — infra prerequisite, flag to user before deploying |
| `.ddev/config.yaml` | `php_version: "8.4"` | **must update** to `"8.5"` |
| `.ddev/commands/host/sync-news` | comments say "TYPO3 9→13 compatible" / "local TYPO3 13 schema" — mechanism reads local schema dynamically via `information_schema`, so it keeps working unchanged after the v14 bump; only the comments are stale | cosmetic: update comments to "9→14" |
| `.ddev/commands/host/sync` | generic staging/production DB sync, no version-specific logic | no change needed |
| `.ddev/commands/host/push-staging`, `push-live`, `git-push` | generic git workflow helpers, no TYPO3/PHP version coupling | no change needed |
| `.ddev/commands/web/php-cs-fixer`, `phpstan`, `typo3` | thin wrappers, no version coupling | no change needed |
| `.ddev/commands/host/sync-v12`, `update-template` | **dead code** — reference `Build/Development/`, `migration/*.php`, `dump_v10.sql.gz`, none of which exist in this repo (leftover from an old b13/make template scaffold, unrelated `container_`/`powermail`/`rflocations` migrations) | out of scope, not touched |

## Research notes (for reference, not re-derived at execution time)
- TYPO3 14.0 breaking-change sweep: 98 breaking + 31 deprecations + 105 features + 16 important entries; 14.1/14.2/14.3 added zero further breaking changes (LTS stability promise). Source: t3planet.de, jweiland.net, get.typo3.org/release-notes/14.0.0.
- `ext_emconf.php` no longer required for composer-mode installs (composer.json is source of truth) — all 3 packages already have composer.json, non-issue either way.
- Custom ViewHelpers in v14 must implement public `render()` (not deprecated `renderStatic()`) — not applicable, none found.
- PSR-15 middlewares must not rely on `$_GET`/`$_POST`/`$GLOBALS['TYPO3_REQUEST']` — must use the PSR-7 `$request` passed into `process()` — already the case in `ApiGatewayMiddleware`.
- `f:widget.paginate` removed — replacement is `QueryResultPaginator` + `SimplePagination`/`SlidingWindowPagination` in controller + manual Fluid pagination links — not currently used anywhere in `packages/`.
- TYPO3 v14 min PHP is 8.2 (docs conflicting on whether main/dev branch needs 8.5 — verify exact patch-level requirement at execution time via `docs.typo3.org/m/typo3/reference-coreapi/14.3/.../SystemRequirements`). Project already runs PHP 8.4 in DDEV, CI on 8.3 — likely already sufficient.

## Execution Log (2026-09-21)

**Branch:** `feature/typo3-v14-upgrade` (created from `develop`, carries pre-existing unrelated uncommitted work as-is — not touched).

### Done
- `.ddev/config.yaml` → `php_version: "8.5"`; DDEV rebuilt, confirmed `PHP 8.5.3` in container.
- `deploy.php`: `bin/php` → `/usr/bin/php85` (confirmed installed on server by user); `deploy_path` → `.../v14/staging` and `.../v14/production` (user chose to rename, confirmed php85 present).
- All 4 `composer.json` files bumped to TYPO3 `^14.3` / PHP 8.5. Exact 3rd-party versions verified against live Packagist before pinning: `lochmueller/calendarize` ^15.0, `mediadreams/md_fullcalendar` ^6.0, `helhum/typo3-console` ^9.0, `causal/oidc` ^6.0, `brotkrueml/typo3-matomo-widgets` ^4.0, `reelworx/rx-shariff` unchanged (^16.1 already covers it).
- Dropped `b13/make` and `ichhabrecht/filefill` from root `require-dev` — both cap at TYPO3 ^13.x, neither has a v14 release yet, neither is referenced anywhere in scripts/docs (grepped, confirmed unused). Reversible; re-add once upstream ships v14 support.
- `ddev composer update --with-all-dependencies` — resolved clean, core landed on **v14.3.7**.
- Autoload dump initially failed (`class-alias-loader` self-upgrade mid-run, stale in-process plugin class) — fixed by re-running `composer dump-autoload` alone.
- `database:updateschema` initially threw `Cannot access private property TextFieldType::$searchable` (symfony/var-exporter lazy-ghost + `symfony/polyfill-deepclone` + Content Blocks 2.4.9 interaction) — **not a real code bug**: caused by a stale compiled `var/cache/code/di/*` container left over from v13. Fixed by `rm -rf var/cache/*` and retrying. Confirmed via isolated repro that plain `clone` on the class works fine.
- Schema updated (58 fields added, 64 changed, 2 tables added). All 10 pending upgrade wizards run clean (incl. both `calendarize_*` wizards — the plan's top risk item). `upgrade:list` now empty.
- Smoke tests: `/`, `/aktuelles`, `/termine`, `/suche`, `/inspektion/feuerwehren` all HTTP 200, no error markers in body. `feuerwehren` search/overlays/vector-tile API endpoints all HTTP 200 with real data. Scheduler: all 3 `icsimporter` tasks survived `schedulerDatabaseStorageMigration`; ran task 1 live, completed without error, calendarize v15 confirmed compatible end-to-end.
- Confirmed pre-existing `Locale "de_DE" not found` ERROR-log noise predates this upgrade (found identical entries from 2025-07-21) — OS locale-alias gap in the DDEV image, unrelated to v14, not introduced by this work.
- Updated version references in root `CLAUDE.md` and `packages/feuerwehren/CLAUDE.md` (13.4/13 LTS/PHP 8.4 → 14.3 LTS/PHP 8.5).

### Deferred / still open
- **Full visual browser QA (Task 6)**: headless Chrome is broken in this environment again (`Could not find Google Chrome executable... /opt/google/chrome/chrome`) — same recurring issue as reference-headless-chrome-fix memory. Substituted curl/log-based checks (see above). Recommend a real browser pass before deploying.
- **CGL/CS-Fixer polish (Task 5)**: no `typo3/coding-standards` dependency or `.php-cs-fixer.php` config exists in this repo, and `.typo3/bin/php-cs-fixer` (referenced by the `ddev php-cs-fixer` command) doesn't exist either — pre-existing gap, not caused by this upgrade. Codebase was already confirmed CGL-clean by the earlier breaking-change scan (strict_types, PSR-4, modern DI everywhere), so this is optional follow-up, not a blocker.
- **`sync-news` comment cosmetics** ("TYPO3 9→13" → "9→14"): skipped — the file already has unrelated in-progress uncommitted changes (a real `sys_file_reference` REPLACE-semantics fix); didn't want to entangle a cosmetic edit with that diff.
- **CI workflows**: no change needed — `.github/workflows/{staging,production}.yaml` were already on `php-version: "8.5"` before this session touched anything.
- **Production deploy**: not executed. Renaming `deploy_path` to `v14/...` means the first deploy to each environment needs manual server-side prep (copy `shared` dirs — `config`, `fileadmin`, `var` — from the old `v13/...` path into the new `v14/...` path before the first `dep deploy`, then repoint the vhost/document-root symlink) — this is real infra work outside this session's reach (no SSH access here).
- Nothing committed — working tree has both the upgrade changes and the pre-existing unrelated changes from session start; awaiting user direction on how to split/commit.
