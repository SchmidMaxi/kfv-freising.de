import { useState } from "react";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import { Calendar as CalendarIcon, Clock, MapPin, ChevronLeft, ChevronRight, List, Grid } from "lucide-react";
import { de } from "date-fns/locale";
import { format, isSameDay, parseISO, startOfMonth, endOfMonth, eachDayOfInterval, addMonths, subMonths } from "date-fns";

interface Termin {
  id: number;
  datum: string; // ISO date string
  uhrzeit: string;
  titel: string;
  ort: string;
  kategorie: string;
  beschreibung: string;
}

const termine: Termin[] = [
  {
    id: 1,
    datum: "2025-01-15",
    uhrzeit: "19:00",
    titel: "Jahreshauptversammlung KFV Freising",
    ort: "Bürgerhaus Freising",
    kategorie: "Versammlung",
    beschreibung: "Ordentliche Jahreshauptversammlung des Kreisfeuerwehrverbands mit Wahlen und Ehrungen.",
  },
  {
    id: 2,
    datum: "2025-01-22",
    uhrzeit: "18:30",
    titel: "Kommandantendienstversammlung",
    ort: "Feuerwehrhaus Moosburg",
    kategorie: "Dienstversammlung",
    beschreibung: "Quartalsmäßige Versammlung aller Kommandanten im Landkreis.",
  },
  {
    id: 3,
    datum: "2025-02-08",
    uhrzeit: "09:00",
    titel: "Maschinisten-Lehrgang",
    ort: "FF Hallbergmoos",
    kategorie: "Ausbildung",
    beschreibung: "Zweitägiger Lehrgang für Maschinisten an Löschfahrzeugen.",
  },
  {
    id: 4,
    datum: "2025-03-15",
    uhrzeit: "10:00",
    titel: "Tag der offenen Tür - 150 Jahre FF Freising",
    ort: "Feuerwehrhaus Freising",
    kategorie: "Veranstaltung",
    beschreibung: "Großer Tag der offenen Tür anlässlich des Jubiläums mit Fahrzeugschau und Vorführungen.",
  },
  {
    id: 5,
    datum: "2025-04-05",
    uhrzeit: "08:00",
    titel: "Leistungsprüfung THL",
    ort: "Übungsgelände Eching",
    kategorie: "Prüfung",
    beschreibung: "Abnahme der Leistungsprüfung Technische Hilfeleistung.",
  },
  {
    id: 6,
    datum: "2025-05-17",
    uhrzeit: "14:00",
    titel: "Landkreis-Feuerwehrtag",
    ort: "Neufahrn",
    kategorie: "Veranstaltung",
    beschreibung: "Jährlicher Feuerwehrtag mit allen Feuerwehren des Landkreises.",
  },
  {
    id: 7,
    datum: "2025-01-28",
    uhrzeit: "19:00",
    titel: "Atemschutz-Fortbildung",
    ort: "FF Freising",
    kategorie: "Ausbildung",
    beschreibung: "Fortbildung für Atemschutzgeräteträger mit praktischen Übungen.",
  },
  {
    id: 8,
    datum: "2025-02-14",
    uhrzeit: "18:00",
    titel: "Jugendleiter-Treffen",
    ort: "Landratsamt Freising",
    kategorie: "Versammlung",
    beschreibung: "Austausch und Planung der Jugendarbeit im Landkreis.",
  },
  {
    id: 9,
    datum: "2025-02-22",
    uhrzeit: "09:00",
    titel: "Gruppenführer-Lehrgang Start",
    ort: "FF Moosburg",
    kategorie: "Ausbildung",
    beschreibung: "Beginn des 5-wöchigen Gruppenführer-Lehrgangs.",
  },
  {
    id: 10,
    datum: "2025-03-08",
    uhrzeit: "10:00",
    titel: "Frühjahrs-Leistungsprüfung",
    ort: "FF Neufahrn",
    kategorie: "Prüfung",
    beschreibung: "Leistungsprüfung Löschangriff in verschiedenen Stufen.",
  },
];

const kategorieColors: Record<string, { bg: string; text: string; dot: string }> = {
  Versammlung: { bg: "bg-primary/10", text: "text-primary", dot: "bg-primary" },
  Dienstversammlung: { bg: "bg-secondary", text: "text-secondary-foreground", dot: "bg-secondary-foreground" },
  Ausbildung: { bg: "bg-accent/20", text: "text-accent-foreground", dot: "bg-accent" },
  Veranstaltung: { bg: "bg-fire-red/10", text: "text-fire-red", dot: "bg-fire-red" },
  Prüfung: { bg: "bg-fire-yellow/10", text: "text-fire-yellow", dot: "bg-fire-yellow" },
};

const Termine = () => {
  const [currentMonth, setCurrentMonth] = useState(new Date(2025, 0, 1)); // Start in January 2025
  const [selectedDate, setSelectedDate] = useState<Date | undefined>(undefined);
  const [selectedKategorie, setSelectedKategorie] = useState<string | null>(null);
  const [viewMode, setViewMode] = useState<"calendar" | "list">("calendar");

  // Get dates with events
  const eventDates = termine.map((t) => parseISO(t.datum));

  // Get events for selected date
  const eventsForSelectedDate = selectedDate
    ? termine.filter((t) => isSameDay(parseISO(t.datum), selectedDate))
    : [];

  // Get events for current month
  const monthStart = startOfMonth(currentMonth);
  const monthEnd = endOfMonth(currentMonth);
  const eventsThisMonth = termine.filter((t) => {
    const date = parseISO(t.datum);
    return date >= monthStart && date <= monthEnd;
  });

  // Filter by category
  const filteredTermine = selectedKategorie
    ? termine.filter((t) => t.kategorie === selectedKategorie)
    : termine;

  const filteredMonthEvents = selectedKategorie
    ? eventsThisMonth.filter((t) => t.kategorie === selectedKategorie)
    : eventsThisMonth;

  // Navigate months
  const goToPreviousMonth = () => setCurrentMonth(subMonths(currentMonth, 1));
  const goToNextMonth = () => setCurrentMonth(addMonths(currentMonth, 1));

  // Get unique categories
  const kategorien = Array.from(new Set(termine.map((t) => t.kategorie)));

  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main className="py-12 md:py-20">
        <div className="container">
          {/* Page Header */}
          <div className="mb-12">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                <CalendarIcon className="h-6 w-6 text-primary-foreground" />
              </div>
              <h1 className="font-heading text-3xl md:text-4xl font-bold text-foreground">
                Termine & Events
              </h1>
            </div>
            <p className="text-muted-foreground text-lg max-w-2xl">
              Alle wichtigen Termine, Veranstaltungen und Ausbildungen im Überblick.
            </p>
          </div>

          {/* Controls */}
          <div className="flex flex-col sm:flex-row gap-4 mb-8 justify-between items-start sm:items-center">
            {/* Category Filter */}
            <div className="flex flex-wrap gap-2">
              <Badge
                variant={selectedKategorie === null ? "default" : "outline"}
                className="cursor-pointer hover:bg-secondary"
                onClick={() => setSelectedKategorie(null)}
              >
                Alle
              </Badge>
              {kategorien.map((kat) => (
                <Badge
                  key={kat}
                  variant={selectedKategorie === kat ? "default" : "outline"}
                  className="cursor-pointer hover:bg-secondary"
                  onClick={() => setSelectedKategorie(kat)}
                >
                  {kat}
                </Badge>
              ))}
            </div>

            {/* View Mode Toggle */}
            <div className="flex gap-2">
              <Button
                variant={viewMode === "calendar" ? "default" : "outline"}
                size="sm"
                onClick={() => setViewMode("calendar")}
              >
                <Grid className="h-4 w-4 mr-2" />
                Kalender
              </Button>
              <Button
                variant={viewMode === "list" ? "default" : "outline"}
                size="sm"
                onClick={() => setViewMode("list")}
              >
                <List className="h-4 w-4 mr-2" />
                Liste
              </Button>
            </div>
          </div>

          {viewMode === "calendar" ? (
            <div className="grid lg:grid-cols-[1fr_400px] gap-8">
              {/* Calendar View */}
              <Card className="bg-card border-border overflow-hidden">
                <CardContent className="p-0">
                  {/* Month Navigation */}
                  <div className="flex items-center justify-between p-4 border-b border-border bg-muted/30">
                    <Button variant="ghost" size="icon" onClick={goToPreviousMonth}>
                      <ChevronLeft className="h-5 w-5" />
                    </Button>
                    <h2 className="font-heading text-xl font-bold text-foreground">
                      {format(currentMonth, "MMMM yyyy", { locale: de })}
                    </h2>
                    <Button variant="ghost" size="icon" onClick={goToNextMonth}>
                      <ChevronRight className="h-5 w-5" />
                    </Button>
                  </div>

                  {/* Calendar */}
                  <div className="p-4">
                    <Calendar
                      mode="single"
                      selected={selectedDate}
                      onSelect={setSelectedDate}
                      month={currentMonth}
                      onMonthChange={setCurrentMonth}
                      locale={de}
                      className="w-full pointer-events-auto"
                      modifiers={{
                        hasEvent: (date) =>
                          eventDates.some((eventDate) => isSameDay(date, eventDate)),
                      }}
                      modifiersStyles={{
                        hasEvent: {
                          fontWeight: "bold",
                          textDecoration: "underline",
                          textDecorationColor: "hsl(var(--fire-red))",
                          textUnderlineOffset: "4px",
                        },
                      }}
                    />
                  </div>

                  {/* Events this month */}
                  <div className="border-t border-border p-4 bg-muted/20">
                    <h3 className="font-heading font-semibold text-foreground mb-3">
                      {filteredMonthEvents.length} Termine in {format(currentMonth, "MMMM", { locale: de })}
                    </h3>
                    <div className="space-y-2 max-h-48 overflow-y-auto">
                      {filteredMonthEvents.length > 0 ? (
                        filteredMonthEvents.map((termin) => {
                          const colors = kategorieColors[termin.kategorie] || { dot: "bg-muted-foreground" };
                          return (
                            <button
                              key={termin.id}
                              onClick={() => setSelectedDate(parseISO(termin.datum))}
                              className="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-muted text-left transition-colors"
                            >
                              <div className={`w-2 h-2 rounded-full ${colors.dot}`} />
                              <span className="text-sm text-muted-foreground">
                                {format(parseISO(termin.datum), "dd.MM.")}
                              </span>
                              <span className="text-sm text-foreground font-medium truncate flex-1">
                                {termin.titel}
                              </span>
                            </button>
                          );
                        })
                      ) : (
                        <p className="text-sm text-muted-foreground">
                          Keine Termine in diesem Monat.
                        </p>
                      )}
                    </div>
                  </div>
                </CardContent>
              </Card>

              {/* Selected Date Details */}
              <div className="space-y-4">
                {selectedDate ? (
                  <>
                    <div className="bg-primary rounded-xl p-6 text-primary-foreground">
                      <p className="text-sm opacity-80">Ausgewähltes Datum</p>
                      <p className="font-heading text-2xl font-bold">
                        {format(selectedDate, "EEEE, dd. MMMM yyyy", { locale: de })}
                      </p>
                      <p className="text-sm opacity-80 mt-2">
                        {eventsForSelectedDate.length} Termin(e)
                      </p>
                    </div>

                    {eventsForSelectedDate.length > 0 ? (
                      eventsForSelectedDate.map((termin) => {
                        const colors = kategorieColors[termin.kategorie] || { bg: "bg-muted", text: "text-muted-foreground", dot: "bg-muted-foreground" };
                        return (
                          <Card key={termin.id} className="bg-card border-border overflow-hidden">
                            <div className={`h-2 ${colors.dot}`} />
                            <CardContent className="p-6">
                              <Badge className={`${colors.bg} ${colors.text} mb-3`}>
                                {termin.kategorie}
                              </Badge>
                              <h3 className="font-heading font-semibold text-lg text-foreground mb-2">
                                {termin.titel}
                              </h3>
                              <p className="text-muted-foreground text-sm mb-4">
                                {termin.beschreibung}
                              </p>
                              <div className="space-y-2 text-sm">
                                <p className="text-muted-foreground flex items-center gap-2">
                                  <Clock className="h-4 w-4 text-fire-red" /> {termin.uhrzeit} Uhr
                                </p>
                                <p className="text-muted-foreground flex items-center gap-2">
                                  <MapPin className="h-4 w-4 text-fire-red" /> {termin.ort}
                                </p>
                              </div>
                            </CardContent>
                          </Card>
                        );
                      })
                    ) : (
                      <Card className="bg-card border-border">
                        <CardContent className="p-6 text-center text-muted-foreground">
                          Keine Termine an diesem Tag.
                        </CardContent>
                      </Card>
                    )}
                  </>
                ) : (
                  <Card className="bg-muted/30 border-border">
                    <CardContent className="p-8 text-center">
                      <CalendarIcon className="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                      <p className="text-muted-foreground">
                        Wählen Sie ein Datum im Kalender, um die Details zu sehen.
                      </p>
                    </CardContent>
                  </Card>
                )}
              </div>
            </div>
          ) : (
            /* List View */
            <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
              {filteredTermine.map((termin) => {
                const colors = kategorieColors[termin.kategorie] || { bg: "bg-muted", text: "text-muted-foreground", dot: "bg-muted-foreground" };
                const date = parseISO(termin.datum);
                return (
                  <Card key={termin.id} className="bg-card border-border hover:shadow-lg transition-shadow overflow-hidden">
                    <div className={`h-2 ${colors.dot}`} />
                    <CardContent className="p-6">
                      <div className="flex items-start justify-between mb-4">
                        <div className="flex items-center gap-3">
                          <div className="w-14 h-14 bg-secondary rounded-lg flex flex-col items-center justify-center">
                            <span className="text-xs text-muted-foreground uppercase">
                              {format(date, "MMM", { locale: de })}
                            </span>
                            <span className="text-xl font-bold text-foreground">
                              {format(date, "dd")}
                            </span>
                          </div>
                        </div>
                        <Badge className={`${colors.bg} ${colors.text}`}>
                          {termin.kategorie}
                        </Badge>
                      </div>
                      <h3 className="font-heading font-semibold text-lg text-foreground mb-3">
                        {termin.titel}
                      </h3>
                      <p className="text-muted-foreground text-sm mb-4 line-clamp-2">
                        {termin.beschreibung}
                      </p>
                      <div className="space-y-2 text-sm">
                        <p className="text-muted-foreground flex items-center gap-2">
                          <Clock className="h-4 w-4" /> {termin.uhrzeit} Uhr
                        </p>
                        <p className="text-muted-foreground flex items-center gap-2">
                          <MapPin className="h-4 w-4" /> {termin.ort}
                        </p>
                      </div>
                    </CardContent>
                  </Card>
                );
              })}
            </div>
          )}
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default Termine;
