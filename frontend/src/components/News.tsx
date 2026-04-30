import { Link } from "react-router-dom";
import { Calendar, ArrowRight } from "lucide-react";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

const newsItems = [
  {
    id: 1,
    title: "Großübung im Gewerbegebiet Freising-Süd",
    excerpt: "Am vergangenen Samstag fand eine landkreisweite Großübung mit über 150 Einsatzkräften statt...",
    date: "10. Dezember 2025",
    category: "Übung",
    image: "https://images.unsplash.com/photo-1599059813005-11265ba4b4ce?q=80&w=800&auto=format&fit=crop",
  },
  {
    id: 2,
    title: "Neues Löschfahrzeug für die FF Marzling",
    excerpt: "Die Freiwillige Feuerwehr Marzling konnte ihr neues HLF 20 in Empfang nehmen...",
    date: "8. Dezember 2025",
    category: "Fahrzeuge",
    image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=800&auto=format&fit=crop",
  },
  {
    id: 3,
    title: "Jahreshauptversammlung 2025 – Rückblick und Ausblick",
    excerpt: "Bei der diesjährigen Jahreshauptversammlung wurden die Weichen für die Zukunft gestellt...",
    date: "5. Dezember 2025",
    category: "Verband",
    image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800&auto=format&fit=crop",
  },
];

const upcomingEvents = [
  { title: "Kommandantenversammlung", date: "15.12.2025", location: "Landratsamt Freising" },
  { title: "Winterschulung Atemschutz", date: "18.12.2025", location: "Feuerwache Freising" },
  { title: "Jugendfeuerwehr Weihnachtsfeier", date: "21.12.2025", location: "FF Hallbergmoos" },
];

export const News = () => {
  return (
    <section id="aktuelles" className="py-16 md:py-24 bg-background">
      <div className="container">
        {/* Section Header */}
        <div className="flex flex-col md:flex-row md:items-end md:justify-between mb-12">
          <div>
            <Badge variant="outline" className="mb-4 border-accent text-accent">
              Neuigkeiten
            </Badge>
            <h2 className="font-heading text-3xl md:text-4xl font-bold text-foreground">
              Aktuelles & Termine
            </h2>
          </div>
          <Button variant="ghost" className="mt-4 md:mt-0 text-accent hover:text-accent/80">
            Alle Nachrichten
            <ArrowRight className="ml-2 h-4 w-4" />
          </Button>
        </div>

        <div className="grid lg:grid-cols-3 gap-8">
          {/* News Cards */}
          <div className="lg:col-span-2 flex flex-col gap-6">
            {newsItems.map((item) => (
              <Link key={item.id} to={`/news/${item.id}`}>
                <Card className="overflow-hidden group hover:shadow-lg transition-shadow">
                  <div className="flex flex-col sm:flex-row">
                    <div className="sm:w-48 h-48 sm:h-auto overflow-hidden flex-shrink-0">
                      <img
                        src={item.image}
                        alt={item.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                      />
                    </div>
                    <CardContent className="flex-1 p-6">
                      <div className="flex items-center gap-3 mb-3">
                        <Badge className="gradient-fire text-primary-foreground">
                          {item.category}
                        </Badge>
                        <span className="text-sm text-muted-foreground flex items-center gap-1">
                          <Calendar className="h-3 w-3" />
                          {item.date}
                        </span>
                      </div>
                      <h3 className="font-heading font-semibold text-xl text-card-foreground mb-2 group-hover:text-accent transition-colors">
                        {item.title}
                      </h3>
                      <p className="text-muted-foreground text-sm leading-relaxed">
                        {item.excerpt}
                      </p>
                    </CardContent>
                  </div>
                </Card>
              </Link>
            ))}
          </div>

          {/* Upcoming Events Sidebar */}
          <div>
            <Card className="bg-primary text-primary-foreground">
              <CardHeader>
                <h3 className="font-heading font-semibold text-xl">Kommende Termine</h3>
              </CardHeader>
              <CardContent className="space-y-4">
                {upcomingEvents.map((event, index) => (
                  <div
                    key={index}
                    className="p-4 rounded-lg bg-primary-foreground/10 hover:bg-primary-foreground/15 transition-colors cursor-pointer"
                  >
                    <p className="font-heading font-medium text-primary-foreground">
                      {event.title}
                    </p>
                    <div className="flex items-center gap-2 mt-2 text-sm text-primary-foreground/70">
                      <Calendar className="h-3 w-3" />
                      <span>{event.date}</span>
                    </div>
                    <p className="text-sm text-primary-foreground/70 mt-1">
                      {event.location}
                    </p>
                  </div>
                ))}
                <Button className="w-full bg-fire-red hover:bg-fire-red/90 text-primary-foreground font-heading mt-4">
                  Alle Termine anzeigen
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </section>
  );
};