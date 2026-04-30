import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Download, FileText, Calendar, HelpCircle, ExternalLink } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";

const downloads = [
  {
    category: "Ausbildung",
    items: [
      { name: "Lehrgangsanmeldung MTA", format: "PDF", size: "245 KB" },
      { name: "Prüfungsordnung THL", format: "PDF", size: "1.2 MB" },
      { name: "Ausbildungsplan 2025", format: "PDF", size: "890 KB" },
    ],
  },
  {
    category: "Verwaltung",
    items: [
      { name: "Mitgliedsantrag", format: "PDF", size: "156 KB" },
      { name: "Satzung KFV Freising", format: "PDF", size: "2.1 MB" },
      { name: "Beitragsordnung", format: "PDF", size: "98 KB" },
    ],
  },
  {
    category: "Einsatz",
    items: [
      { name: "Alarmierungsübersicht", format: "PDF", size: "445 KB" },
      { name: "Funkrufnamen Landkreis", format: "PDF", size: "312 KB" },
      { name: "Einsatzberichte Vorlage", format: "DOCX", size: "67 KB" },
    ],
  },
];

const forms = [
  {
    title: "Lehrgangsanmeldung",
    description: "Anmeldung zu Lehrgängen an der Staatlichen Feuerwehrschule",
    href: "#",
  },
  {
    title: "Mitgliedsantrag",
    description: "Antrag auf Mitgliedschaft im Kreisfeuerwehrverband",
    href: "#",
  },
  {
    title: "Zuschussantrag",
    description: "Antrag auf Bezuschussung von Ausrüstung und Fahrzeugen",
    href: "#",
  },
  {
    title: "Veranstaltungsanmeldung",
    description: "Anmeldung zu Veranstaltungen des Verbandes",
    href: "#",
  },
];

const events = [
  {
    date: "15. Jan 2025",
    title: "Jahreshauptversammlung",
    location: "Landratsamt Freising",
    type: "Versammlung",
  },
  {
    date: "22. Feb 2025",
    title: "Leistungsprüfung THL",
    location: "FFW Moosburg",
    type: "Prüfung",
  },
  {
    date: "08. Mär 2025",
    title: "Führungskräfte-Fortbildung",
    location: "FFW Freising",
    type: "Ausbildung",
  },
  {
    date: "15. Apr 2025",
    title: "Kreisfeuerwehrtag",
    location: "Verschiedene Standorte",
    type: "Veranstaltung",
  },
  {
    date: "10. Mai 2025",
    title: "Jugendleistungsprüfung",
    location: "FFW Neufahrn",
    type: "Prüfung",
  },
];

const faqs = [
  {
    question: "Wie kann ich der Feuerwehr beitreten?",
    answer:
      "Jeder ab 12 Jahren kann Mitglied werden. Wende dich an deine örtliche Feuerwehr oder nutze unser Kontaktformular. Die Jugendfeuerwehr nimmt Mitglieder ab 12 Jahren auf, der aktive Dienst beginnt mit 18 Jahren.",
  },
  {
    question: "Welche Ausbildung erhalte ich?",
    answer:
      "Die Grundausbildung (Modulare Truppausbildung) dauert ca. 100 Stunden und umfasst Theorie und Praxis. Danach gibt es zahlreiche Weiterbildungsmöglichkeiten wie Atemschutz, Maschinisten-Ausbildung oder Führungslehrgänge.",
  },
  {
    question: "Muss ich für die Ausrüstung bezahlen?",
    answer:
      "Nein, die persönliche Schutzausrüstung wird von der Gemeinde gestellt. Dazu gehören Einsatzkleidung, Helm, Handschuhe und Stiefel.",
  },
  {
    question: "Wie oft finden Übungen statt?",
    answer:
      "In der Regel übt jede Feuerwehr ein- bis zweimal im Monat. Die genauen Termine werden von der jeweiligen Feuerwehr festgelegt.",
  },
  {
    question: "Kann ich Feuerwehr und Beruf vereinbaren?",
    answer:
      "Ja! Die Freiwillige Feuerwehr ist ehrenamtlich. Arbeitgeber sind gesetzlich verpflichtet, Feuerwehrleute für Einsätze freizustellen. Die meisten Übungen finden abends oder am Wochenende statt.",
  },
  {
    question: "Was ist der Kreisfeuerwehrverband?",
    answer:
      "Der KFV ist der Zusammenschluss aller Feuerwehren im Landkreis Freising. Wir koordinieren überörtliche Ausbildung, vertreten gemeinsame Interessen und fördern die Zusammenarbeit der Wehren.",
  },
];

const Service = () => {
  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />
      
      <main className="flex-1">
        {/* Hero Section */}
        <section className="bg-primary py-16 md:py-24">
          <div className="container text-center">
            <h1 className="font-heading text-4xl md:text-5xl font-bold text-primary-foreground mb-4">
              Service & Downloads
            </h1>
            <p className="text-lg text-primary-foreground/80 max-w-2xl mx-auto">
              Hier finden Sie alle wichtigen Dokumente, Formulare und Informationen 
              rund um die Feuerwehren im Landkreis Freising.
            </p>
          </div>
        </section>

        {/* Downloads Section */}
        <section id="downloads" className="py-16 md:py-24">
          <div className="container">
            <div className="flex items-center gap-3 mb-8">
              <div className="p-3 rounded-lg bg-fire-red/10">
                <Download className="h-6 w-6 text-fire-red" />
              </div>
              <h2 className="font-heading text-3xl font-bold text-foreground">Downloads</h2>
            </div>
            
            <div className="grid md:grid-cols-3 gap-8">
              {downloads.map((category) => (
                <div key={category.category} className="bg-card rounded-xl border border-border p-6">
                  <h3 className="font-heading text-xl font-semibold text-card-foreground mb-4">
                    {category.category}
                  </h3>
                  <ul className="space-y-3">
                    {category.items.map((item) => (
                      <li key={item.name}>
                        <a
                          href="#"
                          className="flex items-center justify-between p-3 rounded-lg bg-muted hover:bg-fire-red transition-colors group"
                        >
                          <div className="flex items-center gap-3">
                            <FileText className="h-5 w-5 text-muted-foreground group-hover:text-white transition-colors" />
                            <span className="text-sm font-medium text-foreground group-hover:text-white transition-colors">{item.name}</span>
                          </div>
                          <span className="text-xs text-muted-foreground group-hover:text-white/80 transition-colors">
                            {item.format} · {item.size}
                          </span>
                        </a>
                      </li>
                    ))}
                  </ul>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Forms Section */}
        <section id="formulare" className="py-16 md:py-24 bg-muted">
          <div className="container">
            <div className="flex items-center gap-3 mb-8">
              <div className="p-3 rounded-lg bg-fire-red/10">
                <FileText className="h-6 w-6 text-fire-red" />
              </div>
              <h2 className="font-heading text-3xl font-bold text-foreground">Formulare</h2>
            </div>
            
            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {forms.map((form) => (
                <a
                  key={form.title}
                  href={form.href}
                  className="bg-card rounded-xl border border-border p-6 hover:border-fire-red hover:shadow-lg transition-all group"
                >
                  <h3 className="font-heading text-lg font-semibold text-card-foreground mb-2 group-hover:text-fire-red transition-colors">
                    {form.title}
                  </h3>
                  <p className="text-sm text-muted-foreground mb-4">{form.description}</p>
                  <div className="flex items-center gap-2 text-fire-red text-sm font-medium">
                    <span>Zum Formular</span>
                    <ExternalLink className="h-4 w-4" />
                  </div>
                </a>
              ))}
            </div>
          </div>
        </section>

        {/* Events Section */}
        <section id="termine" className="py-16 md:py-24">
          <div className="container">
            <div className="flex items-center gap-3 mb-8">
              <div className="p-3 rounded-lg bg-fire-red/10">
                <Calendar className="h-6 w-6 text-fire-red" />
              </div>
              <h2 className="font-heading text-3xl font-bold text-foreground">Termine</h2>
            </div>
            
            <div className="bg-card rounded-xl border border-border overflow-hidden">
              <div className="divide-y divide-border">
                {events.map((event, index) => (
                  <div
                    key={index}
                    className="flex flex-col sm:flex-row sm:items-center gap-4 p-6 hover:bg-muted/50 transition-colors"
                  >
                    <div className="sm:w-32 flex-shrink-0">
                      <span className="inline-block px-3 py-1 rounded-full bg-fire-red/10 text-fire-red text-sm font-medium">
                        {event.date}
                      </span>
                    </div>
                    <div className="flex-1">
                      <h3 className="font-heading font-semibold text-card-foreground">
                        {event.title}
                      </h3>
                      <p className="text-sm text-muted-foreground">{event.location}</p>
                    </div>
                    <span className="text-xs px-2 py-1 rounded bg-secondary text-secondary-foreground">
                      {event.type}
                    </span>
                  </div>
                ))}
              </div>
            </div>
            
            <div className="mt-6 text-center">
              <Button variant="outline" className="border-fire-red text-fire-red hover:bg-fire-red hover:text-white">
                Alle Termine anzeigen
              </Button>
            </div>
          </div>
        </section>

        {/* FAQ Section */}
        <section id="faq" className="py-16 md:py-24 bg-muted">
          <div className="container max-w-3xl">
            <div className="flex items-center gap-3 mb-8 justify-center">
              <div className="p-3 rounded-lg bg-fire-red/10">
                <HelpCircle className="h-6 w-6 text-fire-red" />
              </div>
              <h2 className="font-heading text-3xl font-bold text-foreground">Häufige Fragen</h2>
            </div>
            
            <Accordion type="single" collapsible className="space-y-4">
              {faqs.map((faq, index) => (
                <AccordionItem
                  key={index}
                  value={`item-${index}`}
                  className="bg-card rounded-xl border border-border px-6"
                >
                  <AccordionTrigger className="text-left font-heading font-semibold text-card-foreground hover:text-fire-red">
                    {faq.question}
                  </AccordionTrigger>
                  <AccordionContent className="text-muted-foreground leading-relaxed">
                    {faq.answer}
                  </AccordionContent>
                </AccordionItem>
              ))}
            </Accordion>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default Service;
