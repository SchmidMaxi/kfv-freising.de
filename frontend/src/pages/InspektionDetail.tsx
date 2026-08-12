import { useParams, Link } from "react-router-dom";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { 
  ArrowLeft, 
  Phone, 
  Mail, 
  MapPin, 
  Calendar, 
  Award, 
  Users, 
  FileText,
  Shield,
  Building2,
  Flame
} from "lucide-react";

interface InspektionMember {
  id: string;
  name: string;
  position: string;
  funkrufname: string;
  level: "kbr" | "kbi" | "sbi" | "kbm" | "fkbm";
  section?: string;
  email?: string;
  phone?: string;
  address?: string;
  image?: string;
  bio?: string;
  activesSince?: string;
  achievements?: string[];
  responsibilities?: string[];
  zustaendigeFeuerwehren?: string[];
  specializations?: string[];
}

const membersData: Record<string, InspektionMember> = {
  "kreisbrandrat": {
    id: "kreisbrandrat",
    name: "Manfred Danner",
    position: "Kreisbrandrat",
    funkrufname: "FS-Land 1",
    level: "kbr",
    email: "kbr@kfv-freising.de",
    phone: "08161 / 123 456",
    address: "Landratsamt Freising, Landshuter Str. 31, 85356 Freising",
    bio: "Manfred Danner ist seit 2018 Kreisbrandrat des Landkreises Freising und damit der oberste Feuerwehrführer im Landkreis. Mit über 35 Jahren Erfahrung im Feuerwehrwesen hat er verschiedene Führungspositionen durchlaufen und war zuvor als Kreisbrandinspektor im Abschnitt 3 tätig. Als gelernter Maschinenbauingenieur bringt er wertvolles technisches Know-how in seine ehrenamtliche Tätigkeit ein.",
    activesSince: "1989",
    achievements: [
      "Feuerwehr-Ehrenzeichen in Gold (2015)",
      "Verdienstmedaille des Bezirksfeuerwehrverbandes Oberbayern (2019)",
      "Steckkreuz des Deutschen Feuerwehrverbandes (2022)",
      "Kommunale Verdienstmedaille in Bronze (2023)"
    ],
    responsibilities: [
      "Leitung der gesamten Kreisbrandinspektion",
      "Beratung des Landrats in Feuerwehrangelegenheiten",
      "Koordination der überörtlichen Einsätze",
      "Vertretung der Feuerwehren gegenüber Behörden",
      "Überwachung des Ausbildungsstandes",
      "Mitwirkung bei der Beschaffung von Feuerwehrfahrzeugen",
      "Zusammenarbeit mit den Rettungsdiensten"
    ],
    zustaendigeFeuerwehren: [
      "Alle 86 Feuerwehren im Landkreis Freising"
    ],
    specializations: [
      "Technische Hilfeleistung",
      "Führungslehre",
      "Katastrophenschutz"
    ]
  },
  "kbi-helmut-schmid": {
    id: "kbi-helmut-schmid",
    name: "Helmut Schmid",
    position: "Kreisbrandinspektor",
    funkrufname: "FS-Land 2",
    level: "kbi",
    section: "Abschnitt 2",
    email: "kbi2@kfv-freising.de",
    phone: "08161 / 123 457",
    bio: "Helmut Schmid ist Kreisbrandinspektor für den Abschnitt 2 und verfügt über umfangreiche Erfahrung in der Feuerwehrführung.",
    activesSince: "1992",
    achievements: [
      "Feuerwehr-Ehrenzeichen in Silber (2012)",
      "Feuerwehr-Ehrenzeichen in Gold (2020)"
    ],
    responsibilities: [
      "Leitung des Inspektionsbereichs Abschnitt 2",
      "Überwachung der Einsatzbereitschaft",
      "Koordination der Kreisbrandmeister im Abschnitt"
    ],
    zustaendigeFeuerwehren: [
      "FF Allershausen", "FF Haag", "FF Kranzberg", "FF Paunzhausen"
    ]
  }
};

const levelColors: Record<string, string> = {
  kbr: "bg-fire-red text-white",
  kbi: "bg-fire-red-dark text-white",
  sbi: "bg-fire-red-dark text-white",
  kbm: "bg-fire-orange text-white",
  fkbm: "bg-fire-yellow text-primary",
};

const levelLabels: Record<string, string> = {
  kbr: "Kreisbrandrat",
  kbi: "Kreisbrandinspektor",
  sbi: "Stadtbrandinspektor",
  kbm: "Kreisbrandmeister",
  fkbm: "Fach-Kreisbrandmeister",
};

const InspektionDetail = () => {
  const { id } = useParams<{ id: string }>();
  const member = membersData[id || ""];

  if (!member) {
    return (
      <div className="min-h-screen flex flex-col bg-background">
        <Header />
        <main className="flex-1 container py-16 text-center">
          <h1 className="text-2xl font-heading font-bold mb-4">Mitglied nicht gefunden</h1>
          <Button asChild>
            <Link to="/verband">
              <ArrowLeft className="mr-2 h-4 w-4" />
              Zurück zum Verband
            </Link>
          </Button>
        </main>
        <Footer />
      </div>
    );
  }

  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />
      
      <main className="flex-1">
        {/* Hero Section */}
        <section className="bg-primary py-12 md:py-20">
          <div className="container">
            <Button variant="ghost" asChild className="mb-6 text-primary-foreground/80 hover:text-primary-foreground hover:bg-primary-foreground/10">
              <Link to="/verband">
                <ArrowLeft className="mr-2 h-4 w-4" />
                Zurück zum Verband
              </Link>
            </Button>
            
            <div className="flex flex-col md:flex-row items-center md:items-start gap-8">
              {/* Profile Image */}
              <div className="w-48 h-48 rounded-2xl bg-primary-foreground/20 flex items-center justify-center shadow-xl">
                <Users className="h-24 w-24 text-primary-foreground/60" />
              </div>
              
              {/* Basic Info */}
              <div className="text-center md:text-left flex-1">
                <Badge className={`${levelColors[member.level]} mb-3`}>
                  {levelLabels[member.level]}
                </Badge>
                <h1 className="font-heading text-3xl md:text-4xl lg:text-5xl font-bold text-primary-foreground mb-2">
                  {member.name}
                </h1>
                <p className="text-xl text-primary-foreground/80 mb-4">
                  {member.position}
                  {member.section && ` – ${member.section}`}
                </p>
                
                {member.funkrufname && (
                  <div className="inline-flex items-center gap-2 bg-primary-foreground/20 px-4 py-2 rounded-lg">
                    <Shield className="h-5 w-5 text-primary-foreground/80" />
                    <span className="font-mono text-primary-foreground">
                      Funkrufname: {member.funkrufname}
                    </span>
                  </div>
                )}
              </div>
            </div>
          </div>
        </section>

        {/* Content Section */}
        <section className="py-12 md:py-16">
          <div className="container">
            <div className="grid lg:grid-cols-3 gap-8">
              {/* Left Column - Main Content */}
              <div className="lg:col-span-2 space-y-8">
                {/* Biography */}
                {member.bio && (
                  <Card>
                    <CardHeader>
                      <CardTitle className="flex items-center gap-2 font-heading">
                        <FileText className="h-5 w-5 text-fire-red" />
                        Zur Person
                      </CardTitle>
                    </CardHeader>
                    <CardContent>
                      <p className="text-muted-foreground leading-relaxed">
                        {member.bio}
                      </p>
                      {member.activesSince && (
                        <div className="mt-4 flex items-center gap-2 text-sm text-muted-foreground">
                          <Calendar className="h-4 w-4" />
                          <span>Aktiv bei der Feuerwehr seit {member.activesSince}</span>
                        </div>
                      )}
                    </CardContent>
                  </Card>
                )}

                {/* Responsibilities */}
                {member.responsibilities && member.responsibilities.length > 0 && (
                  <Card>
                    <CardHeader>
                      <CardTitle className="flex items-center gap-2 font-heading">
                        <Shield className="h-5 w-5 text-fire-red" />
                        Aufgaben & Verantwortungsbereiche
                      </CardTitle>
                    </CardHeader>
                    <CardContent>
                      <ul className="space-y-3">
                        {member.responsibilities.map((item, index) => (
                          <li key={index} className="flex items-start gap-3">
                            <Flame className="h-5 w-5 text-fire-red mt-0.5 flex-shrink-0" />
                            <span className="text-muted-foreground">{item}</span>
                          </li>
                        ))}
                      </ul>
                    </CardContent>
                  </Card>
                )}

                {/* Zuständige Feuerwehren */}
                {member.zustaendigeFeuerwehren && member.zustaendigeFeuerwehren.length > 0 && (
                  <Card>
                    <CardHeader>
                      <CardTitle className="flex items-center gap-2 font-heading">
                        <Building2 className="h-5 w-5 text-fire-red" />
                        Zuständige Feuerwehren
                      </CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="flex flex-wrap gap-2">
                        {member.zustaendigeFeuerwehren.map((fw, index) => (
                          <Badge key={index} variant="secondary" className="text-sm">
                            {fw}
                          </Badge>
                        ))}
                      </div>
                    </CardContent>
                  </Card>
                )}

                {/* Achievements */}
                {member.achievements && member.achievements.length > 0 && (
                  <Card>
                    <CardHeader>
                      <CardTitle className="flex items-center gap-2 font-heading">
                        <Award className="h-5 w-5 text-fire-red" />
                        Auszeichnungen & Ehrungen
                      </CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="grid sm:grid-cols-2 gap-4">
                        {member.achievements.map((achievement, index) => (
                          <div 
                            key={index} 
                            className="flex items-center gap-3 p-3 rounded-lg bg-muted/50"
                          >
                            <Award className="h-5 w-5 text-fire-orange flex-shrink-0" />
                            <span className="text-sm text-muted-foreground">{achievement}</span>
                          </div>
                        ))}
                      </div>
                    </CardContent>
                  </Card>
                )}
              </div>

              {/* Right Column - Sidebar */}
              <div className="space-y-6">
                {/* Contact Card */}
                <Card className="border-fire-red/20">
                  <CardHeader className="bg-fire-red/5">
                    <CardTitle className="flex items-center gap-2 font-heading text-lg">
                      <Phone className="h-5 w-5 text-fire-red" />
                      Kontakt
                    </CardTitle>
                  </CardHeader>
                  <CardContent className="pt-4 space-y-4">
                    {member.phone && (
                      <a 
                        href={`tel:${member.phone.replace(/\s/g, '')}`}
                        className="flex items-center gap-3 text-muted-foreground hover:text-fire-red transition-colors"
                      >
                        <Phone className="h-4 w-4" />
                        <span>{member.phone}</span>
                      </a>
                    )}
                    {member.email && (
                      <a 
                        href={`mailto:${member.email}`}
                        className="flex items-center gap-3 text-muted-foreground hover:text-fire-red transition-colors"
                      >
                        <Mail className="h-4 w-4" />
                        <span>{member.email}</span>
                      </a>
                    )}
                    {member.address && (
                      <div className="flex items-start gap-3 text-muted-foreground">
                        <MapPin className="h-4 w-4 mt-1 flex-shrink-0" />
                        <span className="text-sm">{member.address}</span>
                      </div>
                    )}
                  </CardContent>
                </Card>

                {/* Specializations */}
                {member.specializations && member.specializations.length > 0 && (
                  <Card>
                    <CardHeader>
                      <CardTitle className="flex items-center gap-2 font-heading text-lg">
                        <Flame className="h-5 w-5 text-fire-red" />
                        Fachgebiete
                      </CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="flex flex-wrap gap-2">
                        {member.specializations.map((spec, index) => (
                          <Badge key={index} className="bg-fire-red/10 text-fire-red hover:bg-fire-red/20">
                            {spec}
                          </Badge>
                        ))}
                      </div>
                    </CardContent>
                  </Card>
                )}

                {/* Quick Stats */}
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center gap-2 font-heading text-lg">
                      <Users className="h-5 w-5 text-fire-red" />
                      Auf einen Blick
                    </CardTitle>
                  </CardHeader>
                  <CardContent className="space-y-4">
                    <div className="flex justify-between items-center py-2 border-b border-border">
                      <span className="text-sm text-muted-foreground">Position</span>
                      <span className="font-medium text-sm">{member.position}</span>
                    </div>
                    {member.section && (
                      <div className="flex justify-between items-center py-2 border-b border-border">
                        <span className="text-sm text-muted-foreground">Bereich</span>
                        <span className="font-medium text-sm">{member.section}</span>
                      </div>
                    )}
                    {member.activesSince && (
                      <div className="flex justify-between items-center py-2 border-b border-border">
                        <span className="text-sm text-muted-foreground">Aktiv seit</span>
                        <span className="font-medium text-sm">{member.activesSince}</span>
                      </div>
                    )}
                    <div className="flex justify-between items-center py-2">
                      <span className="text-sm text-muted-foreground">Funkrufname</span>
                      <span className="font-mono text-sm">{member.funkrufname || "–"}</span>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </div>
          </div>
        </section>

        {/* CTA Section */}
        <section className="py-12 bg-muted">
          <div className="container text-center">
            <h2 className="font-heading text-2xl font-bold mb-4">
              Haben Sie Fragen?
            </h2>
            <p className="text-muted-foreground mb-6 max-w-lg mx-auto">
              Bei Fragen zur Feuerwehr im Landkreis Freising stehen wir Ihnen gerne zur Verfügung.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button asChild>
                <Link to="/kontakt">
                  <Mail className="mr-2 h-4 w-4" />
                  Kontakt aufnehmen
                </Link>
              </Button>
              <Button variant="outline" asChild>
                <Link to="/verband">
                  <Users className="mr-2 h-4 w-4" />
                  Alle Inspektionsmitglieder
                </Link>
              </Button>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default InspektionDetail;
