import { useState } from "react";
import { Link } from "react-router-dom";
import { Menu, X, Phone, Mail, ChevronDown, Search } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { cn } from "@/lib/utils";
import { ThemeToggle } from "@/components/ThemeToggle";
import { SearchDialog } from "@/components/SearchDialog";

const navItems = [
  { label: "Startseite", href: "/", isRoute: true },
  { 
    label: "Aktuelles", 
    href: "/aktuelles", 
    isRoute: true,
    children: [
      { label: "Berichte & News", href: "/aktuelles", isRoute: true },
      { label: "Einsätze", href: "/einsaetze", isRoute: true },
      { label: "Termine", href: "/termine", isRoute: true },
    ]
  },
  { 
    label: "Verband", 
    href: "/verband", 
    isRoute: true,
    children: [
      { label: "Über uns", href: "/verband", isRoute: true },
      { label: "Organigramm", href: "/verband#organigramm", isRoute: true },
      { label: "Ansprechpartner", href: "/kontakt", isRoute: true },
    ]
  },
  { 
    label: "Feuerwehren", 
    href: "/feuerwehren", 
    isRoute: true,
    children: [
      { label: "Karte & Übersicht", href: "/feuerwehren", isRoute: true },
      { label: "Freiwillige Feuerwehren", href: "/feuerwehren#freiwillige", isRoute: true },
      { label: "Jugendfeuerwehren", href: "/feuerwehren#jugend", isRoute: true },
    ]
  },
  { 
    label: "Ausbildung", 
    href: "/ausbildung", 
    isRoute: true,
    children: [
      { label: "Lehrgänge", href: "/ausbildung", isRoute: true },
      { label: "Jugendarbeit", href: "/ausbildung#jugend", isRoute: true },
      { label: "Termine", href: "/termine", isRoute: true },
    ]
  },
  { 
    label: "Service", 
    href: "/service", 
    isRoute: true,
    children: [
      { label: "Downloads", href: "/downloads", isRoute: true },
      { label: "FAQ", href: "/service#faq", isRoute: true },
    ]
  },
  { label: "Kontakt", href: "/kontakt", isRoute: true },
];

export const Header = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [openMobileDropdown, setOpenMobileDropdown] = useState<string | null>(null);
  const [searchOpen, setSearchOpen] = useState(false);

  const toggleMobileDropdown = (label: string) => {
    setOpenMobileDropdown(openMobileDropdown === label ? null : label);
  };

  return (
    <header className="sticky top-0 z-50 w-full">
      {/* Top Bar */}
      <div className="bg-surface-dark text-surface-dark-foreground py-2 hidden md:block">
        <div className="container flex justify-between items-center text-sm">
          <div className="flex items-center gap-6">
            <a href="tel:+498161123456" className="flex items-center gap-2 hover:text-fire-red-light transition-colors">
              <Phone className="h-4 w-4" />
              <span>08161 / 123 456</span>
            </a>
            <a href="mailto:info@kfv-freising.de" className="flex items-center gap-2 hover:text-fire-red-light transition-colors">
              <Mail className="h-4 w-4" />
              <span>info@kfv-freising.de</span>
            </a>
          </div>
          <div className="flex items-center gap-4">
            <span className="text-surface-dark-foreground/70">Notruf:</span>
            <span className="font-heading font-bold text-fire-red-light text-lg">112</span>
          </div>
        </div>
      </div>

      {/* Main Navigation */}
      <nav className="bg-background/95 backdrop-blur-md border-b border-border shadow-sm">
        <div className="container flex items-center justify-between h-16 md:h-20">
          {/* Logo */}
          <Link to="/" className="flex items-center gap-3">
            <img
              src="/images/logo.png"
              alt="Kreisfeuerwehrverband Freising e.V. Logo"
              className="h-12 md:h-14 w-auto block dark:hidden"
            />
            <img
              src="/images/logo-white.png"
              alt="Kreisfeuerwehrverband Freising e.V. Logo"
              className="h-12 md:h-14 w-auto hidden dark:block"
            />
          </Link>

          {/* Desktop Navigation */}
          <div className="hidden lg:flex items-center gap-1">
            {navItems.map((item) => (
              <div key={item.label} className="relative">
                {item.children ? (
                  <DropdownMenu>
                    <div className="flex items-center">
                      {item.isRoute ? (
                        <Link
                          to={item.href}
                          className={cn(
                            "inline-flex h-9 items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors",
                            "text-foreground hover:bg-secondary hover:text-foreground"
                          )}
                        >
                          {item.label}
                        </Link>
                      ) : (
                        <a
                          href={item.href}
                          className={cn(
                            "inline-flex h-9 items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors",
                            "text-foreground hover:bg-secondary hover:text-foreground"
                          )}
                        >
                          {item.label}
                        </a>
                      )}
                      <DropdownMenuTrigger asChild>
                        <button
                          className={cn(
                            "inline-flex h-9 items-center justify-center rounded-md px-1 py-2 text-sm font-medium transition-colors",
                            "text-foreground hover:bg-secondary hover:text-foreground focus:outline-none"
                          )}
                        >
                          <ChevronDown className="h-4 w-4" />
                          <span className="sr-only">Untermenü öffnen</span>
                        </button>
                      </DropdownMenuTrigger>
                    </div>
                    <DropdownMenuContent align="start" className="w-48 bg-card border-border">
                      {item.children.map((child) => (
                        <DropdownMenuItem key={child.label} asChild>
                          {child.isRoute ? (
                            <Link
                              to={child.href}
                              className="w-full cursor-pointer"
                            >
                              {child.label}
                            </Link>
                          ) : (
                            <a
                              href={child.href}
                              className="w-full cursor-pointer"
                            >
                              {child.label}
                            </a>
                          )}
                        </DropdownMenuItem>
                      ))}
                    </DropdownMenuContent>
                  </DropdownMenu>
                ) : (
                  item.isRoute ? (
                    <Link
                      to={item.href}
                      className={cn(
                        "inline-flex h-9 w-max items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors",
                        "text-foreground hover:bg-secondary hover:text-foreground"
                      )}
                    >
                      {item.label}
                    </Link>
                  ) : (
                    <a
                      href={item.href}
                      className={cn(
                        "inline-flex h-9 w-max items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors",
                        "text-foreground hover:bg-secondary hover:text-foreground"
                      )}
                    >
                      {item.label}
                    </a>
                  )
                )}
              </div>
            ))}
          </div>

          {/* Search & Theme Toggle */}
          <div className="hidden lg:flex items-center gap-2">
            <Button
              variant="ghost"
              size="icon"
              onClick={() => setSearchOpen(true)}
              className="text-foreground hover:bg-secondary"
            >
              <Search className="h-5 w-5" />
              <span className="sr-only">Suche öffnen</span>
            </Button>
            <ThemeToggle />
          </div>
          
          <SearchDialog open={searchOpen} onOpenChange={setSearchOpen} />

          {/* Mobile Menu Button */}
          <button
            onClick={() => setIsOpen(!isOpen)}
            className="lg:hidden p-2 text-foreground"
            aria-label="Menü öffnen"
          >
            {isOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
          </button>
        </div>

        {/* Mobile Navigation */}
        {isOpen && (
          <div className="lg:hidden border-t border-border bg-background">
            <ul className="container py-4 space-y-1">
              {navItems.map((item) => (
                <li key={item.label}>
                  {item.children ? (
                    <div>
                      <button
                        onClick={() => toggleMobileDropdown(item.label)}
                        className="flex items-center justify-between w-full px-4 py-3 font-medium text-foreground hover:bg-secondary rounded-md transition-colors"
                      >
                        {item.label}
                        <ChevronDown className={cn(
                          "h-4 w-4 transition-transform",
                          openMobileDropdown === item.label && "rotate-180"
                        )} />
                      </button>
                      {openMobileDropdown === item.label && (
                        <ul className="ml-4 mt-1 space-y-1 border-l-2 border-accent pl-4">
                          {item.children.map((child) => (
                            <li key={child.label}>
                              {child.isRoute ? (
                                <Link
                                  to={child.href}
                                  className="block px-4 py-2 text-sm text-muted-foreground hover:text-foreground hover:bg-secondary rounded-md transition-colors"
                                  onClick={() => setIsOpen(false)}
                                >
                                  {child.label}
                                </Link>
                              ) : (
                                <a
                                  href={child.href}
                                  className="block px-4 py-2 text-sm text-muted-foreground hover:text-foreground hover:bg-secondary rounded-md transition-colors"
                                  onClick={() => setIsOpen(false)}
                                >
                                  {child.label}
                                </a>
                              )}
                            </li>
                          ))}
                        </ul>
                      )}
                    </div>
                  ) : (
                    item.isRoute ? (
                      <Link
                        to={item.href}
                        className="block px-4 py-3 font-medium text-foreground hover:bg-secondary rounded-md transition-colors"
                        onClick={() => setIsOpen(false)}
                      >
                        {item.label}
                      </Link>
                    ) : (
                      <a
                        href={item.href}
                        className="block px-4 py-3 font-medium text-foreground hover:bg-secondary rounded-md transition-colors"
                        onClick={() => setIsOpen(false)}
                      >
                        {item.label}
                      </a>
                    )
                  )}
                </li>
              ))}
            </ul>
          </div>
        )}
      </nav>
    </header>
  );
};
