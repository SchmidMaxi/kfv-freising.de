# Plan: BayernAtlas-Tiles statt OSM in feuerwehren-Extension

**Status:** Konzept final. **Zielarchitektur beschlossen: Konzept D (Selektiver Self-Host).** Umsetzung noch nicht gestartet — dieser Plan ist die Grundlage für ein künftiges Umsetzungs-Ticket.
**Quelle-Frage:** Umstellung Feuerwehren-Karte (MapLibre GL JS) von self-hosted OSM-Vector-Tiles auf BayernAtlas-Datenbasis. Tiles weiterhin selbst hostbar? Abdeckung: Landkreis Freising + angrenzende Landkreise.
**Complexity:** Medium

## Entscheidung

Tile-Datenquelle wechselt von eigenem OSM-Extrakt (MBTiles) auf die offizielle **"Webkarte Vektor Bayern"** (identische Datenbasis wie BayernAtlas, PBF/MapLibre-Spezifikation). Bereitstellung als **selektiver Self-Host**: monatlich vollständiges Bayern-Paket laden, nur die Region **Landkreis Freising + direkt angrenzende Landkreise** herausfiltern, davon ausliefern. Bestehende MapLibre-Architektur (Marker, GeoJSON-Overlays, Klick/Popup, Such-Backend) bleibt erhalten — nur Tile-/Style-Quelle und die Ingest-Pipeline ändern sich.

Verworfene Alternativen (Details/Begründung siehe Risikotabelle):

| Alternative | Grund für Verwerfung |
|---|---|
| bav4 WebComponent (`<bayern-atlas>`) | Beta, Dark-Mode + Marker-Custom-Styling nicht dokumentiert, kompletter Kartenschicht-Rewrite |
| Live Pull-Through-Cache/Proxy gegen gehosteten Dienst | Rechtlich ungeklärt (ToS deckt nur Download-Paket-Self-Host und direkte Client-Nutzung ab, kein serverseitiges Caching des gehosteten Diensts) |
| Direkte Client-Nutzung (`vtod1.bayernwolke.de` ohne eigenes Hosting) | Verliert bewusste Offline-/Self-Hosted-Resilienz der bestehenden Architektur (Tracker D5), harte Dauerabhängigkeit von Bayern-Infrastruktur |
| Vollständiger Self-Host (ganz Bayern, 2,8 GB dauerhaft) | Rechtlich sauber, aber unnötiger Speicher-/Bandbreiten-Overhead für Daten außerhalb des Kartenausschnitts |

## Ist-Zustand (Referenz)

- `packages/feuerwehren/Resources/Public/JavaScript/map-feuerwehren.js`: MapLibre GL JS, `buildBaseStyle()` Light-/Dark-Variante (D4), ~80 Marker Farbe nach Typ, Klick→Popup+Detailseite (D8), GeoJSON-Overlays (Gemeinden/KBM/KBI)
- `packages/feuerwehren/Classes/Controller/VectorTileController.php`: liest PBF-Kachel aus SQLite-MBTiles (`fileadmin/tiles/osm.mbtiles`), Gzip-Erkennung, 1-Jahr-Cache-Header, Route `_vt/{z}/{x}/{y}.pbf`
- `packages/feuerwehren/Classes/Command/MbtilesFetchCommand.php` (`feuerwehren:mbtiles:fetch`): lädt/importiert MBTiles-Datei
- `packages/feuerwehren/Classes/Command/TilesPrefetchCommand.php` (`feuerwehren:tiles:prefetch`): rendert/lädt Kacheln gegen OSM vor; bestehende Bbox-Presets `--preset=freising` `[11.40, 48.25, 12.00, 48.60]` und `--preset=oberbayern` `[10.50, 47.25, 12.80, 48.90]` in `resolveBbox()` (Zeile 178ff.) — Freising-Preset zu eng für Nachbar-Landkreise, Oberbayern-Preset viel zu groß
- Eigenes Such-/Filter-Backend (`SearchService`, `FeuerwehrRepository`) — unberührt

## Zielabdeckung: Landkreis Freising + Nachbar-Landkreise

Angrenzend an Landkreis Freising: Landkreis Dachau (West), Landeshauptstadt München + Landkreis München (Süd), Landkreis Erding (Ost), Landkreis Landshut (Nord/Nordost), Landkreis Pfaffenhofen a.d. Ilm (Nordwest).

Neuer Bbox-Preset (Rechteck, grob, analog bestehendem Preset-Muster — keine präzise Polygon-Grenze nötig, da Tile-Filter ohnehin rechteckig auf XYZ-Kacheln arbeitet):

```
'freising-region' => [11.10, 47.95, 12.35, 48.85]  // lon West, lat Süd, lon Ost, lat Nord
```

Deckt Freising + alle 5 Nachbar-Landkreise komplett ab, deutlich kleiner als `oberbayern`-Preset. Vor Umsetzung mit tatsächlichem Kartenbedarf (Zoom-Verhalten, Pan-Radius) gegenprüfen und ggf. nachschärfen.

## Zielkonzept D — Selektiver Self-Host

1. Monatlicher Sync-Job lädt das komplette Download-Paket (~2,8 GB gzip, 15 Zoomstufen) in ein **temporäres** Verzeichnis (kein Dauerspeicher)
2. Per Standard-Slippy-Tile-Mathematik (Bbox `freising-region` × Zoom 8–14, analog aktuellem `--zooms`-Parameter) wird **nur der relevante Kachel-Ausschnitt** in das Serving-Verzeichnis kopiert — reines Datei-Filtern, kein Rendering, da Kacheln im Paket bereits vorgerendert vorliegen
3. Atomarer Swap (neues Verzeichnis → Symlink `fileadmin/tiles/current` umbiegen), altes Verzeichnis bleibt bis Erfolgsprüfung als Fallback erhalten
4. Statisches Serving wie von LDBV dokumentiert (CORS + `Content-Encoding: gzip` + `application/x-protobuf` auf `.pbf`), realisiert über angepassten `VectorTileController`
5. Rechtlich = vollständiger Self-Host (von LDBV explizit für Offline-/Eigenhosting-Anwendungsfall vorgesehen), nur mit vorgeschaltetem Filterschritt — kein Zugriff auf den live gehosteten Dienst zur Laufzeit

**Ergebnis:** Serving-Footprint (Freising + 5 Nachbar-Landkreise, Zoom 8–14) deutlich kleiner als 2,8 GB Bayern-weit, rechtlich auf sicherem Grund (= sanktionierter Self-Host), technisch fast 1:1 Ersatz der heutigen OSM-Pipeline.

## CLI-Command-Anpassung

| Command | Heute | Neu (Konzept D) |
|---|---|---|
| `MbtilesFetchCommand.php` (`feuerwehren:mbtiles:fetch`) | Lädt/importiert fertige MBTiles-Datei (SQLite) von beliebiger URL | Ersetzen durch `feuerwehren:tiles:sync-bayernvektor`: (1) Download-Paket-URL laden nach `var/tmp/`, (2) Bbox+Zoom-Filter (`--preset=freising-region --zooms=8-14`) anwenden, (3) Ergebnis nach `fileadmin/tiles/bayernvektor/` kopieren, (4) Symlink `fileadmin/tiles/current` atomar umbiegen, (5) alte Verzeichnisse nach N erfolgreichen Zyklen aufräumen |
| `TilesPrefetchCommand.php` (`feuerwehren:tiles:prefetch`) | Rendert/lädt Kacheln aktiv gegen OSM-Tile-Server vor, eigene Rendering-Pipeline | **Entfällt komplett** — Bayern-Paket liefert bereits vorgerenderte Kacheln. `resolveBbox()`-Presets (inkl. neuem `freising-region`) wandern in den neuen Sync-Command |
| `Configuration/Services.yaml` (Scheduler-Registrierung, Zeilen 15+21) | Task-Einträge für beide Commands | `TilesPrefetchCommand`-Eintrag entfernen, `MbtilesFetchCommand`-Eintrag durch neuen Sync-Command ersetzen, Intervall auf **monatlich** |
| `VectorTileController.php` | Liest PBF via `SQLite3` aus `.mbtiles` | `readTileFromMbtiles()` ersetzen durch Datei-Lesen `fileadmin/tiles/current/{z}/{x}/{y}.pbf` — Gzip-Erkennung/Cache-Header-Logik unverändert, Route `_vt/{z}/{x}/{y}.pbf` bleibt stabil (kein Änderungsbedarf im Frontend bzgl. URL) |

URL-Vertrag zum Frontend bleibt exakt gleich — nur die serverseitige Quelle hinter dem Endpoint wechselt.

## Was am MapLibre-Frontend erhalten bleibt

- Marker-Rendering + Farbcodierung nach Typ (eigene MapLibre-Marker-API, unabhängig vom Style)
- GeoJSON-Overlays (Gemeinden/KBM/KBI)
- Klick/Popup/Detailseiten-Navigation (D8)
- Eigenes Such-/Filter-Backend unverändert
- `buildBaseStyle()` referenziert künftig Bayern-`style.json` (Standard/**Nacht** für Dark-Mode, D4) statt eigenem Style — einzige Style-seitige Änderung

## Risikovergleich (Referenz, warum D gewählt wurde)

| Risiko | bav4 WebComponent | Vollständiger Self-Host | Live Pull-Through-Cache | Direkt-Client | **D (final)** |
|---|---|---|---|---|---|
| Dark-Mode-Regression | Hoch (unbelegt) | Niedrig (Nacht-Stil, Test nötig) | Niedrig | Niedrig | Niedrig |
| Marker-Custom-Styling-Regression | Hoch (unbelegt) | Keine | Keine | Keine | Keine |
| Rechtliche Klarheit | Embedding ja, Details offen | ✅ explizit sanktioniert | ❌ ungeklärt | ✅ explizit empfohlen | ✅ explizit sanktioniert |
| Externe Laufzeit-Abhängigkeit | Ja, dauerhaft | Nein | Ja, bei Cache-Miss | Ja, dauerhaft | Nein (nur beim monatlichen Sync) |
| Speicher-/Bandbreiten-Aufwand | — | Hoch (2,8 GB dauerhaft) | Niedrig | Keiner | Niedrig (Freising + Nachbarn) |
| Architektur-Umfang | Groß (Kartenschicht neu) | Mittel | Mittel-Groß | Klein | Mittel (Sync-Job + Controller) |
| Reifegrad | Beta | Stabil | Stabil, aber ToS-Risiko | Stabil | Stabil |

## Offene Punkte vor Umsetzung

1. Bbox `freising-region` gegen tatsächlichen Kartenbedarf verifizieren (Pan-Radius, Zoom-Verhalten)
2. Bbox/Zoom-Filter-Logik implementieren + testen (kleiner Spike, Ergebnisgröße messen)
3. Visueller Vergleich Nacht-Stil vs. bestehendes Dark-Theme (D4)
4. Speicherbedarf des temporären 2,8-GB-Downloads auf Produktionsserver klären (www416.your-server.de, Hosting-Kontingent prüfen)
5. Scheduler-Task-Design für monatlichen Sync (Fehlerfall: alten Tile-Stand behalten, kein kaputter Swap)
6. Attribution/Quellenvermerk (DL-DE-BY-2.0/CC BY) im Kartenbereich ergänzen — Text/Platzierung festlegen
7. Fonts/Sprites aus Download-Paket mit ausliefern (statische Assets, kein Filterbedarf)

## Nächste Schritte

1. Kleiner Spike: Download-Paket laden, Bbox-Filter-Skript gegen `freising-region` testen, Ergebnisgröße messen
2. `by_style_standard.json` + `by_style_nacht.json` lokal gegen bestehende Marker-/Overlay-Logik prüfen
3. Scheduler-Task-Konzept für `feuerwehren:tiles:sync-bayernvektor` entwerfen
4. Nach erfolgreichem Spike: Umsetzungs-Ticket aus diesem Plan ableiten (CLI-Command, Controller-Umbau, Style-Wechsel, Scheduler)

## Quellen

- https://ldbv-by.github.io/bav4-docs/
- https://github.com/ldbv-by/bav4
- https://www.ldbv.bayern.de/produkte/karten/webvektor.html
- https://geodaten.bayern.de/odd/m/3/pdf/webkartevektordownload_anleitung.pdf (offizielle Hosting-Anleitung)
- https://www.ldbv.bayern.de/mam/ldbv/dateien/faq_webkarte_vektor_bayern.pdf (offizielle FAQ, u. a. Q3/Q6/Q7/Q8/Q11)
- https://github.com/ldbv-by/webkarte-vektor-bayern
- LDBV Nutzungsbedingungen Geoportal/BayernAtlas
