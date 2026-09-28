# Downloads-Extension

TYPO3-Plugin, das Dateien aus dem Fileadmin als Download-Liste anzeigt — gruppiert
nach Kategorie, filterbar nach Kategorie und durchsuchbar per Stichwort/Dateiname.

Es gibt **keine eigene Datenbanktabelle** für Downloads. Die Extension nutzt
ausschließlich TYPO3-Bordmittel:

- **Kategorie** = TYPO3-Systemkategorien (`sys_category`)
- **Schlagwörter** = das Standardfeld „Keywords" der Datei-Metadaten
- **Dateiauswahl** = TYPO3-Datei-Sammlungen (`sys_file_collection`, Typ „Ordner")

Das heißt: Redakteure pflegen Downloads komplett über die normale
Dateiliste — keine Extra-Tabelle, kein Extra-Backend-Modul.

---

## Anleitung: Neue Downloads pflegen

### 1. Datei hochladen

1. TYPO3-Backend → **Dateiliste**
2. In den passenden Ordner wechseln (z. B. `fileadmin/downloads/`)
3. Datei per Drag & Drop hochladen

> Tipp: Für Downloads einen eigenen Ordner (oder Unterordner je Bereich)
> anlegen, z. B. `fileadmin/downloads/satzung/`, `fileadmin/downloads/formulare/`.
> Die Ordnerstruktur selbst hat keine Auswirkung auf die Anzeige — die
> Gruppierung im Frontend läuft ausschließlich über die Kategorie.

### 2. Datei kategorisieren & mit Schlagwörtern versehen

1. In der Dateiliste auf die Datei klicken → **„Metadaten bearbeiten"**
2. Reiter **„Kategorien"**: eine oder mehrere Kategorien auswählen
   (siehe [Kategorien anlegen](#3-kategorien-anlegen-einmalig--bei-bedarf))
3. Feld **„Schlagwörter"** (im Reiter „Allgemein"): kommagetrennte
   Stichwörter eintragen, z. B. `satzung, verband, pflichtlektüre`
4. Optional: **Titel** und **Beschreibung** pflegen — werden im Frontend
   als Anzeigename bzw. Untertitel verwendet. Ohne Titel wird der
   Dateiname angezeigt.
5. Speichern

**Wichtig:** Eine Datei ohne zugewiesene Kategorie erscheint im Frontend
trotzdem — unter der automatischen Sammelkategorie **„Sonstige"**, die
immer als letzte Gruppe angezeigt wird.

### 3. Kategorien anlegen (einmalig / bei Bedarf)

1. Backend → **Liste**-Modul → auf die Wurzelseite wechseln (oder über
   **Web → Liste** die Seite mit den Kategorie-Datensätzen öffnen)
2. Neuen Datensatz vom Typ **„Kategorie"** anlegen
3. Titel vergeben (z. B. „Satzung", „Formulare", „Merkblätter")
4. Empfehlung: alle Downloads-Kategorien einer gemeinsamen
   Über-Kategorie **„Downloads"** unterordnen (Feld „Übergeordnete
   Kategorie"), damit sie sich nicht mit Kategorien anderer Bereiche
   (z. B. Kalender/News) vermischen

Kategorien sind **global** — einmal angelegt, stehen sie bei jeder
Datei zur Auswahl.

### 4. Datei-Sammlung (Ordner) anlegen

Das Plugin zeigt nicht automatisch „den ganzen Fileadmin", sondern nur
Dateien aus explizit ausgewählten **Datei-Sammlungen**. Das verhindert,
dass z. B. Bilder aus Content-Elementen versehentlich als Download
auftauchen.

1. Backend → **Liste**-Modul → Datensatz vom Typ **„Datei-Sammlung"** anlegen
2. **Typ**: „Ordner"
3. **Ordner**: den gewünschten Fileadmin-Ordner auswählen
4. Optional **„Rekursiv"** aktivieren, wenn Unterordner mit einbezogen
   werden sollen
5. Titel vergeben (dient nur der Wiedererkennung im Backend, wird nicht
   im Frontend angezeigt)

Eine Datei-Sammlung kann mehrfach wiederverwendet werden — z. B. auf
mehreren Seiten mit unterschiedlichen Plugin-Instanzen.

### 5. Plugin auf einer Seite einfügen

1. Auf der gewünschten Seite ein neues Inhaltselement anlegen
2. Typ **„Downloads"** wählen (unter „Plugins")
3. Im Reiter **„Plugin"**:
   - **Datei-Sammlungen**: eine oder mehrere der in Schritt 4 angelegten
     Sammlungen auswählen (Mehrfachauswahl möglich — Inhalte werden
     zusammengeführt)
   - **Erlaubte Dateiendungen** (optional): kommagetrennt, z. B.
     `pdf,docx,xlsx` — leer lassen, um alle Dateitypen zuzulassen
4. Speichern

Fertig — die Seite zeigt jetzt alle Dateien aus den gewählten
Sammlungen, nach Kategorie gruppiert, mit Kategorie-Filter und
Suchfeld.

---

## Frontend-Verhalten (zur Einordnung)

- **Gruppierung**: nach der *ersten* zugewiesenen Kategorie einer Datei.
  Hat eine Datei mehrere Kategorien, erscheint sie nur in der Gruppe der
  zuerst zugewiesenen — nicht mehrfach.
- **Kategorie-Filter** (Pills über der Liste): blendet ganze Gruppen
  ein/aus, „Alle Kategorien" setzt den Filter zurück.
- **Suchfeld**: durchsucht Titel, Beschreibung, Dateiname und
  Schlagwörter — rein clientseitig (kein Nachladen), daher auch bei
  vielen Dateien sofort reaktionsschnell.
- **Dateigröße/-typ**: werden automatisch aus der Datei gelesen, keine
  manuelle Pflege nötig.

---

## Typische Stolperfallen

| Problem | Ursache / Lösung |
|---|---|
| Datei erscheint gar nicht | Liegt sie im Ordner einer der im Plugin ausgewählten Datei-Sammlungen? Passt die Dateiendung zum Filter „Erlaubte Dateiendungen"? |
| Datei erscheint unter „Sonstige" statt gewünschter Kategorie | Metadaten der Datei prüfen (Dateiliste → Datei → Metadaten bearbeiten → Kategorien) — wurde wirklich gespeichert? |
| Neue Kategorie taucht nicht als Filter-Pill auf | Pills werden nur für Kategorien angezeigt, die *aktuell mindestens eine sichtbare Datei* haben |
| Änderung an Metadaten erscheint nicht sofort im Frontend | TYPO3-Seiten-Cache leeren (Backend → im Anzeigebereich der Seite auf „Cache löschen", oder als Admin: **Web → Info** → Cache-Symbol) |

---

## Technischer Hintergrund

Für Entwickler: Architektur, Dateistruktur und Implementierungsdetails
siehe [`CLAUDE.md`](./CLAUDE.md) in diesem Verzeichnis.
