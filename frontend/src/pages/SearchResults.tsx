import { useState, useEffect, useMemo } from "react";
import { Link, useSearchParams } from "react-router-dom";
import { Search, FileText, MapPin, Calendar, Users, X, ArrowRight, SearchX } from "lucide-react";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { cn } from "@/lib/utils";

interface SearchResult {
  title: string;
  description: string;
  href: string;
  icon: React.ElementType;
  category: string;
}

const searchData: SearchResult[] = [
  { title: "Startseite", description: "Zurück zur Hauptseite", href: "/", icon: FileText, category: "Seiten" },
  { title: "Aktuelle Einsätze", description: "Einsatzübersicht im Landkreis", href: "/einsaetze", icon: FileText, category: "Seiten" },
  { title: "Termine & Events", description: "Kommende Veranstaltungen", href: "/termine", icon: Calendar, category: "Seiten" },
  { title: "Aktuelles", description: "Berichte, News und Einsätze", href: "/aktuelles", icon: FileText, category: "Seiten" },
  { title: "Service & Downloads", description: "Dokumente und Formulare", href: "/service", icon: FileText, category: "Seiten" },
  { title: "Verband & Organigramm", description: "Kreisbrandinspektion", href: "/verband", icon: Users, category: "Seiten" },
  { title: "Ausbildung", description: "Lehrgänge und Schulungen", href: "/ausbildung", icon: Users, category: "Seiten" },
  { title: "Kontakt", description: "Kontaktformular und Ansprechpartner", href: "/kontakt", icon: FileText, category: "Seiten" },
  { title: "Feuerwehren Übersicht", description: "Alle Feuerwehren im Landkreis", href: "/feuerwehren", icon: MapPin, category: "Seiten" },
  { title: "FFW Freising", description: "Stützpunktfeuerwehr Freising", href: "/feuerwehr/freising", icon: MapPin, category: "Feuerwehren" },
  { title: "FFW Moosburg", description: "Stützpunktfeuerwehr Moosburg", href: "/feuerwehr/moosburg", icon: MapPin, category: "Feuerwehren" },
  { title: "FFW Neufahrn", description: "Ortsfeuerwehr Neufahrn", href: "/feuerwehr/neufahrn", icon: MapPin, category: "Feuerwehren" },
  { title: "Downloads", description: "Alle Downloads", href: "/downloads", icon: FileText, category: "Service" },
  { title: "Formulare", description: "Alle Formulare", href: "/service#formulare", icon: FileText, category: "Service" },
  { title: "FAQ", description: "Häufig gestellte Fragen", href: "/service#faq", icon: FileText, category: "Service" },
];

const categories = ["Alle", "Seiten", "Feuerwehren", "Service"] as const;
type Category = typeof categories[number];

const SearchResults = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const initialQuery = searchParams.get("q") ?? "";
  const [query, setQuery] = useState(initialQuery);
  const [activeCategory, setActiveCategory] = useState<Category>("Alle");

  useEffect(() => {
    setQuery(searchParams.get("q") ?? "");
  }, [searchParams]);

  useEffect(() => {
    document.title = query
      ? `Suchergebnisse für „${query}" – KFV Freising`
      : "Suche – KFV Freising";
  }, [query]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setSearchParams(query ? { q: query } : {});
  };

  const allResults = useMemo(() => {
    if (!query.trim()) return [];
    const q = query.toLowerCase();
    return searchData.filter(
      (item) =>
        item.title.toLowerCase().includes(q) ||
        item.description.toLowerCase().includes(q) ||
        item.category.toLowerCase().includes(q)
    );
  }, [query]);

  const filteredResults = useMemo(
    () =>
      activeCategory === "Alle"
        ? allResults
        : allResults.filter((r) => r.category === activeCategory),
    [allResults, activeCategory]
  );

  const counts = useMemo(() => {
    const map: Record<string, number> = { Alle: allResults.length };
    for (const c of categories) {
      if (c !== "Alle") map[c] = allResults.filter((r) => r.category === c).length;
    }
    return map;
  }, [allResults]);

  const highlight = (text: string) => {
    if (!query.trim()) return text;
    const parts = text.split(new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")})`, "gi"));
    return parts.map((part, i) =>
      part.toLowerCase() === query.toLowerCase() ? (
        <mark key={i} className="bg-primary/20 text-foreground rounded px-0.5">
          {part}
        </mark>
      ) : (
        <span key={i}>{part}</span>
      )
    );
  };

  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />

      <main className="flex-1">
        <section className="border-b border-border bg-muted/30">
          <div className="container py-12 lg:py-16">
            <div className="max-w-3xl">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-sm font-medium mb-4">
                <Search className="h-4 w-4" />
                Suche
              </div>
              <h1 className="font-heading font-bold text-4xl md:text-5xl text-foreground mb-6">
                Suchergebnisse
              </h1>

              <form onSubmit={handleSubmit} className="relative">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground pointer-events-none" />
                <Input
                  type="search"
                  value={query}
                  onChange={(e) => setQuery(e.target.value)}
                  placeholder="Seiten, Feuerwehren, Downloads suchen..."
                  className="h-14 pl-12 pr-32 text-base bg-background"
                  autoFocus
                />
                {query && (
                  <button
                    type="button"
                    onClick={() => {
                      setQuery("");
                      setSearchParams({});
                    }}
                    className="absolute right-24 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                    aria-label="Suche zurücksetzen"
                  >
                    <X className="h-4 w-4" />
                  </button>
                )}
                <Button type="submit" className="absolute right-2 top-1/2 -translate-y-1/2 h-10">
                  Suchen
                </Button>
              </form>

              {query && (
                <p className="mt-4 text-sm text-muted-foreground">
                  {allResults.length === 0 ? (
                    <>Keine Treffer für <span className="font-medium text-foreground">„{query}"</span></>
                  ) : (
                    <>
                      <span className="font-medium text-foreground">{allResults.length}</span>{" "}
                      {allResults.length === 1 ? "Treffer" : "Treffer"} für{" "}
                      <span className="font-medium text-foreground">„{query}"</span>
                    </>
                  )}
                </p>
              )}
            </div>
          </div>
        </section>

        <section className="container py-10 lg:py-12">
          {!query.trim() ? (
            <div className="max-w-2xl mx-auto text-center py-16">
              <div className="w-16 h-16 mx-auto mb-6 rounded-full bg-muted flex items-center justify-center">
                <Search className="h-8 w-8 text-muted-foreground" />
              </div>
              <h2 className="font-heading font-semibold text-2xl text-foreground mb-3">
                Geben Sie einen Suchbegriff ein
              </h2>
              <p className="text-muted-foreground mb-8">
                Durchsuchen Sie Seiten, Feuerwehren, Downloads und Formulare.
              </p>
              <div className="flex flex-wrap justify-center gap-2">
                {["Einsätze", "Termine", "Freising", "Downloads", "Ausbildung"].map((term) => (
                  <button
                    key={term}
                    onClick={() => {
                      setQuery(term);
                      setSearchParams({ q: term });
                    }}
                    className="px-4 py-2 rounded-full bg-muted hover:bg-accent hover:text-accent-foreground text-foreground text-sm font-medium border border-border transition-colors"
                  >
                    {term}
                  </button>
                ))}
              </div>
            </div>
          ) : allResults.length === 0 ? (
            <div className="max-w-2xl mx-auto text-center py-16">
              <div className="w-16 h-16 mx-auto mb-6 rounded-full bg-muted flex items-center justify-center">
                <SearchX className="h-8 w-8 text-muted-foreground" />
              </div>
              <h2 className="font-heading font-semibold text-2xl text-foreground mb-3">
                Keine Ergebnisse gefunden
              </h2>
              <p className="text-muted-foreground mb-8">
                Wir konnten nichts zu „{query}" finden. Versuchen Sie einen anderen Suchbegriff
                oder besuchen Sie eine unserer beliebten Seiten.
              </p>
              <div className="flex flex-wrap justify-center gap-2">
                {[
                  { label: "Aktuelles", href: "/aktuelles" },
                  { label: "Feuerwehren", href: "/feuerwehren" },
                  { label: "Termine", href: "/termine" },
                  { label: "Kontakt", href: "/kontakt" },
                ].map((link) => (
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
          ) : (
            <div className="grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-8 lg:gap-12">
              {/* Sidebar: category filters */}
              <aside>
                <h2 className="font-heading font-semibold text-sm uppercase tracking-wider text-muted-foreground mb-4">
                  Kategorien
                </h2>
                <nav className="flex lg:flex-col gap-2 flex-wrap">
                  {categories.map((cat) => {
                    const count = counts[cat] ?? 0;
                    const disabled = cat !== "Alle" && count === 0;
                    return (
                      <button
                        key={cat}
                        onClick={() => setActiveCategory(cat)}
                        disabled={disabled}
                        className={cn(
                          "flex items-center justify-between gap-2 px-4 py-2 rounded-lg text-sm font-medium border transition-colors text-left",
                          activeCategory === cat
                            ? "bg-primary text-primary-foreground border-primary"
                            : "bg-background hover:bg-muted text-foreground border-border",
                          disabled && "opacity-50 cursor-not-allowed hover:bg-background"
                        )}
                      >
                        <span>{cat}</span>
                        <Badge
                          variant="secondary"
                          className={cn(
                            "ml-auto",
                            activeCategory === cat && "bg-primary-foreground/20 text-primary-foreground"
                          )}
                        >
                          {count}
                        </Badge>
                      </button>
                    );
                  })}
                </nav>
              </aside>

              {/* Results list */}
              <div>
                {filteredResults.length === 0 ? (
                  <div className="text-center py-12">
                    <p className="text-muted-foreground">
                      Keine Ergebnisse in dieser Kategorie.
                    </p>
                  </div>
                ) : (
                  <ul className="space-y-3">
                    {filteredResults.map((result) => {
                      const Icon = result.icon;
                      return (
                        <li key={result.href}>
                          <Link
                            to={result.href}
                            className="group flex items-start gap-4 p-5 rounded-lg bg-card border border-border hover:border-primary hover:shadow-md transition-all"
                          >
                            <div className="flex-shrink-0 w-10 h-10 rounded-lg bg-muted flex items-center justify-center group-hover:bg-primary group-hover:text-primary-foreground transition-colors">
                              <Icon className="h-5 w-5" />
                            </div>
                            <div className="flex-1 min-w-0">
                              <div className="flex items-center gap-2 mb-1">
                                <Badge variant="outline" className="text-xs">
                                  {result.category}
                                </Badge>
                              </div>
                              <h3 className="font-heading font-semibold text-lg text-foreground group-hover:text-primary transition-colors">
                                {highlight(result.title)}
                              </h3>
                              <p className="text-sm text-muted-foreground mt-1">
                                {highlight(result.description)}
                              </p>
                              <p className="text-xs text-muted-foreground/70 mt-2 font-mono">
                                {result.href}
                              </p>
                            </div>
                            <ArrowRight className="flex-shrink-0 h-5 w-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all" />
                          </Link>
                        </li>
                      );
                    })}
                  </ul>
                )}
              </div>
            </div>
          )}
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default SearchResults;
