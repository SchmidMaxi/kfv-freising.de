import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Newspaper, Flame, Calendar, MapPin, Users, Phone } from "lucide-react";

const ShowcaseTabs = () => {
  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />
      <main className="flex-1">
        <div className="container py-16 space-y-20">
          <div>
            <h1 className="font-heading text-4xl font-bold text-foreground mb-2">Tabs</h1>
            <p className="text-muted-foreground text-lg">Verschiedene Tab-Entwürfe für die Navigation innerhalb von Sektionen.</p>
          </div>

          {/* Variante 1: Standard-Tabs mit Icons (bestehend) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 1 – Standard mit Icons</h2>
            <p className="text-muted-foreground">Wie auf der Aktuelles-Seite: Tabs mit Icons und responsivem Label.</p>
            <Tabs defaultValue="berichte" className="space-y-6">
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
              <TabsContent value="berichte">
                <Card><CardContent className="p-6"><p className="text-muted-foreground">Inhalte der Berichte werden hier angezeigt...</p></CardContent></Card>
              </TabsContent>
              <TabsContent value="einsaetze">
                <Card><CardContent className="p-6"><p className="text-muted-foreground">Einsatzübersicht wird hier dargestellt...</p></CardContent></Card>
              </TabsContent>
              <TabsContent value="termine">
                <Card><CardContent className="p-6"><p className="text-muted-foreground">Terminliste erscheint hier...</p></CardContent></Card>
              </TabsContent>
            </Tabs>
          </section>

          {/* Variante 2: Fullwidth Tabs */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 2 – Fullwidth</h2>
            <p className="text-muted-foreground">Tabs über die volle Breite, geeignet für gleichwertige Bereiche.</p>
            <Tabs defaultValue="info" className="space-y-6">
              <TabsList className="w-full grid grid-cols-4">
                <TabsTrigger value="info">Informationen</TabsTrigger>
                <TabsTrigger value="anfahrt">Anfahrt</TabsTrigger>
                <TabsTrigger value="kontakt">Kontakt</TabsTrigger>
                <TabsTrigger value="downloads">Downloads</TabsTrigger>
              </TabsList>
              {["info", "anfahrt", "kontakt", "downloads"].map((tab) => (
                <TabsContent key={tab} value={tab}>
                  <Card>
                    <CardContent className="p-6">
                      <p className="text-muted-foreground">Inhalt für „{tab.charAt(0).toUpperCase() + tab.slice(1)}" wird hier angezeigt.</p>
                    </CardContent>
                  </Card>
                </TabsContent>
              ))}
            </Tabs>
          </section>

          {/* Variante 3: Pill-Tabs (NEU) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 3 – Pill-Tabs (Neu)</h2>
            <p className="text-muted-foreground">Tabs als abgerundete Pills mit Badge-Zähler.</p>
            <Tabs defaultValue="alle" className="space-y-6">
              <TabsList className="bg-transparent gap-2 h-auto flex-wrap">
                {[
                  { value: "alle", label: "Alle", count: 42 },
                  { value: "brand", label: "Brandeinsätze", count: 12 },
                  { value: "thl", label: "Technische Hilfe", count: 18 },
                  { value: "sonstige", label: "Sonstige", count: 12 },
                ].map((tab) => (
                  <TabsTrigger
                    key={tab.value}
                    value={tab.value}
                    className="rounded-full px-4 py-2 data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-none border border-border data-[state=inactive]:bg-transparent"
                  >
                    {tab.label}
                    <Badge variant="secondary" className="ml-2 h-5 text-xs data-[state=active]:bg-primary-foreground/20">{tab.count}</Badge>
                  </TabsTrigger>
                ))}
              </TabsList>
              {["alle", "brand", "thl", "sonstige"].map((tab) => (
                <TabsContent key={tab} value={tab}>
                  <div className="grid md:grid-cols-2 gap-4">
                    {[1, 2, 3, 4].map((n) => (
                      <Card key={n} className="bg-card border-border">
                        <CardContent className="p-4 flex items-center gap-4">
                          <div className="p-2 rounded-md bg-fire-red/10">
                            <Flame className="h-5 w-5 text-fire-red" />
                          </div>
                          <div>
                            <p className="font-semibold text-card-foreground text-sm">Einsatz #{n} – {tab}</p>
                            <p className="text-xs text-muted-foreground">Beispieldaten für den Tab-Inhalt</p>
                          </div>
                        </CardContent>
                      </Card>
                    ))}
                  </div>
                </TabsContent>
              ))}
            </Tabs>
          </section>

          {/* Variante 4: Vertikale Tabs (NEU) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 4 – Vertikale Tabs (Neu)</h2>
            <p className="text-muted-foreground">Seitliche Tab-Navigation für umfangreiche Inhalte.</p>
            <Tabs defaultValue="ueber" orientation="vertical" className="flex gap-6">
              <TabsList className="flex flex-col h-auto bg-muted rounded-lg p-1 min-w-[200px]">
                <TabsTrigger value="ueber" className="justify-start w-full gap-2">
                  <Users className="h-4 w-4" /> Über uns
                </TabsTrigger>
                <TabsTrigger value="standorte" className="justify-start w-full gap-2">
                  <MapPin className="h-4 w-4" /> Standorte
                </TabsTrigger>
                <TabsTrigger value="kontakt" className="justify-start w-full gap-2">
                  <Phone className="h-4 w-4" /> Kontakt
                </TabsTrigger>
              </TabsList>
              <div className="flex-1">
                <TabsContent value="ueber" className="mt-0">
                  <Card><CardContent className="p-6">
                    <h3 className="font-heading font-semibold text-lg text-card-foreground mb-2">Über den Kreisfeuerwehrverband</h3>
                    <p className="text-muted-foreground leading-relaxed">Der KFV Freising vertritt die Interessen von 42 Freiwilligen Feuerwehren im Landkreis und koordiniert Ausbildung, Beschaffung und überörtliche Zusammenarbeit.</p>
                  </CardContent></Card>
                </TabsContent>
                <TabsContent value="standorte" className="mt-0">
                  <Card><CardContent className="p-6">
                    <h3 className="font-heading font-semibold text-lg text-card-foreground mb-2">Standorte im Landkreis</h3>
                    <p className="text-muted-foreground leading-relaxed">42 Feuerwehrstandorte verteilt auf den gesamten Landkreis Freising sorgen für flächendeckenden Brandschutz.</p>
                  </CardContent></Card>
                </TabsContent>
                <TabsContent value="kontakt" className="mt-0">
                  <Card><CardContent className="p-6">
                    <h3 className="font-heading font-semibold text-lg text-card-foreground mb-2">Kontakt</h3>
                    <p className="text-muted-foreground leading-relaxed">Kreisfeuerwehrverband Freising e.V., Landratsamt Freising, Landshuter Str. 31, 85356 Freising</p>
                  </CardContent></Card>
                </TabsContent>
              </div>
            </Tabs>
          </section>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default ShowcaseTabs;
