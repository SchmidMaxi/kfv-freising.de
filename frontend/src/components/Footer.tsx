import * as React from "react";
import { Phone, Mail, MapPin, Facebook, Instagram, Youtube } from "lucide-react";
import { cn } from "@/lib/utils";

const quickLinks = [
  { label: "Startseite", href: "#" },
  { label: "Aktuelles", href: "#aktuelles" },
  { label: "Verband", href: "#verband" },
  { label: "Feuerwehren", href: "#feuerwehren" },
  { label: "Service", href: "#service" },
];

const serviceLinks = [
  { label: "Downloads", href: "#" },
  { label: "Formulare", href: "#" },
  { label: "Mitglied werden", href: "#" },
  { label: "Spenden", href: "#" },
  { label: "Kontakt", href: "#kontakt" },
];

export const Footer = React.forwardRef<HTMLElement, React.ComponentPropsWithoutRef<"footer">>(
  ({ className, ...props }, ref) => {
    return (
      <footer ref={ref} className={cn("bg-surface-dark text-surface-dark-foreground", className)} {...props}>
      <div className="container py-16">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
          {/* Brand Column */}
          <div>
            <div className="flex items-center gap-3 mb-6">
              <img
                src="/images/logo-white.png"
                alt="Kreisfeuerwehrverband Freising e.V. Logo"
                className="h-14 w-auto"
              />
            </div>
            <p className="text-surface-dark-foreground/70 mb-6 leading-relaxed">
              Gemeinsam für Ihre Sicherheit im Landkreis Freising. 
              Rund um die Uhr im Einsatz.
            </p>
            <div className="flex gap-4">
              <a href="#" className="p-2 rounded-lg bg-surface-dark-foreground/35 hover:bg-accent transition-colors">
                <Facebook className="h-5 w-5" />
              </a>
              <a href="#" className="p-2 rounded-lg bg-surface-dark-foreground/35 hover:bg-accent transition-colors">
                <Instagram className="h-5 w-5" />
              </a>
              <a href="#" className="p-2 rounded-lg bg-surface-dark-foreground/35 hover:bg-accent transition-colors">
                <Youtube className="h-5 w-5" />
              </a>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="font-heading font-semibold text-lg mb-6">Schnellzugriff</h3>
            <ul className="space-y-3">
              {quickLinks.map((link) => (
                <li key={link.label}>
                  <a 
                    href={link.href} 
                    className="text-surface-dark-foreground/70 hover:text-accent transition-colors"
                  >
                    {link.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Service Links */}
          <div>
            <h3 className="font-heading font-semibold text-lg mb-6">Service</h3>
            <ul className="space-y-3">
              {serviceLinks.map((link) => (
                <li key={link.label}>
                  <a 
                    href={link.href} 
                    className="text-surface-dark-foreground/70 hover:text-accent transition-colors"
                  >
                    {link.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h3 className="font-heading font-semibold text-lg mb-6">Kontakt</h3>
            <ul className="space-y-4">
              <li className="flex items-start gap-3">
                <MapPin className="h-5 w-5 text-accent flex-shrink-0 mt-0.5" />
                <span className="text-surface-dark-foreground/70">
                  Landratsamt Freising<br />
                  Landshuter Str. 31<br />
                  85356 Freising
                </span>
              </li>
              <li>
                <a href="tel:+498161123456" className="flex items-center gap-3 text-surface-dark-foreground/70 hover:text-accent transition-colors">
                  <Phone className="h-5 w-5 text-accent" />
                  08161 / 123 456
                </a>
              </li>
              <li>
                <a href="mailto:info@kfv-freising.de" className="flex items-center gap-3 text-surface-dark-foreground/70 hover:text-accent transition-colors">
                  <Mail className="h-5 w-5 text-accent" />
                  info@kfv-freising.de
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom Bar */}
      <div className="border-t border-surface-dark-foreground/40">
        <div className="container py-6 flex flex-col md:flex-row justify-between items-center gap-4">
          <p className="text-sm text-surface-dark-foreground/50">
            © {new Date().getFullYear()} Kreisfeuerwehrverband Freising e.V. Alle Rechte vorbehalten.
          </p>
          <div className="flex gap-6 text-sm text-surface-dark-foreground/50">
            <a href="#" className="hover:text-surface-dark-foreground transition-colors">Impressum</a>
            <a href="#" className="hover:text-surface-dark-foreground transition-colors">Datenschutz</a>
            <a href="#" className="hover:text-surface-dark-foreground transition-colors">Barrierefreiheit</a>
          </div>
        </div>
      </div>
    </footer>
    );
  },
);

Footer.displayName = "Footer";