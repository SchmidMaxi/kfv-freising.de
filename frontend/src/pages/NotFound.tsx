import { Link, useLocation } from "react-router-dom";
import { useEffect, useState } from "react";
import { Home, ArrowLeft, Search, Flame } from "lucide-react";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { SearchDialog } from "@/components/SearchDialog";

const popularLinks = [
  { label: "Aktuelles", href: "/aktuelles" },
  { label: "Feuerwehren", href: "/feuerwehren" },
  { label: "Verband", href: "/verband" },
  { label: "Termine", href: "/termine" },
  { label: "Downloads", href: "/downloads" },
  { label: "Kontakt", href: "/kontakt" },
];

const NotFound = () => {
  const location = useLocation();
  const [searchOpen, setSearchOpen] = useState(false);

  useEffect(() => {
    console.error("404 Error: User attempted to access non-existent route:", location.pathname);
  }, [location.pathname]);

  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />

      <main className="flex-1 flex items-center">
        <div className="container py-20 lg:py-28">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            {/* Left: Content */}
            <div>
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-sm font-medium mb-6">
                <Flame className="h-4 w-4" />
                Fehler 404
              </div>

              <h1 className="font-heading font-bold text-6xl md:text-7xl lg:text-8xl text-foreground mb-6 leading-none">
                Seite nicht
                <span className="block text-primary">gefunden</span>
              </h1>

              <p className="text-lg text-muted-foreground mb-8 max-w-xl leading-relaxed">
                Die von Ihnen aufgerufene Seite existiert nicht oder wurde verschoben.
                Kein Grund zur Beunruhigung – wir helfen Ihnen, schnell wieder den richtigen Weg zu finden.
              </p>

              {location.pathname && (
                <div className="mb-8 p-4 rounded-lg bg-muted border border-border">
                  <p className="text-sm text-muted-foreground">
                    Angeforderte Adresse:
                  </p>
                  <code className="text-sm font-mono text-foreground break-all">
                    {location.pathname}
                  </code>
                </div>
              )}

              <div className="mb-8">
                <label htmlFor="notfound-search" className="font-heading font-semibold text-sm uppercase tracking-wider text-muted-foreground mb-3 flex items-center gap-2">
                  <Search className="h-4 w-4" />
                  Inhalte durchsuchen
                </label>
                <button
                  id="notfound-search"
                  type="button"
                  onClick={() => setSearchOpen(true)}
                  className="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-muted hover:bg-accent hover:text-accent-foreground border border-input text-left text-muted-foreground transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background"
                >
                  <Search className="h-5 w-5 flex-shrink-0" />
                  <span className="flex-1 text-sm">Seiten, Feuerwehren, Downloads suchen...</span>
                  <kbd className="hidden sm:inline-flex px-2 py-1 rounded bg-background border border-border text-xs font-mono text-foreground">
                    ⌘K
                  </kbd>
                </button>
              </div>

              <div className="flex flex-wrap gap-4 mb-12">
                <Button asChild size="lg">
                  <Link to="/">
                    <Home className="h-5 w-5" />
                    Zur Startseite
                  </Link>
                </Button>
                <Button asChild size="lg" variant="outline" onClick={() => window.history.back()}>
                  <button type="button">
                    <ArrowLeft className="h-5 w-5" />
                    Zurück
                  </button>
                </Button>
              </div>

              <div>
                <h2 className="font-heading font-semibold text-sm uppercase tracking-wider text-muted-foreground mb-4 flex items-center gap-2">
                  <Search className="h-4 w-4" />
                  Beliebte Seiten
                </h2>
                <div className="flex flex-wrap gap-2">
                  {popularLinks.map((link) => (
                    <Link
                      key={link.href}
                      to={link.href}
                      className="px-4 py-2 rounded-full bg-muted hover:bg-accent hover:text-accent-foreground text-foreground text-sm font-medium border border-border transition-colors"
                    >
                      {link.label}
                    </Link>
                  ))}
                </div>
              </div>
            </div>

            {/* Right: Visual */}
            <div className="relative hidden lg:block">
              <div className="relative aspect-square max-w-lg mx-auto">
                <div className="absolute inset-0 gradient-fire rounded-full blur-3xl opacity-20" />
                <div className="relative h-full flex items-center justify-center">
                  <div className="font-heading font-bold text-foreground/5 text-[20rem] leading-none select-none">
                    404
                  </div>
                  <div className="absolute inset-0 flex items-center justify-center">
                    <div className="w-48 h-48 rounded-full gradient-fire flex items-center justify-center shadow-2xl">
                      <Flame className="h-24 w-24 text-white" strokeWidth={1.5} />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>

      <Footer />

      <SearchDialog open={searchOpen} onOpenChange={setSearchOpen} />
    </div>
  );
};

export default NotFound;
