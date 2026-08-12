import { Link } from "react-router-dom";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Users, Phone, Mail, ChevronRight } from "lucide-react";

interface OrgMember {
  id?: string;
  name: string;
  position: string;
  funkrufname: string;
  level: "kbr" | "kbi" | "sbi" | "kbm" | "fkbm";
  section?: string;
}

const orgData: OrgMember[] = [
  // Kreisbrandrat
  { id: "kreisbrandrat", name: "Manfred Danner", position: "Kreisbrandrat", funkrufname: "FS-Land 1", level: "kbr" },
  
  // Kreisbrandinspektoren
  { id: "kbi-helmut-schmid", name: "Helmut Schmid", position: "Kreisbrandinspektor", funkrufname: "FS-Land 2", level: "kbi", section: "Abschnitt 2" },
  { name: "Erich Frank", position: "Kreisbrandinspektor", funkrufname: "FS-Land 3", level: "kbi", section: "Abschnitt 3" },
  { name: "Andreas Müller", position: "Kreisbrandinspektor", funkrufname: "FS-Land 4", level: "kbi", section: "Abschnitt 4" },
  { name: "Roman Bittrich", position: "Kreisbrandinspektor", funkrufname: "FS-Land 5", level: "kbi", section: "Abschnitt 5" },
  { name: "Oliver Sturde", position: "Stadtbrandinspektor", funkrufname: "FS-1", level: "sbi", section: "Stadt Freising" },
  
  // Kreisbrandmeister
  { name: "Florian Lugauer", position: "Kreisbrandmeister", funkrufname: "FS-Land 2/1", level: "kbm", section: "Abschnitt 2/1" },
  { name: "Reinhold Jasch", position: "Kreisbrandmeister", funkrufname: "FS-Land 2/2", level: "kbm", section: "Abschnitt 2/2" },
  { name: "Alexander Littel", position: "Kreisbrandmeister", funkrufname: "FS-Land 3/1", level: "kbm", section: "Abschnitt 3/1" },
  { name: "Markus Hermann", position: "Kreisbrandmeister", funkrufname: "FS-Land 3/2", level: "kbm", section: "Abschnitt 3/2" },
  { name: "Johannes Neumair", position: "Kreisbrandmeister", funkrufname: "FS-Land 4/1", level: "kbm", section: "Abschnitt 4/1" },
  { name: "Johann Hofmaier", position: "Kreisbrandmeister", funkrufname: "FS-Land 4/2", level: "kbm", section: "Abschnitt 4/2" },
  { name: "Michael Wagensonner", position: "Kreisbrandmeister", funkrufname: "FS-Land 5/1", level: "kbm", section: "Abschnitt 5/1" },
  { name: "Markus Forster", position: "Kreisbrandmeister", funkrufname: "FS-Land 5/2", level: "kbm", section: "Abschnitt 5/2" },
  
  // Fach-Kreisbrandmeister
  { name: "Christian Riedl", position: "Fach-Kreisbrandmeister", funkrufname: "FS-Land 1/1", level: "fkbm", section: "Ausbildung" },
  { name: "Prof. Dr. Holger Schmid", position: "Fach-Kreisbrandmeister", funkrufname: "FS-Land 1/2", level: "fkbm", section: "Feuerwehrarzt" },
  { name: "Stephan Steinberger", position: "Fach-Kreisbrandmeister", funkrufname: "FS-Land 1/3", level: "fkbm", section: "Atemschutz" },
  { name: "Michael Reffgen", position: "Fach-Kreisbrandmeister", funkrufname: "FS-Land 1/5", level: "fkbm", section: "Einsatz- u. Katastrophenschutz" },
  { name: "Markus Krauß", position: "Fach-Kreisbrandmeister", funkrufname: "FS-Land 1/6", level: "fkbm", section: "Gefahrgut" },
  { name: "Franz Rauch", position: "Fach-Kreisbrandmeister", funkrufname: "", level: "fkbm", section: "PSNV-E" },
  { name: "Wolfgang Weiß", position: "Fach-Kreisbrandmeister", funkrufname: "", level: "fkbm", section: "EDV" },
  { name: "Florian Ferdinand", position: "Fach-Kreisbrandmeister", funkrufname: "", level: "fkbm", section: "Ausbildung / Leiter Schiedsrichter" },
];

const levelColors = {
  kbr: "bg-fire-red text-white border-fire-red",
  kbi: "bg-fire-red-dark text-white border-fire-red-dark",
  sbi: "bg-fire-red-dark text-white border-fire-red-dark",
  kbm: "bg-fire-orange text-white border-fire-orange",
  fkbm: "bg-fire-yellow text-primary border-fire-yellow",
};

const levelLabels = {
  kbr: "Kreisbrandrat",
  kbi: "Kreisbrandinspektor",
  sbi: "Stadtbrandinspektor",
  kbm: "Kreisbrandmeister",
  fkbm: "Fach-Kreisbrandmeister",
};

const OrgCard = ({ member }: { member: OrgMember }) => {
  const cardContent = (
    <div className={`rounded-xl border-2 p-4 shadow-md transition-all hover:scale-105 ${levelColors[member.level]} ${member.id ? 'cursor-pointer hover:shadow-lg' : ''}`}>
      <div className="text-center">
        <div className="w-16 h-16 mx-auto mb-3 rounded-full bg-background/20 flex items-center justify-center">
          <Users className="h-8 w-8" />
        </div>
        <h3 className="font-heading font-bold text-lg leading-tight">{member.name}</h3>
        <p className="text-sm opacity-90 mt-1">{member.position}</p>
        {member.section && (
          <p className="text-xs opacity-75 mt-1">{member.section}</p>
        )}
        {member.funkrufname && (
          <span className="inline-block mt-2 px-2 py-1 rounded bg-background/20 text-xs font-mono">
            {member.funkrufname}
          </span>
        )}
        {member.id && (
          <div className="mt-3 flex items-center justify-center gap-1 text-xs opacity-80">
            <span>Mehr erfahren</span>
            <ChevronRight className="h-3 w-3" />
          </div>
        )}
      </div>
    </div>
  );

  if (member.id) {
    return (
      <Link to={`/inspektion/${member.id}`}>
        {cardContent}
      </Link>
    );
  }

  return cardContent;
};

const Verband = () => {
  const kbr = orgData.filter((m) => m.level === "kbr");
  const kbiSbi = orgData.filter((m) => m.level === "kbi" || m.level === "sbi");
  const kbm = orgData.filter((m) => m.level === "kbm");
  const fkbm = orgData.filter((m) => m.level === "fkbm");

  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />
      
      <main className="flex-1">
        {/* Hero Section */}
        <section className="bg-primary py-16 md:py-24">
          <div className="container text-center">
            <h1 className="font-heading text-4xl md:text-5xl font-bold text-primary-foreground mb-4">
              Der Verband
            </h1>
            <p className="text-lg text-primary-foreground/80 max-w-2xl mx-auto">
              Organigramm der Kreisbrandinspektion Freising – 
              die Führungsstruktur unserer Feuerwehren im Überblick.
            </p>
          </div>
        </section>

        {/* Legend */}
        <section className="py-8 border-b border-border">
          <div className="container">
            <div className="flex flex-wrap justify-center gap-4">
              {Object.entries(levelLabels).map(([key, label]) => (
                <div key={key} className="flex items-center gap-2">
                  <div className={`w-4 h-4 rounded ${levelColors[key as keyof typeof levelColors].split(" ")[0]}`} />
                  <span className="text-sm text-muted-foreground">{label}</span>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Organigramm */}
        <section className="py-16 md:py-24">
          <div className="container max-w-6xl">
            {/* Kreisbrandrat */}
            <div className="flex justify-center mb-12">
              <div className="w-64">
                {kbr.map((member) => (
                  <OrgCard key={member.name} member={member} />
                ))}
              </div>
            </div>

            {/* Connecting line */}
            <div className="flex justify-center mb-8">
              <div className="w-0.5 h-12 bg-border" />
            </div>

            {/* KBI / SBI Level */}
            <div className="mb-12">
              <h2 className="text-center font-heading text-xl font-semibold text-muted-foreground mb-6">
                Kreisbrandinspektoren & Stadtbrandinspektor
              </h2>
              <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                {kbiSbi.map((member) => (
                  <OrgCard key={member.name} member={member} />
                ))}
              </div>
            </div>

            {/* Connecting line */}
            <div className="flex justify-center mb-8">
              <div className="w-0.5 h-12 bg-border" />
            </div>

            {/* KBM Level */}
            <div className="mb-12">
              <h2 className="text-center font-heading text-xl font-semibold text-muted-foreground mb-6">
                Kreisbrandmeister
              </h2>
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                {kbm.map((member) => (
                  <OrgCard key={member.name} member={member} />
                ))}
              </div>
            </div>

            {/* Connecting line */}
            <div className="flex justify-center mb-8">
              <div className="w-0.5 h-12 bg-border" />
            </div>

            {/* FKBM Level */}
            <div>
              <h2 className="text-center font-heading text-xl font-semibold text-muted-foreground mb-6">
                Fach-Kreisbrandmeister
              </h2>
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                {fkbm.map((member) => (
                  <OrgCard key={member.name} member={member} />
                ))}
              </div>
            </div>
          </div>
        </section>

        {/* Contact Section */}
        <section className="py-16 bg-muted">
          <div className="container max-w-2xl text-center">
            <h2 className="font-heading text-2xl font-bold text-foreground mb-6">
              Kontakt zur Kreisbrandinspektion
            </h2>
            <div className="flex flex-col sm:flex-row justify-center gap-6">
              <a
                href="tel:+498161123456"
                className="flex items-center justify-center gap-2 text-muted-foreground hover:text-fire-red transition-colors"
              >
                <Phone className="h-5 w-5" />
                <span>08161 / 123 456</span>
              </a>
              <a
                href="mailto:info@kfv-freising.de"
                className="flex items-center justify-center gap-2 text-muted-foreground hover:text-fire-red transition-colors"
              >
                <Mail className="h-5 w-5" />
                <span>info@kfv-freising.de</span>
              </a>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default Verband;
