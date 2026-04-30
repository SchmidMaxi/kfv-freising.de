# CMS Content Types - Übersicht aller Elemente

Diese Dokumentation beschreibt alle Content-Elemente, die für die Website gepflegt werden müssen.

---

## 📰 1. News / Nachrichten

**Seite:** `/news/{id}`  
**Komponente:** `NewsDetail.tsx`, `News.tsx`

### Felder:
| Feldname | Typ | Erforderlich | Beschreibung |
|----------|-----|--------------|--------------|
| `id` | Number | ✅ | Eindeutige ID |
| `title` | Text | ✅ | Überschrift (max. 100 Zeichen) |
| `excerpt` | Text | ✅ | Kurzbeschreibung für Teaser (max. 200 Zeichen) |
| `content` | Rich Text (HTML) | ✅ | Vollständiger Artikelinhalt |
| `date` | Datum | ✅ | Veröffentlichungsdatum (Format: "10. Dezember 2025") |
| `author` | Text | ✅ | Autor/Quelle |
| `category` | Select | ✅ | Kategorie: `Übung`, `Fahrzeuge`, `Verband`, `Einsatz`, `Ausbildung` |
| `image` | Bild (URL) | ✅ | Hauptbild (Hero-Bild, 1600x900px empfohlen) |
| `gallery` | Bildergalerie | ❌ | Optional: Array mit `src`, `alt`, `caption` |
| `relatedNews` | Relation | ❌ | Verknüpfte Artikel (IDs) |

---

## 📅 2. Termine / Events

**Seite:** `/termine`  
**Komponente:** `Termine.tsx`

### Felder:
| Feldname | Typ | Erforderlich | Beschreibung |
|----------|-----|--------------|--------------|
| `id` | Number | ✅ | Eindeutige ID |
| `datum` | Datum | ✅ | Datum im ISO-Format (YYYY-MM-DD) |
| `uhrzeit` | Text | ✅ | Uhrzeit (Format: "19:00") |
| `titel` | Text | ✅ | Titel des Termins |
| `ort` | Text | ✅ | Veranstaltungsort |
| `kategorie` | Select | ✅ | Kategorie: `Versammlung`, `Dienstversammlung`, `Ausbildung`, `Veranstaltung`, `Prüfung` |
| `beschreibung` | Text | ✅ | Beschreibung des Termins |

---

## 📥 3. Downloads / Dokumente

**Seite:** `/downloads`  
**Komponente:** `Downloads.tsx`

### Download-Kategorien:
| ID | Titel | Icon |
|----|-------|------|
| `ausbildung` | Ausbildung & Lehrgänge | BookOpen |
| `verwaltung` | Verwaltung & Satzung | Briefcase |
| `einsatz` | Einsatzunterlagen | Shield |
| `formulare` | Anträge & Formulare | FileText |
| `jugend` | Jugendfeuerwehr | Users |

### Download-Item Felder:
| Feldname | Typ | Erforderlich | Beschreibung |
|----------|-----|--------------|--------------|
| `name` | Text | ✅ | Dateiname/Titel |
| `format` | Text | ✅ | Dateiformat: `PDF`, `DOCX`, `XLSX` |
| `size` | Text | ✅ | Dateigröße (z.B. "245 KB") |
| `beschreibung` | Text | ❌ | Kurze Beschreibung |
| `kategorie` | Select | ✅ | Eine der oben genannten Kategorien |
| `datei` | Datei (URL) | ✅ | Download-Link |

---

## 🧑‍🤝‍🧑 4. Inspektionsmitglieder

**Seite:** `/inspektion/{id}`  
**Komponente:** `InspektionDetail.tsx`, `Verband.tsx`

### Felder:
| Feldname | Typ | Erforderlich | Beschreibung |
|----------|-----|--------------|--------------|
| `id` | Slug | ✅ | URL-Slug (z.B. "kreisbrandrat") |
| `name` | Text | ✅ | Vollständiger Name |
| `position` | Text | ✅ | Position/Titel |
| `funkrufname` | Text | ✅ | Funkrufname (z.B. "FS-Land 1") |
| `level` | Select | ✅ | Rang: `kbr`, `kbi`, `sbi`, `kbm`, `fkbm` |
| `section` | Text | ❌ | Abschnitt/Bereich (z.B. "Abschnitt 2") |
| `email` | E-Mail | ❌ | Kontakt-E-Mail |
| `phone` | Text | ❌ | Telefonnummer |
| `address` | Text | ❌ | Adresse |
| `image` | Bild (URL) | ❌ | Profilbild |
| `bio` | Rich Text | ❌ | Biografie/Beschreibung |
| `activesSince` | Text | ❌ | Aktiv seit (Jahr) |
| `achievements` | Text-Array | ❌ | Auszeichnungen & Ehrungen |
| `responsibilities` | Text-Array | ❌ | Aufgaben & Verantwortungsbereiche |
| `zustaendigeFeuerwehren` | Text-Array | ❌ | Zuständige Feuerwehren |
| `specializations` | Text-Array | ❌ | Fachgebiete |

### Level/Rang-Optionen:
| Wert | Anzeige | Farbe |
|------|---------|-------|
| `kbr` | Kreisbrandrat | Rot |
| `kbi` | Kreisbrandinspektor | Dunkelrot |
| `sbi` | Stadtbrandinspektor | Dunkelrot |
| `kbm` | Kreisbrandmeister | Orange |
| `fkbm` | Fach-Kreisbrandmeister | Gelb |

---

## 🚒 5. Feuerwehren

**Seite:** `/feuerwehr/{id}`  
**Komponente:** `FeuerwehrDetail.tsx`

### Basis-Felder:
| Feldname | Typ | Erforderlich | Beschreibung |
|----------|-----|--------------|--------------|
| `id` | Slug | ✅ | URL-Slug (z.B. "freising") |
| `name` | Text | ✅ | Vollständiger Name |
| `gemeinde` | Text | ✅ | Gemeinde/Stadt |
| `typ` | Select | ✅ | Typ: `Stützpunktfeuerwehr`, `Ortsfeuerwehr`, `Werkfeuerwehr` |
| `adresse` | Text | ✅ | Vollständige Adresse |
| `telefon` | Text | ✅ | Telefonnummer |
| `email` | E-Mail | ✅ | E-Mail-Adresse |
| `website` | URL | ❌ | Website-Link |
| `gruendung` | Number | ✅ | Gründungsjahr |
| `uebungszeiten` | Text | ✅ | Übungszeiten (z.B. "Jeden Dienstag, 19:30 Uhr") |
| `beschreibung` | Rich Text | ✅ | Beschreibungstext |

### Mitglieder-Objekt:
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `aktiv` | Number | Anzahl aktive Mitglieder |
| `jugend` | Number | Anzahl Jugendfeuerwehr |
| `passiv` | Number | Anzahl passive/fördernde Mitglieder |
| `gesamt` | Number | Gesamtzahl |

### Führung:
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `kommandant.name` | Text | Name des 1. Kommandanten |
| `kommandant.funktion` | Text | Funktionsbezeichnung |
| `stellvertreter.name` | Text | Name des 2. Kommandanten |
| `stellvertreter.funktion` | Text | Funktionsbezeichnung |

### Fahrzeuge (Array):
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `typ` | Text | Fahrzeugtyp (z.B. "HLF 20") |
| `kennzeichen` | Text | Amtliches Kennzeichen |
| `baujahr` | Number | Baujahr |
| `beschreibung` | Text | Kurzbeschreibung |

### Einsatzstatistik (Optional):
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `jahr` | Number | Bezugsjahr |
| `gesamt` | Number | Gesamteinsätze |
| `brand` | Number | Brandeinsätze |
| `thl` | Number | Technische Hilfeleistungen |
| `sonstige` | Number | Sonstige Einsätze |

### Letzte Einsätze (Array, Optional):
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `datum` | Text | Datum (Format: "20.12.2024") |
| `art` | Select | Art: `Brand`, `THL`, `Gefahrgut`, `Sonstige` |
| `ort` | Text | Einsatzort |
| `beschreibung` | Text | Kurzbeschreibung |

### Auszeichnungen (Array, Optional):
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `auszeichnung` | Text | Name der Auszeichnung |

### Bildergalerie (Array, Optional):
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `src` | Bild (URL) | Bild-URL |
| `alt` | Text | Alt-Text |
| `caption` | Text | Bildunterschrift |

---

## 🏠 6. Startseite (Homepage)

**Seite:** `/`  
**Komponenten:** `Hero.tsx`, `QuickActions.tsx`, `News.tsx`, `AboutSection.tsx`

### Hero-Bereich:
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `badge` | Text | Badge-Text (z.B. "Retten • Löschen • Bergen • Schützen") |
| `headline` | Text | Hauptüberschrift |
| `subheadline` | Text | Unterüberschrift/Beschreibung |
| `backgroundImage` | Bild (URL) | Hintergrundbild |
| `cta_primary` | Object | Primärer CTA (Text + Link) |
| `cta_secondary` | Object | Sekundärer CTA (Text + Link) |

### Statistik-Kacheln (Array):
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `icon` | Select | Icon: `Users`, `Shield`, `Clock` |
| `value` | Text | Wert (z.B. "4.200+") |
| `label` | Text | Beschriftung (z.B. "Aktive Mitglieder") |

### Schnellzugriff/QuickActions (Array):
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `icon` | Select | Icon-Name |
| `title` | Text | Titel |
| `description` | Text | Beschreibung |
| `href` | URL | Zielseite |
| `variant` | Select | Variante: `accent`, `primary` |

### Über-uns-Bereich:
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `headline` | Text | Überschrift |
| `description` | Rich Text | Beschreibungstext |
| `features` | Array | Feature-Liste (Icon, Titel, Beschreibung) |
| `stats` | Array | Statistiken (Fläche, Einwohner etc.) |

### Kommende Termine Sidebar:
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `title` | Text | Titel des Termins |
| `date` | Text | Datum |
| `location` | Text | Ort |

---

## 📞 7. Kontaktseite

**Seite:** `/kontakt`  
**Komponente:** `Kontakt.tsx`

### Kontaktinformationen (Array):
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `icon` | Select | Icon: `Phone`, `Mail`, `MapPin`, `Clock` |
| `title` | Text | Bezeichnung (z.B. "Telefon") |
| `content` | Text | Inhalt (Telefonnummer, E-Mail etc.) |
| `href` | URL | Link (tel:, mailto:) - optional |

### Notruf-Box:
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `headline` | Text | Überschrift |
| `description` | Text | Beschreibung |
| `number` | Text | Notrufnummer (112) |
| `label` | Text | Label-Text |

---

## 🖼️ 8. Bildergalerie (Wiederverwendbar)

**Komponente:** `ImageGallery.tsx`

### Galerie-Bild Felder:
| Feldname | Typ | Erforderlich | Beschreibung |
|----------|-----|--------------|--------------|
| `src` | Bild (URL) | ✅ | Bild-URL (800px Breite empfohlen) |
| `alt` | Text | ✅ | Alt-Text für Barrierefreiheit |
| `caption` | Text | ❌ | Bildunterschrift |

**Verwendung in:**
- News-Artikel (`/news/{id}`)
- Feuerwehr-Detailseiten (`/feuerwehr/{id}`)

---

## 🎨 9. Globale Elemente

### Header-Navigation:
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `label` | Text | Link-Text |
| `href` | URL | Zielseite |
| `submenu` | Array | Untermenü (optional) |

### Footer:
| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `about_text` | Text | Beschreibungstext |
| `contact_phone` | Text | Telefonnummer |
| `contact_email` | E-Mail | E-Mail-Adresse |
| `quick_links` | Array | Schnellzugriff-Links |
| `social_links` | Array | Social Media Links (Facebook, Instagram, YouTube) |
| `copyright` | Text | Copyright-Text |

---

## 📐 Empfohlene Bildgrößen

| Verwendung | Breite | Höhe | Format |
|------------|--------|------|--------|
| Hero-Hintergrundbild | 2070px | 1380px | JPG/WebP |
| News Hauptbild | 1600px | 900px | JPG/WebP |
| Galerie-Bilder | 800px | 600px | JPG/WebP |
| Profilbilder | 400px | 400px | JPG/PNG |
| Fahrzeugbilder | 800px | 600px | JPG/WebP |

---

## 🏷️ Kategorien-Übersicht

### News-Kategorien:
- `Übung`
- `Fahrzeuge`
- `Verband`
- `Einsatz`
- `Ausbildung`

### Termin-Kategorien:
- `Versammlung`
- `Dienstversammlung`
- `Ausbildung`
- `Veranstaltung`
- `Prüfung`

### Download-Kategorien:
- `ausbildung` - Ausbildung & Lehrgänge
- `verwaltung` - Verwaltung & Satzung
- `einsatz` - Einsatzunterlagen
- `formulare` - Anträge & Formulare
- `jugend` - Jugendfeuerwehr

### Feuerwehr-Typen:
- `Stützpunktfeuerwehr`
- `Ortsfeuerwehr`
- `Werkfeuerwehr`

### Einsatz-Arten:
- `Brand`
- `THL` (Technische Hilfeleistung)
- `Gefahrgut`
- `Sonstige`

---

## 💡 Hinweise für die CMS-Implementierung

1. **Slugs/IDs**: Sollten URL-freundlich sein (Kleinbuchstaben, keine Sonderzeichen, Bindestriche statt Leerzeichen)

2. **Rich Text**: Unterstützt HTML-Tags: `<p>`, `<h3>`, `<ul>`, `<li>`, `<strong>`, `<a>`

3. **Relationen**: 
   - News → News (relatedNews)
   - Inspektionsmitglieder → Feuerwehren (zustaendigeFeuerwehren)

4. **Arrays**: Für wiederholbare Elemente (Fahrzeuge, Auszeichnungen, Galerie-Bilder)

5. **Datumsformate**: 
   - ISO-Format für Termine: `2025-01-15`
   - Lesbares Format für Anzeige: `15. Januar 2025`

6. **Validierung**: E-Mail-Felder sollten validiert werden
