(function () {
    function readConfig() {
        const el = document.getElementById('fw-map-config');
        if (!el) return {};
        try { return JSON.parse(el.textContent || '{}'); } catch(e){ return {}; }
    }
    const cfg = readConfig();

    const vtUrl = cfg.vtUrlTemplate || '/_vt/{z}/{x}/{y}.pbf';
    const apiSearchUrl = cfg.apiSearchUrl || '/?type=171001';
    const overlaysUrl  = cfg.overlaysUrl  || '/?type=171002';

    // Map init
    const map = new maplibregl.Map({
        container: 'map',
        center: [11.75, 48.46], // Landkreis Freising
        zoom: 10,
        style: {
            version: 8,
            glyphs: 'https://demotiles.maplibre.org/font/{fontstack}/{range}.pbf',
            sources: {
                osm: {
                    type: 'vector',
                    tiles: [vtUrl],
                    minzoom: 0,
                    maxzoom: 14
                }
            },
            layers: [
                { id:'land', type:'background', paint:{'background-color':'#f2efe9'} },
                { id:'water', type:'fill', source:'osm', 'source-layer':'water', paint:{'fill-color':'#a0c8f0'} },
                { id:'boundary', type:'line', source:'osm', 'source-layer':'boundary', paint:{'line-color':'#888','line-width':1} }
            ]
        }
    });
    map.addControl(new maplibregl.NavigationControl({visualizePitch:true}), 'top-right');

    const listEl = document.getElementById('fwList');

    map.on('load', async () => {
        // Datenquelle + Marker-Layer
        map.addSource('fw', { type:'geojson', data:{ type:'FeatureCollection', features:[] }, promoteId: 'uid' });
        map.addLayer({
            id:'fw-circles', type:'circle', source:'fw',
            paint:{
                'circle-radius': 6,
                'circle-stroke-width': 2,
                'circle-stroke-color': '#1e88e5',
                'circle-color': '#1e88e5',
                'circle-opacity': [
                    'case',
                    ['boolean', ['feature-state', 'hover'], false],
                    1, 0.8
                ]
            }
        });

        await loadOverlays();   // baut Gemeinden/KBM/KBI mit Turf.union
        await loadData();       // initiale Marker + Liste

        map.on('moveend', loadData);
        document.querySelectorAll('input[name="fz"], input[name="mode"]').forEach(el => {
            el.addEventListener('change', loadData);
        });

        document.getElementById('toggleGemeinden')?.addEventListener('change', e => {
            toggleLayerVisibility(['gemeinden-fill','gemeinden-line'], e.target.checked);
        });
        document.getElementById('toggleKBM')?.addEventListener('change', e => {
            toggleLayerVisibility(['kbm-fill','kbm-line'], e.target.checked);
        });
        document.getElementById('toggleKBI')?.addEventListener('change', e => {
            toggleLayerVisibility(['kbi-fill','kbi-line'], e.target.checked);
        });

        // Hover (Karte → Liste)
        let hoveredId = null;
        map.on('mousemove', 'fw-circles', ev => {
            const f = ev.features && ev.features[0];
            if (!f) return;
            if (hoveredId !== null) map.setFeatureState({source:'fw', id:hoveredId}, {hover:false});
            hoveredId = f.id;
            map.setFeatureState({source:'fw', id:hoveredId}, {hover:true});
            map.getCanvas().style.cursor = 'pointer';
            softenList(String(hoveredId));
        });
        map.on('mouseleave', 'fw-circles', () => {
            if (hoveredId !== null) map.setFeatureState({source:'fw', id:hoveredId}, {hover:false});
            hoveredId = null;
            map.getCanvas().style.cursor = '';
            softenList(null);
        });
        map.on('click', 'fw-circles', ev => {
            const f = ev.features && ev.features[0];
            if (!f) return;
            const p = f.properties;
            new maplibregl.Popup({closeButton:true})
                .setLngLat(ev.lngLat)
                .setHTML(
                    `<strong>${p.name}</strong><br>${p.strasse || ''}, ${p.plz || ''} ${p.ort || ''}` +
                    `<br><em>${(p.fahrzeuge||'').split('|').filter(Boolean).join(', ')}</em>`
                )
                .addTo(map);
        });
    });

    function toggleLayerVisibility(ids, visible) {
        ids.forEach(id => {
            if (map.getLayer(id)) {
                map.setLayoutProperty(id, 'visibility', visible ? 'visible' : 'none');
            }
        });
    }

    function softenList(keepId) {
        Array.from(listEl.querySelectorAll('.fw-item')).forEach(li => {
            li.classList.toggle('dim', keepId !== null && li.dataset.id !== String(keepId));
        });
    }

    async function loadData() {
        const b = map.getBounds();
        const bbox = [b.getWest(), b.getSouth(), b.getEast(), b.getNorth()].join(',');
        const mode = (document.querySelector('input[name="mode"]:checked')?.value || 'and');
        const fz = Array.from(document.querySelectorAll('input[name="fz"]:checked'))
            .map(i => 'f[]=' + encodeURIComponent(i.value))
            .join('&');

        const url = apiSearchUrl + '&bbox=' + encodeURIComponent(bbox) + '&mode=' + mode + (fz ? '&' + fz : '');
        let payload = { items: [] };
        try {
            const res = await fetch(url); payload = await res.json();
        } catch (e) { /* noop */ }

        // Liste + GeoJSON bauen
        listEl.innerHTML = '';
        const feats = [];
        (payload.items || []).forEach(it => {
            const li = document.createElement('div');
            li.className = 'fw-item';
            li.dataset.id = String(it.uid);
            li.innerHTML =
                `<strong>${it.name}</strong><br>` +
                `<span class="legend">${it.strasse || ''}, ${it.plz || ''} ${it.ort || ''}</span><br>` +
                `<em>${(it.fahrzeuge||[]).join(', ')}</em>`;

            li.addEventListener('mouseenter', () => {
                try { map.setFeatureState({source:'fw', id:Number(it.uid)}, {hover:true}); } catch(e){}
                softenList(String(it.uid));
            });
            li.addEventListener('mouseleave', () => {
                try { map.setFeatureState({source:'fw', id:Number(it.uid)}, {hover:false}); } catch(e){}
                softenList(null);
            });
            li.addEventListener('click', () => {
                map.easeTo({ center:[it.lon, it.lat], zoom: Math.max(map.getZoom(), 12) });
            });
            listEl.appendChild(li);

            feats.push({
                type: 'Feature',
                id: it.uid,
                properties: {
                    uid: it.uid,
                    name: it.name,
                    strasse: it.strasse || '',
                    plz: it.plz || '',
                    ort: it.ort || '',
                    fahrzeuge: (it.fahrzeuge||[]).join('|')
                },
                geometry: { type:'Point', coordinates:[it.lon, it.lat] }
            });
        });

        const src = map.getSource('fw');
        if (src) src.setData({ type:'FeatureCollection', features: feats });
    }

    async function loadOverlays() {
        let data = { gemeinden: [], kbm: {}, kbi: {} };
        try {
            const res = await fetch(overlaysUrl); data = await res.json();
        } catch (e) {}

        // Gemeinden als Quelle/Layers
        const gFeatures = [];
        (data.gemeinden || []).forEach(g => {
            try {
                // GeoJSON robust normalisieren
                let gj = g.geojson;
                if (!gj) return;
                if (typeof gj === 'string') gj = JSON.parse(gj);
                if (gj.type !== 'Feature' && gj.type !== 'FeatureCollection') {
                    gj = { type:'Feature', properties:{}, geometry: gj.geometry || gj };
                }
                const features = (gj.type === 'FeatureCollection') ? gj.features : [gj];
                features.forEach(f => {
                    // Eigenschaften mitschleifen
                    f.properties = Object.assign({}, f.properties || {}, { uid: g.uid, name: g.name });
                });
                gFeatures.push(...features);
            } catch(e) { /* invalid geojson -> skip */ }
        });

        map.addSource('gemeinden', { type:'geojson', data:{ type:'FeatureCollection', features: gFeatures } });
        map.addLayer({ id:'gemeinden-fill', type:'fill', source:'gemeinden', paint:{ 'fill-color':'#444', 'fill-opacity':0.05 } });
        map.addLayer({ id:'gemeinden-line', type:'line', source:'gemeinden', paint:{ 'line-color':'#444', 'line-width':1 } });

        // Index nach Gemeinde-UID aufbauen
        const gjIndex = {};
        gFeatures.forEach(f => { const id = f.properties && f.properties.uid; if (id != null) gjIndex[id] = f; });

        // === KBM-Union (Gemeinde → KBM) ===
        const kbmFeatures = [];
        Object.entries(data.kbm || {}).forEach(([kbmUid, gemeindeUids]) => {
            const geoms = (gemeindeUids || [])
                .map(uid => gjIndex[uid]?.geometry)
                .filter(Boolean)
                .map(geom => turf.feature(geom));

            if (!geoms.length) return;
            let merged = geoms[0];
            for (let i = 1; i < geoms.length; i++) {
                try { merged = turf.union(merged, geoms[i]); } catch(e) { /* continue best-effort */ }
            }
            if (merged && merged.geometry) {
                kbmFeatures.push({ type:'Feature', properties:{ uid: Number(kbmUid) }, geometry: merged.geometry });
            }
        });

        map.addSource('kbm', { type:'geojson', data:{ type:'FeatureCollection', features: kbmFeatures } });
        map.addLayer({ id:'kbm-fill', type:'fill', source:'kbm', paint:{ 'fill-color':'#2e7d32', 'fill-opacity':0.07 }, layout:{ visibility:'none' } });
        map.addLayer({ id:'kbm-line', type:'line', source:'kbm', paint:{ 'line-color':'#2e7d32', 'line-dasharray':[4,3], 'line-width':2 }, layout:{ visibility:'none' } });

        // === KBI-Union (KBM → KBI) ===
        const kbmByUid = {};
        kbmFeatures.forEach(f => { kbmByUid[String(f.properties.uid)] = f; });

        const kbiFeatures = [];
        Object.entries(data.kbi || {}).forEach(([kbiUid, kbmUids]) => {
            const geoms = (kbmUids || [])
                .map(uid => kbmByUid[String(uid)]?.geometry)
                .filter(Boolean)
                .map(geom => turf.feature(geom));

            if (!geoms.length) return;
            let merged = geoms[0];
            for (let i = 1; i < geoms.length; i++) {
                try { merged = turf.union(merged, geoms[i]); } catch(e) { /* continue best-effort */ }
            }
            if (merged && merged.geometry) {
                kbiFeatures.push({ type:'Feature', properties:{ uid: Number(kbiUid) }, geometry: merged.geometry });
            }
        });

        map.addSource('kbi', { type:'geojson', data:{ type:'FeatureCollection', features: kbiFeatures } });
        map.addLayer({ id:'kbi-fill', type:'fill', source:'kbi', paint:{ 'fill-color':'#6a1b9a', 'fill-opacity':0.05 }, layout:{ visibility:'none' } });
        map.addLayer({ id:'kbi-line', type:'line', source:'kbi', paint:{ 'line-color':'#6a1b9a', 'line-dasharray':[2,2], 'line-width':2 }, layout:{ visibility:'none' } });
    }
})();
