import { Link } from "react-router-dom";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { 
  GraduationCap, 
  Calendar, 
  Users, 
  BookOpen, 
  Clock, 
  MapPin,
  ArrowRight,
  FileText,
  Star
} from "lucide-react";

const lehrgaenge = [
  {
    id: 1,
    title: "Truppmann Teil 1",
    beschreibung: "Grundausbildung für alle Feuerwehrangehörigen",
    dauer: "70 Stunden",
    naechsterTermin: "März 2026",
    teilnehmer: "max. 24",
    ort: "Kreisausbildungszentrum Freising",
  },
  {
    id: 2,
    title: "Truppmann Teil 2",
    beschreibung: "Weiterführende Ausbildung nach Truppmann Teil 1",
    dauer: "80 Stunden",
    naechsterTermin: "April 2026",
    teilnehmer: "max. 24",
    ort: "Kreisausbildungszentrum Freising",
  },
  {
    id: 3,
    title: "Sprechfunker",
    beschreibung: "Ausbildung im Bereich Funk und Kommunikation",
    dauer: "16 Stunden",
    naechsterTermin: "Februar 2026",
    teilnehmer: "max. 20",
    ort: "Feuerwehrhaus Moosburg",
  },
  {
    id: 4,
    title: "Atemschutzgeräteträger",
    beschreibung: "Ausbildung zum Tragen von Atemschutzgeräten",
    dauer: "25 Stunden",
    naechsterTermin: "Mai 2026",
    teilnehmer: "max. 16",
    ort: "Atemschutzzentrum Freising",
  },
  {
    id: 5,
    title: "Maschinisten",
    beschreibung: "Ausbildung für Maschinisten Löschfahrzeuge",
    dauer: "35 Stunden",
    naechsterTermin: "Juni 2026",
    teilnehmer: "max. 12",
    ort: "Kreisausbildungszentrum Freising",
  },
];

const jugendInfo = [
  {
    title: "Jugendfeuerwehren",
    count: "24",
    beschreibung: "Aktive Jugendfeuerwehren im Landkreis",
    icon: Users,
  },
  {
    title: "Mitglieder",
    count: "480+",
    beschreibung: "Jugendliche zwischen 12 und 18 Jahren",
    icon: Star,
  },
  {
    title: "Kinderfeuerwehren",
    count: "8",
    beschreibung: "Für Kinder zwischen 6 und 12 Jahren",
    icon: Users,
  },
];

const Ausbildung = () => {
  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main className="py-12 md:py-20">
        <div className="container">
          {/* Page Header */}
          <div className="mb-12">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-12 h-12 gradient-fire rounded-lg flex items-center justify-center">
                <GraduationCap className="h-6 w-6 text-primary-foreground" />
              </div>
              <h1 className="font-heading text-3xl md:text-4xl font-bold text-foreground">
                Ausbildung
              </h1>
            </div>
            <p className="text-muted-foreground text-lg max-w-2xl">
              Lehrgänge, Fortbildungen und Informationen zur Ausbildung im Kreisfeuerwehrverband Freising.
            </p>
          </div>

          {/* Quick Links */}
          <div className="grid sm:grid-cols-3 gap-4 mb-12">
            <Link to="/termine">
              <Card className="bg-card border-border hover:shadow-md transition-all hover:-translate-y-1 cursor-pointer">
                <CardContent className="p-6 flex items-center gap-4">
                  <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <Calendar className="h-6 w-6 text-primary" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-foreground">Termine</h3>
                    <p className="text-sm text-muted-foreground">Alle Ausbildungstermine</p>
                  </div>
                </CardContent>
              </Card>
            </Link>
            <Link to="/downloads">
              <Card className="bg-card border-border hover:shadow-md transition-all hover:-translate-y-1 cursor-pointer">
                <CardContent className="p-6 flex items-center gap-4">
                  <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <FileText className="h-6 w-6 text-primary" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-foreground">Downloads</h3>
                    <p className="text-sm text-muted-foreground">Formulare & Unterlagen</p>
                  </div>
                </CardContent>
              </Card>
            </Link>
            <Link to="/kontakt">
              <Card className="bg-card border-border hover:shadow-md transition-all hover:-translate-y-1 cursor-pointer">
                <CardContent className="p-6 flex items-center gap-4">
                  <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <BookOpen className="h-6 w-6 text-primary" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-foreground">Ansprechpartner</h3>
                    <p className="text-sm text-muted-foreground">Kreisausbilder kontaktieren</p>
                  </div>
                </CardContent>
              </Card>
            </Link>
          </div>

          {/* Lehrgänge */}
          <section className="mb-16">
            <h2 className="font-heading text-2xl font-bold text-foreground mb-6 flex items-center gap-2">
              <GraduationCap className="h-6 w-6 text-primary" />
              Lehrgänge auf Kreisebene
            </h2>
            <div className="space-y-4">
              {lehrgaenge.map((lehrgang) => (
                <Card key={lehrgang.id} className="bg-card border-border">
                  <CardContent className="p-6">
                    <div className="flex flex-col lg:flex-row lg:items-center gap-4">
                      <div className="flex-1">
                        <h3 className="font-heading font-semibold text-xl text-foreground mb-2">
                          {lehrgang.title}
                        </h3>
                        <p className="text-muted-foreground mb-3">{lehrgang.beschreibung}</p>
                        <div className="flex flex-wrap gap-4 text-sm">
                          <span className="flex items-center gap-1 text-muted-foreground">
                            <Clock className="h-4 w-4" /> {lehrgang.dauer}
                          </span>
                          <span className="flex items-center gap-1 text-muted-foreground">
                            <Users className="h-4 w-4" /> {lehrgang.teilnehmer}
                          </span>
                          <span className="flex items-center gap-1 text-muted-foreground">
                            <MapPin className="h-4 w-4" /> {lehrgang.ort}
                          </span>
                        </div>
                      </div>
                      <div className="flex items-center gap-4">
                        <div className="text-right">
                          <Badge variant="secondary" className="mb-1">
                            Nächster Termin
                          </Badge>
                          <p className="font-semibold text-primary">{lehrgang.naechsterTermin}</p>
                        </div>
                        <Button variant="outline" size="sm" asChild>
                          <Link to="/downloads">
                            Anmeldung <ArrowRight className="ml-1 h-4 w-4" />
                          </Link>
                        </Button>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </section>

          {/* Jugendarbeit */}
          <section className="mb-16">
            <h2 className="font-heading text-2xl font-bold text-foreground mb-6 flex items-center gap-2">
              <Users className="h-6 w-6 text-primary" />
              Jugend- & Kinderfeuerwehr
            </h2>
            
            <div className="grid sm:grid-cols-3 gap-6 mb-8">
              {jugendInfo.map((info) => (
                <Card key={info.title} className="bg-card border-border text-center">
                  <CardContent className="p-6">
                    <div className="w-12 h-12 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                      <info.icon className="h-6 w-6 text-primary" />
                    </div>
                    <p className="text-3xl font-heading font-bold text-primary mb-1">{info.count}</p>
                    <h3 className="font-semibold text-foreground mb-2">{info.title}</h3>
                    <p className="text-sm text-muted-foreground">{info.beschreibung}</p>
                  </CardContent>
                </Card>
              ))}
            </div>

            <Card className="bg-secondary/50 border-border">
              <CardContent className="p-8">
                <div className="flex flex-col md:flex-row gap-6 items-center">
                  <div className="flex-1">
                    <h3 className="font-heading text-xl font-bold text-foreground mb-3">
                      Interesse an der Jugendfeuerwehr?
                    </h3>
                    <p className="text-muted-foreground mb-4">
                      Die Jugendfeuerwehr bietet Kindern und Jugendlichen die Möglichkeit, 
                      spielerisch die Arbeit der Feuerwehr kennenzulernen und wichtige 
                      Werte wie Teamarbeit und Hilfsbereitschaft zu erlernen.
                    </p>
                    <div className="flex flex-wrap gap-3">
                      <Button asChild>
                        <Link to="/feuerwehren#jugend">
                          Jugendfeuerwehr finden
                        </Link>
                      </Button>
                      <Button variant="outline" asChild>
                        <Link to="/kontakt">
                          Kontakt aufnehmen
                        </Link>
                      </Button>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </section>

          {/* Weiterführende Links */}
          <section>
            <h2 className="font-heading text-2xl font-bold text-foreground mb-6">
              Weiterführende Ausbildung
            </h2>
            <div className="grid sm:grid-cols-2 gap-4">
              <Card className="bg-card border-border">
                <CardContent className="p-6">
                  <h3 className="font-semibold text-foreground mb-2">Staatliche Feuerwehrschulen</h3>
                  <p className="text-muted-foreground text-sm mb-4">
                    Lehrgänge auf Landesebene wie Gruppenführer, Zugführer und Spezialausbildungen.
                  </p>
                  <a 
                    href="https://www.sfs-w.de" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    className="text-primary hover:underline text-sm font-medium inline-flex items-center gap-1"
                  >
                    SFS Würzburg <ArrowRight className="h-3 w-3" />
                  </a>
                </CardContent>
              </Card>
              <Card className="bg-card border-border">
                <CardContent className="p-6">
                  <h3 className="font-semibold text-foreground mb-2">LFV Bayern</h3>
                  <p className="text-muted-foreground text-sm mb-4">
                    Informationen und Fortbildungsangebote des Landesfeuerwehrverbands Bayern.
                  </p>
                  <a 
                    href="https://www.lfv-bayern.de" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    className="text-primary hover:underline text-sm font-medium inline-flex items-center gap-1"
                  >
                    LFV Bayern <ArrowRight className="h-3 w-3" />
                  </a>
                </CardContent>
              </Card>
            </div>
          </section>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default Ausbildung;
