# Downloads Extension - Claude Context

## Overview

TYPO3 14 LTS extension: single Extbase plugin listing fileadmin files as downloads, grouped by category, filterable by category (pills) and free-text search (filename/description/keywords).

## Architecture decision

No custom domain model / DB table. Categorization and tagging reuse core TYPO3 features that already exist on `sys_file_metadata`:

- **Kategorie** = core `sys_category` (hierarchical, multi-select) — editors assign it in Filelist → file → "Metadaten bearbeiten" → Kategorien tab.
- **Schlagwort** = core `keywords` field on the same metadata form, comma-separated free text.

`Classes/Service/DownloadFileService.php` resolves files from one or more `sys_file_collection` (folder-based, chosen per plugin instance via FlexForm) and enriches them with categories (read from `sys_category_record_mm`, `tablenames=sys_file_metadata`, `fieldname=categories`) and keywords. Output is a read-only `DownloadFile` DTO, not an Extbase entity.

## Editor workflow

1. Upload files into a fileadmin folder.
2. Create/reuse a `sys_file_collection` (type "Ordner") pointing at that folder — List module or Filelist context menu.
3. Tag files: Filelist → file → "Metadaten bearbeiten" → set Kategorie(n) (root category "Downloads") + Schlagwörter.
4. Insert plugin "Downloads" on a page, FlexForm → pick the file collection(s), optionally restrict file extensions.

## Frontend behavior

- Grouped by the file's first assigned category; untagged files land in "Sonstige" (always sorted last).
- Category pills toggle whole-group visibility (multi-select, client-side, no reload).
- Search box filters by title/description/filename/category/keywords (`DownloadFile::searchText()`), client-side only — see `Resources/Public/JavaScript/downloads-search.js`. Assumes a folder-sized catalog; revisit with an AJAX endpoint (pattern: `packages/feuerwehren/Classes/Service/Api/SearchService.php`) if the catalog grows large.
- No CSS build step: plugin's own JS is loaded via `f:asset.script` directly from `EXT:downloads/...` in the template, not bundled through sitepackage's Vite pipeline (same pattern as `packages/feuerwehren`'s `map-feuerwehren.js`).

## Key Files

| File | Purpose |
|------|---------|
| `Classes/Controller/DownloadController.php` | Reads FlexForm settings, groups DTOs by category |
| `Classes/Service/DownloadFileService.php` | FAL + category/keyword resolution |
| `Classes/Domain/Dto/DownloadFile.php` | Read-only DTO, no persistence |
| `Configuration/FlexForms/downloads.xml` | File-collection picker + extension filter |
| `Resources/Private/Templates/Download/List.html` | Grouped Bootstrap list-group markup |
| `Resources/Public/JavaScript/downloads-search.js` | Client-side category/search filtering |

## Common Issues

### Plugin renders "Unknown plugin (list_type)"
`schmid/downloads` Set not chained — check `packages/sitepackage/Configuration/Sets/SitePackage/config.yaml` lists it under `dependencies:`, then `ddev exec vendor/bin/typo3 cache:flush`.

### File shows under "Sonstige" despite having a category assigned
Metadata categories are per-`sys_file_metadata`-record (sys_file → one metadata row per storage+identifier). Re-check the category was saved on the same file/storage combination being listed, not a duplicate.
