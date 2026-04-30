import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { Phone, Mail, MapPin, Clock, Send } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { useState } from "react";
import { toast } from "@/hooks/use-toast";
import { z } from "zod";

const contactSchema = z.object({
  name: z.string().trim().min(1, "Name ist erforderlich").max(100, "Name darf maximal 100 Zeichen haben"),
  email: z.string().trim().email("Bitte eine gültige E-Mail-Adresse eingeben").max(255, "E-Mail darf maximal 255 Zeichen haben"),
  subject: z.string().trim().min(1, "Betreff ist erforderlich").max(200, "Betreff darf maximal 200 Zeichen haben"),
  message: z.string().trim().min(10, "Nachricht muss mindestens 10 Zeichen haben").max(2000, "Nachricht darf maximal 2000 Zeichen haben"),
});

const contactInfo = [
  {
    icon: Phone,
    title: "Telefon",
    content: "08161 / 123 456",
    href: "tel:+498161123456",
  },
  {
    icon: Mail,
    title: "E-Mail",
    content: "info@kfv-freising.de",
    href: "mailto:info@kfv-freising.de",
  },
  {
    icon: MapPin,
    title: "Adresse",
    content: "Landratsamt Freising\nLandshuter Str. 31\n85356 Freising",
    href: null,
  },
  {
    icon: Clock,
    title: "Öffnungszeiten",
    content: "Mo - Fr: 08:00 - 16:00 Uhr",
    href: null,
  },
];

const Kontakt = () => {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    subject: "",
    message: "",
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
    if (errors[name]) {
      setErrors((prev) => ({ ...prev, [name]: "" }));
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setErrors({});

    const result = contactSchema.safeParse(formData);

    if (!result.success) {
      const fieldErrors: Record<string, string> = {};
      result.error.errors.forEach((err) => {
        if (err.path[0]) {
          fieldErrors[err.path[0] as string] = err.message;
        }
      });
      setErrors(fieldErrors);
      setIsSubmitting(false);
      return;
    }

    // Simulate form submission
    await new Promise((resolve) => setTimeout(resolve, 1000));

    toast({
      title: "Nachricht gesendet",
      description: "Vielen Dank für Ihre Nachricht. Wir werden uns zeitnah bei Ihnen melden.",
    });

    setFormData({ name: "", email: "", subject: "", message: "" });
    setIsSubmitting(false);
  };

  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Header />

      <main className="flex-1">
        {/* Hero Section */}
        <section className="bg-primary py-16 md:py-24">
          <div className="container text-center">
            <h1 className="font-heading text-4xl md:text-5xl font-bold text-primary-foreground mb-4">
              Kontakt
            </h1>
            <p className="text-lg text-primary-foreground/80 max-w-2xl mx-auto">
              Haben Sie Fragen oder Anliegen? Wir freuen uns auf Ihre Nachricht.
            </p>
          </div>
        </section>

        {/* Contact Section */}
        <section className="py-16 md:py-24">
          <div className="container">
            <div className="grid lg:grid-cols-2 gap-12">
              {/* Contact Form */}
              <div className="bg-card rounded-xl border border-border p-8">
                <h2 className="font-heading text-2xl font-bold text-card-foreground mb-6">
                  Schreiben Sie uns
                </h2>
                <form onSubmit={handleSubmit} className="space-y-6">
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div className="space-y-2">
                      <Label htmlFor="name">Name *</Label>
                      <Input
                        id="name"
                        name="name"
                        value={formData.name}
                        onChange={handleChange}
                        placeholder="Max Mustermann"
                        className={errors.name ? "border-destructive" : ""}
                      />
                      {errors.name && (
                        <p className="text-sm text-destructive">{errors.name}</p>
                      )}
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="email">E-Mail *</Label>
                      <Input
                        id="email"
                        name="email"
                        type="email"
                        value={formData.email}
                        onChange={handleChange}
                        placeholder="max@beispiel.de"
                        className={errors.email ? "border-destructive" : ""}
                      />
                      {errors.email && (
                        <p className="text-sm text-destructive">{errors.email}</p>
                      )}
                    </div>
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="subject">Betreff *</Label>
                    <Input
                      id="subject"
                      name="subject"
                      value={formData.subject}
                      onChange={handleChange}
                      placeholder="Worum geht es?"
                      className={errors.subject ? "border-destructive" : ""}
                    />
                    {errors.subject && (
                      <p className="text-sm text-destructive">{errors.subject}</p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="message">Nachricht *</Label>
                    <Textarea
                      id="message"
                      name="message"
                      value={formData.message}
                      onChange={handleChange}
                      placeholder="Ihre Nachricht an uns..."
                      rows={6}
                      className={errors.message ? "border-destructive" : ""}
                    />
                    {errors.message && (
                      <p className="text-sm text-destructive">{errors.message}</p>
                    )}
                  </div>
                  <Button
                    type="submit"
                    className="w-full gradient-fire text-white font-semibold"
                    disabled={isSubmitting}
                  >
                    {isSubmitting ? (
                      "Wird gesendet..."
                    ) : (
                      <>
                        <Send className="h-4 w-4 mr-2" />
                        Nachricht senden
                      </>
                    )}
                  </Button>
                </form>
              </div>

              {/* Contact Info */}
              <div className="space-y-8">
                <div>
                  <h2 className="font-heading text-2xl font-bold text-foreground mb-6">
                    Kontaktdaten
                  </h2>
                  <div className="grid sm:grid-cols-2 gap-6">
                    {contactInfo.map((info) => (
                      <div
                        key={info.title}
                        className="bg-muted rounded-xl p-6 hover:bg-muted/80 transition-colors"
                      >
                        <div className="p-3 rounded-lg bg-fire-red/10 w-fit mb-4">
                          <info.icon className="h-6 w-6 text-fire-red" />
                        </div>
                        <h3 className="font-heading font-semibold text-foreground mb-2">
                          {info.title}
                        </h3>
                        {info.href ? (
                          <a
                            href={info.href}
                            className="text-muted-foreground hover:text-fire-red transition-colors whitespace-pre-line"
                          >
                            {info.content}
                          </a>
                        ) : (
                          <p className="text-muted-foreground whitespace-pre-line">
                            {info.content}
                          </p>
                        )}
                      </div>
                    ))}
                  </div>
                </div>

                {/* Emergency Info */}
                <div className="bg-fire-red/10 rounded-xl p-8 border border-fire-red/20">
                  <h3 className="font-heading text-xl font-bold text-foreground mb-4">
                    Im Notfall
                  </h3>
                  <p className="text-muted-foreground mb-4">
                    Bei Feuer, Unfällen oder akuten Notfällen wählen Sie bitte sofort den Notruf.
                  </p>
                  <div className="flex items-center gap-4">
                    <span className="font-heading text-4xl font-bold text-fire-red">112</span>
                    <span className="text-muted-foreground">Notruf Feuerwehr & Rettungsdienst</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default Kontakt;
