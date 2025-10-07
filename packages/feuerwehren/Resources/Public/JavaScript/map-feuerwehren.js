(function () {
    const ORIGIN = window.location.origin; // absolute Basis-URL!
    // const API_SEARCH = ORIGIN + '/?type=171001';
    const API_SEARCH = ORIGIN + '/api/feuerwehren/search';
    // const API_OVERLAYS = ORIGIN + '/?type=171002';
    const API_OVERLAYS = ORIGIN + '/api/feuerwehren/overlays';
    const VT_TILES = ORIGIN + '/_vt/{z}/{x}/{y}.pbf';

    const mapContainerId = 'map';
    const listContainerId = 'fwList';
    const fzWrapperSelector = '#fzFilters';
    const layerGemeindenId = 'toggle-gemeinden';
    const layerKbmId = 'toggle-kbm';
    const layerKbiId = 'toggle-kbi';

    let map, hoverUid = null;

    function buildBaseStyle() {
        return {
            "version": 8,
            "sources": {
                "basemap": {
                    "type": "vector",
                    "tiles": [VT_TILES],
                    "minzoom": 0,
                    "maxzoom": 14,
                    "scheme": "xyz"
                },
                "gemeinden": { "type": "geojson", "data": { "type": "FeatureCollection", "features": [] } },
                "kbm":       { "type": "geojson", "data": { "type": "FeatureCollection", "features": [] } },
                "kbi":       { "type": "geojson", "data": { "type": "FeatureCollection", "features": [] } },
                "feuerwehren": { "type": "geojson", "data": { "type": "FeatureCollection", "features": [] } }
            },
            "layers": [
                // sehr simples Basemap-Rendering, damit es nicht grau ist:
                { "id": "water", "type": "fill", "source": "basemap", "source-layer": "water",
                    "paint": { "fill-color": "#a0c8f0" } },
                { "id": "landcover", "type": "fill", "source": "basemap", "source-layer": "landcover",
                    "paint": { "fill-color": "#e8e8e8", "fill-opacity": 0.5 } },
                { "id": "roads", "type": "line", "source": "basemap", "source-layer": "transportation",
                    "paint": { "line-color": "#bdbdbd", "line-width": ["interpolate",["linear"],["zoom"],6,0.2,14,2] } },
                { "id": "boundary", "type": "line", "source": "basemap", "source-layer": "boundary",
                    "paint": { "line-color": "#888", "line-dasharray": [3,2], "line-width": 1 } },

                // Overlays
                { "id": "gemeinden-fill", "type": "fill", "source": "gemeinden",
                    "paint": { "fill-color": "#66bb6a", "fill-opacity": 0.10 } },
                { "id": "gemeinden-outline", "type": "line", "source": "gemeinden",
                    "paint": { "line-color": "#2e7d32", "line-width": 1 } },

                { "id": "kbm-fill", "type": "fill", "source": "kbm",
                    "layout": { "visibility": "none" },
                    "paint": { "fill-color": "#42a5f5", "fill-opacity": 0.08 } },
                { "id": "kbm-outline", "type": "line", "source": "kbm",
                    "layout": { "visibility": "none" },
                    "paint": { "line-color": "#1e88e5", "line-width": 1 } },

                { "id": "kbi-fill", "type": "fill", "source": "kbi",
                    "layout": { "visibility": "none" },
                    "paint": { "fill-color": "#7e57c2", "fill-opacity": 0.06 } },
                { "id": "kbi-outline", "type": "line", "source": "kbi",
                    "layout": { "visibility": "none" },
                    "paint": { "line-color": "#6a1b9a", "line-width": 1 } },

                // Feuerwehren (Basis + Highlight)
                { "id": "feuerwehren", "type": "circle", "source": "feuerwehren",
                    "paint": {
                        "circle-color": "#1e88e5",
                        "circle-radius": 6,
                        "circle-stroke-color": "#ffffff",
                        "circle-stroke-width": 1,
                        "circle-opacity": 0.9
                    } },
                { "id": "feuerwehren-highlight", "type": "circle", "source": "feuerwehren",
                    "filter": ["==", ["get","uid"], "__none__"],
                    "paint": {
                        "circle-color": "#ff6d00",
                        "circle-radius": 8,
                        "circle-stroke-color": "#ffffff",
                        "circle-stroke-width": 2
                    } }
            ]
        };
    }

    function setMarkersDim(dim) {
        if (!map.getLayer('feuerwehren')) return;
        map.setPaintProperty('feuerwehren', 'circle-opacity', dim ? 0.35 : 0.9);
    }

    function setHighlight(uidOrNull) {
        hoverUid = uidOrNull;
        const filter = uidOrNull ? ["==", ["get","uid"], String(uidOrNull)] : ["==", ["get","uid"], "__none__"];
        if (map.getLayer('feuerwehren-highlight')) {
            map.setFilter('feuerwehren-highlight', filter);
        }
        // Liste dimmen
        const list = document.getElementById(listContainerId);
        if (list) {
            Array.from(list.querySelectorAll('.fw-item')).forEach(li => {
                li.classList.toggle('dim', !!uidOrNull && li.dataset.id !== String(uidOrNull));
            });
        }
        setMarkersDim(!!uidOrNull);
    }

    function getSelectedFz() {
        const wrap = document.querySelector(fzWrapperSelector);
        if (!wrap) return [];
        return Array.from(wrap.querySelectorAll('input[name="fz[]"]:checked')).map(i => i.value);
    }

    function getMode() {
        const el = document.querySelector('input[name="mode"]:checked');
        return el ? el.value : 'and';
    }

    async function loadOverlays() {
        try {
            const res = await fetch(API_OVERLAYS);
            const payload = await res.json();

            const gemeinden = payload.gemeinden || [];
            const kbmMap = payload.kbm || {}; // { kbmUid: [gemeindeUid,...] }
            const kbiMap = payload.kbi || {}; // { kbiUid: [kbmUid,...] }

            // 1) Gemeinden in Source schreiben + Index aufbauen
            const gjFeatures = gemeinden.map(g => ({
                type: 'Feature',
                id: String(g.uid),
                properties: { uid: String(g.uid), name: g.name || '' },
                geometry: g.geojson?.type ? g.geojson : (g.geojson?.geometry || g.geojson)
            })).filter(f => !!f.geometry);

            const gemeindeSrc = map.getSource('gemeinden');
            if (gemeindeSrc) {
                gemeindeSrc.setData({ type: 'FeatureCollection', features: gjFeatures });
            }

            const gById = new Map(gjFeatures.map(f => [String(f.properties.uid), f]));

            // helper: Union über Feature-Liste
            function unionAll(features) {
                if (!features.length) return null;
                let merged = features[0];
                for (let i = 1; i < features.length; i++) {
                    try {
                        merged = window.turf.union(merged, features[i]);
                    } catch (e) {
                        // eslint-disable-next-line no-console
                        console.warn('Union error, skipping one piece', e);
                    }
                }
                if (!merged) return null;
                // Sauber klonen + Properties setzen
                const out = JSON.parse(JSON.stringify(merged));
                if (!out.properties) out.properties = {};
                return out;
            }

            // 2) KBM: union(Gemeinden)
            const kbmFeatures = [];
            Object.entries(kbmMap).forEach(([kbmUid, gemeindeUids]) => {
                const parts = (gemeindeUids || [])
                    .map(id => gById.get(String(id)))
                    .filter(Boolean);
                const merged = unionAll(parts);
                if (merged) {
                    merged.properties.uid = String(kbmUid);
                    kbmFeatures.push(merged);
                }
            });

            const kbmSrc = map.getSource('kbm');
            if (kbmSrc) {
                kbmSrc.setData({ type: 'FeatureCollection', features: kbmFeatures });
            }

            const kbmById = new Map(kbmFeatures.map(f => [String(f.properties.uid), f]));

            // 3) KBI: union(KBM-Flächen)
            const kbiFeatures = [];
            Object.entries(kbiMap).forEach(([kbiUid, kbmUids]) => {
                const parts = (kbmUids || [])
                    .map(id => kbmById.get(String(id)))
                    .filter(Boolean);
                const merged = unionAll(parts);
                if (merged) {
                    merged.properties.uid = String(kbiUid);
                    kbiFeatures.push(merged);
                }
            });

            const kbiSrc = map.getSource('kbi');
            if (kbiSrc) {
                kbiSrc.setData({ type: 'FeatureCollection', features: kbiFeatures });
            }
        } catch (e) {
            // eslint-disable-next-line no-console
            console.error('Overlay-Load failed', e);
        }
    }


    function bboxQS() {
        const b = map.getBounds();
        // west,south,east,north
        return [b.getWest(), b.getSouth(), b.getEast(), b.getNorth()].join(',');
    }

    function renderList(items) {
        const listEl = document.getElementById(listContainerId);
        if (!listEl) return;
        listEl.innerHTML = '';
        items.forEach(it => {
            const div = document.createElement('div');
            div.className = 'fw-item';
            div.dataset.id = String(it.uid);
            const fzText = (it.fahrzeuge || []).join(', ');
            div.innerHTML = `<strong>${it.name}</strong><br><span class="legend">${it.strasse || ''}, ${it.plz || ''} ${it.ort || ''}</span><br><em>${fzText}</em>`;
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

    function itemsToGeoJSON(items) {
        const feats = items
            .filter(it => typeof it.lon !== 'undefined' && typeof it.lat !== 'undefined')
            .map(it => ({
                type: 'Feature',
                id: it.uid,
                properties: {
                    uid: String(it.uid),
                    name: it.name || '',
                    fahrzeuge: (it.fahrzeuge || []).join(', ')
                },
                geometry: {
                    type: 'Point',
                    coordinates: [Number(it.lon), Number(it.lat)]
                }
            }));
        return { type: 'FeatureCollection', features: feats };
    }

    async function loadData() {
        const params = new URLSearchParams();
        params.set('bbox', bboxQS());
        params.set('mode', getMode());
        getSelectedFz().forEach(v => params.append('f[]', v));
        params.set('_', String(Date.now())); // cache-bust

        try {
            const res = await fetch(API_SEARCH + '?' + params.toString());
            const data = await res.json();
            const items = Array.isArray(data.items) ? data.items : [];

            // Liste + Marker
            renderList(items);
            const src = map.getSource('feuerwehren');
            if (src) src.setData(itemsToGeoJSON(items));

            // Popup bei Click
            map.off('click', 'feuerwehren'); // doppelte binding vermeiden
            map.on('click', 'feuerwehren', (ev) => {
                const f = ev.features && ev.features[0];
                if (!f) return;
                const p = f.properties || {};
                const html = `<strong>${p.name || ''}</strong><br>${p.fahrzeuge || ''}`;
                new maplibregl.Popup({ closeOnClick: true })
                    .setLngLat(ev.lngLat)
                    .setHTML(html)
                    .addTo(map);
            });

            map.off('mouseenter', 'feuerwehren');
            map.off('mouseleave', 'feuerwehren');
            map.on('mouseenter', 'feuerwehren', (ev) => {
                const f = ev.features && ev.features[0];
                if (!f) return;
                setHighlight(f.properties.uid);
                map.getCanvas().style.cursor = 'pointer';
            });
            map.on('mouseleave', 'feuerwehren', () => {
                setHighlight(null);
                map.getCanvas().style.cursor = '';
            });
        } catch (e) {
            // eslint-disable-next-line no-console
            console.error('Data-Load failed', e);
        }
    }

    function wireToggles() {
        // Layer-Toggles
        const setVis = (layerId, visible) => {
            if (!map.getLayer(layerId)) return;
            map.setLayoutProperty(layerId, 'visibility', visible ? 'visible' : 'none');
        };
        const gEl = document.getElementById(layerGemeindenId);
        const kbmEl = document.getElementById(layerKbmId);
        const kbiEl = document.getElementById(layerKbiId);
        if (gEl) {
            const handler = () => {
                setVis('gemeinden-fill', gEl.checked);
                setVis('gemeinden-outline', gEl.checked);
            };
            gEl.addEventListener('change', handler);
            handler(); // initial
        }
        if (kbmEl) {
            const handler = () => {
                setVis('kbm-fill', kbmEl.checked);
                setVis('kbm-outline', kbmEl.checked);
            };
            kbmEl.addEventListener('change', handler);
            handler();
        }
        if (kbiEl) {
            const handler = () => {
                setVis('kbi-fill', kbiEl.checked);
                setVis('kbi-outline', kbiEl.checked);
            };
            kbiEl.addEventListener('change', handler);
            handler();
        }

        // Filter-Inputs
        document.querySelectorAll('input[name="fz[]"], input[name="mode"]').forEach(el => {
            el.addEventListener('change', () => loadData());
        });

        // Map Events
        map.on('moveend', () => loadData());
        map.on('zoomend', () => loadData());
    }

    function init() {
        // MapLibre initialisieren
        map = new maplibregl.Map({
            container: mapContainerId,
            center: [11.75, 48.46], // Fokus Freising
            zoom: 10,
            style: buildBaseStyle()
        });
        map.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'top-right');

        map.on('load', async () => {
            await loadOverlays();
            await loadData();
            wireToggles();
        });
    }

    // Start
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
