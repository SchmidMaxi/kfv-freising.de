import { MapPin, Users, Award, History } from "lucide-react";
import { Badge } from "@/components/ui/badge";

const features = [
  {
    icon: Users,
    title: "Gemeinschaft",
    description: "Über 4.200 ehrenamtliche Helfer in 42 Feuerwehren",
  },
  {
    icon: Award,
    title: "Tradition",
    description: "Seit über 150 Jahren im Dienst der Bevölkerung",
  },
  {
    icon: History,
    title: "Erfahrung",
    description: "Professionelle Ausbildung und moderne Ausrüstung",
  },
];

export const AboutSection = () => {
  return (
    <section id="verband" className="py-16 md:py-24 bg-muted text-foreground overflow-hidden">
      <div className="container">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          {/* Content */}
          <div>
            <Badge variant="outline" className="mb-4 border-fire-red text-fire-red">
              Über uns
            </Badge>
            <h2 className="font-heading text-3xl md:text-4xl font-bold mb-6">
              Wir im Landkreis
              <span className="text-fire-red"> Freising</span>
            </h2>
            <p className="text-lg text-muted-foreground mb-8 leading-relaxed">
              Der Kreisfeuerwehrverband Freising e.V. ist der Zusammenschluss aller 
              Freiwilligen Feuerwehren im Landkreis Freising. Wir koordinieren die 
              Zusammenarbeit, fördern die Ausbildung und vertreten die Interessen 
              unserer Mitglieder gegenüber Politik und Gesellschaft.
            </p>

            {/* Feature List */}
            <div className="space-y-6">
              {features.map((feature) => (
                <div key={feature.title} className="flex items-start gap-4">
                  <div className="p-3 rounded-lg bg-fire-red/20 flex-shrink-0">
                    <feature.icon className="h-6 w-6 text-fire-red" />
                  </div>
                  <div>
                    <h3 className="font-heading font-semibold text-lg mb-1">
                      {feature.title}
                    </h3>
                    <p className="text-muted-foreground">
                      {feature.description}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Map Placeholder */}
          <div className="relative">
            <div className="aspect-square bg-background rounded-2xl overflow-hidden border border-border">
              <div className="w-full h-full flex flex-col items-center justify-center p-8 text-center">
                <MapPin className="h-16 w-16 text-fire-red mb-4" />
                <h3 className="font-heading text-2xl font-bold mb-2">
                  Landkreis Freising
                </h3>
                <p className="text-muted-foreground mb-6">
                  42 Feuerwehren verteilt auf 24 Gemeinden
                </p>
                <div className="grid grid-cols-2 gap-4 w-full max-w-xs">
                  <div className="p-4 rounded-lg bg-fire-red/20 text-center">
                    <p className="font-heading text-2xl font-bold text-fire-red">800</p>
                    <p className="text-sm text-muted-foreground">km² Fläche</p>
                  </div>
                  <div className="p-4 rounded-lg bg-fire-red/20 text-center">
                    <p className="font-heading text-2xl font-bold text-fire-red">185k</p>
                    <p className="text-sm text-muted-foreground">Einwohner</p>
                  </div>
                </div>
              </div>
            </div>
            {/* Decorative Elements */}
            <div className="absolute -top-4 -right-4 w-24 h-24 bg-fire-red/20 rounded-full blur-2xl" />
            <div className="absolute -bottom-8 -left-8 w-32 h-32 bg-fire-red/10 rounded-full blur-3xl" />
          </div>
        </div>
      </div>
    </section>
  );
};