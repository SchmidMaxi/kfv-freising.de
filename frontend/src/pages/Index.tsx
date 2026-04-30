import { Header } from "@/components/Header";
import { Hero } from "@/components/Hero";
import { QuickActions } from "@/components/QuickActions";
import { News } from "@/components/News";
import { AboutSection } from "@/components/AboutSection";
import { Footer } from "@/components/Footer";

const Index = () => {
  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main>
        <Hero />
        <QuickActions />
        <News />
        <AboutSection />
      </main>
      <Footer />
    </div>
  );
};

export default Index;