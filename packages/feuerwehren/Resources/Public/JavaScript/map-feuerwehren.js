(function () {
    // --- 1. KONSTANTEN UND GLOBALE VARIABLEN ---
    const ORIGIN = window.location.origin;
    const API_SEARCH = ORIGIN + '/api/feuerwehren/search';
    const API_OVERLAYS = ORIGIN + '/api/feuerwehren/overlays';
    const VT_TILES = ORIGIN + '/_vt/{z}/{x}/{y}.pbf';

    const mapContainerId = 'map';
    const listContainerId = 'fwList';
    const fzWrapperSelector = '#fzFilters';
    const layerGemeindenId = 'toggle-gemeinden';
    const layerKbmId = 'toggle-kbm';
    const layerKbiId = 'toggle-kbi';

    // Kanonische Seite mit dem Karten-Plugin (Feuerwehr::showAction lebt auf derselben
    // Plugin-Instanz). Siehe Tracker D8: Marker-/Listen-Klick verlinkt hierher, damit auch
    // die kompakte Karten-Einbettung (Startseite) zur vollen Detailseite führt.
    const DETAIL_BASE_PATH = '/inspektion/feuerwehren';

    let map;

    // --- Detail-Link-Helfer (D8) ---
    function detailUrl(uid) {
        const params = new URLSearchParams();
        params.set('tx_feuerwehren_karte[controller]', 'Feuerwehr');
        params.set('tx_feuerwehren_karte[action]', 'show');
        params.set('tx_feuerwehren_karte[feuerwehr]', String(uid));
        return `${DETAIL_BASE_PATH}?${params.toString()}`;
    }

    // --- 2. KARTEN-STIL DEFINITION ---
    // Farbpalette je Theme (D4: Dark-Mode-Unterstützung). Nur die Basiskarten-Töne
    // (Hintergrund/Wasser/Landbedeckung/Straßen/Grenzen) wechseln — Gemeinden-/KBM-/KBI-/
    // Feuerwehr-Layer bleiben farblich gleich, da sie auf beiden Hintergründen gut lesbar sind.
    const MAP_THEME_COLORS = {
        light: { background: '#f2f2f2', water: '#a0c8f0', landcover: '#e8e8e8', landcoverOpacity: 0.5, roads: '#bdbdbd', boundary: '#888888' },
        dark: { background: '#1a1d21', water: '#16324a', landcover: '#2a2e33', landcoverOpacity: 0.6, roads: '#4a4f57', boundary: '#6b7280' },
    };

    function getTheme() {
        return document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
    }

    function buildBaseStyle(theme) {
        const c = MAP_THEME_COLORS[theme] || MAP_THEME_COLORS.light;
        return {
            "version": 8,
            "sources": {
                "basemap": { "type": "vector", "tiles": [VT_TILES], "minzoom": 0, "maxzoom": 14 },
                "gemeinden": { "type": "geojson", "data": { "type": "FeatureCollection", "features": [] } },
                "kbm": { "type": "geojson", "data": { "type": "FeatureCollection", "features": [] } },
                "kbi": { "type": "geojson", "data": { "type": "FeatureCollection", "features": [] } },
                "feuerwehren": { "type": "geojson", "data": { "type": "FeatureCollection", "features": [] } }
            },
            "layers": [
                { "id": "background", "type": "background", "paint": { "background-color": c.background } },
                { "id": "water", "type": "fill", "source": "basemap", "source-layer": "water", "paint": { "fill-color": c.water } },
                { "id": "landcover", "type": "fill", "source": "basemap", "source-layer": "landcover", "paint": { "fill-color": c.landcover, "fill-opacity": c.landcoverOpacity } },
                { "id": "roads", "type": "line", "source": "basemap", "source-layer": "transportation", "paint": { "line-color": c.roads, "line-width": ["interpolate", ["linear"], ["zoom"], 6, 0.2, 14, 2] } },
                { "id": "boundary", "type": "line", "source": "basemap", "source-layer": "boundary", "paint": { "line-color": c.boundary, "line-dasharray": [3, 2], "line-width": 1 } },
                { "id": "gemeinden-fill", "type": "fill", "source": "gemeinden", "paint": { "fill-color": "#66bb6a", "fill-opacity": 0.10 } },
                { "id": "gemeinden-outline", "type": "line", "source": "gemeinden", "paint": { "line-color": "#2e7d32", "line-width": 1 } },
                { "id": "kbm-fill", "type": "fill", "source": "kbm", "layout": { "visibility": "none" }, "paint": { "fill-color": "#42a5f5", "fill-opacity": 0.08 } },
                { "id": "kbm-outline", "type": "line", "source": "kbm", "layout": { "visibility": "none" }, "paint": { "line-color": "#1e88e5", "line-width": 1 } },
                { "id": "kbi-fill", "type": "fill", "source": "kbi", "layout": { "visibility": "none" }, "paint": { "fill-color": "#7e57c2", "fill-opacity": 0.06 } },
                { "id": "kbi-outline", "type": "line", "source": "kbi", "layout": { "visibility": "none" }, "paint": { "line-color": "#6a1b9a", "line-width": 1 } },
                { "id": "feuerwehren", "type": "circle", "source": "feuerwehren", "paint": { "circle-color": "#1e88e5", "circle-radius": 6, "circle-stroke-color": "#ffffff", "circle-stroke-width": 1, "circle-opacity": 0.9 } },
                { "id": "feuerwehren-highlight", "type": "circle", "source": "feuerwehren", "filter": ["==", ["get", "uid"], "__none__"], "paint": { "circle-color": "#ff6d00", "circle-radius": 8, "circle-stroke-color": "#ffffff", "circle-stroke-width": 2 } }
            ]
        };
    }

    // --- 3. HILFSFUNKTIONEN ---
    function setHighlight(uidOrNull) {
        const filter = uidOrNull ? ["==", ["get", "uid"], String(uidOrNull)] : ["==", ["get", "uid"], "__none__"];
        if (map.getLayer('feuerwehren-highlight')) map.setFilter('feuerwehren-highlight', filter);
        const list = document.getElementById(listContainerId);
        if (list) {
            Array.from(list.querySelectorAll('.fw-item')).forEach(li => {
                li.classList.toggle('dim', !!uidOrNull && li.dataset.id !== String(uidOrNull));
            });
        }
        if (map.getLayer('feuerwehren')) map.setPaintProperty('feuerwehren', 'circle-opacity', uidOrNull ? 0.35 : 0.9);
    }

    function getSelectedFz() {
        const wrap = document.querySelector(fzWrapperSelector);
        return wrap ? Array.from(wrap.querySelectorAll('input[name="fz[]"]:checked')).map(i => i.value) : [];
    }

    function getMode() {
        return document.querySelector('input[name="mode"]:checked')?.value || 'and';
    }

    // --- 4. DATENLADE-FUNKTIONEN ---
    async function loadOverlays() {
        try {
            const res = await fetch(API_OVERLAYS);
            if (!res.ok) return;
            const payload = await res.json();

            const gemeindenFeatures = (payload.gemeinden || [])
                .map(g => ({ type: 'Feature', id: String(g.uid), properties: { uid: String(g.uid), name: g.name || '' }, geometry: g.geojson }))
                .filter(f => f.geometry);
            if (map.getSource('gemeinden')) map.getSource('gemeinden').setData({ type: 'FeatureCollection', features: gemeindenFeatures });

            const processAndDissolve = (fc, sourceName) => {
                if (!fc || !fc.features || fc.features.length === 0) return;
                try {
                    // KORREKTUR: turf.flatten ist die korrekte und robuste Methode, um MultiPolygons aufzulösen.
                    const flattened = turf.flatten(fc);
                    const dissolved = turf.dissolve(flattened, { propertyName: 'title' });

                    dissolved.features.forEach(f => {
                        const original = fc.features.find(orig => orig.properties.title === f.properties.title);
                        if (original) {
                            f.properties = { ...original.properties, name: f.properties.title };
                        }
                    });

                    if (map.getSource(sourceName)) map.getSource(sourceName).setData(dissolved);
                } catch (e) {
                    console.error(`Dissolve for ${sourceName} failed:`, e);
                    if (map.getSource(sourceName)) map.getSource(sourceName).setData(fc);
                }
            };

            processAndDissolve(payload.kbmFeatures, 'kbm');
            processAndDissolve(payload.kbiFeatures, 'kbi');

        } catch (e) {
            console.error('Overlay-Load failed', e);
        }
    }

    function itemsToGeoJSON(items) {
        return {
            type: 'FeatureCollection',
            features: items
                .filter(it => it.lon !== undefined && it.lat !== undefined)
                .map(it => ({
                    type: 'Feature', id: it.uid,
                    properties: { uid: String(it.uid), name: it.name || '', fahrzeuge: (it.fahrzeuge || []).join(', ') },
                    geometry: { type: 'Point', coordinates: [Number(it.lon), Number(it.lat)] }
                }))
        };
    }

    function renderList(items) {
        const listEl = document.getElementById(listContainerId);
        if (!listEl) return;
        listEl.innerHTML = '';
        items.forEach(it => {
            const div = document.createElement('div');
            div.className = 'fw-item';
            div.dataset.id = String(it.uid);

            // DOM-Aufbau statt innerHTML-Stringinterpolation, damit Feuerwehr-Daten
            // (Name/Adresse aus der API) nicht ungefiltert als HTML interpretiert werden.
            const strong = document.createElement('strong');
            strong.textContent = it.name || '';
            const legend = document.createElement('span');
            legend.className = 'legend';
            legend.textContent = `${it.strasse || ''}, ${it.plz || ''} ${it.ort || ''}`;
            const em = document.createElement('em');
            em.textContent = (it.fahrzeuge || []).join(', ');
            const detailsLink = document.createElement('a');
            detailsLink.href = detailUrl(it.uid);
            detailsLink.className = 'fw-item-details-link';
            detailsLink.textContent = 'Details ansehen →';

            div.append(strong, document.createElement('br'), legend, document.createElement('br'), em, document.createElement('br'), detailsLink);

            div.addEventListener('mouseenter', () => setHighlight(it.uid));
            div.addEventListener('mouseleave', () => setHighlight(null));

            div.addEventListener('click', (evt) => {
                // Klick auf den "Details ansehen"-Link soll normal navigieren, nicht die Karte schwenken.
                if (evt.target && evt.target.closest('.fw-item-details-link')) return;
                if (typeof it.lon === 'number' && typeof it.lat === 'number') {
                    map.easeTo({ center: [it.lon, it.lat], zoom: Math.max(map.getZoom(), 12) });
                }
            });
            listEl.appendChild(div);
        });
    }

    async function loadData() {
        const bounds = map.getBounds();
        // KORREKTUR: `toBBoxString` manuell und korrekt erstellen.
        const bbox = `${bounds.getWest()},${bounds.getSouth()},${bounds.getEast()},${bounds.getNorth()}`;

        const params = new URLSearchParams({
            bbox: bbox,
            mode: getMode(),
            '_': Date.now()
        });
        getSelectedFz().forEach(v => params.append('f[]', v));

        try {
            const res = await fetch(`${API_SEARCH}?${params.toString()}`);
            if (!res.ok) throw new Error(`Search API request failed`);
            const data = await res.json();
            const items = Array.isArray(data.items) ? data.items : [];
            renderList(items);
            if (map.getSource('feuerwehren')) map.getSource('feuerwehren').setData(itemsToGeoJSON(items));
        } catch (e) {
            console.error('Data-Load failed', e);
        }
    }

    // --- 5. EVENT-HANDLER ---
    function setVis(layerId, visible) {
        if (map.getLayer(layerId)) map.setLayoutProperty(layerId, 'visibility', visible ? 'visible' : 'none');
    }

    // Wendet die aktuellen Checkbox-Zustände (Gemeinden/KBM/KBI) auf die Kartenlayer an.
    // Wird initial in wireEvents() und erneut nach einem Theme-bedingten setStyle() aufgerufen,
    // da setStyle() alle Layer (inkl. layout.visibility) auf den Style-Default zurücksetzt.
    function syncLayerVisibilityFromControls() {
        const gEl = document.getElementById(layerGemeindenId);
        const kbmEl = document.getElementById(layerKbmId);
        const kbiEl = document.getElementById(layerKbiId);
        if (gEl) { setVis('gemeinden-fill', gEl.checked); setVis('gemeinden-outline', gEl.checked); }
        if (kbmEl) { ['kbm-fill', 'kbm-outline'].forEach(l => setVis(l, kbmEl.checked)); }
        if (kbiEl) { ['kbi-fill', 'kbi-outline'].forEach(l => setVis(l, kbiEl.checked)); }
    }

    function wireEvents() {
        // Layer-Sichtbarkeit
        const gEl = document.getElementById(layerGemeindenId);
        const kbmEl = document.getElementById(layerKbmId);
        const kbiEl = document.getElementById(layerKbiId);

        if (gEl) {
            const handler = () => { setVis('gemeinden-fill', gEl.checked); setVis('gemeinden-outline', gEl.checked); };
            gEl.addEventListener('change', handler);
            handler();
        }

        const kbmLayers = ['kbm-fill', 'kbm-outline'];
        const kbiLayers = ['kbi-fill', 'kbi-outline'];

        if (kbmEl && kbiEl) {
            kbmEl.addEventListener('change', () => {
                if (kbmEl.checked) {
                    kbiEl.checked = false;
                    kbiLayers.forEach(l => setVis(l, false));
                }
                kbmLayers.forEach(l => setVis(l, kbmEl.checked));
            });
            kbiEl.addEventListener('change', () => {
                if (kbiEl.checked) {
                    kbmEl.checked = false;
                    kbmLayers.forEach(l => setVis(l, false));
                }
                kbiLayers.forEach(l => setVis(l, kbiEl.checked));
            });
            kbmLayers.forEach(l => setVis(l, kbmEl.checked));
            kbiLayers.forEach(l => setVis(l, kbiEl.checked));
        }

        // Popups
        const createPopup = (e) => {
            const feature = e.features && e.features[0];
            if (!feature) return;
            const props = feature.properties;

            // DOM-Aufbau statt setHTML(), damit Namen/Feature-Properties nicht als HTML interpretiert werden.
            const container = document.createElement('div');
            const strong = document.createElement('strong');
            strong.textContent = props.name || props.title || 'Info';
            container.appendChild(strong);
            if (props.fahrzeuge) {
                container.appendChild(document.createElement('br'));
                container.appendChild(document.createTextNode(`Fahrzeuge: ${props.fahrzeuge}`));
            }
            // Detail-Link nur für Feuerwehr-Marker (D8), nicht für Gemeinden/KBM/KBI-Flächen.
            if (feature.layer && feature.layer.id === 'feuerwehren' && props.uid) {
                container.appendChild(document.createElement('br'));
                const link = document.createElement('a');
                link.href = detailUrl(props.uid);
                link.textContent = 'Details ansehen →';
                container.appendChild(link);
            }
            new maplibregl.Popup().setLngLat(e.lngLat).setDOMContent(container).addTo(map);
        };
        ['gemeinden-fill', 'kbm-fill', 'kbi-fill', 'feuerwehren'].forEach(layerId => {
            map.on('click', layerId, createPopup);
        });

        // Hover-Effekte
        map.on('mouseenter', 'feuerwehren', (e) => {
            map.getCanvas().style.cursor = 'pointer';
            setHighlight(e.features[0].properties.uid);
        });
        map.on('mouseleave', 'feuerwehren', () => {
            map.getCanvas().style.cursor = '';
            setHighlight(null);
        });

        // Allgemeine Event-Listener
        document.querySelectorAll('input[name="fz[]"], input[name="mode"]').forEach(el => {
            el.addEventListener('change', loadData);
        });

        // Events zum Laden bei manueller Kartenbewegung
        map.on('dragend', loadData);
        map.on('zoomend', loadData);
    }

    // Reagiert auf Theme-Wechsel (data-bs-theme, siehe colormode.js) durch Umschalten der
    // Kartenfarben (D4). setStyle() ersetzt Layer/Sources komplett, daher müssen Overlay-/
    // Suchdaten sowie die Toggle-Zustände danach neu angewendet werden.
    function watchThemeChanges() {
        let currentTheme = getTheme();
        const observer = new MutationObserver(() => {
            const nextTheme = getTheme();
            if (nextTheme === currentTheme) return;
            currentTheme = nextTheme;
            map.setStyle(buildBaseStyle(nextTheme));
            map.once('style.load', async () => {
                await loadOverlays();
                await loadData();
                syncLayerVisibilityFromControls();
            });
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
    }

    // --- 6. INITIALISIERUNG ---
    function init() {
        if (!document.getElementById(mapContainerId)) return;
        map = new maplibregl.Map({
            container: mapContainerId,
            center: [11.75, 48.46],
            zoom: 9,
            style: buildBaseStyle(getTheme())
        });
        map.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'top-right');

        map.on('load', async () => {
            await loadOverlays();
            await loadData();
            wireEvents();
            watchThemeChanges();
        });
    }

    document.addEventListener('DOMContentLoaded', init);
})();