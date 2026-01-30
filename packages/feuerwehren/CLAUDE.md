# Feuerwehren Extension - Claude Context

## Overview

TYPO3 13 LTS extension for managing and displaying fire department data with interactive maps, organizational charts, and jubilee management for the KFV Freising (Kreisfeuerwehrverband Freising - Fire Department District Association).

## Tech Stack

- **TYPO3**: 13.4+ LTS
- **PHP**: 8.3+
- **Map Rendering**: MapLibre GL JS (vector tiles)
- **Tile Format**: MBTiles (SQLite with PBF tiles)
- **GeoJSON**: For overlay boundaries (Gemeinden, KBM, KBI areas)
- **Frontend**: Vanilla JavaScript, Bootstrap 5

## Architecture

### Domain Model Hierarchy

```
Feuerwehr (Fire Department)
├── name, slug, strasse, plz, ort
├── latitude, longitude (coordinates)
├── gruendungsdatum (founding date)
├── fahrzeugkategorien (M:M → Fahrzeugkategorie)
└── jubilaeen (1:N → Jubilaeum, cascade delete)

Area (Organizational Unit)
├── type: 'kbr' | 'kbi' | 'kbm' | 'fach-kbm'
├── parent: ?Area (hierarchical structure)
├── person: ?Person (assigned leader)
├── children: ObjectStorage<Area>
└── gemeinden: ObjectStorage<Gemeinde> (M:N)

Gemeinde (Municipality)
├── name, slug
├── logo: FileReference
├── gemeindegebiet: string (GeoJSON geometry)
├── kbmArea: ?Area
└── feuerwehren: ObjectStorage<Feuerwehr>

Person
├── title (role/function)
├── slug
└── feUser: ?FrontendUser
```

### Organizational Hierarchy

```
KBR (Kreisbrandrat - District Fire Chief)
 └── KBI (Kreisbrandinspektor - District Fire Inspector)
      └── KBM (Kreisbrandmeister - District Fire Master)
           └── Gemeinden (Municipalities with fire departments)

Fach-KBM (Specialist positions, report directly to KBR)
```

## API Endpoints

### Search API
- **URL**: `GET /api/feuerwehren/search`
- **Parameters**:
  - `bbox`: `"west,south,east,north"` (WGS84 coordinates)
  - `f[]`: Vehicle category UIDs (multiple)
  - `mode`: `"and"` | `"or"` (filter combination)
- **Response**: `{ "items": [{ uid, name, strasse, plz, ort, lat, lon, fahrzeuge }] }`
- **Rate Limit**: 60 requests/minute per IP

### Overlays API
- **URL**: `GET /api/feuerwehren/overlays`
- **Response**:
  ```json
  {
    "gemeinden": [{ "uid": int, "name": string, "geojson": Geometry }],
    "kbmFeatures": { "type": "FeatureCollection", "features": [...] },
    "kbiFeatures": { "type": "FeatureCollection", "features": [...] }
  }
  ```
- **Cache**: 5 minutes

### Vector Tiles
- **URL**: `GET /_vt/{z}/{x}/{y}.pbf`
- **Source**: MBTiles SQLite database
- **Coordinate System**: XYZ (automatically converted to TMS internally)
- **Cache**: 1 year (immutable content)

## CLI Commands

### Prefetch Tiles
```bash
ddev exec vendor/bin/typo3 feuerwehren:tiles:prefetch \
  --preset=freising \
  --zooms=8-14 \
  --concurrency=6
```

### Download MBTiles
```bash
ddev exec vendor/bin/typo3 feuerwehren:mbtiles:fetch \
  --url=https://example.com/tiles.mbtiles \
  --dest=fileadmin/tiles/osm.mbtiles \
  --sha256=<checksum>
```

## Services

### GeoJsonService
Central service for GeoJSON operations:
- `normalizeGeometry()`: Handles Feature, FeatureCollection, or raw geometry
- `createMultiPolygonFromGemeinden()`: Combines municipality geometries
- `parseCoordinate()`: Validates and parses coordinate values
- `createFeature()`: Builds GeoJSON Feature objects

### SearchService
Handles map search requests with bounding box and category filtering.

### OverlaysService
Generates GeoJSON overlay data for municipalities and organizational areas.

### SettingsReader
Reads settings from TYPO3 Site Configuration.

## Configuration

### Site Settings (config/sites/*/config.yaml)
```yaml
settings:
  feuerwehren:
    vectorTiles:
      mbtilesPath: 'fileadmin/tiles/osm.mbtiles'
    api:
      allowedOrigins:
        - 'https://kfv-freising.de'
        - 'https://www.kfv-freising.de'
```

### TypoScript Settings
```typoscript
plugin.tx_feuerwehren.settings {
  vtUrlTemplate = /_vt/{z}/{x}/{y}.pbf
  apiSearchUrl = /api/feuerwehren/search
  overlaysUrl = /api/feuerwehren/overlays
}
```

## Frontend Plugins

1. **Karte** (Map): Interactive fire department map with filters
2. **Organigramm** (Org Chart): Hierarchical leadership display
3. **Jubilaeen** (Jubilees): Anniversary celebration table

## Database Tables

- `tx_feuerwehren_domain_model_feuerwehr`
- `tx_feuerwehren_domain_model_person`
- `tx_feuerwehren_domain_model_gemeinde`
- `tx_feuerwehren_domain_model_area`
- `tx_feuerwehren_domain_model_fahrzeugkategorie`
- `tx_feuerwehren_domain_model_jubilaeum`
- Junction tables for M:M relations

## Key Files

| File | Purpose |
|------|---------|
| `Classes/Controller/FeuerwehrController.php` | Map plugin controller |
| `Classes/Controller/VectorTileController.php` | PBF tile server |
| `Classes/Middleware/ApiGatewayMiddleware.php` | API routing, CORS, rate limiting |
| `Classes/Service/GeoJsonService.php` | GeoJSON utilities |
| `Classes/Service/Api/SearchService.php` | Search API logic |
| `Classes/Service/Api/OverlaysService.php` | Overlay generation |
| `Resources/Public/JavaScript/map-feuerwehren.js` | Map frontend logic |

## Development Notes

### Adding New Vehicle Categories
1. Add record in TYPO3 backend (List module → Fahrzeugkategorie)
2. Categories automatically appear in map filters

### Modifying Map Layers
Edit `map-feuerwehren.js`:
- `buildBaseStyle()`: Layer definitions
- `wireEvents()`: Interaction handlers

### Extending API
1. Add endpoint in `ApiGatewayMiddleware::process()`
2. Create service method
3. Update CORS and rate limiting if needed

## Testing

### API Testing
```bash
# Search
curl "https://kfv-freising.ddev.site/api/feuerwehren/search?bbox=11.3,48.3,12.0,48.7"

# Overlays
curl "https://kfv-freising.ddev.site/api/feuerwehren/overlays"

# Vector Tile
curl -I "https://kfv-freising.ddev.site/_vt/10/545/352.pbf"
```

## Common Issues

### Tiles not loading
1. Check MBTiles file exists at configured path
2. Verify Site Settings configuration
3. Check browser console for CORS errors

### Rate limiting triggered
- 60 requests/minute limit per IP
- Check `feuerwehren_rate` cache
- Adjust limit in `ApiGatewayMiddleware` if needed

### GeoJSON not rendering
1. Verify `gemeindegebiet` field contains valid GeoJSON
2. Check browser console for turf.js errors
3. Validate geometry type (must be Polygon or MultiPolygon)
