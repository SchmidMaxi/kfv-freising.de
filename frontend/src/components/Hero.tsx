import { ArrowRight, Shield, Users, Clock } from "lucide-react";
import { Button } from "@/components/ui/button";

export const Hero = () => {
  return (
    <section className="relative min-h-[600px] md:min-h-[700px] flex items-center overflow-hidden">
      {/* Background with warm overlay */}
      <div 
        className="absolute inset-0 bg-cover bg-center bg-no-repeat"
        style={{
          backgroundImage: `linear-gradient(135deg, hsl(0 100% 25% / 0.85), hsl(0 100% 35% / 0.75)), url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?q=80&w=2070&auto=format&fit=crop')`
        }}
      />

      {/* Decorative diagonal element */}
      <div className="absolute right-0 bottom-0 w-1/2 h-1/2 bg-gradient-to-tl from-primary/30 to-transparent hidden lg:block" />

      <div className="container relative z-10 py-12">
        <div className="max-w-3xl">
          {/* Badge */}
          <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-background/20 border border-background/30 text-background mb-6 animate-fade-in backdrop-blur-sm">
            <span className="w-2 h-2 rounded-full bg-background animate-pulse" />
            <span className="text-sm font-medium">Retten • Löschen • Bergen • Schützen</span>
          </div>

          {/* Main Headline */}
          <h1 className="font-heading text-4xl md:text-5xl lg:text-6xl font-bold text-background leading-tight mb-6 animate-fade-in" style={{ animationDelay: "0.1s" }}>
            Kreisfeuerwehrverband
            <span className="block text-background/90">Freising e.V.</span>
          </h1>

          {/* Subheadline */}
          <p className="text-lg md:text-xl text-background/85 mb-8 max-w-2xl animate-fade-in" style={{ animationDelay: "0.2s" }}>
            Gemeinsam für Ihre Sicherheit im Landkreis Freising. 
            Über 4.000 ehrenamtliche Feuerwehrleute in 42 Feuerwehren 
            stehen rund um die Uhr für Sie bereit.
          </p>

          {/* CTA Buttons */}
          <div className="flex flex-wrap gap-4 mb-12 animate-fade-in" style={{ animationDelay: "0.3s" }}>
            <Button size="lg" className="bg-background text-accent hover:bg-background/90 font-heading font-semibold shadow-lg">
              Aktuelle Einsätze
              <ArrowRight className="ml-2 h-5 w-5" />
            </Button>
            <Button size="lg" variant="outline" className="border-background/40 bg-background/10 text-background hover:bg-background/20 hover:text-background font-heading font-semibold backdrop-blur-sm">
              Feuerwehr finden
            </Button>
          </div>

          {/* Stats */}
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 animate-fade-in" style={{ animationDelay: "0.4s" }}>
            <div className="flex items-center gap-4 p-4 rounded-lg bg-background/15 backdrop-blur-sm border border-background/20">
              <div className="p-3 rounded-lg bg-background/20">
                <Users className="h-6 w-6 text-background" />
              </div>
              <div>
                <p className="text-2xl font-heading font-bold text-background">4.200+</p>
                <p className="text-sm text-background/70">Aktive Mitglieder</p>
              </div>
            </div>
            <div className="flex items-center gap-4 p-4 rounded-lg bg-background/15 backdrop-blur-sm border border-background/20">
              <div className="p-3 rounded-lg bg-background/20">
                <Shield className="h-6 w-6 text-background" />
              </div>
              <div>
                <p className="text-2xl font-heading font-bold text-background">42</p>
                <p className="text-sm text-background/70">Feuerwehren</p>
              </div>
            </div>
            <div className="flex items-center gap-4 p-4 rounded-lg bg-background/15 backdrop-blur-sm border border-background/20">
              <div className="p-3 rounded-lg bg-background/20">
                <Clock className="h-6 w-6 text-background" />
              </div>
              <div>
                <p className="text-2xl font-heading font-bold text-background">24/7</p>
                <p className="text-sm text-background/70">Einsatzbereit</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};