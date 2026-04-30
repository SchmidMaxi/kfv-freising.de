import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Flame, Calendar, MapPin, ArrowRight, Clock, Users, Phone } from "lucide-react";

const ShowcaseCards = () => {
  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />
      <main className="flex-1">
        <div className="container py-16 space-y-20">
          <div>
            <h1 className="font-heading text-4xl font-bold text-foreground mb-2">Cards</h1>
            <p className="text-muted-foreground text-lg">Verschiedene Card-Entwürfe für unterschiedliche Inhaltstypen.</p>
          </div>

          {/* Variante 1: QuickAction Cards (bestehend) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 1 – Quick-Action Cards</h2>
            <p className="text-muted-foreground">Icon-Cards mit Hover-Effekt, wie auf der Startseite verwendet.</p>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {[
                { icon: Flame, title: "Aktuelle Einsätze", desc: "Übersicht der Einsätze im Landkreis", variant: "accent" },
                { icon: Calendar, title: "Termine & Events", desc: "Veranstaltungen und Übungen", variant: "primary" },
                { icon: MapPin, title: "Feuerwehr finden", desc: "Ihre zuständige Feuerwehr finden", variant: "accent" },
                { icon: Phone, title: "Kontakt", desc: "Kreisfeuerwehrverband erreichen", variant: "primary" },
              ].map((action) => (
                <Card key={action.title} className="h-full border-0 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden bg-card cursor-pointer group">
                  <CardContent className="p-6">
                    <div className={`w-14 h-14 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform ${action.variant === "accent" ? "gradient-fire" : "bg-primary"}`}>
                      <action.icon className="h-7 w-7 text-primary-foreground" />
                    </div>
                    <h3 className="font-heading font-semibold text-xl text-card-foreground mb-2">{action.title}</h3>
                    <p className="text-muted-foreground text-sm leading-relaxed">{action.desc}</p>
                  </CardContent>
                </Card>
              ))}
            </div>
          </section>

          {/* Variante 2: News Cards (bestehend) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 2 – News Cards (horizontal)</h2>
            <p className="text-muted-foreground">Cards mit Bild und Text nebeneinander, wie im News-Bereich.</p>
            <div className="space-y-6 max-w-3xl">
              {[
                { title: "Großübung im Gewerbegebiet", date: "10. Dez 2025", cat: "Übung", img: "https://images.unsplash.com/photo-1599059813005-11265ba4b4ce?q=80&w=400&auto=format&fit=crop" },
                { title: "Neues Löschfahrzeug eingetroffen", date: "8. Dez 2025", cat: "Fahrzeuge", img: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=400&auto=format&fit=crop" },
              ].map((item) => (
                <Card key={item.title} className="overflow-hidden group hover:shadow-lg transition-shadow cursor-pointer">
                  <div className="flex flex-col sm:flex-row">
                    <div className="sm:w-48 h-48 sm:h-auto overflow-hidden flex-shrink-0">
                      <img src={item.img} alt={item.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    </div>
                    <CardContent className="flex-1 p-6">
                      <div className="flex items-center gap-3 mb-3">
                        <Badge className="gradient-fire text-primary-foreground">{item.cat}</Badge>
                        <span className="text-sm text-muted-foreground flex items-center gap-1"><Calendar className="h-3.5 w-3.5" /> {item.date}</span>
                      </div>
                      <h3 className="font-heading font-semibold text-xl text-card-foreground mb-2 group-hover:text-accent transition-colors">{item.title}</h3>
                      <p className="text-muted-foreground text-sm">Kurze Beschreibung des Artikels mit den wichtigsten Informationen...</p>
                    </CardContent>
                  </div>
                </Card>
              ))}
            </div>
          </section>

          {/* Variante 3: Event-Card (bestehend) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 3 – Event-Sidebar-Card</h2>
            <p className="text-muted-foreground">Farbige Card für Termine, wie in der Seitenleiste verwendet.</p>
            <div className="max-w-sm">
              <Card className="bg-primary text-primary-foreground">
                <CardHeader>
                  <h3 className="font-heading font-semibold text-xl">Kommende Termine</h3>
                </CardHeader>
                <CardContent className="space-y-4">
                  {[
                    { title: "Kommandantenversammlung", date: "20. Jan", loc: "Landratsamt" },
                    { title: "THL-Ausbildung Stufe 2", date: "15. Feb", loc: "Übungsgelände" },
                    { title: "Jahreshauptversammlung", date: "8. Mär", loc: "Bürgerhaus" },
                  ].map((e) => (
                    <div key={e.title} className="flex gap-4 items-start border-b border-primary-foreground/20 pb-3 last:border-0">
                      <div className="text-center min-w-[3rem]">
                        <div className="text-xs opacity-80">{e.date.split(" ")[1]}</div>
                        <div className="text-2xl font-bold">{e.date.split(" ")[0].replace(".", "")}</div>
                      </div>
                      <div>
                        <p className="font-semibold">{e.title}</p>
                        <p className="text-sm opacity-80 flex items-center gap-1"><MapPin className="h-3 w-3" />{e.loc}</p>
                      </div>
                    </div>
                  ))}
                </CardContent>
              </Card>
            </div>
          </section>

          {/* Variante 4: Statistik-Card (NEU) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 4 – Statistik-Cards (Neu)</h2>
            <p className="text-muted-foreground">Kompakte Cards für Kennzahlen und Statistiken.</p>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {[
                { label: "Einsätze 2025", value: "1.247", icon: Flame, trend: "+12%" },
                { label: "Aktive Mitglieder", value: "2.850", icon: Users, trend: "+3%" },
                { label: "Feuerwehren", value: "42", icon: MapPin, trend: "—" },
                { label: "Ausbildungsstunden", value: "18.400", icon: Clock, trend: "+8%" },
              ].map((stat) => (
                <Card key={stat.label} className="bg-card border-border">
                  <CardContent className="p-5">
                    <div className="flex items-center justify-between mb-3">
                      <div className="p-2 rounded-md bg-primary/10">
                        <stat.icon className="h-4 w-4 text-primary" />
                      </div>
                      <span className="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full dark:bg-emerald-900/30 dark:text-emerald-400">{stat.trend}</span>
                    </div>
                    <div className="text-2xl font-bold text-card-foreground">{stat.value}</div>
                    <p className="text-xs text-muted-foreground mt-1">{stat.label}</p>
                  </CardContent>
                </Card>
              ))}
            </div>
          </section>

          {/* Variante 5: Feature-Card mit Footer (NEU) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 5 – Feature-Cards mit Footer (Neu)</h2>
            <p className="text-muted-foreground">Cards mit Header, Body und Footer-Bereich für detailliertere Inhalte.</p>
            <div className="grid md:grid-cols-3 gap-6">
              {[
                { title: "Atemschutz", desc: "Ausbildung und Überwachung der Atemschutzgeräteträger im Landkreis.", badge: "Ausbildung" },
                { title: "Jugendarbeit", desc: "Nachwuchsförderung und Jugendausbildung in allen Feuerwehren.", badge: "Jugend" },
                { title: "Katastrophenschutz", desc: "Planung und Koordination für den Katastrophenfall.", badge: "Einsatz" },
              ].map((item) => (
                <Card key={item.title} className="flex flex-col bg-card border-border hover:shadow-lg transition-shadow">
                  <CardHeader>
                    <Badge variant="outline" className="w-fit mb-2">{item.badge}</Badge>
                    <CardTitle className="text-lg">{item.title}</CardTitle>
                    <CardDescription>{item.desc}</CardDescription>
                  </CardHeader>
                  <CardFooter className="mt-auto">
                    <Button variant="ghost" size="sm" className="ml-auto text-primary">
                      Details <ArrowRight className="ml-1 h-4 w-4" />
                    </Button>
                  </CardFooter>
                </Card>
              ))}
            </div>
          </section>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default ShowcaseCards;
