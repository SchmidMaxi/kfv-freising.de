# KFV Freising - Claude Context

## Project Overview

Website for **KFV Freising** (Kreisfeuerwehrverband Freising - Fire Department District Association) built with TYPO3 13 LTS. Features fire department maps, organizational charts, news, events, and general content management.

## Tech Stack

| Component | Technology | Version |
|-----------|------------|---------|
| CMS | TYPO3 | 13.4 LTS |
| PHP | PHP | 8.4 (CI: 8.3) |
| Database | MySQL | 8.0 |
| Frontend | Bootstrap | 5.3.3 |
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
- Content blocks (accordion, cards, sliders)
- News extension overrides
- Calendar integration
- SCSS/Vite build pipeline

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

### SCSS Structure
```
packages/sitepackage/Resources/Public/Scss/
├── layout.scss           # Main entry
├── _variables.scss       # Light mode variables
├── _variables-dark.scss  # Dark mode variables
├── _footer.scss
├── _forms.scss
├── _mixins.scss
└── _utilities.scss
```

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

Custom content elements in `packages/sitepackage/ContentBlocks/`:

| Block | Description |
|-------|-------------|
| `accordion` | Collapsible FAQ sections |
| `card` | Single card element |
| `card-group` | Card container |
| `card-slider` | Splide-based carousel |
| `heroslider` | Hero image slider |

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
1. Create folder in `packages/sitepackage/ContentBlocks/<name>/`
2. Add `EditorInterface.yaml` and templates
3. Run `ddev exec vendor/bin/typo3 cache:flush`

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
