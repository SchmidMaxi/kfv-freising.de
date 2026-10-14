# Spalten werden aus dem TCA erzeugt; hier nur Indizes für die Bestandsabfragen.
CREATE TABLE tx_katschutz_domain_model_buchung (
    KEY artikel (artikel),
    KEY einzelstueck (einzelstueck),
    KEY bestellung (bestellung),
    KEY feuerwehr (feuerwehr)
);

CREATE TABLE tx_katschutz_domain_model_bestellung (
    KEY status (status),
    KEY besteller (besteller)
);
