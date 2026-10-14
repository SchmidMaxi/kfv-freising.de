# Plan: Inventar-Katalog Katastrophenschutz-Halle (EXT:katschutz)

**Stand:** 2026-10-14
**Entscheidung:** TYPO3-Extension (`packages/katschutz`, Namespace `Schmid\Katschutz`), keine Laravel-Lösung.
**Komplexität:** Mittel–Groß (MVP ≈ 5–7 Arbeitstage)

## Ziel

Alle Gegenstände der KatS-Halle erfassen (was, wie viel, wo eingelagert), Bestellungen durch die
Inspektion (FE-Gruppe) mit **Pflichtangabe der anfordernden Feuerwehr**, und ein Verwalter-Dashboard
(FE), das jederzeit zeigt: Bestand, Lagerort, Verbleib (bei welcher Feuerwehr ist was gerade).

## Getroffene Entscheidungen (Rückmeldung 2026-10-14)

| # | Frage | Entscheidung |
|---|-------|--------------|
| 1 | Mengenartikel oder Einzelstück? | **Beides.** Artikel hat `typ = menge|einzeln`. |
| 2 | Freigabe durch Verwalter? | **Ja, abschaltbar** über Site-Setting `katschutz.freigabeErforderlich` (settings.yaml). |
| 3 | Verwalter FE oder BE? | **FE-User.** Verwalter pflegen Stammdaten im Frontend (CRUD), beschränkt auf die Verwalter-Gruppe. |
| 4 | Bestellung nur für eigenen Bereich? | **Nein.** Im Katastrophenfall werden KBM/KBI Einsatzabschnitten zugeteilt. Select mit zwei `<optgroup>`: 1. „Mein Bereich“, 2. „Weitere Feuerwehren“, beide alphabetisch. |
| 5 | Extras im MVP | Lieferschein-PDF, Prüffristen, Sets/Module, Großschadenslage-Modus |

## Neue Ideen aus den Antworten (in den Plan übernommen)

1. **Bereichsfremde Bestellung wird markiert.** Wählt eine Inspektion eine Feuerwehr aus „Weitere
   Feuerwehren“, wird das Feld *Einsatzabschnitt / Begründung* Pflicht und die Bestellung bekommt
   das Flag `bereichsfremd` (Badge im Dashboard). Kein Verbot, aber nachvollziehbar.
2. **Großschadenslage als Datensatz („Lage“) statt bloßem Schalter.** Verwalter aktivieren im FE eine
   Lage (Titel, Beginn, Ende). Solange aktiv: Freigabeschritt entfällt, Banner auf allen
   Katschutz-Seiten, alle Bestellungen/Buchungen werden der Lage zugeordnet → Abschlussübersicht
   „Was wurde während Lage X wohin ausgegeben, was ist noch draußen“ (wichtig für Abrechnung).
3. **Umbuchung zwischen Feuerwehren** ohne Rückweg in die Halle (Pumpe wandert im Einsatz von
   FF A zu FF B). Eigener Buchungstyp `umbuchung`, im Verbleib sofort sichtbar.
4. **Bestand nur über Buchungen.** Da Verwalter im FE Datensätze anlegen/bearbeiten, darf der
   Bestand nicht frei editierbar sein. Zugang, Inventur-Korrektur, Aussonderung sind eigene
   Buchungen → lückenloser Audit-Trail, wer wann was geändert hat.
5. **Archivieren statt Löschen.** Artikel/Einzelstücke mit Buchungen werden nur ausgeblendet
   (`hidden`), nie gelöscht, sonst bricht die Historie.
6. **Reservierter Bestand.** Freigegebene, noch nicht ausgegebene Mengen zählen als reserviert →
   „verfügbar = in Halle − reserviert“. Katalog zeigt Verfügbarkeit, Dashboard warnt bei Überbuchung.
7. **Prüffällige Einzelstücke sind gesperrt.** Überfällige Prüfung oder Mangel → Einzelstück kann
   nicht ausgegeben werden (Verwalter sieht Grund).
8. **Rückgabe überfällig** (Bestellung `bis` < heute, Leihgut noch draußen) als Dashboard-Kachel.
9. **Einzelstücke wählt der Verwalter bei der Ausgabe**, nicht der Besteller. Inspektion bestellt
   „2× Tauchpumpe“, Verwalter ordnet konkrete Inventarnummern zu; die landen auf dem Lieferschein.
10. **Rücknahmebeleg** als zweites PDF (gleiches Template, andere Überschrift/Positionen).

## Rollen & Zugriff

| Rolle | FE-Gruppe (Setting) | Darf |
|-------|---------------------|------|
| Inspektion | `katschutz.gruppeInspektion` (Default `Inspektion`, Titel oder UID) | Katalog ansehen, bestellen, eigene Bestellungen ansehen/stornieren, Lieferschein der eigenen Bestellung |
| Verwalter | `katschutz.gruppeVerwalter` (Default `KatS-Verwalter`) | Alles: Dashboard, Freigabe, Ausgabe/Rücknahme/Umbuchung, Stammdaten-CRUD, Prüfungen, Lage aktivieren |

Doppelte Absicherung: Seiten per `fe_group` geschützt **und** jede Controller-Action prüft die
Gruppe serverseitig (`AccessService`). Alle Plugins sind komplett nicht cachebar.

**Mein Bereich** wird ermittelt über: `fe_users` → `tx_feuerwehren_domain_model_person.fe_user`
→ `tx_feuerwehren_domain_model_area.person` → je nach Typ:
- `kbm`: Gemeinden mit `kbm_area = area` → Feuerwehren (`tx_feuerwehren_gemeinde_feuerwehr_mm`)
- `kbi`: alle KBM-Kinder (`parent = area`) → wie oben
- `kbr`: alle Feuerwehren (zweite Optgroup entfällt)
- `fach-kbm` / keine Zuordnung: kein eigener Bereich → nur „Alle Feuerwehren“

## Datenmodell

```
Kategorie     title, sorting
Lagerort      title, beschreibung                                  (Halle > Regal > Fach als Freitext)
Artikel       title, beschreibung, kategorie, bilder (FAL), typ (menge|einzeln),
              verbrauch (bool), einheit, mindestbestand, lagerort, lagerfach,
              pruefintervall (Monate), hidden
Einzelstueck  artikel, inventarnummer, seriennummer, lagerort, zustand (ok|mangel|defekt),
              naechste_pruefung (date), bemerkung, hidden
Pruefung      einzelstueck, datum, ergebnis (bestanden|mangel), pruefer, bemerkung
              → setzt einzelstueck.naechste_pruefung = datum + artikel.pruefintervall
Set           title, beschreibung, positionen (1:n SetPosition)
SetPosition   set, artikel, menge
Lage          title, beginn, ende, aktiv (bool)                     (Großschadenslage)
Bestellung    nummer, feuerwehr → tx_feuerwehren_domain_model_feuerwehr, besteller → fe_users,
              anlass, einsatzabschnitt, von, bis, status, bereichsfremd, lage, notiz,
              verwalter_notiz, freigegeben_von, freigegeben_am
Position      bestellung, artikel, menge, set (nur Herkunftsinfo)
Buchung       typ, artikel, einzelstueck, menge, feuerwehr, ziel_feuerwehr, bestellung,
              lage, fe_user, crdate, bemerkung                      (nur INSERT, nie UPDATE)
```

**Buchungstypen und Wirkung** (Leihgut; bei Verbrauchsmaterial zählt Ausgabe nicht als „draußen“):

| Typ | Halle | Bei Feuerwehr |
|-----|-------|---------------|
| zugang | + m | |
| korrektur (Inventur, ± m) | ± m | |
| aussonderung | − m | |
| ausgabe → FW | − m | + m (nur Leihgut) |
| ruecknahme ← FW | + m | − m (nur Leihgut) |
| umbuchung FW → Ziel-FW | | − m / + m |
| verlust (in Halle oder bei FW) | − m (Halle) | − m (bei FW) |

**Bestellstatus:** `neu` → `freigegeben` (automatisch, wenn Freigabe aus oder Lage aktiv) →
`ausgegeben` (sobald etwas ausgegeben ist) → `abgeschlossen` (Leihgut komplett zurück oder Verwalter
schließt ab). Nebenwege: `abgelehnt` (Verwalter), `storniert` (Besteller, nur `neu`/`freigegeben`).

## Oberflächen (Plugins = eigene CTypes)

**`katschutz_bestellung` (Inspektion)**
- Katalog: Kategorien-Filter, Suche, Karten mit Bild, Verfügbarkeit, Lagerort
- Bestellformular: Feuerwehr-Select mit 2 Optgroups, Anlass, Einsatzabschnitt, Zeitraum,
  Mengenfelder je Artikel + Sets (kein Warenkorb-State, JS-frei)
- Meine Bestellungen: Liste, Detail, Stornieren, Lieferschein-PDF

**`katschutz_verwaltung` (Verwalter)**
- Dashboard: Kacheln (offene Anfragen, Leihgut draußen, unter Mindestbestand, Prüfung fällig,
  Rückgabe überfällig, aktive Lage), Bestandstabelle, Verbleib nach Feuerwehr
- Bestellungen: freigeben/ablehnen, Ausgabe buchen (Einzelstücke zuordnen), Rücknahme
  (Zustand/Verlust), Umbuchung, abschließen, Lieferschein-/Rücknahme-PDF
- Stammdaten-CRUD: Artikel, Kategorien, Lagerorte, Einzelstücke, Sets
- Buchungen: Zugang, Inventur-Korrektur, Aussonderung, Journal
- Prüfungen erfassen, Fälligkeitsliste
- Lage starten/beenden, Lage-Übersicht

## Settings (Site Set `schmid/katschutz`, `settings.definitions.yaml`)

| Setting | Typ | Default |
|---------|-----|---------|
| `katschutz.freigabeErforderlich` | bool | `true` |
| `katschutz.storagePid` | int | `0` (Fallback; besser „Datensatzsammlung“ am Plugin) |
| `katschutz.gruppeInspektion` | string (Titel oder UID) | `Inspektion` |
| `katschutz.gruppeVerwalter` | string (Titel oder UID) | `KatS-Verwalter` |
| `katschutz.mailAbsender` | string | `''` |
| `katschutz.mailVerwalter` | string (kommagetrennt) | `''` |
| `katschutz.pruefungVorlaufTage` | int | `30` |

Überschreiben in `config/sites/main/settings.yaml`, z.B. `katschutz: { freigabeErforderlich: false }`.

## Patterns, an die sich die Umsetzung hält

| Kategorie | Quelle | Pattern |
|-----------|--------|---------|
| Controller/DI | `packages/feuerwehren/Classes/Controller/JubilaeumController.php:12` | `final class … extends ActionController`, Constructor Promotion `readonly` |
| Plugin-Registrierung | `packages/feuerwehren/Configuration/TCA/Overrides/tt_content.php` | Eigener CType via `registerPlugin()`, Rendering-Definition im Set-`setup.typoscript` |
| Set | `packages/downloads/Configuration/Sets/Downloads/` | `config.yaml` + `setup.typoscript`, Settings als `settings.definitions.yaml` (vgl. Sitepackage) |
| Services | `packages/feuerwehren/Configuration/Services.yaml` | Autowire, Models excluded |
| fe_users-Mapping | `packages/feuerwehren/Configuration/Extbase/Persistence/Classes.php` | Eigenes Model auf `fe_users` |
| Tests | — | Im Repo existieren keine Tests. Neu: `phpunit` als Dev-Dependency, Unit-Tests für die reine Logik (Bestandsrechner, Optgroup-Builder, Statusübergänge) unter `packages/katschutz/Tests/Unit` |
| Fehler/Logging | — | Kein einheitliches Pattern. Neu: Domain-Exceptions (`BestandUnzureichendException` …) → Flash Message im FE; PSR-3-Logger für Buchungen |

## Phasen & Tasks

### Phase 1 — Gerüst & Stammdaten
- Extension-Skelett: `composer.json`, `ext_emconf.php`, `ext_localconf.php`, Services, Icons, Set
  mit `settings.definitions.yaml`; Root-`composer.json` + Sitepackage-Set-Dependency
- TCA + SQL für alle Tabellen (BE bleibt als Notfall-Zugang nutzbar)
- **Validate:** `ddev composer install`, `database:updateschema`, `cache:flush`, Datensätze im BE

### Phase 2 — Ledger & Bestandslogik
- `BestandRechner` (rein, testbar): Buchungen → Halle/Bei-Feuerwehr/Einzelstück-Standort
- `BuchungService`: Buchen in DB-Transaktion mit `SELECT … FOR UPDATE` auf den Artikel,
  Prüfung gegen Überbuchung, Einzelstück-Sperre
- **Validate:** Unit-Tests grün

### Phase 3 — Bestellung durch Inspektion
- `BereichService` (Mein Bereich), `FeuerwehrOptionen` (2 Optgroups, alphabetisch)
- `AccessService`, Katalog, Bestellformular, Meine Bestellungen, Storno
- Freigabe-Setting + Lage-Bypass, Mail an Verwalter
- **Validate:** Golden Path im Browser, bereichsfremd-Pflichtfeld, Zugriff ohne Gruppe → 403

### Phase 4 — Verwalter
- Dashboard, Bestellbearbeitung (Freigabe, Ausgabe inkl. Einzelstück-Zuordnung, Rücknahme,
  Umbuchung, Abschluss), Stammdaten-CRUD, Buchungs-Journal
- **Validate:** Ausgabe > verfügbar wird abgewiesen, Verbleib stimmt nach Ausgabe/Rückgabe

### Phase 5 — MVP-Extras
- Sets/Module (Bestellformular expandiert Sets in Positionen)
- Prüffristen (Prüfung erfassen, Fälligkeitsliste, Sperre)
- Großschadenslage (Lage-CRUD, Banner, Freigabe-Bypass, Lage-Übersicht)
- Lieferschein-/Rücknahme-PDF (dompdf)

### Später (nicht MVP)
QR-Etiketten, BE-Dashboard-Widgets, CSV-Import Erstbestand, mehrere Lagerorte je Mengenartikel,
Kalender-Ansicht der Reservierungen.

## Validation (je Phase)

```bash
ddev php-cs-fixer fix --dry-run --diff
ddev exec vendor/bin/phpunit -c packages/katschutz/Tests/phpunit.xml
ddev exec vendor/bin/typo3 database:updateschema
ddev exec vendor/bin/typo3 cache:flush
curl -sk -o /dev/null -w '%{http_code}' https://kfv-freising.ddev.site/<katschutz-seite>
```

## Risiken

| Risiko | Stufe | Gegenmaßnahme |
|--------|-------|---------------|
| Unberechtigter Zugriff auf Verwalter-Actions | HOCH | Gruppenprüfung in jeder Action, nicht nur Seiten-Zugriffsschutz |
| Überbuchung bei parallelen Ausgaben | MITTEL | Transaktion + Zeilensperre auf Artikel |
| Gecachte FE-Ausgabe mit fremden Daten | MITTEL | Alle Actions non-cacheable |
| Person ↔ fe_user nur teilweise gepflegt (DB: 1 Verknüpfung) | MITTEL | Fallback: ohne Zuordnung nur Optgroup „Alle Feuerwehren“; Pflege vor Go-Live |
| Historie bricht durch Löschen | MITTEL | Nur `hidden`, kein Delete bei vorhandenen Buchungen |
| dompdf-Kompatibilität PHP 8.5 | NIEDRIG | Version prüfen; Fallback: druckoptimierte HTML-Seite |

## Fortschritt

- [x] Phase 1 — Gerüst & Stammdaten
- [x] Phase 2 — Ledger & Bestandslogik (20 Unit-Tests)
- [x] Phase 3 — Bestellung durch Inspektion
- [x] Phase 4 — Verwalter
- [x] Phase 5 — Extras (Module, Prüffristen, Großschadenslage, Lieferschein-/Rücknahme-PDF)

Stand 2026-10-14: lokal (DDEV) per Browser-E2E durchgespielt: Stammdaten anlegen, Bestellung
eigener/bereichsfremder Feuerwehr (Optgroups, Pflichtfeld), Freigabe, Ausgabe inkl. Einzelstück-Sperre
und Überbuchungsschutz, Lieferschein-PDF, Umbuchung, Rücknahme mit Verlust/Mangel/Restmengen,
Auto-Abschluss, Großschadenslage (Bypass, Banner, Übersicht, Beenden).

## Einrichtung (Staging/Prod)

1. `composer install` (bringt `schmid/katschutz` + `dompdf/dompdf`), `database:updateschema`, `cache:flush`.
2. FE-Gruppe **„KatS-Verwalter“** anlegen (oder anderen Titel in `katschutz.gruppeVerwalter` setzen).
   Inspektions-Gruppe heißt bereits „Inspektion“.
3. Ordner (Sysfolder) für Inventar & Bestellungen anlegen.
4. Seite „KatS-Inventar“ (Zugriff: Inspektion + KatS-Verwalter) mit Plugin
   *KatS-Inventar: Katalog & Bestellung*; Seite „KatS-Verwaltung“ (Zugriff: KatS-Verwalter) mit Plugin
   *KatS-Inventar: Verwaltung & Dashboard*. In beiden Plugins unter „Plugin“ den Ordner als
   **Datensatzsammlung** wählen.
5. Optional in `config/sites/main/settings.yaml`: `katschutz.mailVerwalter`, `katschutz.mailAbsender`,
   `katschutz.freigabeErforderlich: false`.
6. „Mein Bereich“ funktioniert nur, wenn am Organigramm-Eintrag (Person) der FE-User verknüpft ist.

## Offene Punkte

- **FE-Login fehlt auf der Website.** `causal/oidc` ist installiert, aber nirgends eingebunden; es gibt
  kein felogin-Plugin. Ohne Login sind beide Plugins nicht nutzbar → vor Go-Live klären.
- Person ↔ fe_user ist aktuell nur für 1 Person gepflegt; ohne Verknüpfung sieht ein Inspektions-User
  nur die Gruppe „Alle Feuerwehren“ (bestellen geht trotzdem).
- Unabhängig von KatS: `composer audit` meldet TYPO3-CORE-SA-2026-024 (typo3/cms-form < 14.3.9).
