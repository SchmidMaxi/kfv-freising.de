import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { ThemeProvider } from "@/components/ThemeProvider";
import Index from "./pages/Index";
import NewsDetail from "./pages/NewsDetail";
import FireStationsMap from "./pages/FireStationsMap";
import Service from "./pages/Service";
import Verband from "./pages/Verband";
import Einsaetze from "./pages/Einsaetze";
import Termine from "./pages/Termine";
import Aktuelles from "./pages/Aktuelles";
import Ausbildung from "./pages/Ausbildung";
import Kontakt from "./pages/Kontakt";
import FeuerwehrDetail from "./pages/FeuerwehrDetail";
import InspektionDetail from "./pages/InspektionDetail";
import Downloads from "./pages/Downloads";
import Styleguide from "./pages/Styleguide";
import ShowcaseSlider from "./pages/ShowcaseSlider";
import ShowcaseCards from "./pages/ShowcaseCards";
import ShowcaseAccordions from "./pages/ShowcaseAccordions";
import ShowcaseTabs from "./pages/ShowcaseTabs";
import NotFound from "./pages/NotFound";
import SearchResults from "./pages/SearchResults";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <ThemeProvider defaultTheme="light" storageKey="kfv-ui-theme">
      <TooltipProvider>
        <Toaster />
        <Sonner />
        <BrowserRouter>
          <Routes>
            <Route path="/" element={<Index />} />
            <Route path="/news/:id" element={<NewsDetail />} />
            <Route path="/feuerwehren" element={<FireStationsMap />} />
            <Route path="/service" element={<Service />} />
            <Route path="/verband" element={<Verband />} />
            <Route path="/einsaetze" element={<Einsaetze />} />
            <Route path="/termine" element={<Termine />} />
            <Route path="/aktuelles" element={<Aktuelles />} />
            <Route path="/ausbildung" element={<Ausbildung />} />
            <Route path="/kontakt" element={<Kontakt />} />
            <Route path="/feuerwehr/:id" element={<FeuerwehrDetail />} />
            <Route path="/inspektion/:id" element={<InspektionDetail />} />
            <Route path="/downloads" element={<Downloads />} />
            <Route path="/styleguide" element={<Styleguide />} />
            <Route path="/slider" element={<ShowcaseSlider />} />
            <Route path="/cards" element={<ShowcaseCards />} />
            <Route path="/accordions" element={<ShowcaseAccordions />} />
            <Route path="/tabs" element={<ShowcaseTabs />} />
            <Route path="/suche" element={<SearchResults />} />
            {/* ADD ALL CUSTOM ROUTES ABOVE THE CATCH-ALL "*" ROUTE */}
            <Route path="*" element={<NotFound />} />
          </Routes>
        </BrowserRouter>
      </TooltipProvider>
    </ThemeProvider>
  </QueryClientProvider>
);

export default App;