import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Download, FileText, BookOpen, Briefcase, Shield, Users, Search, FolderOpen } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { useState } from "react";

interface DownloadItem {
  name: string;
  format: string;
  size: string;
  beschreibung?: string;
}

interface DownloadCategory {
  id: string;
  title: string;
  icon: React.ElementType;
  description: string;
  items: DownloadItem[];
}

const downloadCategories: DownloadCategory[] = [
  {
    id: "ausbildung",
    title: "Ausbildung & Lehrgänge",
    icon: BookOpen,
    description: "Anmeldeformulare, Prüfungsordnungen und Lehrpläne",
    items: [
      { name: "Lehrgangsanmeldung MTA", format: "PDF", size: "245 KB", beschreibung: "Anmeldung zur Modularen Truppausbildung" },
      { name: "Prüfungsordnung THL", format: "PDF", size: "1.2 MB", beschreibung: "Prüfungsordnung Technische Hilfeleistung" },
      { name: "Ausbildungsplan 2025", format: "PDF", size: "890 KB", beschreibung: "Jahresausbildungsplan mit allen Terminen" },
      { name: "Lehrgang Atemschutz - Antrag", format: "PDF", size: "312 KB", beschreibung: "Anmeldung zum Atemschutzlehrgang" },
      { name: "Maschinisten-Lehrgang Unterlagen", format: "PDF", size: "2.4 MB", beschreibung: "Begleitmaterial für Maschinisten-Ausbildung" },
      { name: "Gruppenführer-Lehrgang Info", format: "PDF", size: "567 KB", beschreibung: "Informationen zum Gruppenführer-Lehrgang" },
    ],
  },
  {
    id: "verwaltung",
    title: "Verwaltung & Satzung",
    icon: Briefcase,
    description: "Satzungen, Ordnungen und Verwaltungsunterlagen",
    items: [
      { name: "Mitgliedsantrag KFV", format: "PDF", size: "156 KB", beschreibung: "Antrag auf Mitgliedschaft im Verband" },
      { name: "Satzung KFV Freising", format: "PDF", size: "2.1 MB", beschreibung: "Aktuelle Verbandssatzung" },
      { name: "Beitragsordnung", format: "PDF", size: "98 KB", beschreibung: "Regelung der Mitgliedsbeiträge" },
      { name: "Geschäftsordnung", format: "PDF", size: "445 KB", beschreibung: "Geschäftsordnung des Verbands" },
      { name: "Ehrungsordnung", format: "PDF", size: "234 KB", beschreibung: "Regelungen zu Ehrungen und Auszeichnungen" },
      { name: "Datenschutzerklärung", format: "PDF", size: "189 KB", beschreibung: "Informationen zum Datenschutz" },
    ],
  },
  {
    id: "einsatz",
    title: "Einsatzunterlagen",
    icon: Shield,
    description: "Alarmierungspläne, Funkrufnamen und Einsatzberichte",
    items: [
      { name: "Alarmierungsübersicht Landkreis", format: "PDF", size: "445 KB", beschreibung: "Übersicht aller Alarmierungsstufen" },
      { name: "Funkrufnamen Landkreis Freising", format: "PDF", size: "312 KB", beschreibung: "Aktuelle Funkrufnamenliste" },
      { name: "Einsatzbericht Vorlage", format: "DOCX", size: "67 KB", beschreibung: "Vorlage für Einsatzberichte" },
      { name: "AAO Landkreis Freising", format: "PDF", size: "1.8 MB", beschreibung: "Alarm- und Ausrückeordnung" },
      { name: "Gefahrgut-Merkblätter", format: "PDF", size: "3.2 MB", beschreibung: "Wichtige Gefahrgut-Informationen" },
      { name: "Einsatzleiter-Checkliste", format: "PDF", size: "234 KB", beschreibung: "Checkliste für Einsatzleiter" },
    ],
  },
  {
    id: "formulare",
    title: "Anträge & Formulare",
    icon: FileText,
    description: "Alle wichtigen Antragsformulare zum Download",
    items: [
      { name: "Zuschussantrag Ausrüstung", format: "PDF", size: "289 KB", beschreibung: "Antrag auf Bezuschussung von Ausrüstung" },
      { name: "Zuschussantrag Fahrzeuge", format: "PDF", size: "345 KB", beschreibung: "Antrag für Fahrzeugbeschaffung" },
      { name: "Veranstaltungsanmeldung", format: "PDF", size: "178 KB", beschreibung: "Anmeldung zu Verbandsveranstaltungen" },
      { name: "Freistellungsantrag", format: "PDF", size: "134 KB", beschreibung: "Antrag auf Arbeitsfreistellung" },
      { name: "Unfallanzeige", format: "PDF", size: "267 KB", beschreibung: "Formular für Unfallmeldungen" },
      { name: "Kostenerstattung", format: "PDF", size: "198 KB", beschreibung: "Antrag auf Kostenerstattung" },
    ],
  },
  {
    id: "jugend",
    title: "Jugendfeuerwehr",
    icon: Users,
    description: "Unterlagen für die Jugendarbeit",
    items: [
      { name: "Aufnahmeantrag Jugendfeuerwehr", format: "PDF", size: "234 KB", beschreibung: "Mitgliedsantrag für Jugendliche" },
      { name: "Einverständniserklärung Eltern", format: "PDF", size: "145 KB", beschreibung: "Einverständnis für Veranstaltungen" },
      { name: "Jugendleistungsprüfung Ordnung", format: "PDF", size: "567 KB", beschreibung: "Prüfungsordnung JF" },
      { name: "Wissenstest Unterlagen", format: "PDF", size: "1.1 MB", beschreibung: "Lernmaterial für den Wissenstest" },
      { name: "Zeltlager Anmeldung", format: "PDF", size: "198 KB", beschreibung: "Anmeldung zum JF-Zeltlager" },
      { name: "Jugendordnung", format: "PDF", size: "345 KB", beschreibung: "Ordnung der Jugendfeuerwehr" },
    ],
  },
];

const Downloads = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [activeCategory, setActiveCategory] = useState<string | null>(null);

  // Filter downloads by search query
  const filteredCategories = downloadCategories.map((category) => ({
    ...category,
    items: category.items.filter(
      (item) =>
        item.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        (item.beschreibung && item.beschreibung.toLowerCase().includes(searchQuery.toLowerCase()))
    ),
  })).filter((category) => category.items.length > 0 || !searchQuery);

  // Get displayed categories
  const displayedCategories = activeCategory
    ? filteredCategories.filter((c) => c.id === activeCategory)
    : filteredCategories;

  // Count total downloads
  const totalDownloads = downloadCategories.reduce((acc, cat) => acc + cat.items.length, 0);

  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />

      <main className="flex-1">
        {/* Hero Section */}
        <section className="bg-primary py-16 md:py-24">
          <div className="container text-center">
            <div className="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <Download className="h-8 w-8 text-primary-foreground" />
            </div>
            <h1 className="font-heading text-4xl md:text-5xl font-bold text-primary-foreground mb-4">
              Downloads & Formulare
            </h1>
            <p className="text-lg text-primary-foreground/80 max-w-2xl mx-auto">
              Hier finden Sie alle wichtigen Dokumente, Formulare und Unterlagen
              für die Feuerwehren im Landkreis Freising.
            </p>
            <div className="mt-6">
              <Badge variant="secondary" className="text-lg py-2 px-4">
                <FolderOpen className="h-5 w-5 mr-2" />
                {totalDownloads} Dokumente verfügbar
              </Badge>
            </div>
          </div>
        </section>

        {/* Search and Filter */}
        <section className="py-8 border-b border-border bg-card sticky top-0 z-10">
          <div className="container">
            <div className="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
              {/* Search */}
              <div className="relative w-full md:w-96">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                <Input
                  placeholder="Dokumente durchsuchen..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="pl-10"
                />
              </div>

              {/* Category Filter */}
              <div className="flex flex-wrap gap-2">
                <Badge
                  variant={activeCategory === null ? "default" : "outline"}
                  className="cursor-pointer hover:bg-secondary"
                  onClick={() => setActiveCategory(null)}
                >
                  Alle Kategorien
                </Badge>
                {downloadCategories.map((category) => (
                  <Badge
                    key={category.id}
                    variant={activeCategory === category.id ? "default" : "outline"}
                    className="cursor-pointer hover:bg-secondary"
                    onClick={() => setActiveCategory(category.id)}
                  >
                    {category.title}
                  </Badge>
                ))}
              </div>
            </div>
          </div>
        </section>

        {/* Downloads Grid */}
        <section className="py-16">
          <div className="container">
            {displayedCategories.length === 0 ? (
              <div className="text-center py-12">
                <FileText className="h-16 w-16 text-muted-foreground mx-auto mb-4" />
                <h3 className="font-heading text-xl font-semibold text-foreground mb-2">
                  Keine Dokumente gefunden
                </h3>
                <p className="text-muted-foreground">
                  Versuchen Sie einen anderen Suchbegriff.
                </p>
              </div>
            ) : (
              <div className="space-y-12">
                {displayedCategories.map((category) => {
                  const IconComponent = category.icon;
                  return (
                    <div key={category.id} id={category.id}>
                      {/* Category Header */}
                      <div className="flex items-center gap-4 mb-6">
                        <div className="p-3 rounded-xl bg-fire-red/10">
                          <IconComponent className="h-6 w-6 text-fire-red" />
                        </div>
                        <div>
                          <h2 className="font-heading text-2xl font-bold text-foreground">
                            {category.title}
                          </h2>
                          <p className="text-muted-foreground">{category.description}</p>
                        </div>
                        <Badge variant="secondary" className="ml-auto">
                          {category.items.length} Dokumente
                        </Badge>
                      </div>

                      {/* Downloads List */}
                      <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {category.items.map((item, index) => (
                          <a
                            key={index}
                            href="#"
                            className="flex items-start gap-4 p-4 rounded-xl bg-card border border-border hover:border-fire-red hover:shadow-lg transition-all group"
                          >
                            <div className="p-2 rounded-lg bg-muted group-hover:bg-fire-red/10 transition-colors">
                              <FileText className="h-5 w-5 text-muted-foreground group-hover:text-fire-red transition-colors" />
                            </div>
                            <div className="flex-1 min-w-0">
                              <h3 className="font-medium text-foreground group-hover:text-fire-red transition-colors truncate">
                                {item.name}
                              </h3>
                              {item.beschreibung && (
                                <p className="text-sm text-muted-foreground line-clamp-1 mt-1">
                                  {item.beschreibung}
                                </p>
                              )}
                              <div className="flex items-center gap-2 mt-2">
                                <Badge variant="outline" className="text-xs">
                                  {item.format}
                                </Badge>
                                <span className="text-xs text-muted-foreground">
                                  {item.size}
                                </span>
                              </div>
                            </div>
                            <Download className="h-5 w-5 text-muted-foreground group-hover:text-fire-red transition-colors flex-shrink-0" />
                          </a>
                        ))}
                      </div>
                    </div>
                  );
                })}
              </div>
            )}
          </div>
        </section>

        {/* Info Section */}
        <section className="py-12 bg-muted">
          <div className="container">
            <div className="max-w-3xl mx-auto text-center">
              <h2 className="font-heading text-2xl font-bold text-foreground mb-4">
                Dokument nicht gefunden?
              </h2>
              <p className="text-muted-foreground mb-6">
                Sollten Sie ein bestimmtes Formular oder Dokument benötigen, das hier nicht aufgeführt ist,
                kontaktieren Sie uns bitte direkt. Wir helfen Ihnen gerne weiter.
              </p>
              <Button className="gradient-fire text-white" asChild>
                <a href="/kontakt">Kontakt aufnehmen</a>
              </Button>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default Downloads;
