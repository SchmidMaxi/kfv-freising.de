import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { Search, FileText, MapPin, Calendar, Users, X } from "lucide-react";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
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
  { title: "Mitglied werden", description: "Informationen zum Beitritt", href: "/mitglied", icon: Users, category: "Seiten" },
  { title: "Service & Downloads", description: "Dokumente und Formulare", href: "/service", icon: FileText, category: "Seiten" },
  { title: "Verband & Organigramm", description: "Kreisbrandinspektion", href: "/verband", icon: Users, category: "Seiten" },
  { title: "Kontakt", description: "Kontaktformular und Ansprechpartner", href: "/kontakt", icon: FileText, category: "Seiten" },
  { title: "Feuerwehren Übersicht", description: "Alle Feuerwehren im Landkreis", href: "/feuerwehren", icon: MapPin, category: "Seiten" },
  { title: "FFW Freising", description: "Stützpunktfeuerwehr Freising", href: "/feuerwehr/freising", icon: MapPin, category: "Feuerwehren" },
  { title: "FFW Moosburg", description: "Stützpunktfeuerwehr Moosburg", href: "/feuerwehr/moosburg", icon: MapPin, category: "Feuerwehren" },
  { title: "FFW Neufahrn", description: "Ortsfeuerwehr Neufahrn", href: "/feuerwehr/neufahrn", icon: MapPin, category: "Feuerwehren" },
  { title: "Downloads", description: "Alle Downloads", href: "/service#downloads", icon: FileText, category: "Service" },
  { title: "Formulare", description: "Alle Formulare", href: "/service#formulare", icon: FileText, category: "Service" },
  { title: "FAQ", description: "Häufig gestellte Fragen", href: "/service#faq", icon: FileText, category: "Service" },
];

interface SearchDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

export const SearchDialog = ({ open, onOpenChange }: SearchDialogProps) => {
  const [query, setQuery] = useState("");
  const [selectedIndex, setSelectedIndex] = useState(0);
  const navigate = useNavigate();

  const filteredResults = searchData.filter(
    (item) =>
      item.title.toLowerCase().includes(query.toLowerCase()) ||
      item.description.toLowerCase().includes(query.toLowerCase()) ||
      item.category.toLowerCase().includes(query.toLowerCase())
  );

  const groupedResults = filteredResults.reduce((acc, item) => {
    if (!acc[item.category]) {
      acc[item.category] = [];
    }
    acc[item.category].push(item);
    return acc;
  }, {} as Record<string, SearchResult[]>);

  useEffect(() => {
    setSelectedIndex(0);
  }, [query]);

  const handleSelect = (href: string) => {
    navigate(href);
    onOpenChange(false);
    setQuery("");
  };

  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === "ArrowDown") {
      e.preventDefault();
      setSelectedIndex((prev) => Math.min(prev + 1, filteredResults.length - 1));
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      setSelectedIndex((prev) => Math.max(prev - 1, 0));
    } else if (e.key === "Enter") {
      if (filteredResults[selectedIndex]) {
        handleSelect(filteredResults[selectedIndex].href);
      } else if (query.trim()) {
        navigate(`/suche?q=${encodeURIComponent(query.trim())}`);
        onOpenChange(false);
        setQuery("");
      }
    }
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-lg p-0 gap-0 bg-card border-border">
        <DialogHeader className="p-4 pb-0">
          <DialogTitle className="sr-only">Suche</DialogTitle>
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              onKeyDown={handleKeyDown}
              placeholder="Seiten, Feuerwehren, Downloads suchen..."
              className="pl-10 pr-10 bg-muted border-0 focus-visible:ring-1 focus-visible:ring-fire-red"
              autoFocus
            />
            {query && (
              <button
                onClick={() => setQuery("")}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
              >
                <X className="h-4 w-4" />
              </button>
            )}
          </div>
        </DialogHeader>

        <div className="max-h-80 overflow-y-auto p-2">
          {filteredResults.length === 0 ? (
            <div className="py-8 text-center text-muted-foreground">
              <Search className="h-8 w-8 mx-auto mb-2 opacity-50" />
              <p>Keine Ergebnisse gefunden</p>
            </div>
          ) : (
            Object.entries(groupedResults).map(([category, items]) => (
              <div key={category} className="mb-4 last:mb-0">
                <p className="px-3 py-2 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                  {category}
                </p>
                {items.map((item) => {
                  const globalIndex = filteredResults.indexOf(item);
                  return (
                    <button
                      key={item.href}
                      onClick={() => handleSelect(item.href)}
                      className={cn(
                        "w-full flex items-center gap-3 px-3 py-3 rounded-lg text-left transition-colors",
                        globalIndex === selectedIndex
                          ? "bg-fire-red text-white"
                          : "hover:bg-muted text-foreground"
                      )}
                    >
                      <item.icon
                        className={cn(
                          "h-5 w-5 flex-shrink-0",
                          globalIndex === selectedIndex ? "text-white" : "text-muted-foreground"
                        )}
                      />
                      <div>
                        <p className="font-medium">{item.title}</p>
                        <p
                          className={cn(
                            "text-sm",
                            globalIndex === selectedIndex ? "text-white/80" : "text-muted-foreground"
                          )}
                        >
                          {item.description}
                        </p>
                      </div>
                    </button>
                  );
                })}
              </div>
            ))
          )}
        </div>

        <div className="border-t border-border p-3 text-xs text-muted-foreground flex items-center justify-between">
          <div className="flex gap-2">
            <kbd className="px-2 py-1 rounded bg-muted font-mono">↑↓</kbd>
            <span>navigieren</span>
          </div>
          <div className="flex gap-2">
            <kbd className="px-2 py-1 rounded bg-muted font-mono">↵</kbd>
            <span>auswählen</span>
          </div>
          <div className="flex gap-2">
            <kbd className="px-2 py-1 rounded bg-muted font-mono">esc</kbd>
            <span>schließen</span>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
};
