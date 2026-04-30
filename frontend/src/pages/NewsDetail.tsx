import { useParams, Link } from "react-router-dom";
import { ArrowLeft, Calendar, User, Share2, Facebook, Twitter, Image } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { ImageGallery } from "@/components/ImageGallery";

// Mock news data - in production this would come from an API/database
const newsData = {
  "1": {
    id: 1,
    title: "Großübung im Gewerbegebiet Freising-Süd",
    excerpt: "Am vergangenen Samstag fand eine landkreisweite Großübung mit über 150 Einsatzkräften statt...",
    content: `
      <p>Am vergangenen Samstag fand im Gewerbegebiet Freising-Süd eine landkreisweite Großübung statt, an der über 150 Einsatzkräfte aus 12 verschiedenen Feuerwehren teilnahmen. Das Übungsszenario simulierte einen Großbrand in einer Lagerhalle mit mehreren vermissten Personen.</p>
      
      <h3>Übungsszenario</h3>
      <p>Das Szenario ging von einem Brand in einer Lagerhalle für Chemikalien aus. Erschwerend kam hinzu, dass sich zum Zeitpunkt des Brandausbruchs noch mehrere Mitarbeiter in der Halle aufhielten und als vermisst galten. Die Einsatzkräfte mussten unter Atemschutz vorgehen und gleichzeitig eine mögliche Ausbreitung gefährlicher Stoffe berücksichtigen.</p>
      
      <h3>Beteiligte Einheiten</h3>
      <p>Neben den Freiwilligen Feuerwehren aus Freising, Marzling, Hallbergmoos und weiteren Gemeinden waren auch der Rettungsdienst sowie die Polizei an der Übung beteiligt. Die Zusammenarbeit zwischen den verschiedenen Organisationen stand im Mittelpunkt der Übung.</p>
      
      <h3>Fazit</h3>
      <p>Kreisbrandrat Johann Müller zeigte sich zufrieden mit dem Verlauf der Übung: "Die Zusammenarbeit zwischen den Wehren funktionierte hervorragend. Wir konnten wichtige Erkenntnisse gewinnen, die uns bei zukünftigen Einsätzen helfen werden."</p>
    `,
    date: "10. Dezember 2025",
    author: "Pressestelle KFV",
    category: "Übung",
    image: "https://images.unsplash.com/photo-1599059813005-11265ba4b4ce?q=80&w=1600&auto=format&fit=crop",
    gallery: [
      { src: "https://images.unsplash.com/photo-1599059813005-11265ba4b4ce?q=80&w=800&auto=format&fit=crop", alt: "Großübung Übersicht", caption: "Übersicht des Übungsgeländes" },
      { src: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?q=80&w=800&auto=format&fit=crop", alt: "Einsatzkräfte im Einsatz", caption: "Einsatzkräfte bei der Brandbekämpfung" },
      { src: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=800&auto=format&fit=crop", alt: "Löschfahrzeug", caption: "HLF 20 im Einsatz" },
      { src: "https://images.unsplash.com/photo-1580983561371-7f4b242d8ec0?q=80&w=800&auto=format&fit=crop", alt: "Atemschutzträger", caption: "Atemschutzgeräteträger bei der Personensuche" },
      { src: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800&auto=format&fit=crop", alt: "Besprechung", caption: "Einsatzleitung bei der Lagebesprechung" },
      { src: "https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?q=80&w=800&auto=format&fit=crop", alt: "Teamwork", caption: "Zusammenarbeit der Einsatzkräfte" },
    ],
    relatedNews: ["2", "3"],
  },
  "2": {
    id: 2,
    title: "Neues Löschfahrzeug für die FF Marzling",
    excerpt: "Die Freiwillige Feuerwehr Marzling konnte ihr neues HLF 20 in Empfang nehmen...",
    content: `
      <p>Die Freiwillige Feuerwehr Marzling konnte am vergangenen Wochenende ihr neues Hilfeleistungslöschgruppenfahrzeug (HLF 20) offiziell in Empfang nehmen. Das moderne Einsatzfahrzeug ersetzt das bisherige LF 16/12, das nach über 25 Jahren treuer Dienste ausgemustert wurde.</p>
      
      <h3>Technische Ausstattung</h3>
      <p>Das neue HLF 20 verfügt über einen 2.000 Liter Wassertank, eine leistungsstarke Feuerlöschkreiselpumpe und umfangreiche technische Ausrüstung für Hilfeleistungseinsätze. Besonders hervorzuheben ist die moderne LED-Beleuchtung sowie die verbesserte Ergonomie für die Einsatzkräfte.</p>
      
      <h3>Festakt mit Fahrzeugweihe</h3>
      <p>Im Rahmen eines feierlichen Festakts wurde das Fahrzeug von Pfarrer Thomas Weber geweiht. Anschließend übergab Bürgermeisterin Maria Huber symbolisch den Schlüssel an Kommandant Stefan Bauer.</p>
    `,
    date: "8. Dezember 2025",
    author: "FF Marzling",
    category: "Fahrzeuge",
    image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=1600&auto=format&fit=crop",
    gallery: [
      { src: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=800&auto=format&fit=crop", alt: "HLF 20 Frontansicht", caption: "Das neue HLF 20 der FF Marzling" },
      { src: "https://images.unsplash.com/photo-1580983561371-7f4b242d8ec0?q=80&w=800&auto=format&fit=crop", alt: "Innenausstattung", caption: "Moderne Innenausstattung" },
      { src: "https://images.unsplash.com/photo-1599059813005-11265ba4b4ce?q=80&w=800&auto=format&fit=crop", alt: "Fahrzeugweihe", caption: "Feierliche Fahrzeugweihe" },
    ],
    relatedNews: ["1", "3"],
  },
  "3": {
    id: 3,
    title: "Jahreshauptversammlung 2025 – Rückblick und Ausblick",
    excerpt: "Bei der diesjährigen Jahreshauptversammlung wurden die Weichen für die Zukunft gestellt...",
    content: `
      <p>Bei der diesjährigen Jahreshauptversammlung des Kreisfeuerwehrverbands Freising wurden die Weichen für die Zukunft gestellt. Im Mittelpunkt standen der Rückblick auf ein ereignisreiches Jahr sowie die Planung wichtiger Projekte für 2026.</p>
      
      <h3>Einsatzstatistik 2025</h3>
      <p>Die Feuerwehren im Landkreis Freising wurden im Jahr 2025 zu insgesamt 2.847 Einsätzen alarmiert. Dies entspricht einer Steigerung von 8% gegenüber dem Vorjahr. Besonders die technischen Hilfeleistungen haben zugenommen.</p>
      
      <h3>Ausblick 2026</h3>
      <p>Für das kommende Jahr sind mehrere wichtige Projekte geplant, darunter die Erweiterung der digitalen Alarmierung sowie die Einführung eines neuen Ausbildungskonzepts für Atemschutzgeräteträger.</p>
    `,
    date: "5. Dezember 2025",
    author: "Vorstand KFV",
    category: "Verband",
    image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=1600&auto=format&fit=crop",
    gallery: [
      { src: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800&auto=format&fit=crop", alt: "Versammlung", caption: "Die Jahreshauptversammlung im Landratsamt" },
      { src: "https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?q=80&w=800&auto=format&fit=crop", alt: "Ehrungen", caption: "Ehrungen verdienter Kameraden" },
    ],
    relatedNews: ["1", "2"],
  },
};

const NewsDetail = () => {
  const { id } = useParams<{ id: string }>();
  const article = id ? newsData[id as keyof typeof newsData] : null;

  if (!article) {
    return (
      <div className="min-h-screen bg-background">
        <Header />
        <main className="container py-16 text-center">
          <h1 className="font-heading text-3xl font-bold mb-4">Artikel nicht gefunden</h1>
          <p className="text-muted-foreground mb-8">Der gewünschte Artikel existiert nicht.</p>
          <Link to="/">
            <Button>Zurück zur Startseite</Button>
          </Link>
        </main>
        <Footer />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main>
        {/* Hero Image */}
        <div className="relative h-[300px] md:h-[450px] overflow-hidden">
          <img
            src={article.image}
            alt={article.title}
            className="w-full h-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-background via-background/50 to-transparent" />
        </div>

        <article className="container max-w-4xl -mt-32 relative z-10 pb-16">
          {/* Back Link */}
          <Link 
            to="/#aktuelles" 
            className="inline-flex items-center gap-2 text-muted-foreground hover:text-accent transition-colors mb-6"
          >
            <ArrowLeft className="h-4 w-4" />
            Zurück zu Aktuelles
          </Link>

          {/* Article Card */}
          <div className="bg-card rounded-xl shadow-xl p-6 md:p-10">
            {/* Meta */}
            <div className="flex flex-wrap items-center gap-4 mb-6">
              <Badge className="gradient-fire text-primary-foreground">
                {article.category}
              </Badge>
              <span className="flex items-center gap-2 text-sm text-muted-foreground">
                <Calendar className="h-4 w-4" />
                {article.date}
              </span>
              <span className="flex items-center gap-2 text-sm text-muted-foreground">
                <User className="h-4 w-4" />
                {article.author}
              </span>
            </div>

            {/* Title */}
            <h1 className="font-heading text-3xl md:text-4xl lg:text-5xl font-bold text-card-foreground mb-6 leading-tight">
              {article.title}
            </h1>

            {/* Share Buttons */}
            <div className="flex items-center gap-3 pb-6 mb-8 border-b border-border">
              <span className="text-sm text-muted-foreground flex items-center gap-2">
                <Share2 className="h-4 w-4" />
                Teilen:
              </span>
              <button className="p-2 rounded-lg bg-muted hover:bg-accent hover:text-accent-foreground transition-colors">
                <Facebook className="h-4 w-4" />
              </button>
              <button className="p-2 rounded-lg bg-muted hover:bg-accent hover:text-accent-foreground transition-colors">
                <Twitter className="h-4 w-4" />
              </button>
            </div>

            {/* Content */}
            <div 
              className="prose prose-lg max-w-none 
                prose-headings:font-heading prose-headings:text-card-foreground
                prose-h3:text-xl prose-h3:mt-8 prose-h3:mb-4
                prose-p:text-muted-foreground prose-p:leading-relaxed prose-p:mb-4
                prose-a:text-accent prose-a:no-underline hover:prose-a:underline"
              dangerouslySetInnerHTML={{ __html: article.content }}
            />

            {/* Image Gallery */}
            {article.gallery && article.gallery.length > 0 && (
              <div className="mt-12 pt-8 border-t border-border">
                <div className="flex items-center gap-3 mb-6">
                  <Image className="h-5 w-5 text-accent" />
                  <h3 className="font-heading text-xl font-bold text-card-foreground">
                    Bildergalerie
                  </h3>
                </div>
                <ImageGallery images={article.gallery} />
              </div>
            )}

            {/* Related News */}
            {article.relatedNews && article.relatedNews.length > 0 && (
              <div className="mt-12 pt-8 border-t border-border">
                <h3 className="font-heading text-xl font-bold text-card-foreground mb-6">
                  Weitere Nachrichten
                </h3>
                <div className="grid sm:grid-cols-2 gap-4">
                  {article.relatedNews.map((relatedId) => {
                    const related = newsData[relatedId as keyof typeof newsData];
                    if (!related) return null;
                    return (
                      <Link
                        key={relatedId}
                        to={`/news/${relatedId}`}
                        className="group flex gap-4 p-4 rounded-lg bg-muted hover:bg-muted/70 transition-colors"
                      >
                        <img
                          src={related.image}
                          alt={related.title}
                          className="w-20 h-20 rounded-lg object-cover flex-shrink-0"
                        />
                        <div>
                          <Badge variant="outline" className="mb-2 text-xs">
                            {related.category}
                          </Badge>
                          <h4 className="font-heading font-medium text-card-foreground group-hover:text-accent transition-colors line-clamp-2">
                            {related.title}
                          </h4>
                        </div>
                      </Link>
                    );
                  })}
                </div>
              </div>
            )}
          </div>
        </article>
      </main>
      <Footer />
    </div>
  );
};

export default NewsDetail;