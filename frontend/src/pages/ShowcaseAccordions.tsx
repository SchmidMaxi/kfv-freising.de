import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { HelpCircle, FileText, Shield, AlertTriangle } from "lucide-react";

const faqs = [
  { q: "Wie trete ich der Feuerwehr bei?", a: "Wenden Sie sich an Ihre örtliche Freiwillige Feuerwehr. In der Regel gibt es regelmäßige Übungsabende, an denen Sie unverbindlich teilnehmen können." },
  { q: "Ab welchem Alter kann man Mitglied werden?", a: "Kinder können ab 12 Jahren der Jugendfeuerwehr beitreten. Der aktive Dienst beginnt ab 18 Jahren." },
  { q: "Was kostet die Mitgliedschaft?", a: "Die Mitgliedschaft in der Freiwilligen Feuerwehr ist kostenlos. In manchen Gemeinden gibt es einen Feuerwehrverein mit einem geringen Jahresbeitrag." },
  { q: "Welche Ausbildung durchlaufe ich?", a: "Neue Mitglieder absolvieren zunächst die Modulare Truppausbildung (MTA), die ca. 120 Stunden umfasst." },
];

const ShowcaseAccordions = () => {
  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />
      <main className="flex-1">
        <div className="container py-16 space-y-20">
          <div>
            <h1 className="font-heading text-4xl font-bold text-foreground mb-2">Accordions</h1>
            <p className="text-muted-foreground text-lg">Verschiedene Accordion-Entwürfe für FAQ, Informationen und mehr.</p>
          </div>

          {/* Variante 1: FAQ-Accordion (bestehend) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 1 – FAQ-Accordion</h2>
            <p className="text-muted-foreground">Wie auf der Service-Seite: Card-basiert mit abgerundeten Ecken.</p>
            <div className="max-w-3xl">
              <Accordion type="single" collapsible className="space-y-4">
                {faqs.map((faq, i) => (
                  <AccordionItem
                    key={i}
                    value={`faq-${i}`}
                    className="bg-card rounded-xl border border-border px-6"
                  >
                    <AccordionTrigger className="text-left font-heading font-semibold text-card-foreground hover:text-fire-red">
                      {faq.q}
                    </AccordionTrigger>
                    <AccordionContent className="text-muted-foreground leading-relaxed">
                      {faq.a}
                    </AccordionContent>
                  </AccordionItem>
                ))}
              </Accordion>
            </div>
          </section>

          {/* Variante 2: Einfaches Accordion mit Trennlinien */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 2 – Minimalistisch</h2>
            <p className="text-muted-foreground">Schlichtes Accordion nur mit Trennlinien, ohne Card-Hintergrund.</p>
            <div className="max-w-3xl">
              <Accordion type="single" collapsible>
                {faqs.map((faq, i) => (
                  <AccordionItem key={i} value={`min-${i}`}>
                    <AccordionTrigger className="text-foreground hover:text-primary">
                      {faq.q}
                    </AccordionTrigger>
                    <AccordionContent className="text-muted-foreground">
                      {faq.a}
                    </AccordionContent>
                  </AccordionItem>
                ))}
              </Accordion>
            </div>
          </section>

          {/* Variante 3: Mit Icons und Badges (NEU) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 3 – Mit Icons & Badges (Neu)</h2>
            <p className="text-muted-foreground">Accordion-Items mit farbigen Icons und Kategorie-Badges.</p>
            <div className="max-w-3xl">
              <Accordion type="single" collapsible className="space-y-3">
                {[
                  { icon: HelpCircle, color: "text-blue-500", badge: "Allgemein", title: "Wie trete ich der Feuerwehr bei?", content: "Wenden Sie sich an Ihre örtliche Feuerwehr. Übungsabende sind der beste Einstieg." },
                  { icon: Shield, color: "text-fire-red", badge: "Sicherheit", title: "Welche Schutzausrüstung erhalte ich?", content: "Jedes aktive Mitglied erhält eine vollständige persönliche Schutzausrüstung (PSA) kostenfrei gestellt." },
                  { icon: FileText, color: "text-emerald-500", badge: "Verwaltung", title: "Welche Formulare brauche ich?", content: "Für den Eintritt benötigen Sie das Aufnahmeformular und eine ärztliche Untersuchungsbescheinigung." },
                  { icon: AlertTriangle, color: "text-amber-500", badge: "Einsatz", title: "Was passiert bei einem Alarm?", content: "Bei Alarm werden Sie per Funkmeldeempfänger oder App alarmiert und begeben sich zum Feuerwehrhaus." },
                ].map((item, i) => (
                  <AccordionItem
                    key={i}
                    value={`icon-${i}`}
                    className="bg-card rounded-xl border border-border px-6"
                  >
                    <AccordionTrigger className="text-left text-card-foreground hover:text-primary">
                      <div className="flex items-center gap-3">
                        <item.icon className={`h-5 w-5 ${item.color} shrink-0`} />
                        <span className="font-heading font-semibold">{item.title}</span>
                        <Badge variant="outline" className="ml-auto mr-4 hidden sm:inline-flex">{item.badge}</Badge>
                      </div>
                    </AccordionTrigger>
                    <AccordionContent className="text-muted-foreground leading-relaxed pl-8">
                      {item.content}
                    </AccordionContent>
                  </AccordionItem>
                ))}
              </Accordion>
            </div>
          </section>

          {/* Variante 4: Verschachtelt / Grouped (NEU) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 4 – Gruppiert in Karten (Neu)</h2>
            <p className="text-muted-foreground">Accordion-Gruppen innerhalb von Cards für strukturierte Inhalte.</p>
            <div className="grid md:grid-cols-2 gap-6 max-w-4xl">
              {[
                {
                  group: "Mitgliedschaft",
                  items: [
                    { q: "Wie werde ich Mitglied?", a: "Wenden Sie sich an die örtliche Feuerwehr." },
                    { q: "Gibt es eine Altersgrenze?", a: "Jugendfeuerwehr ab 12, aktiver Dienst ab 18 Jahren." },
                  ],
                },
                {
                  group: "Ausbildung",
                  items: [
                    { q: "Welche Lehrgänge gibt es?", a: "MTA, Gruppenführer, Zugführer, Atemschutz und weitere Speziallehrgänge." },
                    { q: "Wo finden Lehrgänge statt?", a: "An den Standorten der Staatlichen Feuerwehrschulen oder im Landkreis." },
                  ],
                },
              ].map((group) => (
                <Card key={group.group} className="p-6 bg-card border-border">
                  <h3 className="font-heading font-semibold text-lg text-card-foreground mb-4">{group.group}</h3>
                  <Accordion type="single" collapsible>
                    {group.items.map((item, i) => (
                      <AccordionItem key={i} value={`${group.group}-${i}`}>
                        <AccordionTrigger className="text-sm text-card-foreground hover:text-primary">
                          {item.q}
                        </AccordionTrigger>
                        <AccordionContent className="text-muted-foreground text-sm">
                          {item.a}
                        </AccordionContent>
                      </AccordionItem>
                    ))}
                  </Accordion>
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

export default ShowcaseAccordions;
