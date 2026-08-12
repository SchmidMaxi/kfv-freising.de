import { useState } from "react";
import { Link } from "react-router-dom";
import { ArrowLeft, MapPin, Phone, Users, ExternalLink } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";

// Fire station data for Landkreis Freising
const fireStations = [
  { id: 1, name: "FF Freising", lat: 48.4028, lng: 11.7489, members: 120, type: "Stützpunkt" },
  { id: 2, name: "FF Marzling", lat: 48.4194, lng: 11.8089, members: 65, type: "Ortsfeuerwehr" },
  { id: 3, name: "FF Hallbergmoos", lat: 48.3283, lng: 11.7556, members: 95, type: "Stützpunkt" },
  { id: 4, name: "FF Neufahrn", lat: 48.3144, lng: 11.6656, members: 85, type: "Stützpunkt" },
  { id: 5, name: "FF Eching", lat: 48.2978, lng: 11.6183, members: 70, type: "Ortsfeuerwehr" },
  { id: 6, name: "FF Moosburg", lat: 48.4694, lng: 11.9383, members: 110, type: "Stützpunkt" },
  { id: 7, name: "FF Au i.d. Hallertau", lat: 48.5583, lng: 11.7500, members: 55, type: "Ortsfeuerwehr" },
  { id: 8, name: "FF Attenkirchen", lat: 48.4833, lng: 11.8167, members: 45, type: "Ortsfeuerwehr" },
  { id: 9, name: "FF Langenbach", lat: 48.4500, lng: 11.8500, members: 40, type: "Ortsfeuerwehr" },
  { id: 10, name: "FF Zolling", lat: 48.4500, lng: 11.7667, members: 50, type: "Ortsfeuerwehr" },
  { id: 11, name: "FF Kirchdorf a.d. Amper", lat: 48.3500, lng: 11.6333, members: 38, type: "Ortsfeuerwehr" },
  { id: 12, name: "FF Kranzberg", lat: 48.4167, lng: 11.6167, members: 42, type: "Ortsfeuerwehr" },
];

const FireStationsMap = () => {
  const [selectedStation, setSelectedStation] = useState<typeof fireStations[0] | null>(null);
  const [searchTerm, setSearchTerm] = useState("");

  const filteredStations = fireStations.filter(station =>
    station.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  // Calculate center of all stations for the map view
  const centerLat = fireStations.reduce((sum, s) => sum + s.lat, 0) / fireStations.length;
  const centerLng = fireStations.reduce((sum, s) => sum + s.lng, 0) / fireStations.length;

  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main className="py-8 md:py-12">
        <div className="container">
          {/* Back Link */}
          <Link 
            to="/" 
            className="inline-flex items-center gap-2 text-muted-foreground hover:text-accent transition-colors mb-6"
          >
            <ArrowLeft className="h-4 w-4" />
            Zurück zur Startseite
          </Link>

          {/* Page Header */}
          <div className="mb-8">
            <Badge variant="outline" className="mb-4 border-accent text-accent">
              Feuerwehren
            </Badge>
            <h1 className="font-heading text-3xl md:text-4xl font-bold text-foreground mb-4">
              Feuerwehren im Landkreis Freising
            </h1>
            <p className="text-muted-foreground max-w-2xl">
              42 Freiwillige Feuerwehren mit über 4.200 aktiven Mitgliedern sorgen für Ihre 
              Sicherheit im gesamten Landkreis Freising.
            </p>
          </div>

          <div className="grid lg:grid-cols-3 gap-8">
            {/* Map Container */}
            <div className="lg:col-span-2">
              <Card className="overflow-hidden">
                <div className="relative aspect-[4/3] bg-secondary">
                  {/* Interactive Map Visualization */}
                  <div className="absolute inset-0 p-4">
                    <svg 
                      viewBox="0 0 400 300" 
                      className="w-full h-full"
                      style={{ filter: "drop-shadow(0 4px 6px rgba(0,0,0,0.1))" }}
                    >
                      {/* Landkreis outline (simplified) */}
                      <path
                        d="M50,50 L350,50 L380,150 L350,250 L50,250 L20,150 Z"
                        fill="hsl(var(--muted))"
                        stroke="hsl(var(--border))"
                        strokeWidth="2"
                      />
                      
                      {/* Grid lines */}
                      {[100, 150, 200].map(y => (
                        <line 
                          key={`h-${y}`}
                          x1="20" y1={y} x2="380" y2={y} 
                          stroke="hsl(var(--border))" 
                          strokeWidth="0.5" 
                          strokeDasharray="4,4"
                        />
                      ))}
                      {[100, 200, 300].map(x => (
                        <line 
                          key={`v-${x}`}
                          x1={x} y1="50" x2={x} y2="250" 
                          stroke="hsl(var(--border))" 
                          strokeWidth="0.5" 
                          strokeDasharray="4,4"
                        />
                      ))}

                      {/* Fire stations as markers */}
                      {filteredStations.map((station, index) => {
                        // Map lat/lng to SVG coordinates (simplified projection)
                        const x = 50 + ((station.lng - 11.5) / 0.6) * 300;
                        const y = 250 - ((station.lat - 48.25) / 0.4) * 200;
                        const isSelected = selectedStation?.id === station.id;
                        
                        return (
                          <g 
                            key={station.id}
                            className="cursor-pointer"
                            onClick={() => setSelectedStation(station)}
                          >
                            {/* Pulse animation for selected */}
                            {isSelected && (
                              <circle
                                cx={x} cy={y} r="20"
                                fill="hsl(var(--fire-red))"
                                opacity="0.3"
                                className="animate-pulse"
                              />
                            )}
                            {/* Marker */}
                            <circle
                              cx={x} cy={y}
                              r={isSelected ? 12 : 8}
                              fill={station.type === "Stützpunkt" ? "hsl(var(--fire-red))" : "hsl(var(--primary))"}
                              stroke="white"
                              strokeWidth="2"
                              className="transition-all duration-200 hover:r-12"
                            />
                            {/* Label for selected or stützpunkt */}
                            {(isSelected || station.type === "Stützpunkt") && (
                              <text
                                x={x} y={y - 16}
                                textAnchor="middle"
                                className="text-xs font-medium fill-foreground"
                              >
                                {station.name.replace("FF ", "")}
                              </text>
                            )}
                          </g>
                        );
                      })}
                    </svg>
                  </div>

                  {/* Legend */}
                  <div className="absolute bottom-4 left-4 bg-card/95 backdrop-blur-sm rounded-lg p-3 shadow-lg">
                    <p className="text-xs font-medium text-card-foreground mb-2">Legende</p>
                    <div className="flex items-center gap-4 text-xs">
                      <div className="flex items-center gap-2">
                        <span className="w-3 h-3 rounded-full bg-fire-red" />
                        <span className="text-muted-foreground">Stützpunkt</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <span className="w-3 h-3 rounded-full bg-primary" />
                        <span className="text-muted-foreground">Ortsfeuerwehr</span>
                      </div>
                    </div>
                  </div>

                  {/* Zoom hint */}
                  <div className="absolute top-4 right-4 bg-card/95 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                    <p className="text-xs text-muted-foreground">Klicken Sie auf eine Feuerwehr für Details</p>
                  </div>
                </div>
              </Card>

              {/* Selected Station Details */}
              {selectedStation && (
                <Card className="mt-4 border-accent/50">
                  <CardContent className="p-6">
                    <div className="flex items-start justify-between">
                      <div>
                        <Badge className={selectedStation.type === "Stützpunkt" ? "gradient-fire text-primary-foreground" : "bg-primary text-primary-foreground"}>
                          {selectedStation.type}
                        </Badge>
                        <h3 className="font-heading text-2xl font-bold text-card-foreground mt-3 mb-2">
                          {selectedStation.name}
                        </h3>
                        <div className="flex items-center gap-6 text-muted-foreground">
                          <span className="flex items-center gap-2">
                            <Users className="h-4 w-4" />
                            {selectedStation.members} Mitglieder
                          </span>
                          <span className="flex items-center gap-2">
                            <MapPin className="h-4 w-4" />
                            Landkreis Freising
                          </span>
                        </div>
                      </div>
                      <Button asChild variant="outline" size="sm" className="flex items-center gap-2">
                        <Link to={`/feuerwehr/${selectedStation.name.replace("FF ", "").toLowerCase()}`}>
                          <ExternalLink className="h-4 w-4" />
                          Details
                        </Link>
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              )}
            </div>

            {/* Station List Sidebar */}
            <div>
              <Card className="sticky top-24">
                <CardContent className="p-4">
                  {/* Search */}
                  <div className="mb-4">
                    <Input
                      type="search"
                      placeholder="Feuerwehr suchen..."
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                      className="w-full"
                    />
                  </div>

                  {/* Stats */}
                  <div className="grid grid-cols-2 gap-3 mb-4">
                    <div className="p-3 rounded-lg bg-muted text-center">
                      <p className="font-heading text-2xl font-bold text-fire-red">42</p>
                      <p className="text-xs text-muted-foreground">Feuerwehren</p>
                    </div>
                    <div className="p-3 rounded-lg bg-muted text-center">
                      <p className="font-heading text-2xl font-bold text-foreground">4.200+</p>
                      <p className="text-xs text-muted-foreground">Mitglieder</p>
                    </div>
                  </div>

                  {/* Station List */}
                  <div className="space-y-2 max-h-[400px] overflow-y-auto">
                    {filteredStations.map((station) => (
                      <button
                        key={station.id}
                        onClick={() => setSelectedStation(station)}
                        className={`w-full text-left p-3 rounded-lg transition-colors ${
                          selectedStation?.id === station.id
                            ? "bg-accent/10 border border-accent"
                            : "bg-muted hover:bg-muted/70"
                        }`}
                      >
                        <div className="flex items-center justify-between">
                          <div className="flex items-center gap-3">
                            <span className={`w-2 h-2 rounded-full ${
                              station.type === "Stützpunkt" ? "bg-fire-red" : "bg-primary"
                            }`} />
                            <span className="font-medium text-card-foreground text-sm">
                              {station.name}
                            </span>
                          </div>
                          <span className="text-xs text-muted-foreground">
                            {station.members}
                          </span>
                        </div>
                      </button>
                    ))}
                  </div>

                  {filteredStations.length === 0 && (
                    <p className="text-center text-muted-foreground py-8">
                      Keine Feuerwehr gefunden
                    </p>
                  )}
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default FireStationsMap;