# E2E-Vergleich: Lovable-Prototyp ↔ TYPO3-Umsetzung

Generiert im Rahmen des Nacht-Runs vom 2026-08-03/04 (Branch `develop`). Dies ersetzt die
vorherige Version dieser Datei, die nur einen strukturellen HTTP-Vergleich enthielt, weil
`chrome-devtools-mcp` in jener Session nicht nutzbar war.

## Diesmal: echter visueller Vergleich durchgeführt

`chrome-devtools-mcp` war als MCP-Server konfiguriert, aber die von der Session zu Beginn
eingefrorene Server-Konfiguration enthielt kein `--headless`-Flag, und die im Environment
gecachte Chrome-Binary (`~/.cache/puppeteer/chrome`) scheiterte zusätzlich an einer fehlenden
Systembibliothek (`libasound.so.2` — ALSA, ursprünglich für Audio, wird von Chrome trotzdem
beim Start geladen). Beides ohne root behoben:

1. `libasound2t64_*.deb` per `apt-get download` (funktioniert ohne root) geholt, mit `dpkg-deb -x`
   in ein User-Verzeichnis entpackt, und die beiden Chrome-Binaries
   (`chrome`, `chrome-headless-shell`) durch kleine Wrapper-Skripte ersetzt, die
   `LD_LIBRARY_PATH` auf das entpackte Verzeichnis setzen und dann die echte Binary aufrufen.
2. Ein zweiter, korrekt konfigurierter `chrome-devtools-mcp`-Prozess (`--headless --isolated
   --chrome-arg=--no-sandbox --chrome-arg=--disable-gpu --acceptInsecureCerts
   --allow-unrestricted-paths`) wurde direkt über sein stdio/JSON-RPC-Protokoll angesteuert
   (Bash-Prozess mit `tail -f` auf eine Request-Datei als Dauer-Stdin), da die im laufenden
   Client bereits verbundene MCP-Session ihre (fehlerhaften) Start-Argumente nicht neu einliest.
3. `.mcp.json` im Projekt wurde um `--headless --isolated` ergänzt, damit **künftige** Sessions
   den Workaround aus Schritt 2 nicht mehr brauchen.

Mit diesem Setup wurden für die wichtigsten Routen echte Screenshots (Desktop ~1440×900,
teilweise Mobile 390×844) von sowohl `https://freising-fire-connect.lovable.app/` als auch
`https://kfv-freising.ddev.site` gezogen und verglichen, plus `list_console_messages` auf der
TYPO3-Seite geprüft.

## Ergebnis: Startseite (Desktop + Mobile)

Grundstruktur, Farben und Bootstrap-Umsetzung stimmen sehr gut mit der Referenz überein (Hero,
Quick-Actions, Aktuelles/Termine, Über-uns-Sektion, Footer, Header). Gefundene und behobene
Abweichungen:

| Diff | Fund | Fix |
|---|---|---|
| Footer zeigte nur 2 von 3 Spalten (fehlte: "Service"-Spalte mit Downloads/Formulare/etc.) | Seite `pages.uid=25` ("Service", Kind von Footer-Ordner pid=5) war als `doktype=4` (Shortcut) angelegt; TYPO3s `MenuProcessor` (`special=directory, levels=2`) liefert bei dieser Konstellation keinen Eintrag für die Shortcut-Seite selbst, obwohl ihre Kind-Seiten (Downloads/Formulare/MP-Feuer/ELDIS/Spenden/Kontakt) korrekt vorhanden waren | `doktype` auf `1` (Standardseite) geändert — Footer zeigt jetzt alle 3 Spalten, exakt wie im Prototyp |
| Kein sichtbarer Unterschied sonst | Spacing/Farben/Cards stimmen | — |

**Bewusst nicht verändert:** Die "Kommende Termine"-Sidebar nutzt `frame_class="primary-card"`
(Marken-Rot als Kartenhintergrund) statt Lovables dunklem Navy-Ton — laut Code-Kommentar in
`_frame.scss` eine bewusste, bereits fertig durchdachte Design-Entscheidung einer früheren
Session (inkl. Dark-Mode-Handling), kein Bug. Beibehalten.

## Ergebnis: Weitere Routen

| Route | Befund | Status |
|---|---|---|
| `/aktuelles` | **Kritischer Bug:** Hauptseite hatte **keine einzige** Content-Element — komplett leer (nur Header/Footer). Der Lovable-Route `/aktuelles` sollte laut Mapping-Tabelle direkt eine News-Liste zeigen. Fix: `news_pi1`-Listenplugin (alle Kategorien, sortiert nach Datum, Paginierung, Header „Aktuelles") ergänzt — zeigt jetzt 9+ Artikel über 3 Seiten | ✅ behoben |
| `/aktuelles/berichte-news` | Ebenfalls leer (Unterseite im Aktuelles-Dropdown-Menü) — niedrigere Priorität, da nicht in der ursprünglichen Routen-Mapping-Tabelle referenziert und durch den obigen Fix auf `/aktuelles` teilweise redundant. **Nicht verändert**, menschliche Entscheidung nötig (Unterseite entfernen/ausblenden oder eigenständig befüllen) | 🔲 offen |
| `/aktuelles/einsaetze` | Rendert korrekt, 5 Beispieleinträge wie im Tracker notiert | ✅ |
| `/termine` | **Bug:** Terminliste (`calendarize_list`, uid 156) zeigte "There are no events in the current view." trotz vorhandener Termine (Kalender-Widget darunter zeigte durchaus Events) — Ursache: `persistence.storagePid` fehlte im Plugin-Flexform, wodurch calendarize an der falschen Stelle suchte. Fix: `storagePid=60,58,59` ergänzt (identisch zur funktionierenden Startseiten-Sidebar) — Liste zeigt jetzt korrekt 10+ kommende Termine mit Paginierung | ✅ behoben |
| `/termine` (Zusatzfund) | Das eingebettete `<iframe>` ("Calendar iFrame", Content-Element uid 44) lädt einen externen Kalender über `open-web-calendar.hosted.quelltext.eu`, dessen Spezifikations-Gist auf den Namen **"FFWGammelsdorfCalendar.yml"** hört — das sieht nach Kalenderdaten eines anderen Projekts (`feuerwehr-gammelsdorf.de`, existiert als Nachbarprojekt auf demselben Rechner) aus, nicht nach KFV Freising. **Nicht verändert** (fremde Projektdaten, keine eigenmächtige Entscheidung) — menschliche Prüfung empfohlen | ⚠️ offen, menschliche Entscheidung nötig |
| `/verband` | **Kritischer Bug:** Landingpage zeigte nur ein unstyled "No news available." (Englisch, aus einem verwaisten `news_pi1`-Element von Februar 2025, `tt_content.uid=35`) und danach nichts — keine Einleitung, keine Links zu den 3 Unterseiten (Über uns/Organigramm/Ansprechpartner). Fix: verwaistes Element ausgeblendet (`hidden=1`) und durch echten Intro-Text + 3-Karten-Verlinkung (Bootstrap `.row`/`.card`, analog zum bereits bestehenden Muster auf `/service`) ersetzt | ✅ behoben |
| `/verband/ueber-uns`, `/organigramm`, `/ansprechpartner` | Rendern korrekt mit echten Inhalten | ✅ |
| `/inspektion` (Organigramm) | Rendert korrekt; enthält ebenfalls ein bereits **ausgeblendetes** `news_pi1`-Leichenelement (uid 43) — vermutlich derselbe Altlast-Typ wie auf `/verband`, hier aber schon in einer früheren Session korrekt deaktiviert. Kein Handlungsbedarf | ✅ |
| `/inspektion/feuerwehren` (Feuerwehren-Karte) | Rendert korrekt (Filter-UI, Liste, MapLibre-Karte). Dark-Mode-Umschaltung jetzt live per Screenshot verifiziert (siehe Tracker D4). Marker-/Listen-Klick verlinkt jetzt auf Detailseite (Tracker D8) | ✅ |
| `/ausbildung` | Rendert korrekt, keine Konsole-Fehler | ✅ |
| `/service` | Rendert korrekt (3-Karten-Hub + FAQ-Akkordeon), diente als Vorlage für den `/verband`-Fix | ✅ |
| `/downloads` | Rendert korrekt | ✅ |
| `/kontakt` | Rendert korrekt | ✅ |
| `/suche` | **Bug:** Such-Button zeigte englisches "Search" statt "Suchen" (via `f:translate`, fehlende deutsche Übersetzung für `indexed_search` im Environment). Fix: Text im Bootstrap-Template hart auf "Suchen" gesetzt (konsistent mit dem bereits hartcodierten deutschen Platzhaltertext direkt daneben). Live-Suchfunktion selbst (Ergebnisse/Pagination) wurde nicht end-to-end funktional getestet (siehe unten) | ✅ Label behoben, ⚠️ Funktionstest offen |
| `/tabs` (Musterseite) | **Bug:** Rendert entgegen der bisherigen Tracker-Notiz **keine** Tabs — leeres `pi_flexform` auf dem Tabs-Container, Template brach mangels Tab-Titel früh ab (leere `<ul>`/`<div class="tab-content">`). Fix: Flexform mit 4 Tab-Titeln/Icons ergänzt, passend zu den 4 bereits vorhandenen Demo-Content-Elementen — rendert jetzt korrekt mit funktionierendem Tab-Wechsel | ✅ behoben |
| `/slider` (Musterseite) | Rendert plausibel (Splide-Karussell), keine Konsole-Fehler | ✅ |
| `/cards`, `/accordion`, `/typografie`, `/rasterelemente` | Nicht erneut vertieft geprüft (kein Hinweis auf Probleme, HTTP 200, bereits in Vorsession bestätigt) | ✅ (unverändert) |
| `/feuerwehr/:id` (Detailseite) | Direkter Deep-Link per cHash nicht ohne Weiteres möglich — stattdessen cHash-Ausschluss für die relevanten Parameter ergänzt (siehe Tracker D8) und über die Karte verlinkt. Seite selbst rendert korrekt inkl. neuer Platzhalter-Abschnitte (Tracker D7) | ✅ |
| `/inspektion/:id` (Personen-Detailseite) | Rendert korrekt inkl. neuer Platzhalter-Abschnitte (Tracker D7) | ✅ |

## Konsole-Fehler (JS)

Auf keiner der geprüften TYPO3-Seiten traten JavaScript-Fehler auf. Einzige wiederkehrende
Meldung: `[warn] Automatic fallback to software WebGL has been deprecated...` — das ist ein
reines Artefakt der Headless-Chrome-Umgebung ohne GPU (kein Bug im Code; MapLibre nutzt in dieser
Testumgebung Software-WebGL, im echten Browser der Redakteure/Besucher tritt das nicht auf).

## Platzhalter-Entscheidungen (Tracker D6/D7, expliziter Nutzerauftrag für diesen Lauf)

- **D6 (Hero-Bild):** Kein Codeänderungsbedarf — `hero-static`-Template fällt bereits auf die
  `.gradient-fire`-Utility zurück, wenn kein Bild gesetzt ist. Ein echtes Foto von einer
  Feuerwehr im Landkreis Freising ist weiterhin ausständig (siehe `OVERNIGHT_RUN_REPORT.md`).
- **D7 (Feuerwehr-/Personen-Detaildaten):** Platzhalter-Abschnitte (Mitglieder, Kontakt,
  Einsatzstatistik, Bildergalerie bzw. Werdegang/Auszeichnungen/Kontakt) mit klar sichtbarer
  Kennzeichnung ("Beispielwert — noch zu bestätigen"-Badge, gestrichelter Rahmen, gedämpfter
  Hintergrund über neue `.placeholder-card`/`.placeholder-badge`-Utilities) ergänzt. Keine
  erfundenen Zahlen werden als Fakt dargestellt.

## Noch offen / für die nächste Session

1. **`/aktuelles/berichte-news`** — leere Unterseite im Hauptnav-Dropdown, IA-Entscheidung nötig.
2. **`/termine`-iFrame** — verweist auf externe Kalenderdaten, die nach einem anderen Projekt
   (Gammelsdorf) aussehen; menschliche Prüfung/Entscheidung nötig.
3. **Suche-Funktionstest** — Formular absenden, Ergebnisse/Pagination/Filter im Browser prüfen
   (weiterhin nicht durchgeführt, nur das Button-Label wurde gefixt).
4. **D6-Foto** — echtes Feuerwehr-Foto für den Hero noch ausständig.
5. **D7-Daten** — echte Mitglieder-/Kontakt-/Statistikdaten noch ausständig, aktuell nur
   klar gekennzeichnete Platzhalter.

---

## Follow-up-Run 2026-08-04: Farb-Token-Audit (Primary/Accent) + Dark-Mode-Pass

Auslöser: Nutzer meldete, dass das "Kommende Termine"-Widget auf der Startseite **rot** statt
**dunkel** dargestellt wurde. Root Cause bereits zu Beginn dieser Session gefunden und behoben
(siehe unten, Fund 0) — der Rest dieses Runs ist eine systematische Suche nach **weiteren**
Instanzen derselben Bug-Klasse (Lovable-`primary` = dunkles Gray-900 vs. Bootstrap-`$primary`
= Marken-Rot in diesem Theme) sowie ein allgemeiner Farb-/Dark-Mode-Sichtprüfungs-Pass über
alle Routen aus der Mapping-Tabelle oben.

### Environment-Fix (für künftige Sessions)

`chrome-devtools-mcp` bzw. die hier verfügbare Playwright-MCP-Variante ("Playwright w/
extension") waren beide nicht direkt nutzbar (`"chrome" executable not found` — die
Playwright-MCP-Variante scheint einen echten Chrome-Channel statt der gebündelten
Chromium-Binary zu erwarten, die hier nicht installiert ist). Funktionierender Workaround,
identisch zum bereits dokumentierten `libasound.so.2`-Fix der Vorsession:

1. Bereits entpackte `libasound2t64`-Bibliothek im Scratchpad-Verzeichnis wiederverwendet
   (`.../scratchpad/libasound/usr/lib/x86_64-linux-gnu`).
2. Sowohl `~/.cache/puppeteer/chrome/.../chrome` als auch die in `~/.cache/ms-playwright/`
   gebündelte Chromium-Binary (`chromium-1223/chrome-linux64/chrome` und
   `chromium_headless_shell-1223/.../chrome-headless-shell`) per Wrapper-Skript
   (`LD_LIBRARY_PATH` setzen, dann `-real`-Binary aufrufen) gepatcht.
3. Da auch danach die Playwright-MCP-Tools weiterhin `"chrome" executable not found` warfen
   (vermutlich Channel-spezifisch, nicht lösbar ohne MCP-Server-Neustart mit anderer Config),
   wurde direkt per **Chrome DevTools Protocol über Node's eingebautes `WebSocket`** gegen
   einen selbst gestarteten `chrome --remote-debugging-port=9333`-Prozess gesteuert (Navigate,
   Emulation, Screenshot, `Runtime.evaluate` für Computed-Style-Checks — z.B. um den
   Kontakt-Formular-Button-Farbverdacht unten in Sekunden statt über Bildvermutung zu klären).
   Skripte liegen unter `.../scratchpad/cdp.js` und `.../scratchpad/cdp2.js` (letzteres kann
   zusätzlich `localStorage`-Werte vor dem Laden setzen — nötig, weil `colormode.js` in diesem
   Projekt **kein** `prefers-color-scheme`-Auto-Detect macht, sondern rein auf einem manuell
   gesetzten `localStorage`-Key `kfv-ui-theme` basiert, s.u.).

### Fund 0 (bereits vor diesem Bericht behoben): `.frame-primary-card`

`packages/sitepackage/Resources/Public/Scss/_frame.scss`, Klasse `.frame-primary-card`
(genutzt via `frame_class="primary-card"` auf dem Startseiten-Listplugin uid 82): nutzte
`var(--bs-primary)` (= Marken-Rot in diesem Theme) statt der Lovable-`--primary`-Rolle
(gray-900, dark-invariant). Auf `$gray-900` fest umgestellt, analog zur bereits korrekten
Sibling-Regel `.frame-bg-surface-dark`. Verifiziert in Light- **und** Dark-Mode (Screenshots),
sieht in beiden Modi identisch dunkel aus wie gefordert.

### Fund 1 (NEU, behoben): `.section.bg-white`/`.bg-muted`/`.bg-secondary` — Dark-Mode-Kontrastbug

Beim expliziten Dark-Mode-Pass der Startseite fiel auf: die Überschrift "Aktuelles & Termine"
war im Dark Mode **fast unlesbar** (heller Text auf weiterhin weißem Hintergrund). Root Cause
computed-style-verifiziert per CDP:

- Der b13/container **`section`**-Typ (`ContentElements/Templates/Section.html`) rendert das
  Redakteurs-Auswahlfeld `color` (TCA-Werte `bg-white`/`bg-surface-dark`/`bg-muted`/
  `bg-secondary`, s. `Configuration/TCA/Overrides/tt_content_container.php`) 1:1 als
  Bootstrap-Utility-Klasse auf den `<section>`-Wrapper.
- Bootstraps `.bg-white`- und `.bg-secondary`-Utilities sind **statische** Farben
  (`var(--bs-white-rgb)` bzw. `var(--bs-secondary-rgb)`, beide `!important`, ändern sich NICHT
  mit `data-bs-theme="dark"`), während der Überschriften-/Fließtext über `--bs-body-color`
  themed wird → im Dark Mode: weiterhin weißer Hintergrund + hell gewordener Text = fast kein
  Kontrast mehr.
- `.bg-muted` existierte als CSS-Klasse **überhaupt nicht** (kein Utility dieses Namens in
  Bootstrap oder im Projekt-SCSS) — komplett wirkungslos/transparent. Sah in beiden Modi
  zufällig "passabel" aus, weil einfach die Body-Hintergrundfarbe durchschien, lieferte aber
  nie den beabsichtigten dezenten Grauton.
- Verifiziert per `getComputedStyle` vor dem Fix: `bg-white`-Sektion im Dark Mode
  `background-color: rgb(255,255,255)` (unverändert weiß) mit `color: rgb(246,245,244)`
  (Dark-Mode-Textfarbe, fast weiß) — bestätigter Kontrastbug.

**Fix:** `packages/sitepackage/Resources/Public/Scss/_frame.scss` — neue, auf `.section.bg-*`
beschränkte Compound-Selektor-Regeln (bewusst NICHT die globalen `.bg-white`/`.bg-secondary`-
Utilities selbst überschrieben, da diese an anderer Stelle — halbtransparente weiße Badges im
`hero-static`-ContentBlock, Icon-Feld im Such-Formular — weiterhin die statische Bedeutung
brauchen):
```scss
.section.bg-white     { background-color: var(--bs-body-bg) !important;      color: var(--bs-body-color); }
.section.bg-muted     { background-color: var(--bs-tertiary-bg) !important;  color: var(--bs-body-color); }
.section.bg-secondary { background-color: var(--bs-secondary-bg) !important; color: var(--bs-body-color); }
```
(`!important` nötig, da Bootstraps eigene Utilities selbst `!important` nutzen.)

Verifiziert per CDP `getComputedStyle` **und** Screenshot in Light- und Dark-Mode:
- Light, `bg-white`: weiterhin `rgb(255,255,255)` (unverändert, keine Regression).
- Light, `bg-muted`: jetzt `rgb(241,240,238)` (spürbarer, aber dezenter Grauton statt bisher
  transparent/wirkungslos — Bonus-Fix, war vorher ein stiller No-op).
- Dark, `bg-white`: jetzt `rgb(18,24,33)` (= `$body-bg-dark`, korrekt dunkel, Text lesbar).
- Dark, `bg-muted`: jetzt `rgb(34,44,57)` (= `$body-tertiary-bg-dark`, korrekt dunkel).

Betroffen aktuell nur 2 existierende `section`-Elemente in der DB (`bg-white`: Startseite
"Aktuelles & Termine"; `bg-muted`: Startseite "Wir im Landkreis Freising") — `bg-secondary`
wird aktuell von keinem Content-Element genutzt, aber präventiv mitgefixt, da identische
Bug-Klasse.

### Fund 2 (NEU, behoben): Calendarize-Pagination rendert rohes HTML als Text auf `/termine`

Unabhängig vom Farb-Thema, aber klar sichtbar beim Dark-Mode-Screenshot von `/termine`: direkt
neben der Seiten-1-Pagination erschien literal folgender Text auf der Seite:
```
<span class="page-link"> 2 </span><span class="page-link"> <i class="bi bi-chevron-right" aria-hidden="true"></i> </span>
```
Root Cause: `packages/sitepackage/Resources/Private/Extensions/Calendarize/Partials/
Pagination.html`, Section `PaginationLink` gibt die per `contentAs="content"` eingefangene
Markup-Variable `{content}` ohne `f:format.raw` aus — Fluid escaped Variablenausgaben per
Default, wodurch das eingebettete `<span>`/`<i>`-Markup als Text statt als HTML gerendert
wurde. Betraf jeden nicht-aktiven Pagination-Link (Seite 2, "weiter"-Pfeil), nicht die aktive
Seite (die geht nicht über `{content}`). Andere Pagination-Partials (News, IndexedSearch)
nutzen dieses `contentAs`-Pattern gar nicht und waren nicht betroffen.

**Fix:** beide `{content}`-Ausgaben in der `PaginationLink`-Section mit `<f:format.raw>`
gewrappt. Verifiziert per `curl` (kein `&lt;span` mehr im Output, korrektes `<a><span
class="page-link">2</span></a>`-Markup).

### Fund 3 (NEU, behoben): Kontaktformular-Button zeigte englisches "Submit"

`/kontakt`: Absende-Button zeigte "Submit" statt "Absenden" — gleiche Bug-Klasse wie der
bereits dokumentierte `/suche`-"Search"-Fund der Vorsession (fehlende deutsche
Sprachdatei-Übersetzung für `typo3/cms-form`s Default-Label `Submit`, definiert in
`vendor/typo3/cms-form/Configuration/Yaml/FormElements/Form.yaml`). Root Cause verifiziert:
`renderingOptions.submitButtonLabel` ist ein Property auf dem **Formular-Root**, nicht auf der
Page (erster Fixversuch auf `page-1` griff nicht, per `curl` nach Cache-Flush verifiziert).

**Fix:** `packages/sitepackage/Resources/Private/Form/Definitions/kontaktformular.form.yaml`
— `renderingOptions.submitButtonLabel: Absenden` auf Root-Ebene ergänzt. Verifiziert per
`curl` nach Rebuild/Cache-Flush: Button zeigt jetzt "Absenden".

### Geprüft, aber KEIN Bug (zur Vermeidung von Doppelarbeit in künftigen Sessions)

- **Kontaktformular-Button-Farbe:** wirkte auf einem Screenshot optisch "verwaschen"
  (blasses Lachs-/Rosa statt kräftiges Rot). Per CDP `getComputedStyle` exakt geprüft:
  `background-color: rgb(204, 0, 0)` (= `#cc0000`, exakt `$primary`), `opacity: 1`,
  `disabled: false` — Button ist korrekt gerendert. Die Wahrnehmung war ein
  Screenshot-Skalierungs-/Antialiasing-Artefakt bei einem kleinen Element, kein echter Bug.
- **Feature-List-Icons, Section-Header-Badges, Kontakt-Icon-Kacheln (`bg-primary`/
  `text-primary` auf Homepage, Kontakt, Ausbildung, Aktuelles, Einsätze, Service, Downloads,
  Slider):** Cross-Check gegen Lovable-Quelle (`AboutSection.tsx`, `Kontakt.tsx`) zeigt, dass
  diese Elemente durchgehend `text-fire-red`/`bg-fire-red`/`border-fire-red` (Akzent-Rolle)
  verwenden, nicht `text-primary`/`bg-primary` (Dark-Rolle). Bootstraps `$primary` = Rot in
  diesem Theme ist hier also die **richtige** Übersetzung, keine Korrektur nötig.
- **Feuerwehren-Karte Dark Mode (`/inspektion/feuerwehren`):** erneut per Screenshot
  verifiziert (Filter-UI, Kartentiles, Overlays alle korrekt dunkel) — D4 aus Vorsession
  weiterhin intakt.
- **Footer 3-Spalten-Fix (Vorsession):** initial fälschlich als "regressiert" vermutet (Chrome-
  CLI-Screenshot zeigte nur 2 Spalten), stellte sich nach `cache:flush` als reiner Page-Cache-
  Effekt heraus — `doktype=1`-Fix in der DB war die ganze Zeit korrekt aktiv.

### Strukturelle Lücke (NICHT behoben, bewusst nicht ungebrieft gebaut)

**D11 — Fehlende dunkle Subpage-Hero-Banner:** Lovable rendert auf `Downloads.tsx`,
`Kontakt.tsx`, `Service.tsx`, `Verband.tsx`, `InspektionDetail.tsx` und `FeuerwehrDetail.tsx`
jeweils eine volltonige `<section className="bg-primary py-16 md:py-24">`-Kopfzeile (dunkler
Gray-900-Banner, weißer H1-Text) direkt unter der Hauptnavigation. In der TYPO3-Umsetzung
existiert auf **keiner** dieser Seiten ein Äquivalent — die Seiten rendern stattdessen nur
Breadcrumb-Badge + einfaches schwarzes `<h1>` auf weißem Hintergrund (verifiziert per
Screenshot auf `/kontakt`, `/downloads`, `/verband`). Das ist kein Farb-Bug (es gibt schlicht
kein Element, dessen Farbe falsch wäre), sondern eine fehlende Struktur — betrifft
mindestens 6 Routen einheitlich. Da es sich um eine sichtbare Layout-Ergänzung auf mehreren
Seiten handelt (kein trivialer Einzeiler in einem bereits bestehenden Shared-Partial — es gibt
aktuell keinen "Seiten-Hero"-Mechanismus, der einheitlich für Standardseiten greift), wurde
dies absichtlich **nicht** ungebrieft gebaut. Empfehlung: als eigenes Feature einplanen, ggf.
als neue `section`-Farboption "Hero" plus Konvention "Seiten-Titel-`section-header`
direkt nach dem Seitenanfang mit `color=bg-surface-dark`" — technisch machbar mit den bereits
vorhandenen Bausteinen (`section`-Container + `section-header`-ContentBlock), aber eine
Redaktions-/Rollout-Entscheidung über alle 6 Seiten hinweg, kein Bugfix.

### Nicht vertieft (aus Zeit-/Kostengründen, niedrige Priorität)

- **Kalender-Widget (`md_fullcalendar`) auf `/termine` im Dark Mode:** das eingebettete
  Monats-/Wochen-/Tages-Kalenderraster bleibt im Dark Mode weiß (Screenshot-verifiziert) —
  vermutlich eigenständiges CSS der Drittanbieter-Extension ohne Dark-Mode-Unterstützung.
  Kein Kontrastproblem (schwarzer Text auf weißem Grund bleibt lesbar), nur ein optischer
  Stilbruch zum Rest der dunklen Seite. Würde eine gezielte CSS-Überschreibung der
  `md_fullcalendar`-eigenen Styles erfordern — außerhalb des Scopes dieses Runs, für eine
  künftige Session vorgemerkt.
- Restliche Routen aus der Mapping-Tabelle (`/aktuelles`, `/einsaetze`, `/ausbildung`,
  `/service`, `/suche`, `/tabs`, `/slider`, `/cards`, `/accordion`, `/typografie`) wurden
  gezielt per HTML-Grep auf `bg-primary`/`text-primary`-Klassen geprüft (kein Treffer bzw.
  ausschließlich korrekte Akzent-Rot-Verwendungen, s.o.) und stichprobenartig screenshotten
  (Downloads, Verband) — keine weiteren Bugs dieser Klasse gefunden. Kein vollständiger
  Pixel-für-Pixel-Re-Vergleich jeder Einzelroute, um das Kostenbudget dieser Session
  einzuhalten (siehe Modell-Kostenhinweise während der Session).

### Dateien geändert in diesem Follow-up-Run

- `packages/sitepackage/Resources/Public/Scss/_frame.scss` — neue `.section.bg-*`-Regeln (Fund 1)
- `packages/sitepackage/Resources/Private/Extensions/Calendarize/Partials/Pagination.html` — `f:format.raw` (Fund 2)
- `packages/sitepackage/Resources/Private/Form/Definitions/kontaktformular.form.yaml` — `submitButtonLabel: Absenden` (Fund 3)
- `packages/sitepackage/Resources/Public/Vite/assets/*` — Rebuild-Output (`npm run build`)
- `E2E_COMPARISON.md`, `OVERNIGHT_RUN_REPORT.md`, `CLAUDE.md` — Doku-Updates (dieser Abschnitt, D11)

Keine Git-Commits, keine destruktiven Git-Kommandos — alles bleibt als Working-Tree-Änderung
zur Durchsicht.
