# feuerwehren (TYPO3 13 LTS, PHP 8.4)

Local OpenStreetMap tiles + Leaflet overlays for Gemeinden/Feuerwehren, plus Organigramm & Jubiläen.  
**Vendor:** Schmid · **Extension key:** `feuerwehren`

---

## ✨ Features
- **Karte (Leaflet):** Marker für Feuerwehren, Filter nach Fahrzeugkategorien, Gemeindegrenzen als GeoJSON-Overlay.
- **Organigramm:** Personen/Zuordnungen (Rolle, Gemeinde, Untergeordnete).
- **Jubiläen:** Inline zu Feuerwehr; eigenes Plugin zur Ausgabe.
- **Lokale OSM-Tiles:** Tile-Endpunkt `/_tiles/{z}/{x}/{y}.png` aus **MBTiles** (empfohlen) oder **Proxy+Cache** (Fallback).  
- **Scheduler/CLI:** `feuerwehren:update-tiles` zum Aktualisieren/Prewarming des lokalen Tile-Caches.

---

## ✅ Requirements
- TYPO3 **13 LTS**
- PHP **8.4** (kompatibel ab 8.2)
- Datenbank: MariaDB/MySQL (oder kompatibel)
- Optional: **SQLite3** (für *.mbtiles* Zugriff)

---

## 📦 Installation

### 1) Code bereitstellen
**Composer** (empfohlen):
```bash
composer config repositories.feuerwehren path ./packages/feuerwehren
# oder VCS/Git: composer config repositories.feuerwehren vcs https://git.example/feuerwehren.git
composer require schmid/feuerwehren:dev-main
```
**Classic**: Code nach `typo3conf/ext/feuerwehren/` kopieren.

### 2) Aktivieren & DB-Schema
- Im TYPO3-Backend → **Extensions**: `feuerwehren` aktivieren.
- **Admin Tools → Upgrade**: DB-Struktur aktualisieren (oder CLI `vendor/bin/typo3 database:updateschema`).

### 3) TypoScript laden
- **Site** oder **Root-Template** öffnen und **Setup/Constants** der Extension einbinden, falls nicht automatisch:
  - `EXT:feuerwehren/Configuration/TypoScript/setup.typoscript`
  - `EXT:feuerwehren/Configuration/TypoScript/constants.typoscript`

### 4) Grundeinstellungen (Tiles)
In den **Constants** (oder in der Site-Config) setzen:
```typoscript
plugin.tx_feuerwehren.settings {
  tileSource = mbtiles          # mbtiles | proxy
  mbtilesPath = fileadmin/tiles/osm.mbtiles
  proxyUrl = https://a.tile.openstreetmap.org/{z}/{x}/{y}.png  # nur Fallback
}
```
> **Empfehlung:** Eigene MBTiles nutzen (Performance, Nutzungsbedingungen). Der Proxy-Fallback ist nur für Entwicklung.

### 5) Scheduler / CLI
- **Scheduler → Aufgabe hinzufügen → Execute console commands** → Command: `feuerwehren:update-tiles` (z. B. täglich).
- CLI-Test:
```bash
vendor/bin/typo3 feuerwehren:update-tiles -vvv
```

### 6) Routing prüfen
- Der Tile-Endpunkt wird via `Configuration/Routes.yaml` registriert.  
- Aufruf testen: `/_tiles/6/34/22.png` (Zoom/Koords anpassen) – sollte eine png-Kachel liefern.

---

## 🔧 Datenmodell (Kurzüberblick)
- **Rolle** (title)
- **Fahrzeugkategorie** (title)
- **Person** (title, slug, feUser, rolle→Rolle, gemeinde→Gemeinde, untergeordnet [MM])
- **Gemeinde** (name, slug, logo [FAL], gemeindegebiet [GeoJSON], feuerwehren [MM])
- **Feuerwehr** (Adresse, geo, `gruendungsdatum` [`DateTimeImmutable`], fahrzeugkategorien [MM], `jubilaeen` [IRRE])
- **Jubiläum** (feuerwehr→Feuerwehr, jahr, titel, beschreibung)

`crdate` wird überall als **UNIX-Timestamp (int)** geführt (TYPO3-Standard).

---

## 🧭 Plugins anlegen

### Karte
1. Neue Seite → **Inhalt** → **Plugin** → *Karte* (`feuerwehren_karte`).
2. Datensätze (Feuerwehr, Gemeinde, Fahrzeugkategorie) anlegen und zuordnen.
3. Seite aufrufen: Leaflet-Karte erscheint mit lokalen Kacheln und Filtern.

### Organigramm
1. Inhalt → **Plugin** → *Organigramm* (`feuerwehren_organigramm`).
2. Personen, Rollen, Gemeinden befüllen.

### Jubiläen
1. Inhalt → **Plugin** → *Jubiläen* (`feuerwehren_jubilaeen`).
2. Inline-Jubiläen an den jeweiligen Feuerwehren pflegen.

---

## 🗺️ Lokale OSM-Karten

### Variante A: MBTiles (empfohlen)
- Lade ein passendes `*.mbtiles` (z. B. Bereich deiner Landkreise).  
- Lege die Datei in `fileadmin/tiles/osm.mbtiles` ab (oder eigener Pfad) und setze `plugin.tx_feuerwehren.settings.mbtilesPath` entsprechend.

### Variante B: Proxy + Cache (Fallback)
- Setze `tileSource = proxy` und `proxyUrl`.
- Kacheln werden beim ersten Abruf in `var/tiles/` gecacht.

> **Hinweis zu Nutzungsbedingungen:** Beim Proxy-Betrieb die jeweiligen AGB/Tile-Policies beachten. Selbst gehostete Tiles/MBTiles bevorzugen.

---

## 🧩 Leaflet-Overlays
- **Gemeindegrenzen** werden aus `Gemeinde.gemeindegebiet` (GeoJSON) gelesen und als Layer gerendert.
- **Farbige Marker**: Beispielhaft über Fahrzeugkategorien; anpassbar in `Resources/Private/Templates/Feuerwehr/List.html`.

---

## 🔐 Rechte & Caches
- Redakteuren die Tabellenrechte für: `tx_feuerwehren_domain_model_*` geben.
- Nach Deploy: **Alle Caches leeren** (inkl. „PHP Cache“ bei Code-Änderungen).

---

## 🧪 Troubleshooting
- **Weiße Karte / 404-Tiles:** Prüfe `/_tiles/...`-Endpunkt, `mbtilesPath`, Dateirechte & `SQLite3`-Support.
- **Keine Marker:** Existieren Feuerwehr-Datensätze mit Latitude/Longitude? TS & Plugin auf Seite eingebunden?
- **Slug-Konflikte:** `uniqueInSite` kann Slug-Kollisionen erzwingen – Titel/Slug prüfen.
- **Scheduler findet Command nicht:** Caches leeren; `Configuration/Services.yaml` korrekt? Composer Autoload regenerieren (`composer dump-autoload`).

---

## 🧱 Entwicklung
- **Namespace:** `Schmid\Feuerwehren`
- **PSR-4:** `Classes/`  
- **CLI-Command:** `feuerwehren:update-tiles`
- **Tile-Route:** `/_tiles/{z}/{x}/{y}.png`

Pull Requests willkommen. Für größere Kartenprojekte lohnt sich ein dedizierter Tile-Server (z. B. Tegola/TileServer GL) oder regelmäßige MBTiles-Updates.

