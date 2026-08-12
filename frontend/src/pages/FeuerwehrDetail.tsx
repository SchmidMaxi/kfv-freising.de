import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { useParams, Link } from "react-router-dom";
import { MapPin, Phone, Mail, Users, Calendar, Truck, ArrowLeft, Shield, Award, Flame, Clock, Activity, Target, Medal, Images } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { ImageGallery } from "@/components/ImageGallery";
// Enhanced mock data - in production this would come from an API/database
const feuerwehren: Record<string, {
  name: string;
  gemeinde: string;
  typ: string;
  adresse: string;
  telefon: string;
  email: string;
  website?: string;
  gruendung: number;
  mitglieder: { aktiv: number; jugend: number; passiv: number; gesamt: number };
  fahrzeuge: { typ: string; kennzeichen: string; baujahr: number; beschreibung?: string }[];
  kommandant: { name: string; funktion: string };
  stellvertreter: { name: string; funktion: string };
  uebungszeiten: string;
  beschreibung: string;
  einsaetze?: { jahr: number; gesamt: number; brand: number; thl: number; sonstige: number };
  letzteEinsaetze?: { datum: string; art: string; ort: string; beschreibung: string }[];
  auszeichnungen?: string[];
  galerie?: { src: string; alt: string; caption?: string }[];
}> = {
  "freising": {
    name: "Freiwillige Feuerwehr Freising",
    gemeinde: "Freising",
    typ: "Stützpunktfeuerwehr",
    adresse: "Wippenhauser Str. 51, 85354 Freising",
    telefon: "08161 / 54 23 00",
    email: "info@ffw-freising.de",
    website: "https://ffw-freising.de",
    gruendung: 1867,
    mitglieder: { aktiv: 85, jugend: 24, passiv: 120, gesamt: 229 },
    fahrzeuge: [
      { typ: "HLF 20", kennzeichen: "FS-FW 1", baujahr: 2019, beschreibung: "Hilfeleistungslöschgruppenfahrzeug" },
      { typ: "DLK 23/12", kennzeichen: "FS-FW 2", baujahr: 2018, beschreibung: "Drehleiter mit Korb" },
      { typ: "LF 16/12", kennzeichen: "FS-FW 3", baujahr: 2015, beschreibung: "Löschgruppenfahrzeug" },
      { typ: "GW-L2", kennzeichen: "FS-FW 4", baujahr: 2020, beschreibung: "Gerätewagen Logistik" },
      { typ: "MTW", kennzeichen: "FS-FW 5", baujahr: 2017, beschreibung: "Mannschaftstransportwagen" },
    ],
    kommandant: { name: "Hans Müller", funktion: "1. Kommandant" },
    stellvertreter: { name: "Stefan Huber", funktion: "2. Kommandant" },
    uebungszeiten: "Jeden Dienstag, 19:30 Uhr",
    beschreibung: "Die Freiwillige Feuerwehr Freising ist die größte Feuerwehr im Landkreis und als Stützpunktfeuerwehr für das gesamte Stadtgebiet sowie überörtliche Einsätze zuständig.",
    einsaetze: { jahr: 2024, gesamt: 287, brand: 52, thl: 189, sonstige: 46 },
    letzteEinsaetze: [
      { datum: "20.12.2024", art: "Brand", ort: "Hauptstraße 15", beschreibung: "Zimmerbrand im 2. OG" },
      { datum: "18.12.2024", art: "THL", ort: "B11", beschreibung: "Verkehrsunfall mit 2 PKW" },
      { datum: "15.12.2024", art: "Brand", ort: "Gewerbegebiet", beschreibung: "Containerbrand" },
    ],
    auszeichnungen: ["Bayerisches Feuerwehr-Ehrenzeichen", "Leistungsabzeichen Gold"],
    galerie: [
      { src: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800", alt: "Feuerwehrhaus Freising", caption: "Unser modernes Feuerwehrgerätehaus" },
      { src: "https://images.unsplash.com/photo-1582139329536-e7284fece509?w=800", alt: "HLF 20", caption: "Hilfeleistungslöschgruppenfahrzeug" },
      { src: "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=800", alt: "Übung", caption: "Praktische Übung am Objekt" },
      { src: "https://images.unsplash.com/photo-1569163139599-0f4517e36f51?w=800", alt: "Mannschaft", caption: "Unsere Einsatzmannschaft" },
    ],
  },
  "moosburg": {
    name: "Freiwillige Feuerwehr Moosburg a.d.Isar",
    gemeinde: "Moosburg a.d.Isar",
    typ: "Stützpunktfeuerwehr",
    adresse: "Thalbacher Str. 2, 85368 Moosburg a.d.Isar",
    telefon: "08761 / 12 34 56",
    email: "info@ffw-moosburg.de",
    website: "https://ffw-moosburg.de",
    gruendung: 1872,
    mitglieder: { aktiv: 68, jugend: 22, passiv: 95, gesamt: 185 },
    fahrzeuge: [
      { typ: "HLF 20", kennzeichen: "FS-MO 1", baujahr: 2021, beschreibung: "Hilfeleistungslöschgruppenfahrzeug mit 2.000l Tank" },
      { typ: "DLK 18/12", kennzeichen: "FS-MO 2", baujahr: 2016, beschreibung: "Drehleiter mit Korb, 18m Rettungshöhe" },
      { typ: "LF 8/6", kennzeichen: "FS-MO 3", baujahr: 2014, beschreibung: "Löschgruppenfahrzeug für Erst-angriff" },
      { typ: "RW 2", kennzeichen: "FS-MO 4", baujahr: 2019, beschreibung: "Rüstwagen für schwere technische Hilfeleistung" },
      { typ: "GW-G", kennzeichen: "FS-MO 5", baujahr: 2020, beschreibung: "Gerätewagen Gefahrgut" },
      { typ: "MTW", kennzeichen: "FS-MO 6", baujahr: 2018, beschreibung: "Mannschaftstransportwagen" },
      { typ: "Kdow", kennzeichen: "FS-MO 10", baujahr: 2022, beschreibung: "Kommandowagen für Führungskräfte" },
    ],
    kommandant: { name: "Thomas Weber", funktion: "1. Kommandant" },
    stellvertreter: { name: "Michael Bauer", funktion: "2. Kommandant" },
    uebungszeiten: "Jeden Mittwoch, 19:00 Uhr",
    beschreibung: "Die Freiwillige Feuerwehr Moosburg a.d.Isar ist eine der traditionsreichsten Feuerwehren im Landkreis Freising. Als Stützpunktfeuerwehr schützt sie die zweitgrößte Stadt im Landkreis mit rund 19.000 Einwohnern. Mit modernster Ausrüstung und gut ausgebildeten Einsatzkräften sind wir für alle Notfälle gerüstet – von Bränden über Verkehrsunfälle bis hin zu Gefahrguteinsätzen.",
    einsaetze: { jahr: 2024, gesamt: 198, brand: 38, thl: 127, sonstige: 33 },
    letzteEinsaetze: [
      { datum: "22.12.2024", art: "THL", ort: "Isarbrücke", beschreibung: "Person im Wasser - Rettungseinsatz" },
      { datum: "21.12.2024", art: "Brand", ort: "Stadtplatz", beschreibung: "Adventskranz in Brand geraten" },
      { datum: "19.12.2024", art: "THL", ort: "St.-Kastulus-Platz", beschreibung: "Türöffnung - Hilflose Person" },
      { datum: "17.12.2024", art: "Brand", ort: "Industriegebiet Degernpoint", beschreibung: "Brandmeldeanlage - Fehlalarm" },
      { datum: "15.12.2024", art: "THL", ort: "A92", beschreibung: "VU mit LKW - Ölspur beseitigt" },
      { datum: "14.12.2024", art: "Brand", ort: "Bahnhofstraße", beschreibung: "Kellerbrand in Mehrfamilienhaus" },
      { datum: "12.12.2024", art: "Gefahrgut", ort: "Gewerbegebiet", beschreibung: "Austritt von Betriebsstoffen" },
      { datum: "10.12.2024", art: "THL", ort: "B11", beschreibung: "Verkehrsunfall mit eingeklemmter Person" },
    ],
    auszeichnungen: [
      "Bayerisches Feuerwehr-Ehrenzeichen in Gold",
      "Leistungsabzeichen Gold-Blau",
      "THL-Leistungsprüfung Stufe 3",
      "Atemschutzleistungsabzeichen Gold"
    ],
    galerie: [
      { src: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800", alt: "Feuerwehrhaus Moosburg", caption: "Feuerwehrgerätehaus an der Isar" },
      { src: "https://images.unsplash.com/photo-1582139329536-e7284fece509?w=800", alt: "Drehleiter", caption: "Unsere Drehleiter DLK 18/12" },
      { src: "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=800", alt: "Rüstwagen", caption: "Rüstwagen RW 2 für schwere THL" },
      { src: "https://images.unsplash.com/photo-1569163139599-0f4517e36f51?w=800", alt: "Jugendfeuerwehr", caption: "Übung der Jugendfeuerwehr" },
      { src: "https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=800", alt: "Übungseinsatz", caption: "Gemeinsame Übung mit Nachbarwehr" },
      { src: "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800", alt: "Fahrzeughalle", caption: "Blick in unsere Fahrzeughalle" },
    ],
  },
  "neufahrn": {
    name: "Freiwillige Feuerwehr Neufahrn",
    gemeinde: "Neufahrn b.Freising",
    typ: "Ortsfeuerwehr",
    adresse: "Bahnhofstr. 32, 85375 Neufahrn",
    telefon: "08165 / 98 76 54",
    email: "info@ffw-neufahrn.de",
    gruendung: 1878,
    mitglieder: { aktiv: 45, jugend: 12, passiv: 60, gesamt: 117 },
    fahrzeuge: [
      { typ: "HLF 10", kennzeichen: "FS-FW 301", baujahr: 2018, beschreibung: "Hilfeleistungslöschgruppenfahrzeug" },
      { typ: "LF 8/6", kennzeichen: "FS-FW 302", baujahr: 2012, beschreibung: "Löschgruppenfahrzeug" },
      { typ: "MTW", kennzeichen: "FS-FW 303", baujahr: 2019, beschreibung: "Mannschaftstransportwagen" },
    ],
    kommandant: { name: "Markus Schmidt", funktion: "1. Kommandant" },
    stellvertreter: { name: "Andreas Meier", funktion: "2. Kommandant" },
    uebungszeiten: "Jeden Freitag, 19:30 Uhr",
    beschreibung: "Die Freiwillige Feuerwehr Neufahrn ist durch die Nähe zum Flughafen München ein wichtiger Partner im Katastrophenschutz.",
    einsaetze: { jahr: 2024, gesamt: 89, brand: 15, thl: 62, sonstige: 12 },
    letzteEinsaetze: [
      { datum: "19.12.2024", art: "THL", ort: "Bahnhofstraße", beschreibung: "Ölspur auf der Fahrbahn" },
      { datum: "16.12.2024", art: "Brand", ort: "Wohngebiet", beschreibung: "Brandmeldeanlage - Fehlalarm" },
    ],
  },
};

const FeuerwehrDetail = () => {
  const { id } = useParams<{ id: string }>();
  const feuerwehr = id ? feuerwehren[id] : null;

  if (!feuerwehr) {
    return (
      <div className="min-h-screen flex flex-col bg-background">
        <Header />
        <main className="flex-1 container py-16">
          <div className="text-center">
            <h1 className="font-heading text-3xl font-bold text-foreground mb-4">
              Feuerwehr nicht gefunden
            </h1>
            <p className="text-muted-foreground mb-8">
              Die gesuchte Feuerwehr existiert nicht oder wurde verschoben.
            </p>
            <Button asChild>
              <Link to="/feuerwehren">
                <ArrowLeft className="h-4 w-4 mr-2" />
                Zur Übersicht
              </Link>
            </Button>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />

      <main className="flex-1">
        {/* Hero Section */}
        <section className="bg-primary py-16 md:py-24">
          <div className="container">
            <Link
              to="/feuerwehren"
              className="inline-flex items-center gap-2 text-primary-foreground/80 hover:text-primary-foreground mb-6 transition-colors"
            >
              <ArrowLeft className="h-4 w-4" />
              Alle Feuerwehren
            </Link>
            <div className="flex flex-col md:flex-row md:items-center gap-4">
              <div className="p-4 rounded-xl bg-white/10">
                <Shield className="h-12 w-12 text-primary-foreground" />
              </div>
              <div>
                <Badge variant="secondary" className="mb-2">
                  {feuerwehr.typ}
                </Badge>
                <h1 className="font-heading text-3xl md:text-4xl font-bold text-primary-foreground">
                  {feuerwehr.name}
                </h1>
                <p className="text-primary-foreground/80">{feuerwehr.gemeinde} • Gegründet {feuerwehr.gruendung}</p>
              </div>
            </div>
          </div>
        </section>

        {/* Statistics Bar */}
        {feuerwehr.einsaetze && (
          <section className="bg-card border-b border-border py-6">
            <div className="container">
              <div className="grid grid-cols-2 md:grid-cols-5 gap-4 text-center">
                <div>
                  <p className="text-3xl font-heading font-bold text-fire-red">{feuerwehr.mitglieder.gesamt}</p>
                  <p className="text-sm text-muted-foreground">Mitglieder gesamt</p>
                </div>
                <div>
                  <p className="text-3xl font-heading font-bold text-foreground">{feuerwehr.mitglieder.aktiv}</p>
                  <p className="text-sm text-muted-foreground">Aktive</p>
                </div>
                <div>
                  <p className="text-3xl font-heading font-bold text-foreground">{feuerwehr.einsaetze.gesamt}</p>
                  <p className="text-sm text-muted-foreground">Einsätze {feuerwehr.einsaetze.jahr}</p>
                </div>
                <div>
                  <p className="text-3xl font-heading font-bold text-foreground">{feuerwehr.fahrzeuge.length}</p>
                  <p className="text-sm text-muted-foreground">Fahrzeuge</p>
                </div>
                <div>
                  <p className="text-3xl font-heading font-bold text-foreground">{feuerwehr.mitglieder.jugend}</p>
                  <p className="text-sm text-muted-foreground">Jugendfeuerwehr</p>
                </div>
              </div>
            </div>
          </section>
        )}

        {/* Content */}
        <section className="py-16 md:py-24">
          <div className="container">
            <div className="grid lg:grid-cols-3 gap-8">
              {/* Main Content */}
              <div className="lg:col-span-2 space-y-8">
                {/* Description */}
                <div className="bg-card rounded-xl border border-border p-6">
                  <h2 className="font-heading text-xl font-bold text-card-foreground mb-4">
                    Über uns
                  </h2>
                  <p className="text-muted-foreground leading-relaxed">
                    {feuerwehr.beschreibung}
                  </p>
                </div>

                {/* Einsatz-Ticker */}
                {feuerwehr.letzteEinsaetze && feuerwehr.letzteEinsaetze.length > 0 && (
                  <Card className="border-border">
                    <CardHeader className="pb-4">
                      <div className="flex items-center gap-3">
                        <div className="p-2 rounded-lg bg-fire-red/10">
                          <Activity className="h-5 w-5 text-fire-red" />
                        </div>
                        <h2 className="font-heading text-xl font-bold text-card-foreground">
                          Letzte Einsätze
                        </h2>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <div className="space-y-4">
                        {feuerwehr.letzteEinsaetze.map((einsatz, index) => (
                          <div
                            key={index}
                            className="flex items-start gap-4 p-4 bg-muted rounded-lg hover:bg-muted/70 transition-colors"
                          >
                            <div className={`p-2 rounded-lg ${
                              einsatz.art === "Brand" ? "bg-destructive/10 text-destructive" :
                              einsatz.art === "THL" ? "bg-primary/10 text-primary" :
                              "bg-fire-orange/10 text-fire-orange"
                            }`}>
                              {einsatz.art === "Brand" ? <Flame className="h-5 w-5" /> :
                               einsatz.art === "THL" ? <Target className="h-5 w-5" /> :
                               <Shield className="h-5 w-5" />}
                            </div>
                            <div className="flex-1 min-w-0">
                              <div className="flex items-center gap-2 mb-1">
                                <Badge variant={
                                  einsatz.art === "Brand" ? "destructive" :
                                  einsatz.art === "THL" ? "default" :
                                  "secondary"
                                } className="text-xs">
                                  {einsatz.art}
                                </Badge>
                                <span className="text-xs text-muted-foreground flex items-center gap-1">
                                  <Clock className="h-3 w-3" />
                                  {einsatz.datum}
                                </span>
                              </div>
                              <p className="font-medium text-foreground truncate">{einsatz.beschreibung}</p>
                              <p className="text-sm text-muted-foreground flex items-center gap-1 mt-1">
                                <MapPin className="h-3 w-3" />
                                {einsatz.ort}
                              </p>
                            </div>
                          </div>
                        ))}
                      </div>
                    </CardContent>
                  </Card>
                )}

                {/* Einsatzstatistik */}
                {feuerwehr.einsaetze && (
                  <Card className="border-border">
                    <CardHeader className="pb-4">
                      <div className="flex items-center gap-3">
                        <div className="p-2 rounded-lg bg-fire-red/10">
                          <Target className="h-5 w-5 text-fire-red" />
                        </div>
                        <h2 className="font-heading text-xl font-bold text-card-foreground">
                          Einsatzstatistik {feuerwehr.einsaetze.jahr}
                        </h2>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <div className="grid grid-cols-3 gap-4">
                        <div className="p-4 bg-destructive/10 rounded-lg text-center">
                          <Flame className="h-6 w-6 text-destructive mx-auto mb-2" />
                          <p className="text-2xl font-heading font-bold text-destructive">{feuerwehr.einsaetze.brand}</p>
                          <p className="text-sm text-muted-foreground">Brandeinsätze</p>
                        </div>
                        <div className="p-4 bg-primary/10 rounded-lg text-center">
                          <Target className="h-6 w-6 text-primary mx-auto mb-2" />
                          <p className="text-2xl font-heading font-bold text-primary">{feuerwehr.einsaetze.thl}</p>
                          <p className="text-sm text-muted-foreground">Technische Hilfe</p>
                        </div>
                        <div className="p-4 bg-muted rounded-lg text-center">
                          <Shield className="h-6 w-6 text-muted-foreground mx-auto mb-2" />
                          <p className="text-2xl font-heading font-bold text-foreground">{feuerwehr.einsaetze.sonstige}</p>
                          <p className="text-sm text-muted-foreground">Sonstige</p>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                )}

                {/* Vehicles */}
                <div className="bg-card rounded-xl border border-border p-6">
                  <div className="flex items-center gap-3 mb-6">
                    <div className="p-2 rounded-lg bg-fire-red/10">
                      <Truck className="h-5 w-5 text-fire-red" />
                    </div>
                    <h2 className="font-heading text-xl font-bold text-card-foreground">
                      Fahrzeuge ({feuerwehr.fahrzeuge.length})
                    </h2>
                  </div>
                  <div className="grid sm:grid-cols-2 gap-4">
                    {feuerwehr.fahrzeuge.map((fahrzeug) => (
                      <div
                        key={fahrzeug.kennzeichen}
                        className="flex items-start gap-4 p-4 bg-muted rounded-lg hover:bg-muted/70 transition-colors"
                      >
                        <div className="p-2 rounded-lg bg-background">
                          <Truck className="h-5 w-5 text-fire-red" />
                        </div>
                        <div className="flex-1">
                          <p className="font-medium text-foreground">{fahrzeug.typ}</p>
                          <p className="text-sm text-muted-foreground">{fahrzeug.kennzeichen} • Bj. {fahrzeug.baujahr}</p>
                          {fahrzeug.beschreibung && (
                            <p className="text-xs text-muted-foreground mt-1">{fahrzeug.beschreibung}</p>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Leadership */}
                <div className="bg-card rounded-xl border border-border p-6">
                  <div className="flex items-center gap-3 mb-6">
                    <div className="p-2 rounded-lg bg-fire-red/10">
                      <Award className="h-5 w-5 text-fire-red" />
                    </div>
                    <h2 className="font-heading text-xl font-bold text-card-foreground">
                      Führung
                    </h2>
                  </div>
                  <div className="grid sm:grid-cols-2 gap-6">
                    <div className="p-4 bg-muted rounded-lg">
                      <p className="text-sm text-muted-foreground mb-1">
                        {feuerwehr.kommandant.funktion}
                      </p>
                      <p className="font-medium text-foreground">{feuerwehr.kommandant.name}</p>
                    </div>
                    <div className="p-4 bg-muted rounded-lg">
                      <p className="text-sm text-muted-foreground mb-1">
                        {feuerwehr.stellvertreter.funktion}
                      </p>
                      <p className="font-medium text-foreground">{feuerwehr.stellvertreter.name}</p>
                    </div>
                  </div>
                </div>

                {/* Auszeichnungen */}
                {feuerwehr.auszeichnungen && feuerwehr.auszeichnungen.length > 0 && (
                  <div className="bg-card rounded-xl border border-border p-6">
                    <div className="flex items-center gap-3 mb-6">
                      <div className="p-2 rounded-lg bg-fire-yellow/10">
                        <Medal className="h-5 w-5 text-fire-yellow" />
                      </div>
                      <h2 className="font-heading text-xl font-bold text-card-foreground">
                        Auszeichnungen & Ehrungen
                      </h2>
                    </div>
                    <div className="flex flex-wrap gap-2">
                      {feuerwehr.auszeichnungen.map((auszeichnung, index) => (
                        <Badge key={index} variant="secondary" className="py-2 px-4">
                          <Medal className="h-4 w-4 mr-2 text-fire-yellow" />
                          {auszeichnung}
                        </Badge>
                      ))}
                    </div>
                  </div>
                )}

                {/* Bildergalerie */}
                {feuerwehr.galerie && feuerwehr.galerie.length > 0 && (
                  <div className="bg-card rounded-xl border border-border p-6">
                    <div className="flex items-center gap-3 mb-6">
                      <div className="p-2 rounded-lg bg-fire-red/10">
                        <Images className="h-5 w-5 text-fire-red" />
                      </div>
                      <h2 className="font-heading text-xl font-bold text-card-foreground">
                        Bildergalerie
                      </h2>
                    </div>
                    <ImageGallery images={feuerwehr.galerie} />
                  </div>
                )}
              </div>

              {/* Sidebar */}
              <div className="space-y-6">
                {/* Contact Info */}
                <div className="bg-card rounded-xl border border-border p-6">
                  <h3 className="font-heading text-lg font-bold text-card-foreground mb-4">
                    Kontakt
                  </h3>
                  <div className="space-y-4">
                    <div className="flex items-start gap-3">
                      <MapPin className="h-5 w-5 text-fire-red flex-shrink-0 mt-0.5" />
                      <p className="text-muted-foreground">{feuerwehr.adresse}</p>
                    </div>
                    <div className="flex items-center gap-3">
                      <Phone className="h-5 w-5 text-fire-red" />
                      <a
                        href={`tel:${feuerwehr.telefon.replace(/\s/g, "")}`}
                        className="text-muted-foreground hover:text-fire-red transition-colors"
                      >
                        {feuerwehr.telefon}
                      </a>
                    </div>
                    <div className="flex items-center gap-3">
                      <Mail className="h-5 w-5 text-fire-red" />
                      <a
                        href={`mailto:${feuerwehr.email}`}
                        className="text-muted-foreground hover:text-fire-red transition-colors"
                      >
                        {feuerwehr.email}
                      </a>
                    </div>
                    {feuerwehr.website && (
                      <Button asChild variant="outline" className="w-full mt-4">
                        <a href={feuerwehr.website} target="_blank" rel="noopener noreferrer">
                          Website besuchen
                        </a>
                      </Button>
                    )}
                  </div>
                </div>

                {/* Statistics */}
                <div className="bg-card rounded-xl border border-border p-6">
                  <h3 className="font-heading text-lg font-bold text-card-foreground mb-4">
                    Mitglieder
                  </h3>
                  <div className="space-y-4">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        <Users className="h-5 w-5 text-fire-red" />
                        <span className="text-muted-foreground">Aktive</span>
                      </div>
                      <span className="font-medium text-foreground">{feuerwehr.mitglieder.aktiv}</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        <Users className="h-5 w-5 text-primary" />
                        <span className="text-muted-foreground">Jugend</span>
                      </div>
                      <span className="font-medium text-foreground">{feuerwehr.mitglieder.jugend}</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        <Users className="h-5 w-5 text-muted-foreground" />
                        <span className="text-muted-foreground">Passiv/Fördernd</span>
                      </div>
                      <span className="font-medium text-foreground">{feuerwehr.mitglieder.passiv}</span>
                    </div>
                    <div className="pt-4 border-t border-border flex items-center justify-between">
                      <span className="font-medium text-foreground">Gesamt</span>
                      <span className="font-heading font-bold text-fire-red text-xl">{feuerwehr.mitglieder.gesamt}</span>
                    </div>
                  </div>
                </div>

                {/* Training Times */}
                <div className="bg-fire-red/10 rounded-xl border border-fire-red/20 p-6">
                  <h3 className="font-heading text-lg font-bold text-foreground mb-2">
                    Übungszeiten
                  </h3>
                  <p className="text-muted-foreground">{feuerwehr.uebungszeiten}</p>
                  <p className="text-sm text-muted-foreground mt-2">
                    Interessierte sind herzlich willkommen!
                  </p>
                  <Button asChild className="w-full mt-4 gradient-fire text-white">
                    <Link to="/mitglied">Jetzt Mitglied werden</Link>
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default FeuerwehrDetail;
