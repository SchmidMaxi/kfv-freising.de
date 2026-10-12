(function () {
    // --- 1. KONSTANTEN UND GLOBALE VARIABLEN ---
    const ORIGIN = window.location.origin;
    const API_SEARCH = ORIGIN + '/api/feuerwehren/search';
    const API_OVERLAYS = ORIGIN + '/api/feuerwehren/overlays';
    const VT_TILES = ORIGIN + '/_vt/{z}/{x}/{y}.pbf';

    const mapContainerId = 'map';
    const listContainerId = 'fwList';
    const detailContainerId = 'fwDetail';
    const emptyStateId = 'fwEmpty';
    const searchInputId = 'fwSearch';
    const searchScopeId = 'fwSearchScope';
    const statCountId = 'fwStatCount';
    const statMembersId = 'fwStatMembers';
    const statMembersWrapId = 'fwStatMembersWrap';
    const filterCountId = 'fwFilterCount';
    const filterResetId = 'fwFilterReset';
    const filterSummaryId = 'fwFilterSummary';
    const filterBodyId = 'fwFilterBody';
    const fzWrapperSelector = '#fzFilters';
    const layerGemeindenId = 'toggle-gemeinden';
    const layerKbmId = 'toggle-kbm';
    const layerKbiId = 'toggle-kbi';

    const SEARCH_DEBOUNCE_MS = 300;

    // Kanonische Seite mit dem Karten-Plugin (Feuerwehr::showAction lebt auf derselben
    // Plugin-Instanz). Siehe Tracker D8: Marker-/Listen-Klick verlinkt hierher, damit auch
    // die kompakte Karten-Einbettung (Startseite) zur vollen Detailseite führt.
    const DETAIL_BASE_PATH = '/inspektion/feuerwehren';

    // Einstufung -> Markerfarbe/Badge. Der Bootstrap-$primary dieses Themes ist Rot,
    // deshalb entspricht Lovables neutrales "primary" hier dem dunklen $gray-800.
    // Feuerwehren ohne gepflegten Typ bleiben blau, statt einer der beiden Kategorien
    // zugeordnet zu werden.
    const TYPE_STYLE = {
        stuetzpunkt: { color: '#cc0000', label: 'Stützpunktfeuerwehr' },
        ort: { color: '#2a384b', label: 'Ortsfeuerwehr' },
        '': { color: '#1e88e5', label: '' },
    };

    let map;

    // Zuletzt geladene Items, damit Listen-/Markerklick ohne erneuten Request
    // die Detailkarte füllen kann.
    let currentItems = [];
    let selectedUid = null;

    // Verhindert, dass ein programmatisches easeTo/fitBounds einen weiteren
    // loadData()-Zyklus auslöst (Endlosschleife bei aktiver Namenssuche).
    let programmaticMove = false;

    // Auf welchen Suchbegriff die Karte bereits gezoomt wurde. Nur bei einem
    // *neuen* Begriff wird der Ausschnitt an die Treffer angepasst.
    let lastFittedQuery = '';

    let searchDebounceTimer = null;

    // --- Detail-Link-Helfer (D8) ---
    function detailUrl(uid) {
        const params = new URLSearchParams();
        params.set('tx_feuerwehren_karte[controller]', 'Feuerwehr');
        params.set('tx_feuerwehren_karte[action]', 'show');
        params.set('tx_feuerwehren_karte[feuerwehr]', String(uid));
        return `${DETAIL_BASE_PATH}?${params.toString()}`;
    }

    function typeKey(typ) {
        return (typ === 'stuetzpunkt' || typ === 'ort') ? typ : '';
    }

    function formatAddress(item) {
        const city = [item.plz, item.ort].filter(Boolean).join(' ');
        return [item.strasse, city].filter(Boolean).join(', ');
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
        const typeColor = [
            'match', ['get', 'typ'],
            'stuetzpunkt', TYPE_STYLE.stuetzpunkt.color,
            'ort', TYPE_STYLE.ort.color,
            TYPE_STYLE[''].color
        ];
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
                { "id": "gemeinden-fill", "type": "fill", "source": "gemeinden", "layout": { "visibility": "visible" }, "paint": { "fill-color": "#66bb6a", "fill-opacity": 0.10 } },
                { "id": "gemeinden-outline", "type": "line", "source": "gemeinden", "layout": { "visibility": "visible" }, "paint": { "line-color": "#2e7d32", "line-width": 1 } },
                { "id": "kbm-fill", "type": "fill", "source": "kbm", "layout": { "visibility": "none" }, "paint": { "fill-color": "#42a5f5", "fill-opacity": 0.08 } },
                { "id": "kbm-outline", "type": "line", "source": "kbm", "layout": { "visibility": "none" }, "paint": { "line-color": "#1e88e5", "line-width": 1 } },
                { "id": "kbi-fill", "type": "fill", "source": "kbi", "layout": { "visibility": "none" }, "paint": { "fill-color": "#7e57c2", "fill-opacity": 0.06 } },
                { "id": "kbi-outline", "type": "line", "source": "kbi", "layout": { "visibility": "none" }, "paint": { "line-color": "#6a1b9a", "line-width": 1 } },
                { "id": "feuerwehren", "type": "circle", "source": "feuerwehren", "paint": { "circle-color": typeColor, "circle-radius": 6, "circle-stroke-color": "#ffffff", "circle-stroke-width": 1, "circle-opacity": 0.9 } },
                { "id": "feuerwehren-highlight", "type": "circle", "source": "feuerwehren", "filter": ["==", ["get", "uid"], "__none__"], "paint": { "circle-color": "#ff6d00", "circle-radius": 9, "circle-stroke-color": "#ffffff", "circle-stroke-width": 2 } }
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

    function getSearchQuery() {
        const el = document.getElementById(searchInputId);
        return el ? el.value.trim() : '';
    }

    function getLayerToggles() {
        return [
            document.getElementById(layerGemeindenId),
            document.getElementById(layerKbmId),
            document.getElementById(layerKbiId),
        ];
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
                    properties: {
                        uid: String(it.uid),
                        name: it.name || '',
                        typ: typeKey(it.typ),
                        fahrzeuge: (it.fahrzeuge || []).join(', ')
                    },
                    geometry: { type: 'Point', coordinates: [Number(it.lon), Number(it.lat)] }
                }))
        };
    }

    // --- 5. SIDEBAR: LISTE, STATISTIK, DETAILKARTE ---
    function buildVehicleBadges(fahrzeuge) {
        const wrap = document.createElement('span');
        wrap.className = 'fw-item-badges d-flex flex-wrap gap-1 mt-2';

        const addBadge = (text) => {
            const badge = document.createElement('span');
            badge.className = 'fw-vehicle-badge';
            badge.textContent = text;
            wrap.appendChild(badge);
        };

        fahrzeuge.slice(0, 3).forEach(addBadge);
        if (fahrzeuge.length > 3) addBadge('+' + (fahrzeuge.length - 3));

        return wrap;
    }

    function renderList(items) {
        const listEl = document.getElementById(listContainerId);
        const emptyEl = document.getElementById(emptyStateId);

        if (emptyEl) emptyEl.classList.toggle('d-none', items.length > 0);
        if (!listEl) return;

        listEl.replaceChildren();

        items.forEach(it => {
            // DOM-Aufbau statt innerHTML-Stringinterpolation, damit Feuerwehr-Daten
            // (Name/Adresse aus der API) nicht ungefiltert als HTML interpretiert werden.
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'fw-item w-100 text-start';
            btn.dataset.id = String(it.uid);
            if (String(it.uid) === String(selectedUid)) btn.classList.add('active');

            const head = document.createElement('span');
            head.className = 'd-flex align-items-center justify-content-between gap-2';

            const title = document.createElement('span');
            title.className = 'd-flex align-items-center gap-2 text-truncate';
            const dot = document.createElement('span');
            dot.className = 'fw-dot fw-dot--' + (typeKey(it.typ) || 'unbekannt');
            const nameEl = document.createElement('span');
            nameEl.className = 'fw-semibold small text-truncate';
            nameEl.textContent = it.name || '';
            title.append(dot, nameEl);
            head.appendChild(title);

            // Mitgliederzahl nur zeigen, wenn im Backend gepflegt (0 = unbekannt).
            if (Number(it.mitglieder) > 0) {
                const members = document.createElement('span');
                members.className = 'small text-body-secondary flex-shrink-0';
                members.textContent = String(it.mitglieder);
                head.appendChild(members);
            }
            btn.appendChild(head);

            const address = formatAddress(it);
            if (address) {
                const addr = document.createElement('span');
                addr.className = 'fw-item-address d-block small text-body-secondary mt-1';
                addr.textContent = address;
                btn.appendChild(addr);
            }

            const fahrzeuge = it.fahrzeuge || [];
            if (fahrzeuge.length) btn.appendChild(buildVehicleBadges(fahrzeuge));

            btn.addEventListener('mouseenter', () => setHighlight(it.uid));
            btn.addEventListener('mouseleave', () => setHighlight(selectedUid));
            btn.addEventListener('click', () => selectStation(it.uid, true));

            listEl.appendChild(btn);
        });
    }

    function updateStats(items) {
        const countEl = document.getElementById(statCountId);
        if (countEl) countEl.textContent = String(items.length);

        const membersEl = document.getElementById(statMembersId);
        const membersWrap = document.getElementById(statMembersWrapId);
        if (!membersEl || !membersWrap) return;

        const sum = items.reduce((total, it) => total + (Number(it.mitglieder) || 0), 0);
        // Kachel bleibt verborgen, solange keine Mitgliederzahlen gepflegt sind —
        // lieber nichts anzeigen als eine Null als Aussage.
        membersWrap.classList.toggle('d-none', sum === 0);
        membersEl.textContent = sum.toLocaleString('de-DE');
    }

    function renderDetail(item) {
        const el = document.getElementById(detailContainerId);
        if (!el) return;

        el.replaceChildren();

        if (!item) {
            el.classList.add('d-none');
            return;
        }

        // Overlay-Karte unten rechts auf der Karte (Lovable FireStationsMap, Stand 9a112b2).
        const body = document.createElement('div');
        body.className = 'p-3';

        const top = document.createElement('div');
        top.className = 'd-flex align-items-start justify-content-between gap-2 mb-2';

        const heading = document.createElement('h3');
        heading.className = 'h5 fw-bold mb-0';
        heading.textContent = item.name || '';

        // Ohne gepflegten Typ kein leerer Badge-Platzhalter: Name rückt neben den Schließen-Button.
        const key = typeKey(item.typ);
        if (key) {
            const badge = document.createElement('span');
            badge.className = 'badge fw-type-badge fw-type-badge--' + key;
            badge.textContent = TYPE_STYLE[key].label;
            top.appendChild(badge);
            heading.classList.replace('mb-0', 'mb-2');
        } else {
            top.appendChild(heading);
        }

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'btn-close';
        close.setAttribute('aria-label', 'Detailansicht schließen');
        close.addEventListener('click', () => selectStation(null, false));
        top.appendChild(close);
        body.appendChild(top);
        if (key) body.appendChild(heading);

        const meta = document.createElement('div');
        meta.className = 'vstack gap-1 small text-body-secondary mb-3';
        const addMeta = (icon, text) => {
            if (!text) return;
            const span = document.createElement('span');
            span.className = 'd-flex align-items-start gap-2';
            const i = document.createElement('i');
            i.className = 'bi bi-' + icon + ' flex-shrink-0';
            i.setAttribute('aria-hidden', 'true');
            span.append(i, document.createTextNode(text));
            meta.appendChild(span);
        };
        // Mitglieder nur, wenn im Backend gepflegt — keine erfundenen Zahlen (D7).
        if (Number(item.mitglieder) > 0) addMeta('people', `${item.mitglieder} Mitglieder`);
        addMeta('geo-alt', formatAddress(item));
        addMeta('truck', (item.fahrzeuge || []).join(', '));
        body.appendChild(meta);

        const link = document.createElement('a');
        link.className = 'fw-detail-link btn btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-2';
        link.href = detailUrl(item.uid);
        const linkIcon = document.createElement('i');
        linkIcon.className = 'bi bi-box-arrow-up-right';
        linkIcon.setAttribute('aria-hidden', 'true');
        link.append(linkIcon, document.createTextNode('Details ansehen'));
        body.appendChild(link);

        el.appendChild(body);
        el.classList.remove('d-none');
    }

    function selectStation(uid, panMap) {
        const item = currentItems.find(it => String(it.uid) === String(uid)) || null;
        selectedUid = item ? String(item.uid) : null;

        const listEl = document.getElementById(listContainerId);
        if (listEl) {
            Array.from(listEl.querySelectorAll('.fw-item')).forEach(el => {
                el.classList.toggle('active', el.dataset.id === selectedUid);
            });
        }

        renderDetail(item);
        setHighlight(selectedUid);

        if (panMap && item && typeof item.lon === 'number' && typeof item.lat === 'number') {
            programmaticMove = true;
            map.easeTo({ center: [item.lon, item.lat], zoom: Math.max(map.getZoom(), 12) });
        }
    }

    function fitToItems(items) {
        const coords = items
            .filter(it => typeof it.lon === 'number' && typeof it.lat === 'number')
            .map(it => [it.lon, it.lat]);

        if (coords.length === 0) return;

        const bounds = coords.reduce(
            (acc, coord) => acc.extend(coord),
            new maplibregl.LngLatBounds(coords[0], coords[0])
        );

        programmaticMove = true;
        // Asymmetrisches Padding: oben links liegt der Klick-Hinweis, unten links
        // die Legende. Ohne den Zuschlag landen Treffer hinter diesen Overlays.
        map.fitBounds(bounds, {
            padding: { top: 80, right: 60, bottom: 130, left: 60 },
            maxZoom: 13,
            duration: 600
        });
    }

    // --- 6. FILTER-STATUS (Zähler + Reset) ---
    function updateFilterUi() {
        const activeLayers = getLayerToggles().filter(el => el && el.checked !== el.defaultChecked).length;
        const count = getSelectedFz().length + activeLayers;

        const badge = document.getElementById(filterCountId);
        if (badge) {
            badge.textContent = String(count);
            badge.classList.toggle('d-none', count === 0);
        }

        // Zugeklappter Filter: aktive Auswahl als Textzeile zeigen (Lovable „Aktiv: …“).
        const summary = document.getElementById(filterSummaryId);
        if (summary) {
            const names = [];
            const wrap = document.querySelector(fzWrapperSelector);
            if (wrap) {
                wrap.querySelectorAll('input[name="fz[]"]:checked').forEach(input => {
                    const label = wrap.querySelector(`label[for="${input.id}"]`);
                    names.push(label ? label.textContent.trim() : input.value);
                });
            }
            getLayerToggles().forEach(toggle => {
                if (toggle && toggle.checked !== toggle.defaultChecked) {
                    const label = document.querySelector(`label[for="${toggle.id}"]`);
                    const text = label ? label.textContent.trim() : toggle.id;
                    names.push(toggle.checked ? text : text + ' aus');
                }
            });
            summary.textContent = 'Aktiv: ' + names.join(', ');
            const filterBody = document.getElementById(filterBodyId);
            const open = filterBody ? filterBody.classList.contains('show') : true;
            summary.classList.toggle('d-none', count === 0 || open);
        }

        const reset = document.getElementById(filterResetId);
        if (reset) reset.classList.toggle('d-none', count === 0 && getSearchQuery() === '');

        const scope = document.getElementById(searchScopeId);
        if (scope) scope.classList.toggle('d-none', getSearchQuery() === '');

        getLayerToggles().forEach((el, index) => {
            const name = ['gemeinden', 'kbm', 'kbi'][index];
            const legend = document.querySelector(`[data-legend-layer="${name}"]`);
            if (legend) legend.classList.toggle('d-none', !(el && el.checked));
        });

        // Ohne Typ-Labels (keine Feuerwehr eingestuft) nur zeigen, wenn ein Layer aktiv ist.
        const legendBox = document.querySelector('[data-legend-box]');
        if (legendBox && !legendBox.querySelector('.fw-dot')) {
            legendBox.classList.toggle('d-none', !legendBox.querySelector('[data-legend-layer]:not(.d-none)'));
        }
    }

    function resetFilters() {
        const wrap = document.querySelector(fzWrapperSelector);
        if (wrap) {
            wrap.querySelectorAll('input[name="fz[]"]:checked').forEach(el => { el.checked = false; });
        }

        getLayerToggles().forEach(el => {
            if (el && el.checked !== el.defaultChecked) {
                el.checked = el.defaultChecked;
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        const search = document.getElementById(searchInputId);
        if (search) search.value = '';

        updateFilterUi();
        loadData();
    }

    // --- 7. HAUPT-LADEFUNKTION ---
    async function loadData() {
        const query = getSearchQuery();
        const bounds = map.getBounds();
        // KORREKTUR: `toBBoxString` manuell und korrekt erstellen.
        const bbox = `${bounds.getWest()},${bounds.getSouth()},${bounds.getEast()},${bounds.getNorth()}`;

        const params = new URLSearchParams({
            bbox: bbox,
            mode: getMode(),
            '_': Date.now()
        });
        getSelectedFz().forEach(v => params.append('f[]', v));
        // Bei gesetztem q sucht die API landkreisweit und ignoriert die bbox.
        if (query !== '') params.set('q', query);

        try {
            const res = await fetch(`${API_SEARCH}?${params.toString()}`);
            if (!res.ok) throw new Error(`Search API request failed`);
            const data = await res.json();
            const items = Array.isArray(data.items) ? data.items : [];

            currentItems = items;

            // Auswahl fallenlassen, wenn sie nicht mehr im Ergebnis enthalten ist.
            if (selectedUid && !items.some(it => String(it.uid) === selectedUid)) {
                selectedUid = null;
                renderDetail(null);
            }

            renderList(items);
            updateStats(items);
            updateFilterUi();

            if (map.getSource('feuerwehren')) map.getSource('feuerwehren').setData(itemsToGeoJSON(items));

            // Nur bei einem neuen Suchbegriff nachzoomen — sonst würde jedes
            // zoomend-Reload den Ausschnitt erneut verschieben.
            if (query !== lastFittedQuery) {
                lastFittedQuery = query;
                if (query !== '') fitToItems(items);
            }
        } catch (e) {
            console.error('Data-Load failed', e);
        }
    }

    // --- 8. EVENT-HANDLER ---
    function setVis(layerId, visible) {
        if (map.getLayer(layerId)) map.setLayoutProperty(layerId, 'visibility', visible ? 'visible' : 'none');
    }

    // Wendet die aktuellen Checkbox-Zustände (Gemeinden/KBM/KBI) auf die Kartenlayer an.
    // Wird initial in wireEvents() und erneut nach einem Theme-bedingten setStyle() aufgerufen,
    // da setStyle() alle Layer (inkl. layout.visibility) auf den Style-Default zurücksetzt.
    function syncLayerVisibilityFromControls() {
        const [gEl, kbmEl, kbiEl] = getLayerToggles();
        if (gEl) { ['gemeinden-fill', 'gemeinden-outline'].forEach(l => setVis(l, gEl.checked)); }
        if (kbmEl) { ['kbm-fill', 'kbm-outline'].forEach(l => setVis(l, kbmEl.checked)); }
        if (kbiEl) { ['kbi-fill', 'kbi-outline'].forEach(l => setVis(l, kbiEl.checked)); }
    }

    function wireEvents() {
        const [gEl, kbmEl, kbiEl] = getLayerToggles();

        const gemeindenLayers = ['gemeinden-fill', 'gemeinden-outline'];
        const kbmLayers = ['kbm-fill', 'kbm-outline'];
        const kbiLayers = ['kbi-fill', 'kbi-outline'];

        if (gEl) {
            gEl.addEventListener('change', () => {
                gemeindenLayers.forEach(l => setVis(l, gEl.checked));
                updateFilterUi();
            });
        }

        // KBM und KBI bleiben gegenseitig exklusiv — übereinanderliegende Flächen
        // sind auf der Vektorkarte kaum noch auseinanderzuhalten.
        if (kbmEl && kbiEl) {
            kbmEl.addEventListener('change', () => {
                if (kbmEl.checked) {
                    kbiEl.checked = false;
                    kbiLayers.forEach(l => setVis(l, false));
                }
                kbmLayers.forEach(l => setVis(l, kbmEl.checked));
                updateFilterUi();
            });
            kbiEl.addEventListener('change', () => {
                if (kbiEl.checked) {
                    kbmEl.checked = false;
                    kbmLayers.forEach(l => setVis(l, false));
                }
                kbiLayers.forEach(l => setVis(l, kbiEl.checked));
                updateFilterUi();
            });
        }

        syncLayerVisibilityFromControls();

        // Popup für Flächen-Layer. Feuerwehr-Marker füllen stattdessen die Detailkarte —
        // fehlt die (kompakte Einbettung auf der Startseite), greift das Popup als Fallback.
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
            if (feature.layer && feature.layer.id === 'feuerwehren' && props.uid) {
                container.appendChild(document.createElement('br'));
                const link = document.createElement('a');
                link.href = detailUrl(props.uid);
                link.textContent = 'Details ansehen →';
                container.appendChild(link);
            }
            new maplibregl.Popup().setLngLat(e.lngLat).setDOMContent(container).addTo(map);
        };

        ['gemeinden-fill', 'kbm-fill', 'kbi-fill'].forEach(layerId => {
            map.on('click', layerId, createPopup);
        });

        map.on('click', 'feuerwehren', (e) => {
            const feature = e.features && e.features[0];
            if (!feature) return;

            if (document.getElementById(detailContainerId)) {
                selectStation(feature.properties.uid, false);
            } else {
                createPopup(e);
            }
        });

        // Hover-Effekte
        map.on('mouseenter', 'feuerwehren', (e) => {
            map.getCanvas().style.cursor = 'pointer';
            setHighlight(e.features[0].properties.uid);
        });
        map.on('mouseleave', 'feuerwehren', () => {
            map.getCanvas().style.cursor = '';
            setHighlight(selectedUid);
        });

        // Filter-Eingaben
        document.querySelectorAll('input[name="fz[]"], input[name="mode"]').forEach(el => {
            el.addEventListener('change', () => {
                updateFilterUi();
                loadData();
            });
        });

        const search = document.getElementById(searchInputId);
        if (search) {
            search.addEventListener('input', () => {
                updateFilterUi();
                window.clearTimeout(searchDebounceTimer);
                searchDebounceTimer = window.setTimeout(loadData, SEARCH_DEBOUNCE_MS);
            });
        }

        const reset = document.getElementById(filterResetId);
        if (reset) reset.addEventListener('click', resetFilters);

        // Zusammenfassung nur bei zugeklapptem Filter zeigen
        const filterBody = document.getElementById(filterBodyId);
        if (filterBody) {
            filterBody.addEventListener('show.bs.collapse', updateFilterUi);
            filterBody.addEventListener('hidden.bs.collapse', updateFilterUi);
        }

        // Nachladen bei manueller Kartenbewegung. Programmatische Bewegungen
        // (easeTo/fitBounds) werden übersprungen, moveend räumt das Flag auf.
        const reloadOnUserMove = () => { if (!programmaticMove) loadData(); };
        map.on('dragend', reloadOnUserMove);
        map.on('zoomend', reloadOnUserMove);
        map.on('moveend', () => { programmaticMove = false; });

        updateFilterUi();
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
                setHighlight(selectedUid);
            });
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
    }

    // --- 9. INITIALISIERUNG ---
    function init() {
        if (!document.getElementById(mapContainerId)) return;
        map = new maplibregl.Map({
            container: mapContainerId,
            center: [11.75, 48.46],
            zoom: 9,
            // Lokale Tiles (public/_vt) existieren erst ab Zoom 9 — darunter bliebe die Karte leer.
            minZoom: 9,
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
