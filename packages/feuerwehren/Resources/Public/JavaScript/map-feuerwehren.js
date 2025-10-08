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

    let map;

    // --- 2. KARTEN-STIL DEFINITION (unverändert) ---
    function buildBaseStyle() {
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
                { "id": "water", "type": "fill", "source": "basemap", "source-layer": "water", "paint": { "fill-color": "#a0c8f0" } },
                { "id": "landcover", "type": "fill", "source": "basemap", "source-layer": "landcover", "paint": { "fill-color": "#e8e8e8", "fill-opacity": 0.5 } },
                { "id": "roads", "type": "line", "source": "basemap", "source-layer": "transportation", "paint": { "line-color": "#bdbdbd", "line-width": ["interpolate", ["linear"], ["zoom"], 6, 0.2, 14, 2] } },
                { "id": "boundary", "type": "line", "source": "basemap", "source-layer": "boundary", "paint": { "line-color": "#888", "line-dasharray": [3, 2], "line-width": 1 } },
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
            div.innerHTML = `<strong>${it.name}</strong><br><span class="legend">${it.strasse || ''}, ${it.plz || ''} ${it.ort || ''}</span><br><em>${(it.fahrzeuge || []).join(', ')}</em>`;

            div.addEventListener('mouseenter', () => setHighlight(it.uid));
            div.addEventListener('mouseleave', () => setHighlight(null));

            div.addEventListener('click', () => {
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
    function wireEvents() {
        // Layer-Sichtbarkeit
        const gEl = document.getElementById(layerGemeindenId);
        const kbmEl = document.getElementById(layerKbmId);
        const kbiEl = document.getElementById(layerKbiId);

        const setVis = (layerId, visible) => {
            if (map.getLayer(layerId)) map.setLayoutProperty(layerId, 'visibility', visible ? 'visible' : 'none');
        };

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
            let html = `<strong>${props.name || props.title || 'Info'}</strong>`;
            if (props.fahrzeuge) html += `<br>Fahrzeuge: ${props.fahrzeuge}`;
            new maplibregl.Popup().setLngLat(e.lngLat).setHTML(html).addTo(map);
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

    // --- 6. INITIALISIERUNG ---
    function init() {
        if (!document.getElementById(mapContainerId)) return;
        map = new maplibregl.Map({
            container: mapContainerId,
            center: [11.75, 48.46],
            zoom: 10,
            style: buildBaseStyle()
        });
        map.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'top-right');

        map.on('load', async () => {
            await loadOverlays();
            await loadData();
            wireEvents();
        });
    }

    document.addEventListener('DOMContentLoaded', init);
})();