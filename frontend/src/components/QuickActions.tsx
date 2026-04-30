import { Link } from "react-router-dom";
import { Flame, Calendar, MapPin, FileText } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";

const actions = [
  {
    icon: Flame,
    title: "Aktuelle Einsätze",
    description: "Übersicht der aktuellen und vergangenen Einsätze im Landkreis",
    href: "/einsaetze",
    variant: "accent" as const,
  },
  {
    icon: Calendar,
    title: "Termine & Events",
    description: "Veranstaltungen, Übungen und wichtige Termine im Überblick",
    href: "/termine",
    variant: "primary" as const,
  },
  {
    icon: MapPin,
    title: "Feuerwehr finden",
    description: "Finden Sie Ihre zuständige Feuerwehr im Landkreis Freising",
    href: "/feuerwehren",
    variant: "accent" as const,
  },
  {
    icon: FileText,
    title: "Service & Downloads",
    description: "Formulare, Dokumente und wichtige Informationen",
    href: "/downloads",
    variant: "primary" as const,
  },
];

export const QuickActions = () => {
  return (
    <section className="py-16 bg-secondary">
      <div className="container">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {actions.map((action) => (
            <Link key={action.title} to={action.href} className="group">
              <Card className="h-full border-0 shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 overflow-hidden bg-card">
                <CardContent className="p-6">
                  <div className={`w-14 h-14 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform ${
                    action.variant === "accent" ? "gradient-fire" : "bg-primary"
                  }`}>
                    <action.icon className="h-7 w-7 text-primary-foreground" />
                  </div>
                  <h3 className="font-heading font-semibold text-xl text-card-foreground mb-2">
                    {action.title}
                  </h3>
                  <p className="text-muted-foreground text-sm leading-relaxed">
                    {action.description}
                  </p>
                </CardContent>
              </Card>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
};