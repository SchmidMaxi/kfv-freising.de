import { useState } from "react";
import { Link } from "react-router-dom";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Input } from "@/components/ui/input";
import { 
  Newspaper, 
  Flame, 
  Calendar, 
  Clock, 
  MapPin, 
  ArrowRight,
  Search,
  AlertTriangle,
  CheckCircle
} from "lucide-react";

// Mock News Data
const newsItems = [
  {
    id: "1",
    title: "Neue Drehleiter für FF Freising",
    excerpt: "Die Freiwillige Feuerwehr Freising hat eine neue Drehleiter DLA(K) 23/12 erhalten.",
    date: "15.01.2026",
    category: "Beschaffung",
    image: "/placeholder.svg",
  },
  {
    id: "2",
    title: "Erfolgreiche Abnahme der Leistungsprüfung",
    excerpt: "32 Kameraden haben die Leistungsprüfung 'Die Gruppe im Löscheinsatz' bestanden.",
    date: "10.01.2026",
    category: "Ausbildung",
    image: "/placeholder.svg",
  },
  {
    id: "3",
    title: "Jahreshauptversammlung des KFV",
    excerpt: "Rückblick auf ein ereignisreiches Jahr mit über 1.200 Einsätzen im Landkreis.",
    date: "05.01.2026",
    category: "Verband",
    image: "/placeholder.svg",
  },
  {
    id: "4",
    title: "Jugendfeuerwehr gewinnt Bezirkswettbewerb",
    excerpt: "Die JF Moosburg sichert sich den ersten Platz beim Bezirksentscheid.",
    date: "02.01.2026",
    category: "Jugend",
    image: "/placeholder.svg",
  },
];

// Mock Einsätze Data
const einsaetze = [
  {
    id: 1,
    datum: "20.01.2026",
    uhrzeit: "14:32",
    art: "Brandeinsatz",
    ort: "Freising, Hauptstraße 15",
    beschreibung: "Zimmerbrand im 2. Obergeschoss - Brand gelöscht, keine Verletzten",
    status: "abgeschlossen",
    feuerwehren: ["FF Freising", "FF Lerchenfeld"],
  },
  {
    id: 2,
    datum: "19.01.2026",
    uhrzeit: "08:15",
    art: "Technische Hilfeleistung",
    ort: "B11 bei Moosburg",
    beschreibung: "Verkehrsunfall mit eingeklemmter Person",
    status: "abgeschlossen",
    feuerwehren: ["FF Moosburg", "FF Langenpreising"],
  },
  {
    id: 3,
    datum: "18.01.2026",
    uhrzeit: "22:45",
    art: "Brandeinsatz",
    ort: "Hallbergmoos, Industriegebiet",
    beschreibung: "Containerbrand auf Firmengelände",
    status: "abgeschlossen",
    feuerwehren: ["FF Hallbergmoos"],
  },
];

// Mock Termine Data
const termine = [
  {
    id: 1,
    title: "Kreisverbandsversammlung",
    date: "2026-02-15",
    time: "19:00",
    location: "Feuerwehrhaus Freising",
    category: "Verband",
  },
  {
    id: 2,
    title: "Lehrgang Gruppenführer",
    date: "2026-02-20",
    time: "08:00",
    location: "Staatl. Feuerwehrschule Geretsried",
    category: "Ausbildung",
  },
  {
    id: 3,
    title: "Jugendwart-Fortbildung",
    date: "2026-03-01",
    time: "09:00",
    location: "Feuerwehrhaus Moosburg",
    category: "Jugend",
  },
];

const Aktuelles = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [activeTab, setActiveTab] = useState("berichte");

  const filteredNews = newsItems.filter(item =>
    item.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
    item.excerpt.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main className="py-12 md:py-20">
        <div className="container">
          {/* Page Header */}
          <div className="mb-8">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-12 h-12 gradient-fire rounded-lg flex items-center justify-center">
                <Newspaper className="h-6 w-6 text-primary-foreground" />
              </div>
              <h1 className="font-heading text-3xl md:text-4xl font-bold text-foreground">
                Aktuelles
              </h1>
            </div>
            <p className="text-muted-foreground text-lg max-w-2xl">
              Neuigkeiten, Einsatzberichte und Termine aus dem Kreisfeuerwehrverband Freising.
            </p>
          </div>

          {/* Search */}
          <div className="relative mb-8 max-w-md">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Suchen..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-10"
            />
          </div>

          {/* Tabs */}
          <Tabs value={activeTab} onValueChange={setActiveTab} className="space-y-8">
            <TabsList className="grid w-full max-w-md grid-cols-3">
              <TabsTrigger value="berichte" className="flex items-center gap-2">
                <Newspaper className="h-4 w-4" />
                <span className="hidden sm:inline">Berichte</span>
              </TabsTrigger>
              <TabsTrigger value="einsaetze" className="flex items-center gap-2">
                <Flame className="h-4 w-4" />
                <span className="hidden sm:inline">Einsätze</span>
              </TabsTrigger>
              <TabsTrigger value="termine" className="flex items-center gap-2">
                <Calendar className="h-4 w-4" />
                <span className="hidden sm:inline">Termine</span>
              </TabsTrigger>
            </TabsList>

            {/* Berichte Tab */}
            <TabsContent value="berichte" className="space-y-6">
              <div className="grid gap-6 md:grid-cols-2">
                {filteredNews.map((item) => (
                  <Link key={item.id} to={`/news/${item.id}`}>
                    <Card className="h-full overflow-hidden hover:shadow-lg transition-all duration-300 group bg-card border-border">
                      <div className="aspect-video overflow-hidden bg-muted">
                        <img
                          src={item.image}
                          alt={item.title}
                          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                      </div>
                      <CardContent className="p-6">
                        <div className="flex items-center gap-2 mb-3">
                          <Badge variant="secondary">{item.category}</Badge>
                          <span className="text-sm text-muted-foreground">{item.date}</span>
                        </div>
                        <h3 className="font-heading font-semibold text-xl text-card-foreground mb-2 group-hover:text-primary transition-colors">
                          {item.title}
                        </h3>
                        <p className="text-muted-foreground line-clamp-2">{item.excerpt}</p>
                        <div className="mt-4 flex items-center text-primary font-medium">
                          <span>Weiterlesen</span>
                          <ArrowRight className="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                      </CardContent>
                    </Card>
                  </Link>
                ))}
              </div>
            </TabsContent>

            {/* Einsätze Tab */}
            <TabsContent value="einsaetze" className="space-y-4">
              {/* Statistik */}
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <Card className="bg-card border-border">
                  <CardContent className="p-4 text-center">
                    <p className="text-3xl font-heading font-bold text-primary">247</p>
                    <p className="text-sm text-muted-foreground">Einsätze 2026</p>
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

              <div className="text-center pt-4">
                <Link to="/einsaetze" className="text-primary hover:underline font-medium">
                  Alle Einsätze anzeigen →
                </Link>
              </div>
            </TabsContent>

            {/* Termine Tab */}
            <TabsContent value="termine" className="space-y-4">
              {termine.map((termin) => (
                <Card key={termin.id} className="bg-card border-border hover:shadow-md transition-shadow">
                  <CardContent className="p-6">
                    <div className="flex flex-col sm:flex-row gap-4">
                      <div className="flex items-center gap-4 sm:w-32 shrink-0">
                        <div className="w-14 h-14 bg-primary/10 rounded-lg flex flex-col items-center justify-center">
                          <span className="text-xs text-primary font-medium">
                            {new Date(termin.date).toLocaleDateString('de-DE', { month: 'short' })}
                          </span>
                          <span className="text-xl font-bold text-primary">
                            {new Date(termin.date).getDate()}
                          </span>
                        </div>
                      </div>
                      <div className="flex-1">
                        <Badge variant="outline" className="mb-2">{termin.category}</Badge>
                        <h3 className="font-heading font-semibold text-lg text-foreground mb-2">
                          {termin.title}
                        </h3>
                        <div className="flex flex-wrap gap-4 text-sm text-muted-foreground">
                          <span className="flex items-center gap-1">
                            <Clock className="h-4 w-4" /> {termin.time} Uhr
                          </span>
                          <span className="flex items-center gap-1">
                            <MapPin className="h-4 w-4" /> {termin.location}
                          </span>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}

              <div className="text-center pt-4">
                <Link to="/termine" className="text-primary hover:underline font-medium">
                  Alle Termine & Kalender anzeigen →
                </Link>
              </div>
            </TabsContent>
          </Tabs>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default Aktuelles;
