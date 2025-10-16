# feuerwehren (TYPO3 13 LTS, PHP 8.4)

Vektor-Karten (MapLibre GL JS) für Gemeinden/Feuerwehren, Organigramm & Jubiläen.
**Vendor:** Schmid · **Extension key:** `feuerwehren`

---

## ✨ Features
- **Karte (MapLibre GL JS):** Marker für Feuerwehren, Filter nach Fahrzeugkategorien, Gemeindegrenzen als GeoJSON-Overlay. Basiert auf Vektor-Kacheln.
- **Organigramm:** Abbildung von Personen und deren Zuordnungen (Rolle, Gemeinde, Hierarchie).
- **Jubiläen:** Verwaltung von Jubiläen als Inline-Relation zu Feuerwehren.
- **Lokale Vektor-Tiles:** Endpunkt `/_vt/{z}/{x}/{y}.pbf` aus **MBTiles**.
- **Mächtiger CLI-Command:** `feuerwehren:tiles:prefetch` zum Herunterladen von Vektor-Kacheln von Anbietern wie MapTiler.

---

## ✅ Requirements
- TYPO3 **13 LTS**
- PHP **8.4** (kompatibel ab 8.2)
- Datenbank: MariaDB/MySQL (oder kompatibel)
- **SQLite3** PHP-Erweiterung (für den Zugriff auf `.mbtiles`-Dateien)

---

## 📦 Installation & Konfiguration

### 1. Code & Aktivierung
- **Composer (empfohlen):** `composer require schmid/feuerwehren`
- **Aktivieren:** Im TYPO3-Backend → Extensions `feuerwehren` aktivieren und die Datenbank aktualisieren.

### 2. TypoScript einbinden
- Binden Sie das TypoScript der Extension in Ihr Seiten-Template oder Ihre Site-Konfiguration ein.

### 3. Vektor-Kacheln (MBTiles) bereitstellen
Diese Extension ist für die Verwendung mit Vektor-Kacheln im MBTiles-Format optimiert.

1.  **MBTiles-Datei besorgen:** Laden Sie eine `*.mbtiles`-Datei mit Vektor-Kacheln für Ihre Region herunter (z.B. von [MapTiler Data](https://data.maptiler.com/downloads/planet/)).
2.  **Datei ablegen:** Platzieren Sie die Datei auf Ihrem Server, z.B. unter `fileadmin/tiles/vektorkarte.mbtiles`.
3.  **Pfad konfigurieren:** Setzen Sie den Pfad in Ihrer TypoScript-Site-Konfiguration:
    ```typoscript
    plugin.tx_feuerwehren.settings.vectorTiles.mbtilesPath = fileadmin/tiles/vektorkarte.mbtiles
    ```

### 4. Vektor-Kacheln per CLI herunterladen (Alternativ)
Sie können Kacheln auch direkt von einem Anbieter herunterladen und in eine `mbtiles`-Datei speichern. Der `tiles:prefetch`-Befehl ist dafür ideal.

**Beispiel:** Lädt Kacheln für den Landkreis Freising (Zoom 9-14) von MapTiler.
```bash
ddev exec vendor/bin/typo3 feuerwehren:tiles:prefetch --preset="oberbayern" --zooms="6-14" --source="https://api.maptiler.com/tiles/v3/{z}/{x}/{y}.pbf?key=qKjtLVvUmYMRbgrRm72l