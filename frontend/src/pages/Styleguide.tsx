import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination";
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
  BreadcrumbEllipsis,
} from "@/components/ui/breadcrumb";
import { Link } from "react-router-dom";

const Styleguide = () => {
  const brandColors = [
    { name: "Fire Red", variable: "--fire-red", class: "bg-fire-red", textClass: "text-white" },
    { name: "Fire Red Light", variable: "--fire-red-light", class: "bg-fire-red-light", textClass: "text-white" },
    { name: "Fire Red Dark", variable: "--fire-red-dark", class: "bg-fire-red-dark", textClass: "text-white" },
    { name: "Fire Orange", variable: "--fire-orange", class: "bg-fire-orange", textClass: "text-white" },
    { name: "Fire Yellow", variable: "--fire-yellow", class: "bg-fire-yellow", textClass: "text-foreground" },
  ];

  const grayScale = [
    { name: "White", variable: "--white", class: "bg-white", textClass: "text-foreground", note: "Reines Weiß / Hintergrund" },
    { name: "Gray 100", variable: "--gray-100", class: "bg-gray-100", textClass: "text-foreground", note: "Surface — leichte Fläche (cream)" },
    { name: "Gray 200", variable: "--gray-200", class: "bg-gray-200", textClass: "text-foreground", note: "Muted Surface — alternative Fläche (warm-gray)" },
    { name: "Gray 300", variable: "--gray-300", class: "bg-gray-300", textClass: "text-foreground", note: "Border / Divider / Disabled" },
    { name: "Gray 500", variable: "--gray-500", class: "bg-gray-500", textClass: "text-white", note: "Muted Text — sekundärer Text" },
    { name: "Gray 900", variable: "--gray-900", class: "bg-gray-900", textClass: "text-white", note: "Foreground — Primärtext / Charcoal" },
    { name: "Black", variable: "--black", class: "bg-black", textClass: "text-white", note: "Reines Schwarz" },
  ];

  const semanticColors = [
    { name: "Background", variable: "--background", class: "bg-background", textClass: "text-foreground" },
    { name: "Foreground", variable: "--foreground", class: "bg-foreground", textClass: "text-background" },
    { name: "Primary", variable: "--primary", class: "bg-primary", textClass: "text-primary-foreground" },
    { name: "Secondary", variable: "--secondary", class: "bg-secondary", textClass: "text-secondary-foreground" },
    { name: "Muted", variable: "--muted", class: "bg-muted", textClass: "text-muted-foreground" },
    { name: "Accent", variable: "--accent", class: "bg-accent", textClass: "text-accent-foreground" },
    { name: "Destructive", variable: "--destructive", class: "bg-destructive", textClass: "text-destructive-foreground" },
    { name: "Card", variable: "--card", class: "bg-card", textClass: "text-card-foreground" },
    { name: "Border", variable: "--border", class: "bg-border", textClass: "text-foreground" },
  ];

  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main className="container mx-auto px-4 py-12">
        {/* Page Title */}
        <div className="mb-12">
          <h1 className="text-4xl md:text-5xl font-heading font-bold text-foreground mb-4">
            Corporate Identity Styleguide
          </h1>
          <p className="text-lg text-muted-foreground max-w-2xl">
            Übersicht aller Design-Elemente des Kreisfeuerwehrverbands Freising. 
            Dieses Dokument dient als Referenz für die konsistente Gestaltung aller Inhalte.
          </p>
        </div>

        {/* Typography Section */}
        <section className="mb-16">
          <h2 className="text-3xl font-heading font-bold text-foreground mb-6 flex items-center gap-3">
            <span className="w-1 h-8 bg-accent rounded-full"></span>
            Typografie
          </h2>
          
          <Card className="mb-8">
            <CardHeader>
              <CardTitle>Überschriften (Headings)</CardTitle>
              <CardDescription>
                Schriftart: <strong>Oswald</strong> (font-heading) — Verfügbare Weights: 400, 500, 600, 700
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">H1 — text-4xl md:text-5xl · font-bold (700)</p>
                <h1 className="text-4xl md:text-5xl font-heading font-bold">Kreisfeuerwehrverband Freising</h1>
              </div>
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">H2 — text-3xl · font-bold (700)</p>
                <h2 className="text-3xl font-heading font-bold">Aktuelles & Neuigkeiten</h2>
              </div>
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">H3 — text-2xl · font-semibold (600)</p>
                <h3 className="text-2xl font-heading font-semibold">Unsere Feuerwehren</h3>
              </div>
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">H4 — text-xl · font-semibold (600)</p>
                <h4 className="text-xl font-heading font-semibold">Kommandantenversammlung</h4>
              </div>
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">H5 — text-lg · font-medium (500)</p>
                <h5 className="text-lg font-heading font-medium">Termine & Veranstaltungen</h5>
              </div>
              <div>
                <p className="text-sm text-muted-foreground mb-2">H6 — text-base · font-medium (500)</p>
                <h6 className="text-base font-heading font-medium">Kontaktinformationen</h6>
              </div>
            </CardContent>
          </Card>

          <Card className="mb-8">
            <CardHeader>
              <CardTitle>Oswald Font Weights</CardTitle>
              <CardDescription>
                Alle verfügbaren Schriftstärken für Headlines (keine Italic-Variante verfügbar)
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-normal (400)</p>
                  <p className="font-heading font-normal text-2xl">Feuerwehr Freising</p>
                </div>
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-medium (500)</p>
                  <p className="font-heading font-medium text-2xl">Feuerwehr Freising</p>
                </div>
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-semibold (600)</p>
                  <p className="font-heading font-semibold text-2xl">Feuerwehr Freising</p>
                </div>
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-bold (700)</p>
                  <p className="font-heading font-bold text-2xl">Feuerwehr Freising</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card className="mb-8">
            <CardHeader>
              <CardTitle>Fließtext & Absätze</CardTitle>
              <CardDescription>
                Schriftart: <strong>Roboto</strong> (font-body) — Verfügbare Weights: 300, 400, 500, 700 + Italic
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">Lead / Intro — text-lg · font-normal (400)</p>
                <p className="text-lg text-muted-foreground font-body">
                  Der Kreisfeuerwehrverband Freising vertritt die Interessen von über 4.500 aktiven 
                  Feuerwehrdienstleistenden in 96 Feuerwehren des Landkreises.
                </p>
              </div>
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">Body — text-base · font-normal (400)</p>
                <p className="text-base text-foreground font-body">
                  Die Feuerwehren im Landkreis Freising sind rund um die Uhr einsatzbereit, um Menschen 
                  in Not zu helfen. Mit moderner Ausrüstung und bestens ausgebildeten Kräften bewältigen 
                  wir jährlich über 3.000 Einsätze. Unser Engagement für die Sicherheit der Bürgerinnen 
                  und Bürger steht dabei immer im Mittelpunkt.
                </p>
              </div>
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">Small — text-sm · font-normal (400)</p>
                <p className="text-sm text-muted-foreground font-body">
                  Hinweis: Die Einsatzstatistiken werden regelmäßig aktualisiert. Stand: Januar 2025.
                </p>
              </div>
              <div>
                <p className="text-sm text-muted-foreground mb-2">Extra Small / Caption — text-xs · font-normal (400)</p>
                <p className="text-xs text-muted-foreground font-body">
                  © 2025 Kreisfeuerwehrverband Freising e.V. Alle Rechte vorbehalten.
                </p>
              </div>
            </CardContent>
          </Card>

          <Card className="mb-8">
            <CardHeader>
              <CardTitle>Roboto Font Weights</CardTitle>
              <CardDescription>
                Alle verfügbaren Schriftstärken für Fließtext (inklusive Italic)
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-light (300)</p>
                  <p className="font-body font-light text-lg">Brandschutz und Hilfeleistung</p>
                </div>
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-light italic (300)</p>
                  <p className="font-body font-light italic text-lg">Brandschutz und Hilfeleistung</p>
                </div>
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-normal (400)</p>
                  <p className="font-body font-normal text-lg">Brandschutz und Hilfeleistung</p>
                </div>
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-normal italic (400)</p>
                  <p className="font-body font-normal italic text-lg">Brandschutz und Hilfeleistung</p>
                </div>
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-medium (500)</p>
                  <p className="font-body font-medium text-lg">Brandschutz und Hilfeleistung</p>
                </div>
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-medium italic (500)</p>
                  <p className="font-body font-medium italic text-lg">Brandschutz und Hilfeleistung</p>
                </div>
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-bold (700)</p>
                  <p className="font-body font-bold text-lg">Brandschutz und Hilfeleistung</p>
                </div>
                <div className="border border-border rounded-lg p-4">
                  <p className="text-xs text-muted-foreground mb-1">font-bold italic (700)</p>
                  <p className="font-body font-bold italic text-lg">Brandschutz und Hilfeleistung</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Textauszeichnungen</CardTitle>
              <CardDescription>
                Kombinationen von Gewicht und Stil für verschiedene Anwendungsfälle
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">Hervorhebung im Fließtext</p>
                <p className="font-body text-base">
                  Die Feuerwehr Freising ist <strong className="font-bold">rund um die Uhr</strong> einsatzbereit. 
                  Unsere <em className="italic">ehrenamtlichen</em> Kräfte sind bestens ausgebildet. 
                  Bei Fragen wenden Sie sich an <span className="font-medium">Kreisbrandrat Johann Eitzenberger</span>.
                </p>
              </div>
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">Zitat / Blockquote</p>
                <blockquote className="font-body italic text-lg border-l-4 border-accent pl-4 text-muted-foreground">
                  „Helfen ist unsere Leidenschaft – seit über 150 Jahren im Dienst der Bürger."
                </blockquote>
              </div>
              <div>
                <p className="text-sm text-muted-foreground mb-2">Bild-Unterschrift / Caption</p>
                <p className="font-body text-sm italic text-muted-foreground">
                  Foto: Jahresübung der Freiwilligen Feuerwehr Freising, September 2024
                </p>
              </div>
            </CardContent>
          </Card>

          <Card className="mt-8">
            <CardHeader>
              <CardTitle>Links</CardTitle>
              <CardDescription>
                Verschiedene Link-Stile für Text und Navigation
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">Standard Link — text-accent mit hover:underline</p>
                <p className="text-base">
                  Besuchen Sie unsere{" "}
                  <a href="#" className="text-accent hover:underline font-medium">
                    Feuerwehr-Übersicht
                  </a>{" "}
                  für mehr Informationen.
                </p>
              </div>
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">Link mit Unterstreichung — underline hover:text-accent</p>
                <p className="text-base">
                  Weitere Informationen finden Sie auf der{" "}
                  <a href="#" className="underline hover:text-accent transition-colors">
                    Kontaktseite
                  </a>.
                </p>
              </div>
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">Dezenter Link — text-muted-foreground hover:text-foreground</p>
                <p className="text-base text-muted-foreground">
                  Stand: Januar 2025 ·{" "}
                  <a href="#" className="text-muted-foreground hover:text-foreground underline-offset-4 hover:underline transition-colors">
                    Impressum
                  </a>{" "}
                  ·{" "}
                  <a href="#" className="text-muted-foreground hover:text-foreground underline-offset-4 hover:underline transition-colors">
                    Datenschutz
                  </a>
                </p>
              </div>
            </CardContent>
          </Card>
        </section>

        <Separator className="my-12" />

        {/* Navigation Section */}
        <section className="mb-16">
          <h2 className="text-3xl font-heading font-bold text-foreground mb-6 flex items-center gap-3">
            <span className="w-1 h-8 bg-accent rounded-full"></span>
            Navigation
          </h2>

          <Card className="mb-8">
            <CardHeader>
              <CardTitle>Navigation Links</CardTitle>
              <CardDescription>
                Link-Stile für Hauptnavigation und Menüs
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="border-b border-border pb-4">
                <p className="text-sm text-muted-foreground mb-2">Navigation Link — font-medium hover:text-accent</p>
                <nav className="flex gap-6">
                  <a href="#" className="font-medium text-foreground hover:text-accent transition-colors">Startseite</a>
                  <a href="#" className="font-medium text-foreground hover:text-accent transition-colors">Aktuelles</a>
                  <a href="#" className="font-medium text-foreground hover:text-accent transition-colors">Verband</a>
                  <a href="#" className="font-medium text-foreground hover:text-accent transition-colors">Kontakt</a>
                </nav>
              </div>
              <div>
                <p className="text-sm text-muted-foreground mb-2">Aktiver Link — text-accent font-semibold (für aktuelle Seite)</p>
                <nav className="flex gap-6">
                  <a href="#" className="font-medium text-muted-foreground hover:text-accent transition-colors">Startseite</a>
                  <a href="#" className="font-semibold text-accent">Styleguide</a>
                  <a href="#" className="font-medium text-muted-foreground hover:text-accent transition-colors">Verband</a>
                </nav>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Pagination</CardTitle>
              <CardDescription>
                Seitennavigation für Listen und Übersichten
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-8">
              <div className="border-b border-border pb-6">
                <p className="text-sm text-muted-foreground mb-4">Standard Pagination</p>
                <Pagination>
                  <PaginationContent>
                    <PaginationItem>
                      <PaginationPrevious href="#" />
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink href="#">1</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink href="#" isActive>2</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink href="#">3</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationEllipsis />
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink href="#">10</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationNext href="#" />
                    </PaginationItem>
                  </PaginationContent>
                </Pagination>
              </div>
              <div className="border-b border-border pb-6">
                <p className="text-sm text-muted-foreground mb-4">Kompakte Pagination (nur Pfeile)</p>
                <Pagination>
                  <PaginationContent>
                    <PaginationItem>
                      <PaginationPrevious href="#" />
                    </PaginationItem>
                    <PaginationItem>
                      <span className="px-4 text-sm text-muted-foreground">Seite 2 von 10</span>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationNext href="#" />
                    </PaginationItem>
                  </PaginationContent>
                </Pagination>
              </div>
              <div>
                <p className="text-sm text-muted-foreground mb-4">Pagination mit vielen Seiten</p>
                <Pagination>
                  <PaginationContent>
                    <PaginationItem>
                      <PaginationPrevious href="#" />
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink href="#">1</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationEllipsis />
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink href="#">4</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink href="#" isActive>5</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink href="#">6</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationEllipsis />
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationLink href="#">20</PaginationLink>
                    </PaginationItem>
                    <PaginationItem>
                      <PaginationNext href="#" />
                    </PaginationItem>
                  </PaginationContent>
                </Pagination>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Breadcrumb Navigation</CardTitle>
              <CardDescription>
                Pfadnavigation zur Orientierung auf verschachtelten Seiten
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-8">
              <div className="border-b border-border pb-6">
                <p className="text-sm text-muted-foreground mb-4">Standard Breadcrumb</p>
                <Breadcrumb>
                  <BreadcrumbList>
                    <BreadcrumbItem>
                      <BreadcrumbLink asChild>
                        <Link to="/">Startseite</Link>
                      </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                      <BreadcrumbLink asChild>
                        <Link to="/verband">Verband</Link>
                      </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                      <BreadcrumbPage>Styleguide</BreadcrumbPage>
                    </BreadcrumbItem>
                  </BreadcrumbList>
                </Breadcrumb>
              </div>
              <div className="border-b border-border pb-6">
                <p className="text-sm text-muted-foreground mb-4">Breadcrumb mit Ellipsis (für tiefe Hierarchien)</p>
                <Breadcrumb>
                  <BreadcrumbList>
                    <BreadcrumbItem>
                      <BreadcrumbLink asChild>
                        <Link to="/">Startseite</Link>
                      </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                      <BreadcrumbEllipsis />
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                      <BreadcrumbLink asChild>
                        <Link to="/feuerwehren">Feuerwehren</Link>
                      </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                      <BreadcrumbPage>FF Freising</BreadcrumbPage>
                    </BreadcrumbItem>
                  </BreadcrumbList>
                </Breadcrumb>
              </div>
              <div className="border-b border-border pb-6">
                <p className="text-sm text-muted-foreground mb-4">Einfache Breadcrumb (2 Ebenen)</p>
                <Breadcrumb>
                  <BreadcrumbList>
                    <BreadcrumbItem>
                      <BreadcrumbLink asChild>
                        <Link to="/">Startseite</Link>
                      </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                      <BreadcrumbPage>Aktuelles</BreadcrumbPage>
                    </BreadcrumbItem>
                  </BreadcrumbList>
                </Breadcrumb>
              </div>
              <div>
                <p className="text-sm text-muted-foreground mb-4">Lange Breadcrumb (Detailseite)</p>
                <Breadcrumb>
                  <BreadcrumbList>
                    <BreadcrumbItem>
                      <BreadcrumbLink asChild>
                        <Link to="/">Startseite</Link>
                      </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                      <BreadcrumbLink asChild>
                        <Link to="/aktuelles">Aktuelles</Link>
                      </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                      <BreadcrumbLink asChild>
                        <Link to="/aktuelles">2025</Link>
                      </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                      <BreadcrumbPage>Jahreshauptversammlung</BreadcrumbPage>
                    </BreadcrumbItem>
                  </BreadcrumbList>
                </Breadcrumb>
              </div>
            </CardContent>
          </Card>
        </section>

        <Separator className="my-12" />

        {/* Colors Section */}
        <section className="mb-16">
          <h2 className="text-3xl font-heading font-bold text-foreground mb-6 flex items-center gap-3">
            <span className="w-1 h-8 bg-accent rounded-full"></span>
            Farbpalette
          </h2>

          <Card className="mb-8">
            <CardHeader>
              <CardTitle>Markenfarben</CardTitle>
              <CardDescription>
                Die Primärfarben des Kreisfeuerwehrverbands Freising
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                {brandColors.map((color) => (
                  <div key={color.name} className="space-y-2">
                    <div 
                      className={`${color.class} ${color.textClass} h-24 rounded-lg flex items-end p-3 shadow-md`}
                    >
                      <span className="text-sm font-medium">{color.name}</span>
                    </div>
                    <p className="text-xs text-muted-foreground font-mono">{color.variable}</p>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          <Card className="mb-8">
            <CardHeader>
              <CardTitle>Graustufen-Skala</CardTitle>
              <CardDescription>
                Reduzierte 5-Stufen-Skala für alle neutralen Töne: <strong>100</strong> (Surface),
                <strong> 200</strong> (Muted Surface), <strong>300</strong> (Border/Disabled),
                <strong> 500</strong> (Muted Text), <strong>900</strong> (Foreground).
                Aliase (cream, warm-gray, charcoal*) und semantische Tokens referenzieren diese Werte.
                Im Dark Mode wird die Skala invertiert: <code className="font-mono text-xs">--gray-900</code> ist
                immer die Textfarbe, <code className="font-mono text-xs">--gray-100</code> immer eine helle Fläche.
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
                {grayScale.map((color) => (
                  <div key={color.name} className="space-y-2">
                    <div
                      className={`${color.class} ${color.textClass} h-24 rounded-lg flex items-end p-3 border border-border shadow-sm`}
                    >
                      <span className="text-sm font-medium">{color.name}</span>
                    </div>
                    <p className="text-xs font-mono text-muted-foreground">{color.variable}</p>
                    <p className="text-xs text-muted-foreground">{color.note}</p>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Semantische Farben</CardTitle>
              <CardDescription>
                UI-Farben für konsistente Gestaltung (passen sich automatisch an Light/Dark Mode an)
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                {semanticColors.map((color) => (
                  <div key={color.name} className="space-y-2">
                    <div 
                      className={`${color.class} ${color.textClass} h-20 rounded-lg flex items-end p-3 border border-border shadow-sm`}
                    >
                      <span className="text-sm font-medium">{color.name}</span>
                    </div>
                    <p className="text-xs text-muted-foreground font-mono">{color.variable}</p>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </section>

        <Separator className="my-12" />

        {/* Buttons Section */}
        <section className="mb-16">
          <h2 className="text-3xl font-heading font-bold text-foreground mb-6 flex items-center gap-3">
            <span className="w-1 h-8 bg-accent rounded-full"></span>
            Buttons
          </h2>

          <Card>
            <CardHeader>
              <CardTitle>Button Varianten</CardTitle>
              <CardDescription>
                Alle verfügbaren Button-Stile für verschiedene Anwendungsfälle
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-8">
                <div>
                  <p className="text-sm text-muted-foreground mb-3">Varianten</p>
                  <div className="flex flex-wrap gap-4">
                    <Button variant="default">Default</Button>
                    <Button variant="secondary">Secondary</Button>
                    <Button variant="outline">Outline</Button>
                    <Button variant="ghost">Ghost</Button>
                    <Button variant="link">Link</Button>
                    <Button variant="destructive">Destructive</Button>
                  </div>
                </div>
                <div>
                  <p className="text-sm text-muted-foreground mb-3">Größen</p>
                  <div className="flex flex-wrap items-center gap-4">
                    <Button size="lg">Large</Button>
                    <Button size="default">Default</Button>
                    <Button size="sm">Small</Button>
                    <Button size="icon">◊</Button>
                  </div>
                </div>
                <div>
                  <p className="text-sm text-muted-foreground mb-3">Akzent-Buttons (mit bg-accent)</p>
                  <div className="flex flex-wrap gap-4">
                    <Button className="bg-accent text-accent-foreground hover:bg-accent/90">
                      Primär Aktion
                    </Button>
                    <Button className="bg-fire-red text-white hover:bg-fire-red-dark">
                      Notfall-Button
                    </Button>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </section>

        <Separator className="my-12" />

        {/* Badges Section */}
        <section className="mb-16">
          <h2 className="text-3xl font-heading font-bold text-foreground mb-6 flex items-center gap-3">
            <span className="w-1 h-8 bg-accent rounded-full"></span>
            Badges & Labels
          </h2>

          <Card>
            <CardHeader>
              <CardTitle>Badge Varianten</CardTitle>
              <CardDescription>
                Für Kategorien, Status-Anzeigen und Kennzeichnungen
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="flex flex-wrap gap-3">
                <Badge>Default</Badge>
                <Badge variant="secondary">Secondary</Badge>
                <Badge variant="outline">Outline</Badge>
                <Badge variant="destructive">Destructive</Badge>
                <Badge className="bg-fire-red text-white">Einsatz</Badge>
                <Badge className="bg-fire-orange text-white">Warnung</Badge>
                <Badge className="bg-charcoal text-white">Information</Badge>
              </div>
            </CardContent>
          </Card>
        </section>

        <Separator className="my-12" />

        {/* Cards Section */}
        <section className="mb-16">
          <h2 className="text-3xl font-heading font-bold text-foreground mb-6 flex items-center gap-3">
            <span className="w-1 h-8 bg-accent rounded-full"></span>
            Cards
          </h2>

          <div className="grid md:grid-cols-3 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Standard Card</CardTitle>
                <CardDescription>Mit Titel und Beschreibung</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Inhalt der Karte mit beliebigem Content.
                </p>
              </CardContent>
            </Card>

            <Card className="border-l-4 border-l-accent">
              <CardHeader>
                <CardTitle>Akzent Card</CardTitle>
                <CardDescription>Mit farbigem Rand</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Für hervorgehobene Inhalte.
                </p>
              </CardContent>
            </Card>

            <Card className="bg-charcoal text-white">
              <CardHeader>
                <CardTitle className="text-white">Dark Card</CardTitle>
                <CardDescription className="text-white/70">Dunkler Hintergrund</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-white/80">
                  Für besondere Hervorhebung.
                </p>
              </CardContent>
            </Card>
          </div>
        </section>

        <Separator className="my-12" />

        {/* Shadows Section */}
        <section className="mb-16">
          <h2 className="text-3xl font-heading font-bold text-foreground mb-6 flex items-center gap-3">
            <span className="w-1 h-8 bg-accent rounded-full"></span>
            Schatten
          </h2>

          <Card>
            <CardHeader>
              <CardTitle>Box Shadow Varianten</CardTitle>
              <CardDescription>
                Verschiedene Schattierungsstufen für Tiefenwirkung
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-6">
                {['2xs', 'xs', 'sm', 'md', 'lg', 'xl', '2xl'].map((size) => (
                  <div key={size} className="text-center">
                    <div 
                      className={`bg-background border border-border rounded-lg h-20 w-full shadow-${size} mb-2`}
                    />
                    <p className="text-xs text-muted-foreground font-mono">shadow-{size}</p>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </section>

        <Separator className="my-12" />

        {/* Spacing Section */}
        <section className="mb-16">
          <h2 className="text-3xl font-heading font-bold text-foreground mb-6 flex items-center gap-3">
            <span className="w-1 h-8 bg-accent rounded-full"></span>
            Abstände & Spacing
          </h2>

          <Card>
            <CardHeader>
              <CardTitle>Spacing Scale</CardTitle>
              <CardDescription>
                Basiert auf 4px (0.25rem) Einheiten — Tailwind Standard
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {[1, 2, 3, 4, 6, 8, 12, 16].map((size) => (
                  <div key={size} className="flex items-center gap-4">
                    <div className={`bg-accent h-4 w-${size}`} style={{ width: `${size * 4}px` }} />
                    <span className="text-sm text-muted-foreground font-mono w-16">p-{size}</span>
                    <span className="text-sm text-muted-foreground">{size * 4}px / {size * 0.25}rem</span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </section>

        <Separator className="my-12" />

        {/* Border Radius Section */}
        <section className="mb-16">
          <h2 className="text-3xl font-heading font-bold text-foreground mb-6 flex items-center gap-3">
            <span className="w-1 h-8 bg-accent rounded-full"></span>
            Eckenradien
          </h2>

          <Card>
            <CardHeader>
              <CardTitle>Border Radius</CardTitle>
              <CardDescription>
                Definierte Rundungen für konsistente Gestaltung
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="flex flex-wrap gap-6">
                {[
                  { name: 'rounded-sm', label: 'Small' },
                  { name: 'rounded-md', label: 'Medium' },
                  { name: 'rounded-lg', label: 'Large' },
                  { name: 'rounded-xl', label: 'Extra Large' },
                  { name: 'rounded-full', label: 'Full' },
                ].map((radius) => (
                  <div key={radius.name} className="text-center">
                    <div 
                      className={`bg-accent w-20 h-20 ${radius.name} mb-2`}
                    />
                    <p className="text-xs text-muted-foreground font-mono">{radius.name}</p>
                    <p className="text-xs text-muted-foreground">{radius.label}</p>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </section>

        <Separator className="my-12" />

        {/* Gradients Section */}
        <section className="mb-16">
          <h2 className="text-3xl font-heading font-bold text-foreground mb-6 flex items-center gap-3">
            <span className="w-1 h-8 bg-accent rounded-full"></span>
            Verläufe (Gradients)
          </h2>

          <Card>
            <CardHeader>
              <CardTitle>Definierte Gradienten</CardTitle>
              <CardDescription>
                Vordefinierte Farbverläufe für Hintergründe und Akzente
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid md:grid-cols-2 gap-6">
                <div>
                  <div className="gradient-fire h-24 rounded-lg mb-2" />
                  <p className="text-sm text-muted-foreground font-mono">.gradient-fire</p>
                </div>
                <div>
                  <div className="gradient-charcoal h-24 rounded-lg mb-2" />
                  <p className="text-sm text-muted-foreground font-mono">.gradient-charcoal</p>
                </div>
                <div>
                  <div className="gradient-warm h-24 rounded-lg border border-border mb-2" />
                  <p className="text-sm text-muted-foreground font-mono">.gradient-warm</p>
                </div>
                <div>
                  <div className="gradient-navy h-24 rounded-lg mb-2" />
                  <p className="text-sm text-muted-foreground font-mono">.gradient-navy</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </section>

      </main>
      <Footer />
    </div>
  );
};

export default Styleguide;
