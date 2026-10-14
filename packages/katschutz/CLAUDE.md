# EXT:katschutz – Inventar KatS-Halle

Plan, Entscheidungen, Einrichtung: [`docs/KATSCHUTZ-INVENTAR-PLAN.md`](../../docs/KATSCHUTZ-INVENTAR-PLAN.md).

## Kernprinzipien
- **Bestand nur über das Buchungsjournal** (`tx_katschutz_domain_model_buchung`, nur INSERT).
  Einziger Schreibzugang: `Service/BuchungService::buche()` (Transaktion + `SELECT … FOR UPDATE`
  auf Artikel, Prüfung via `Service/Bestand/BuchungPruefer`). Bestand = `BestandRechner` über das Journal.
- Reine Logik (Rechner, Prüfer, Optgroups) ist ohne DB unit-getestet:
  `ddev exec vendor/bin/phpunit -c packages/katschutz/Tests/phpunit.xml`
- Zugriff: `AbstractKatschutzController::processRequest()` prüft Gruppe **vor** Argument-Mapping;
  schreibende Actions in `SCHREIBENDE_ACTIONS` nur per POST. Gruppen per Titel oder UID (Site-Settings).
- Archivieren (`hidden`) statt Löschen; Einzelstück aussondern = Aussonderungs-Buchung.

## Fallen
- Extbase mappt Enum-Getter → Properties `status`/`typ` sind als Enum typisiert.
- `\DateTimeImmutable` braucht `Property/DateTimeImmutableConverter` (Core kennt nur `\DateTime`);
  Datumsfelder im Formular als `[feld][date]` + `[feld][dateFormat]=!Y-m-d`.
- `crdate` muss als TCA-Spalte deklariert sein, sonst mappt Extbase es nicht.
- `f:form.checkbox multiple="1"` hängt selbst `[]` an – Name ohne `[]` angeben.
- PDF-CSS liegt in `Templates/Pdf/beleg.css` (geschweifte Klammern würden Fluid stören).
