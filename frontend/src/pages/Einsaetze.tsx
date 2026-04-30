import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Flame, Clock, MapPin, AlertTriangle, CheckCircle } from "lucide-react";

const einsaetze = [
  {
    id: 1,
    datum: "20.12.2024",
    uhrzeit: "14:32",
    art: "Brandeinsatz",
    ort: "Freising, Hauptstraße 15",
    beschreibung: "Zimmerbrand im 2. Obergeschoss - Brand gelöscht, keine Verletzten",
    status: "abgeschlossen",
    feuerwehren: ["FF Freising", "FF Lerchenfeld"],
  },
  {
    id: 2,
    datum: "19.12.2024",
    uhrzeit: "08:15",
    art: "Technische Hilfeleistung",
    ort: "B11 bei Moosburg",
    beschreibung: "Verkehrsunfall mit eingeklemmter Person",
    status: "abgeschlossen",
    feuerwehren: ["FF Moosburg", "FF Langenpreising"],
  },
  {
    id: 3,
    datum: "18.12.2024",
    uhrzeit: "22:45",
    art: "Brandeinsatz",
    ort: "Hallbergmoos, Industriegebiet",
    beschreibung: "Containerbrand auf Firmengelände",
    status: "abgeschlossen",
    feuerwehren: ["FF Hallbergmoos"],
  },
  {
    id: 4,
    datum: "17.12.2024",
    uhrzeit: "16:20",
    art: "Sonstiger Einsatz",
    ort: "Neufahrn, Bahnhofstraße",
    beschreibung: "Ölspur auf der Fahrbahn - Bindemittel aufgebracht",
    status: "abgeschlossen",
    feuerwehren: ["FF Neufahrn"],
  },
  {
    id: 5,
    datum: "15.12.2024",
    uhrzeit: "11:00",
    art: "Brandeinsatz",
    ort: "Eching, Gewerbegebiet",
    beschreibung: "Brandmeldeanlage - Fehlalarm",
    status: "abgeschlossen",
    feuerwehren: ["FF Eching"],
  },
];

const Einsaetze = () => {
  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main className="py-12 md:py-20">
        <div className="container">
          {/* Page Header */}
          <div className="mb-12">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-12 h-12 gradient-fire rounded-lg flex items-center justify-center">
                <Flame className="h-6 w-6 text-primary-foreground" />
              </div>
              <h1 className="font-heading text-3xl md:text-4xl font-bold text-foreground">
                Aktuelle Einsätze
              </h1>
            </div>
            <p className="text-muted-foreground text-lg max-w-2xl">
              Übersicht der aktuellen und vergangenen Einsätze der Feuerwehren im Landkreis Freising.
            </p>
          </div>

          {/* Statistik */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
            <Card className="bg-card border-border">
              <CardContent className="p-4 text-center">
                <p className="text-3xl font-heading font-bold text-primary">247</p>
                <p className="text-sm text-muted-foreground">Einsätze 2024</p>
              </CardContent>
            </Card>
            <Card className="bg-card border-border">
              <CardContent className="p-4 text-center">
                <p className="text-3xl font-heading font-bold text-destructive">89</p>
                <p className="text-sm text-muted-foreground">Brandeinsätze</p>
              </CardContent>
            </Card>
            <Card className="bg-card border-border">
              <CardContent className="p-4 text-center">
                <p className="text-3xl font-heading font-bold text-primary">132</p>
                <p className="text-sm text-muted-foreground">Technische Hilfe</p>
              </CardContent>
            </Card>
            <Card className="bg-card border-border">
              <CardContent className="p-4 text-center">
                <p className="text-3xl font-heading font-bold text-muted-foreground">26</p>
                <p className="text-sm text-muted-foreground">Sonstige</p>
              </CardContent>
            </Card>
          </div>

          {/* Einsatzliste */}
          <div className="space-y-4">
            {einsaetze.map((einsatz) => (
              <Card key={einsatz.id} className="bg-card border-border hover:shadow-md transition-shadow">
                <CardContent className="p-6">
                  <div className="flex flex-col md:flex-row md:items-start gap-4">
                    <div className="flex items-center gap-4 md:w-48 shrink-0">
                      <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${
                        einsatz.art === "Brandeinsatz" ? "bg-destructive/10 text-destructive" :
                        einsatz.art === "Technische Hilfeleistung" ? "bg-primary/10 text-primary" :
                        "bg-muted text-muted-foreground"
                      }`}>
                        {einsatz.art === "Brandeinsatz" ? <Flame className="h-5 w-5" /> :
                         einsatz.art === "Technische Hilfeleistung" ? <AlertTriangle className="h-5 w-5" /> :
                         <CheckCircle className="h-5 w-5" />}
                      </div>
                      <div>
                        <p className="font-medium text-foreground">{einsatz.datum}</p>
                        <p className="text-sm text-muted-foreground flex items-center gap-1">
                          <Clock className="h-3 w-3" /> {einsatz.uhrzeit} Uhr
                        </p>
                      </div>
                    </div>
                    <div className="flex-1">
                      <div className="flex flex-wrap items-center gap-2 mb-2">
                        <Badge variant={
                          einsatz.art === "Brandeinsatz" ? "destructive" :
                          einsatz.art === "Technische Hilfeleistung" ? "default" :
                          "secondary"
                        }>
                          {einsatz.art}
                        </Badge>
                        <Badge variant="outline" className="text-muted-foreground">
                          {einsatz.status}
                        </Badge>
                      </div>
                      <p className="text-muted-foreground flex items-center gap-2 mb-2">
                        <MapPin className="h-4 w-4 shrink-0" /> {einsatz.ort}
                      </p>
                      <p className="text-foreground mb-3">{einsatz.beschreibung}</p>
                      <div className="flex flex-wrap gap-2">
                        {einsatz.feuerwehren.map((fw) => (
                          <Badge key={fw} variant="secondary" className="text-xs">
                            {fw}
                          </Badge>
                        ))}
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default Einsaetze;
