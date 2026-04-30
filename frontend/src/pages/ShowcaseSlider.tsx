import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious,
} from "@/components/ui/carousel";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";

const slides = [
  {
    title: "Großübung im Gewerbegebiet",
    image: "https://images.unsplash.com/photo-1599059813005-11265ba4b4ce?q=80&w=800&auto=format&fit=crop",
    category: "Übung",
  },
  {
    title: "Neues Löschfahrzeug für FF Marzling",
    image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=800&auto=format&fit=crop",
    category: "Fahrzeuge",
  },
  {
    title: "Jahreshauptversammlung 2025",
    image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800&auto=format&fit=crop",
    category: "Verband",
  },
  {
    title: "Feuerwehrjugend beim Wissenstest",
    image: "https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?q=80&w=800&auto=format&fit=crop",
    category: "Jugend",
  },
];

const ShowcaseSlider = () => {
  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />
      <main className="flex-1">
        <div className="container py-16 space-y-20">
          <div>
            <h1 className="font-heading text-4xl font-bold text-foreground mb-2">Slider / Carousel</h1>
            <p className="text-muted-foreground text-lg">Verschiedene Slider-Entwürfe für Bildergalerien und Content.</p>
          </div>

          {/* Variante 1: Standard mit Bildern (wie News) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 1 – News-Slider</h2>
            <p className="text-muted-foreground">Horizontaler Slider mit Bild-Cards, wie er für Neuigkeiten verwendet wird.</p>
            <div className="px-12">
              <Carousel opts={{ align: "start", loop: true }}>
                <CarouselContent>
                  {slides.map((slide, i) => (
                    <CarouselItem key={i} className="md:basis-1/2 lg:basis-1/3">
                      <Card className="overflow-hidden group cursor-pointer border-border">
                        <div className="aspect-video overflow-hidden">
                          <img
                            src={slide.image}
                            alt={slide.title}
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                          />
                        </div>
                        <CardContent className="p-4">
                          <Badge className="gradient-fire text-primary-foreground mb-2">{slide.category}</Badge>
                          <h3 className="font-heading font-semibold text-card-foreground">{slide.title}</h3>
                        </CardContent>
                      </Card>
                    </CarouselItem>
                  ))}
                </CarouselContent>
                <CarouselPrevious />
                <CarouselNext />
              </Carousel>
            </div>
          </section>

          {/* Variante 2: Hero-Slider fullwidth */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 2 – Hero-Slider (Fullwidth)</h2>
            <p className="text-muted-foreground">Großformatiger Slider für Hero-Bereiche mit Overlay-Text.</p>
            <Carousel opts={{ loop: true }} className="w-full">
              <CarouselContent>
                {slides.map((slide, i) => (
                  <CarouselItem key={i}>
                    <div className="relative aspect-[21/9] rounded-xl overflow-hidden">
                      <img
                        src={slide.image}
                        alt={slide.title}
                        className="w-full h-full object-cover"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-charcoal-dark/80 via-charcoal-dark/20 to-transparent" />
                      <div className="absolute bottom-0 left-0 p-8">
                        <Badge className="gradient-fire text-primary-foreground mb-3">{slide.category}</Badge>
                        <h3 className="font-heading text-3xl font-bold text-white mb-2">{slide.title}</h3>
                        <Button variant="secondary" size="sm">
                          Mehr erfahren <ArrowRight className="ml-2 h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  </CarouselItem>
                ))}
              </CarouselContent>
              <CarouselPrevious className="left-4" />
              <CarouselNext className="right-4" />
            </Carousel>
          </section>

          {/* Variante 3: Testimonials / Zitate */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 3 – Zitate-Slider</h2>
            <p className="text-muted-foreground">Minimalistischer Slider für Zitate und Testimonials.</p>
            <div className="max-w-2xl mx-auto px-12">
              <Carousel opts={{ loop: true }}>
                <CarouselContent>
                  {[
                    { quote: "Die Zusammenarbeit im Landkreis ist vorbildlich und stärkt jeden einzelnen Standort.", author: "Max Müller, KBR" },
                    { quote: "Durch die gemeinsame Ausbildung konnten wir die Einsatzbereitschaft deutlich verbessern.", author: "Anna Schmidt, KBI" },
                    { quote: "Das ehrenamtliche Engagement unserer Feuerwehrleute ist beeindruckend.", author: "Thomas Huber, Landrat" },
                  ].map((item, i) => (
                    <CarouselItem key={i}>
                      <div className="text-center py-12 px-8">
                        <div className="text-6xl text-fire-red mb-4">"</div>
                        <blockquote className="font-heading text-xl md:text-2xl text-foreground italic leading-relaxed mb-6">
                          {item.quote}
                        </blockquote>
                        <p className="text-muted-foreground font-medium">— {item.author}</p>
                      </div>
                    </CarouselItem>
                  ))}
                </CarouselContent>
                <CarouselPrevious />
                <CarouselNext />
              </Carousel>
            </div>
          </section>

          {/* Variante 4: Logo-Band (NEU) */}
          <section className="space-y-6">
            <h2 className="font-heading text-2xl font-semibold text-foreground">Variante 4 – Logo-Band (Neu)</h2>
            <p className="text-muted-foreground">Kompakter Slider für Partner-Logos oder Sponsoren.</p>
            <div className="px-12">
              <Carousel opts={{ align: "start", loop: true }}>
                <CarouselContent className="-ml-2">
                  {Array.from({ length: 8 }).map((_, i) => (
                    <CarouselItem key={i} className="basis-1/3 md:basis-1/5 lg:basis-1/6 pl-2">
                      <div className="bg-muted rounded-lg aspect-[3/2] flex items-center justify-center border border-border hover:border-primary/30 transition-colors">
                        <span className="text-muted-foreground text-sm font-medium">Partner {i + 1}</span>
                      </div>
                    </CarouselItem>
                  ))}
                </CarouselContent>
                <CarouselPrevious />
                <CarouselNext />
              </Carousel>
            </div>
          </section>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default ShowcaseSlider;
