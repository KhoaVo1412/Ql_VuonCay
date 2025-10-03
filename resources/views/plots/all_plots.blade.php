@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0 title-header">Lô Trồng</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Lô</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div id="map-modal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn" onclick="hideMapModal()">&times;</span>
        <div id="plantFilters" class="map-toolbar" style="width:100%;gap:.5rem;align-items:center;margin-bottom:.5rem;">
            <input id="searchPlant" class="form-control" type="text" placeholder="Tìm mã cây…" style="width:20% ">
            <select id="filterStatus" class="form-select" style="width:10% ">
                <option value="">Trạng Thái</option>
                <option value="Hoạt động">Hoạt động</option>
            </select>
            <select id="filterVariety" class="form-select" style="width:10% ">
                <option value="">Giống</option>
            </select>
            <select id="filterYear" class="form-select" style="width:10% ">
                <option value="">Năm Trồng</option>
            </select>
            <button id="btnZoomVisible" class="btn btn-success" type="button" style="width:5% "><i
                    class="fa-solid fa-magnifying-glass"></i></button>
            <button id="btnResetFilters" class="btn btn-danger" type="button" style="width:5% "><i
                    class="fa-solid fa-delete-left"></i></button>
            <span id="filterCount" style="margin-left:.5rem;color:#555;" style="width:5% "></span>
        </div>
        <div id="viewMap" style="height:740px"></div>

        {{-- <div id="viewMap" style="width:100%; height:740px;"></div> --}}
    </div>
</div>

<div id="loading-message" style="display:none;">Loading...</div>
<script>
    function showMapModal() {
        document.getElementById('map-modal').style.display = 'flex';
    }

    function hideMapModal() {
        document.getElementById('map-modal').style.display = 'none';
    }

    document.getElementById('map-modal').addEventListener('click', function(e) {
        if (e.target.id === 'map-modal') {
            hideMapModal();
        }
    });
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
          try { view && view.destroy(); } catch(e){}
          $('#map-modal').fadeOut();
        }
    });
</script>
<script>
    $(document).on('click', '.view-map', function () {
        const id_plot = $(this).data('id_plot');
        const allPlots = @json($all_plots);
        const apiKey   = @json($apikeys);
        const firstPlot = allPlots.find(p => p.id === id_plot);
        if (!firstPlot) { alert("Không tìm thấy plot!"); return; }
        $('#loading-message').fadeIn();
        $('#map-modal').fadeIn();
        if (window.__arcgisView) { try { window.__arcgisView.destroy(); } catch(e){} }
        const containerEl = document.getElementById('viewMap');
        if (containerEl) containerEl.innerHTML = "";
        require([
            "esri/WebMap",
            "esri/views/MapView",
            "esri/config",
            "esri/layers/GraphicsLayer",
            "esri/Graphic",
            "esri/geometry/Polygon",
            "esri/geometry/Point"
        ], function (WebMap, MapView, esriConfig, GraphicsLayer, Graphic, Polygon, Point) {
            esriConfig.apiKey = apiKey;
            ["/imgs/treemapp.png","/imgs/treemap.png","/imgs/cay_benh.png","/imgs/cay_nga.png","/imgs/tree-dead.png"]
              .forEach(src => { const i = new Image(); i.src = src; });
            const map = new WebMap({ portalItem: { id: "5ec8ed782e5146d1909f76e14e293059" } });
            const view = new MapView({
                container: "viewMap",
                map,
                zoom: 12,
                center: [107.3877, 10.6695]
            });
            window.__arcgisView = view;
            // ====== STATE ======
            let currentPlotId = null;
            const plotGfxMap = new Map();
            const plantCountByPlot = new Map();
            const loadedPlots = new Set();
            let currentPlotGraphic = null;
            // ====== Quản lý popup ======
            view.popup.autoOpenEnabled = false;
            map.when(() => {
                const allow = new Set(["treeLayer","highlightLayer","otherPlotsLayer"]);
                map.allLayers.forEach(layer => {
                    if (!allow.has(layer.id) && "popupEnabled" in layer) layer.popupEnabled = false;
                    if ("allSublayers" in layer && layer.allSublayers) {
                        layer.allSublayers.forEach(sl => { if ("popupEnabled" in sl) sl.popupEnabled = false; });
                    }
                });
            });
            // ====== Layers ======
            const otherPlotsLayer = new GraphicsLayer({ id: "otherPlotsLayer" });
            const highlightLayer  = new GraphicsLayer({ id: "highlightLayer"  });
            const treeLayer       = new GraphicsLayer({ id: "treeLayer"       });
            map.addMany([otherPlotsLayer, highlightLayer, treeLayer]);
            map.layers.reorder(otherPlotsLayer,  map.layers.length - 3);
            map.layers.reorder(highlightLayer,   map.layers.length - 2);
            map.layers.reorder(treeLayer,        map.layers.length - 1);
            // ====== ICONS & helpers ======
            const ICONS = {
                normal:  "/imgs/treemapp.png",
                disease: "/imgs/cay_benh.png",
                broken:  "/imgs/cay_nga.png",
                dead:    "/imgs/tree-dead.png",
                default: "/imgs/treemap.png",
            };
            function treeSymbol(sizePx = 26, url = ICONS.default) {
                return { type: "picture-marker", url, width: `${sizePx}px`, height: `${sizePx}px`, yoffset: `${Math.round(sizePx*0.35)}px` };
            }
            function iconUrlByStatus(status) {
                const s = (status || "").trim().toLowerCase();
                const sNorm = s.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d');
                if (s.includes("sâu bệnh") || sNorm.includes("sau benh") || s.includes("bệnh") || sNorm.includes("benh")) {
                    return ICONS.disease;
                }
                if (s.includes("gãy") || sNorm.includes("gay") || sNorm.includes("gay do") || s.includes("đổ") || sNorm === "do") {
                    return ICONS.broken;
                }
                if (s.includes("chết") || sNorm.includes("chet")) {
                    return ICONS.dead;
                }
                return ICONS.normal;
            }
            function sizeByScale(scale) {
                if (scale > 120000) return 0;
                if (scale >  60000) return 18;
                if (scale >  30000) return 22;
                return 26;
            }
            // --- Hợp nhất hiển thị: theo lọc & theo scale ---
            function updateGraphicVisibility(g) {
                const a = g.attributes || {};
                const byFilter = a.__matchFilter !== false;
                const byScale  = a.__matchScale  !== false;
                g.visible = byFilter && byScale;
            }
            // ====== Utils plot ======
            function buildPolygonFromMapJs(mapJs) {
                const data = JSON.parse(mapJs);
                if (!(data.type === "FeatureCollection" && data.features.length)) return null;
                const f = data.features[0];
                if (f.geometry.type === "Polygon") {
                    return new Polygon({ rings: f.geometry.coordinates, spatialReference: { wkid: 4326 } });
                } else if (f.geometry.type === "MultiPolygon") {
                    return new Polygon({ rings: f.geometry.coordinates.flat(), spatialReference: { wkid: 4326 } });
                }
                return null;
            }
            function makePlotPopupTemplate() {
                return {
                    title: "Lô {plotCode}",
                    content: [
                        { type: "fields", fieldInfos: [
                            { fieldName: "plotCode",   label: "Mã lô" },
                            { fieldName: "plotName",   label: "Tên lô" },
                            { fieldName: "area",       label: "Diện tích", format: { places: 2 } },
                            { fieldName: "year",       label: "Năm trồng" },
                            { fieldName: "status",     label: "Hiện trạng" },
                            { fieldName: "plantCount", label: "Số cây" }
                        ]}
                    ]
                };
            }
            function addOtherPlotGraphic(p) {
                const geom = buildPolygonFromMapJs(p.mapJs);
                if (!geom) return;
                let props = {};
                try { props = JSON.parse(p.mapJs)?.features?.[0]?.properties || {}; } catch {}
                const g = new Graphic({
                    geometry: geom,
                    symbol: { type: "simple-fill", color: [255,255,255,0.08], outline: { color: [120,120,120,0.8], width: 1 } },
                    attributes: {
                        plotID: p.id,
                        plotCode: p.plotCode ?? props.Ma_lo ?? "",
                        plotName: p.plotName ?? props.Ten_lo ?? "",
                        area: p.plotArea ?? props.Dien_tich ?? null,
                        year: p.year ?? props.Nam_trong ?? null,
                        status: p.status ?? props.Hien_trang ?? "",
                        plantCount: plantCountByPlot.get(p.id) || 0
                    },
                    popupTemplate: makePlotPopupTemplate()
                });
                otherPlotsLayer.add(g);
                plotGfxMap.set(p.id, g);
            }
            function renderAllOtherPlots(excludeId = null) {
                otherPlotsLayer.removeAll();
                plotGfxMap.clear();
                for (const p of allPlots) {
                    if (!p.mapJs) continue;
                    if (excludeId && p.id === excludeId) continue;
                    addOtherPlotGraphic(p);
                }
            }

            // ====== Highlight plot ======
            async function setHighlightPlot(plotId, openPopupAt=null) {
                const p = allPlots.find(x => x.id === plotId);
                if (!p || !p.mapJs) return;
                currentPlotId = plotId;
                highlightLayer.removeAll();
                const geom = buildPolygonFromMapJs(p.mapJs);
                if (!geom) return;
                let props = {};
                try { props = JSON.parse(p.mapJs)?.features?.[0]?.properties || {}; } catch {}
                currentPlotGraphic = new Graphic({
                    geometry: geom,
                    symbol: { type: "simple-fill", color: [255,0,0,0.25], outline: { color: [255,255,255,1], width: 2.5 } },
                    attributes: {
                        plotID: p.id,
                        plotCode: p.plotCode ?? props.Ma_lo ?? "",
                        plotName: p.plotName ?? props.Ten_lo ?? "",
                        area: p.plotArea ?? props.Dien_tich ?? null,
                        year: p.year ?? props.Nam_trong ?? null,
                        status: p.status ?? props.Hien_trang ?? "",
                        plantCount: plantCountByPlot.get(p.id) || 0
                    },
                    popupTemplate: makePlotPopupTemplate()
                });
                highlightLayer.add(currentPlotGraphic);
                await view.goTo({ target: geom, zoom: 16 }, { duration: 300 });
                if (openPopupAt) {
                    view.popup.open({ features: [currentPlotGraphic], location: openPopupAt, updateLocationEnabled: true });
                }
                renderAllOtherPlots(plotId);
            }
            // ====== Load cây (mọi lô) ======
            function addPlantGraphic(pObj, plant) {
                const lat = +plant.lat, lng = +plant.lng;
                if (Number.isNaN(lat) || Number.isNaN(lng)) return;
                const pt  = new Point({ latitude: lat, longitude: lng, spatialReference: { wkid: 4326 } });
                const url = iconUrlByStatus(plant.statusTree);
                const initSize = sizeByScale(view.scale) || 26;
                const scaleOK  = sizeByScale(view.scale) > 0;
                const g = new Graphic({
                    geometry: pt,
                    symbol: treeSymbol(initSize, url),
                    attributes: {
                        plotID: pObj.id,
                        id: plant.id,
                        plantCode: plant.plantCode,
                        varietyName: plant.varietyName ?? `#${plant.varietyID}`,
                        year: plant.year,
                        statusTree: plant.statusTree,
                        lat, lng,
                        __matchFilter: true,
                        __matchScale:  scaleOK
                    },
                    popupTemplate: {
                        title: "{plantCode}",
                        content: [
                            { type: "fields", fieldInfos: [
                                { fieldName: "plantCode", label: "Mã Cây" },
                                { fieldName: "varietyName", label: "Giống" },
                                { fieldName: "year",        label: "Năm trồng" },
                                { fieldName: "statusTree",  label: "Trạng thái" },
                                { fieldName: "lat",         label: "Lat" },
                                { fieldName: "lng",         label: "Lng" },
                            ] }
                        ]
                    }
                });
                treeLayer.add(g);
                updateGraphicVisibility(g);

                const old = plantCountByPlot.get(pObj.id) || 0;
                plantCountByPlot.set(pObj.id, old + 1);
            }
            async function loadPlantsForPlot(pObj) {
                if (loadedPlots.has(pObj.id)) return;
                try {
                    const res = await fetch(`/plots/${pObj.id}/plants`, { headers: { 'Accept': 'application/json' }});
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const plants = await res.json();
                    if (Array.isArray(plants) && plants.length) {
                        plants.forEach(pl => addPlantGraphic(pObj, pl));
                        loadedPlots.add(pObj.id);

                        if (currentPlotGraphic && currentPlotGraphic.attributes.plotID === pObj.id) {
                            currentPlotGraphic.attributes.plantCount = plantCountByPlot.get(pObj.id);
                        }
                        const otherG = plotGfxMap.get(pObj.id);
                        if (otherG) otherG.attributes.plantCount = plantCountByPlot.get(pObj.id);
                    }
                } catch (e) {
                    console.warn('Load plants failed for plot', pObj.id, e);
                }
            }
            // Giới hạn đồng thời khi load tất cả
            async function preloadAllPlants(concurrency = 4) {
                const queue = allPlots.filter(p => p.mapJs);
                let idx = 0;
                async function worker() {
                    while (idx < queue.length) {
                        const p = queue[idx++];
                        await loadPlantsForPlot(p);
                    }
                }
                const workers = Array.from({length: concurrency}, worker);
                await Promise.all(workers);
            }
            // ====== Lọc / Tìm kiếm (không ghi đè trực tiếp visible) ======
            function stripVN(s=''){ 
                return s.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/gi,'d').toLowerCase();
            }
            function populateFilterOptionsFromLayer(){
                const selVar = document.getElementById('filterVariety');
                const selYear= document.getElementById('filterYear');
                if (!selVar || !selYear) return;
                const varieties = new Set();
                const years = new Set();
                treeLayer.graphics.forEach(g=>{
                    const a = g.attributes || {};
                    if (a.varietyName) varieties.add(a.varietyName);
                    if (a.year!=null)  years.add(a.year);
                });
                selVar.innerHTML  = `<option value="">Giống</option>` + [...varieties].sort().map(v=>`<option>${v}</option>`).join('');
                selYear.innerHTML = `<option value="">Năm trồng</option>` + [...years].sort((a,b)=>a-b).map(y=>`<option>${y}</option>`).join('');
                const toolbar = document.getElementById('plantFilters');
                if (toolbar) toolbar.style.display = 'flex';
            }
            function goToVisibleTrees(duration=400){
                const geoms = treeLayer.graphics.filter(g=>g.visible).map(g=>g.geometry);
                if (geoms.length) view.goTo(geoms, { duration });
            }
            function applyFiltersGoTo(){
                const sQ = document.getElementById('searchPlant');
                const sS = document.getElementById('filterStatus');
                const sV = document.getElementById('filterVariety');
                const sY = document.getElementById('filterYear');
                const cL = document.getElementById('filterCount');
                const q  = sQ ? stripVN(sQ.value.trim()) : "";
                const st = sS ? sS.value : "";
                const va = sV ? sV.value : "";
                const yr = sY ? sY.value : "";
                let visibleCount = 0;
                treeLayer.graphics.forEach(g=>{
                    const a = g.attributes || {};
                    let ok = true;
                    if (q)  ok = stripVN(a.plantCode||'').includes(q);
                    if (ok && st) ok = (a.statusTree||'') === st;
                    if (ok && va) ok = (a.varietyName||'') === va;
                    if (ok && yr) ok = String(a.year||'') === String(yr);
                    a.__matchFilter = ok;
                    g.attributes = a;
                    updateGraphicVisibility(g);
                    if (g.visible) visibleCount++;
                });
                if (cL) cL.textContent = `${visibleCount} / ${treeLayer.graphics.length} cây`;
                if (visibleCount > 0) goToVisibleTrees(350);
            }
            let filtersWired = false;
            function wireFilterEvents(){
                if (filtersWired) return;
                const el = id => document.getElementById(id);
                if (!el('searchPlant')) return;
                let timer;
                const debouncedRun = ()=>{ clearTimeout(timer); timer=setTimeout(applyFiltersGoTo, 180); };
                el('searchPlant').addEventListener('input', debouncedRun);
                el('filterStatus').addEventListener('change', applyFiltersGoTo);
                el('filterVariety').addEventListener('change', applyFiltersGoTo);
                el('filterYear').addEventListener('change', applyFiltersGoTo);
                el('btnResetFilters').addEventListener('click', ()=>{
                    el('searchPlant').value = '';
                    el('filterStatus').value = '';
                    el('filterVariety').value = '';
                    el('filterYear').value = '';
                    applyFiltersGoTo();
                });
                el('btnZoomVisible').addEventListener('click', ()=> goToVisibleTrees(300));
                filtersWired = true;
            }
            // ====== INIT ======
            view.when(async () => {
                renderAllOtherPlots(firstPlot.id);
                await setHighlightPlot(firstPlot.id);
                await loadPlantsForPlot(firstPlot);
                preloadAllPlants(4).then(()=>{
                    populateFilterOptionsFromLayer();
                    applyFiltersGoTo();
                });
                // Không ghi đè visible ở đây nữa – chỉ cập nhật cờ scale + size
                view.watch("scale", () => {
                    const size = sizeByScale(view.scale);
                    const byScale = size > 0;
                    treeLayer.graphics.forEach(g => {
                        if (g.symbol?.type === "picture-marker" && byScale) {
                            g.symbol.width  = `${size}px`;
                            g.symbol.height = `${size}px`;
                            g.symbol.yoffset = `${Math.round(size * 0.35)}px`;
                        }
                        const a = g.attributes || {};
                        a.__matchScale = byScale;
                        g.attributes = a;
                        updateGraphicVisibility(g);
                    });
                });
                wireFilterEvents();
                $('#loading-message').fadeOut();
            });
            // ====== CLICK ======
            view.on("immediate-click", async (event) => {
                let { results } = await view.hitTest(event, { include: [treeLayer] });
                if (results?.length) {
                    const g = results[0].graphic;
                    await view.goTo({ target: g.geometry, zoom: 18 }, { duration: 250 });
                    view.popup.open({ features: [g], location: g.geometry, updateLocationEnabled: true });
                    return;
                }
                ({ results } = await view.hitTest(event, { include: [highlightLayer] }));
                if (results?.length) {
                    const g = results[0].graphic;
                    await view.goTo({ target: g.geometry, zoom: 16 }, { duration: 250 });
                    view.popup.open({ features: [g], location: event.mapPoint, updateLocationEnabled: true });
                    return;
                }
                ({ results } = await view.hitTest(event, { include: [otherPlotsLayer] }));
                if (results?.length) {
                    const g = results[0].graphic;
                    const pid = g.attributes.plotID;
                    await setHighlightPlot(pid, event.mapPoint);
                    const pObj = allPlots.find(x => x.id === pid);
                    if (pObj) loadPlantsForPlot(pObj);
                    return;
                }
                view.popup.close();
            });
            // Hover: pointer
            view.on("pointer-move", async (evt) => {
                const t = await view.hitTest(evt, { include: [treeLayer, highlightLayer, otherPlotsLayer] });
                view.container.style.cursor = t?.results?.length ? "pointer" : "default";
            });
        });
    });
</script>

{{-- <script>
    $(document).on('click', '.view-map', function () {
        const id_plot = $(this).data('id_plot');
        const allPlots = @json($all_plots);
        const apiKey   = @json($apikeys);
        const firstPlot = allPlots.find(p => p.id === id_plot);
        if (!firstPlot) { alert("Không tìm thấy plot!"); return; }
        $('#loading-message').fadeIn();
        $('#map-modal').fadeIn();
        if (window.__arcgisView) { try { window.__arcgisView.destroy(); } catch(e){} }
        const containerEl = document.getElementById('viewMap');
        if (containerEl) containerEl.innerHTML = "";
        require([
            "esri/WebMap",
            "esri/views/MapView",
            "esri/config",
            "esri/layers/GraphicsLayer",
            "esri/Graphic",
            "esri/geometry/Polygon",
            "esri/geometry/Point"
        ], function (WebMap, MapView, esriConfig, GraphicsLayer, Graphic, Polygon, Point) {
            esriConfig.apiKey = apiKey;
            // === (NEW) Preload icons để tránh nháy ===
            ["/imgs/treemap.png","/imgs/tree-spray.png","/imgs/tree-broken.png","/imgs/tree-dead.png"]
              .forEach(src => { const i = new Image(); i.src = src; });
            const map = new WebMap({ portalItem: { id: "5ec8ed782e5146d1909f76e14e293059" } });
            const view = new MapView({
                container: "viewMap",
                map,
                zoom: 12,
                center: [107.3877, 10.6695]
            });
            window.__arcgisView = view;
            // ====== STATE ======
            let currentPlotId = null;
            const plotGfxMap = new Map();
            const plantCountByPlot = new Map(); 
            const loadedPlots = new Set();
            let currentPlotGraphic = null;
            // ====== Quản lý popup ======
            view.popup.autoOpenEnabled = false;
            map.when(() => {
                const allow = new Set(["treeLayer","highlightLayer","otherPlotsLayer"]);
                map.allLayers.forEach(layer => {
                    if (!allow.has(layer.id) && "popupEnabled" in layer) layer.popupEnabled = false;
                    if ("allSublayers" in layer && layer.allSublayers) {
                        layer.allSublayers.forEach(sl => { if ("popupEnabled" in sl) sl.popupEnabled = false; });
                    }
                });
            });
            // ====== Layers ======
            const otherPlotsLayer = new GraphicsLayer({ id: "otherPlotsLayer" }); // lô khác
            const highlightLayer  = new GraphicsLayer({ id: "highlightLayer"  }); // lô đang chọn
            const treeLayer       = new GraphicsLayer({ id: "treeLayer"       }); // tất cả cây (mọi lô)
            map.addMany([otherPlotsLayer, highlightLayer, treeLayer]);
            map.layers.reorder(otherPlotsLayer,  map.layers.length - 3);
            map.layers.reorder(highlightLayer,   map.layers.length - 2);
            map.layers.reorder(treeLayer,        map.layers.length - 1);
            const ICONS = {
                normal:  "/imgs/treemapp.png",  // tốt
                disease: "/imgs/cay_benh.png",  // sâu bệnh / bệnh -> icon bình phun
                broken:  "/imgs/cay_nga.png",   // gãy đổ
                default: "/imgs/treemap.png",
            };
            function treeSymbol(sizePx = 25, url = ICONS.default) {
                return { type: "picture-marker", url, width: `${sizePx}px`, height: `${sizePx}px`, yoffset: `${Math.round(sizePx*0.35)}px` };
            }
            // (UPDATED) Ánh xạ trạng thái → icon
            function iconUrlByStatus(status) {
                const s = (status || "").trim().toLowerCase();
                // bản không dấu để bắt các cách gõ
                const sNorm = s.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d');
                // sâu bệnh / bệnh
                if (s.includes("sâu bệnh") || sNorm.includes("sau benh") || s.includes("bệnh") || sNorm.includes("benh")) {
                    return ICONS.disease;
                }
                // gãy đổ (bắt "gãy", "gay", "gay do", "đổ", "do")
                if (s.includes("gãy") || sNorm.includes("gay") || sNorm.includes("gay do") || s.includes("đổ") || sNorm === "do") {
                    return ICONS.broken;
                }
                return ICONS.normal;
            }
            function sizeByScale(scale) {
                if (scale > 120000) return 0;
                if (scale >  60000) return 14;
                if (scale >  30000) return 18;
                return 22;
            }
            // ====== Utils plot ======
            function buildPolygonFromMapJs(mapJs) {
                const data = JSON.parse(mapJs);
                if (!(data.type === "FeatureCollection" && data.features.length)) return null;
                const f = data.features[0];
                if (f.geometry.type === "Polygon") {
                    return new Polygon({ rings: f.geometry.coordinates, spatialReference: { wkid: 4326 } });
                } else if (f.geometry.type === "MultiPolygon") {
                    return new Polygon({ rings: f.geometry.coordinates.flat(), spatialReference: { wkid: 4326 } });
                }
                return null;
            }
            function makePlotPopupTemplate() {
                return {
                    title: "Lô {plotCode}",
                    content: [
                        { type: "fields", fieldInfos: [
                            { fieldName: "plotName",   label: "Tên lô" },
                            { fieldName: "area",       label: "Diện tích", format: { places: 2 } },
                            { fieldName: "year",       label: "Năm trồng" },
                            { fieldName: "status",     label: "Hiện trạng" },
                            { fieldName: "plantCount", label: "Số cây" }
                        ]}
                    ]
                };
            }
            function addOtherPlotGraphic(p) {
                const geom = buildPolygonFromMapJs(p.mapJs);
                if (!geom) return;
                let props = {};
                try { props = JSON.parse(p.mapJs)?.features?.[0]?.properties || {}; } catch {}
                const g = new Graphic({
                    geometry: geom,
                    symbol: { type: "simple-fill", color: [255,255,255,0.08], outline: { color: [120,120,120,0.8], width: 1 } },
                    attributes: {
                        plotID: p.id,
                        plotCode: p.plotCode ?? props.Ten_lo ?? "",
                        plotName: p.plotName ?? props.Ten_lo ?? "",
                        area: p.plotArea ?? props.Dien_tich ?? null,
                        year: p.year ?? props.Nam_trong ?? null,
                        status: p.status ?? props.Hien_trang ?? "",
                        plantCount: plantCountByPlot.get(p.id) || 0
                    },
                    popupTemplate: makePlotPopupTemplate()
                });
                otherPlotsLayer.add(g);
                plotGfxMap.set(p.id, g);
            }
            function renderAllOtherPlots(excludeId = null) {
                otherPlotsLayer.removeAll();
                plotGfxMap.clear();
                for (const p of allPlots) {
                    if (!p.mapJs) continue;
                    if (excludeId && p.id === excludeId) continue;
                    addOtherPlotGraphic(p);
                }
            }
            // ====== Highlight plot ======
            async function setHighlightPlot(plotId, openPopupAt=null) {
                const p = allPlots.find(x => x.id === plotId);
                if (!p || !p.mapJs) return;
                currentPlotId = plotId;
                highlightLayer.removeAll();
                const geom = buildPolygonFromMapJs(p.mapJs);
                if (!geom) return;

                let props = {};
                try { props = JSON.parse(p.mapJs)?.features?.[0]?.properties || {}; } catch {}
                currentPlotGraphic = new Graphic({
                    geometry: geom,
                    symbol: { type: "simple-fill", color: [255,0,0,0.25], outline: { color: [255,255,255,1], width: 2.5 } },
                    attributes: {
                        plotID: p.id,
                        plotCode: p.plotCode ?? props.Ten_lo ?? "",
                        plotName: p.plotName ?? props.Ten_lo ?? "",
                        area: p.plotArea ?? props.Dien_tich ?? null,
                        year: p.year ?? props.Nam_trong ?? null,
                        status: p.status ?? props.Hien_trang ?? "",
                        plantCount: plantCountByPlot.get(p.id) || 0
                    },
                    popupTemplate: makePlotPopupTemplate()
                });
                highlightLayer.add(currentPlotGraphic);// di chuyển đến lô
                await view.goTo({ target: geom, zoom: 16 }, { duration: 300 });

                if (openPopupAt) {
                    view.popup.open({ features: [currentPlotGraphic], location: openPopupAt, updateLocationEnabled: true });
                }
                renderAllOtherPlots(plotId);// loại lô đang chọn khỏi lớp "khác"
            }
            // ====== Load cây (mọi lô) ======
            function addPlantGraphic(pObj, plant) {
                const lat = +plant.lat, lng = +plant.lng;
                if (Number.isNaN(lat) || Number.isNaN(lng)) return;
                const pt  = new Point({ latitude: lat, longitude: lng, spatialReference: { wkid: 4326 } });
                const url = iconUrlByStatus(plant.statusTree);
                const initSize = sizeByScale(view.scale) || 18;
                const g = new Graphic({
                    geometry: pt,
                    symbol: treeSymbol(initSize, url),
                    attributes: {
                        plotID: pObj.id,
                        id: plant.id,
                        plantCode: plant.plantCode,
                        varietyName: plant.varietyName ?? `#${plant.varietyID}`,
                        year: plant.year,
                        statusTree: plant.statusTree,
                        lat, lng
                    },
                    popupTemplate: {
                        title: "{plantCode}",
                        content: [
                            { type: "fields", fieldInfos: [
                                { fieldName: "varietyName", label: "Giống" },
                                { fieldName: "year",        label: "Năm trồng" },
                                { fieldName: "statusTree",  label: "Trạng thái" },
                                { fieldName: "lat",         label: "Lat" },
                                { fieldName: "lng",         label: "Lng" },
                            ] }
                        ]
                    }
                });
                treeLayer.add(g);
                const old = plantCountByPlot.get(pObj.id) || 0;
                plantCountByPlot.set(pObj.id, old + 1);
            }
            async function loadPlantsForPlot(pObj) {
                if (loadedPlots.has(pObj.id)) return;
                try {
                    const res = await fetch(`/plots/${pObj.id}/plants`, { headers: { 'Accept': 'application/json' }});
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const plants = await res.json();
                    if (Array.isArray(plants) && plants.length) {
                        plants.forEach(pl => addPlantGraphic(pObj, pl));
                        loadedPlots.add(pObj.id);
                        // cập nhật count cho popup lô đang chọn (nếu trùng)
                        if (currentPlotGraphic && currentPlotGraphic.attributes.plotID === pObj.id) {
                            currentPlotGraphic.attributes.plantCount = plantCountByPlot.get(pObj.id);
                        }
                        // cập nhật count cho graphic ở otherPlotsLayer (nếu có)
                        const otherG = plotGfxMap.get(pObj.id);
                        if (otherG) otherG.attributes.plantCount = plantCountByPlot.get(pObj.id);
                    }
                } catch (e) {
                    console.warn('Load plants failed for plot', pObj.id, e);
                }
            }
            // Giới hạn đồng thời khi load tất cả
            async function preloadAllPlants(concurrency = 4) {
                const queue = allPlots.filter(p => p.mapJs);
                let idx = 0;
                async function worker() {
                    while (idx < queue.length) {
                        const p = queue[idx++];
                        await loadPlantsForPlot(p);
                    }
                }
                const workers = Array.from({length: concurrency}, worker);
                await Promise.all(workers);
            }
            // ====== Lọc / Tìm kiếm (goTo tới kết quả) ======
            function stripVN(s=''){ 
                return s.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/gi,'d').toLowerCase();
            }
            function populateFilterOptionsFromLayer(){
                const selVar = document.getElementById('filterVariety');
                const selYear= document.getElementById('filterYear');
                if (!selVar || !selYear) return;
                const varieties = new Set();
                const years = new Set();
                treeLayer.graphics.forEach(g=>{
                    const a = g.attributes || {};
                    if (a.varietyName) varieties.add(a.varietyName);
                    if (a.year!=null)  years.add(a.year);
                });
                selVar.innerHTML  = `<option value="">Giống (tất cả)</option>` + [...varieties].sort().map(v=>`<option>${v}</option>`).join('');
                selYear.innerHTML = `<option value="">Năm trồng (tất cả)</option>` + [...years].sort((a,b)=>a-b).map(y=>`<option>${y}</option>`).join('');
                const toolbar = document.getElementById('plantFilters');
                if (toolbar) toolbar.style.display = 'flex';
            }
            function goToVisibleTrees(duration=400){
                const geoms = treeLayer.graphics.filter(g=>g.visible).map(g=>g.geometry);
                if (geoms.length) view.goTo(geoms, { duration });
            }
            function applyFiltersGoTo(){
                const sQ = document.getElementById('searchPlant');
                const sS = document.getElementById('filterStatus');
                const sV = document.getElementById('filterVariety');
                const sY = document.getElementById('filterYear');
                const cL = document.getElementById('filterCount');
                const q  = sQ ? stripVN(sQ.value.trim()) : "";
                const st = sS ? sS.value : "";
                const va = sV ? sV.value : "";
                const yr = sY ? sY.value : "";
                let visibleCount = 0;
                treeLayer.graphics.forEach(g=>{
                    const a = g.attributes || {};
                    let ok = true;
                    if (q)  ok = stripVN(a.plantCode||'').includes(q);
                    if (ok && st) ok = (a.statusTree||'') === st;        // so sánh chính xác chuỗi
                    if (ok && va) ok = (a.varietyName||'') === va;
                    if (ok && yr) ok = String(a.year||'') === String(yr);

                    g.visible = ok;
                    if (ok) visibleCount++;
                });
                if (cL) cL.textContent = `${visibleCount} / ${treeLayer.graphics.length} cây`;
                if (visibleCount > 0) goToVisibleTrees(350);
            }
            let filtersWired = false;
            function wireFilterEvents(){
                if (filtersWired) return;
                const el = id => document.getElementById(id);
                if (!el('searchPlant')) return;
                let timer;
                const debouncedRun = ()=>{ clearTimeout(timer); timer=setTimeout(applyFiltersGoTo, 180); };
                el('searchPlant').addEventListener('input', debouncedRun);
                el('filterStatus').addEventListener('change', applyFiltersGoTo);
                el('filterVariety').addEventListener('change', applyFiltersGoTo);
                el('filterYear').addEventListener('change', applyFiltersGoTo);
                el('btnResetFilters').addEventListener('click', ()=>{
                    el('searchPlant').value = '';
                    el('filterStatus').value = '';
                    el('filterVariety').value = '';
                    el('filterYear').value = '';
                    applyFiltersGoTo();
                });
                el('btnZoomVisible').addEventListener('click', ()=> goToVisibleTrees(300));
                filtersWired = true;
            }
            // ====== INIT ======
            view.when(async () => {
                renderAllOtherPlots(firstPlot.id);// vẽ tất cả lô (trừ lô đầu tiên sẽ làm highlight)
                await setHighlightPlot(firstPlot.id);// highlight lô đầu và bay tới
                await loadPlantsForPlot(firstPlot);// load cây cho lô đầu ngay
                // preload TẤT CẢ cây (mọi lô) với giới hạn đồng thời
                preloadAllPlants(4).then(()=>{
                    populateFilterOptionsFromLayer();  // cập nhật options Giống/Năm
                    applyFiltersGoTo();                // chạy lọc (mặc định là "tất cả") + goTo extent cây
                });
                // co giãn icon theo zoom
                view.watch("scale", () => {
                    const size = sizeByScale(view.scale);
                    treeLayer.graphics.forEach(g => {
                        if (g.symbol?.type !== "picture-marker") return;
                        g.visible = size > 0;
                        if (size > 0) {
                            g.symbol.width  = `${size}px`;
                            g.symbol.height = `${size}px`;
                            g.symbol.yoffset = `${Math.round(size * 0.35)}px`;
                        }
                    });
                });
                wireFilterEvents();
                $('#loading-message').fadeOut();
            });
            // ====== CLICK: cây → (goTo cây & popup) | lô (goTo lô & popup) ======
            view.on("immediate-click", async (event) => {
                // 1) cây
                let { results } = await view.hitTest(event, { include: [treeLayer] });
                if (results?.length) {
                    const g = results[0].graphic;
                    // bay tới cây & mở popup
                    await view.goTo({ target: g.geometry, zoom: 18 }, { duration: 250 });
                    view.popup.open({ features: [g], location: g.geometry, updateLocationEnabled: true });
                    return;
                }
                // 2) lô đang chọn
                ({ results } = await view.hitTest(event, { include: [highlightLayer] }));
                if (results?.length) {
                    const g = results[0].graphic;
                    await view.goTo({ target: g.geometry, zoom: 16 }, { duration: 250 });
                    view.popup.open({ features: [g], location: event.mapPoint, updateLocationEnabled: true });
                    return;
                }
                // 3) lô khác → set highlight + bay tới + (nếu chưa load) load cây
                ({ results } = await view.hitTest(event, { include: [otherPlotsLayer] }));
                if (results?.length) {
                    const g = results[0].graphic;
                    const pid = g.attributes.plotID;
                    await setHighlightPlot(pid, event.mapPoint);
                    const pObj = allPlots.find(x => x.id === pid);
                    if (pObj) loadPlantsForPlot(pObj); // nếu chưa load thì load; cây sẽ hiện luôn trong layer chung
                    return;
                }
                view.popup.close();
            });
            view.on("pointer-move", async (evt) => {
                const t = await view.hitTest(evt, { include: [treeLayer, highlightLayer, otherPlotsLayer] });
                view.container.style.cursor = t?.results?.length ? "pointer" : "default";
            });
        });
    });
</script> --}}

<!-- Add plots Modal -->
<form id="plots-form" action="{{ route('plots.save') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="create-plots" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Tạo Lô</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row gy-2">
                        <!-- Vườn (gardenID) -->
                        <div class="col-xl-12">
                            <label for="plantCode" class="form-label">Mã Lô</label>
                            <input type="text" class="form-control" name="plantCode" id="plantCode" required
                                placeholder="Mã lô">
                        </div>
                        <!-- Vườn (gardenID) -->
                        <div class="col-xl-12">
                            <label for="gardenID" class="form-label">Vườn</label>
                            <select class="form-control" name="gardenID" id="gardenID" required>
                                <option value="">Chọn vườn</option>
                                @foreach($gardens as $garden)
                                <option value="{{ $garden->id }}">{{ $garden->gardenName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Tên lô -->
                        <div class="col-xl-12">
                            <label for="plotName" class="form-label">Tên Lô</label>
                            <input type="text" class="form-control" name="plotName" id="plotName" required
                                placeholder="Tên lô">
                        </div>
                        <!-- Diện tích lô -->
                        <div class="col-xl-12">
                            <label for="plotArea" class="form-label">Diện Tích (m2)</label>
                            <input type="number" min="0" step="0.01" class="form-control" name="plotArea" id="plotArea"
                                required placeholder="Diện tích">
                        </div>
                        <!-- Số lượng cây -->
                        <div class="col-xl-12">
                            <label for="plantCount" class="form-label">Số Lượng Cây</label>
                            <input type="number" min="0" step="1" class="form-control" name="plantCount" id="plantCount"
                                required placeholder="Số lượng cây">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" id="submit-btn-plots">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- plots List -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="containers mt-2">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="search-box">
                            <input type="text" class="search-inputs" placeholder="Tìm Kiếm...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <div class="form-section">
                        <!-- First Filter Row -->
                        <div class="form-row">
                            {{-- <div class="form-group">
                                <label class="form-label">Tên Vườn Cây</label>
                                <select class="form-select" id="taskType">
                                    <option value="">Tất cả</option>

                                </select>
                            </div> --}}
                            {{-- <div class="form-group">
                                <label class="form-label">Lô</label>
                                <select class="form-select" id="taskplot">
                                    <option value="">Tất cả vườn</option>
                                    <option value="Khu A">Khu A</option>
                                    <option value="Khu B">Khu B</option>
                                    <option value="Khu C">Khu C</option>
                                    <option value="Khu D">Khu D</option>
                                </select>
                            </div> --}}
                            <div class="form-group">
                                <label class="form-label">Diện Tích</label>
                                <select class="form-select" id="taskLot">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nhập Lô(Excel)</label>
                                <a href="{{route('add-excel')}}" class="text-white">
                                    <button class="btn btn-success btn-w" onclick="filterTasks()">
                                        <i class="fa fa-plus"></i>
                                        Nhập
                                    </button>
                                </a>
                            </div>
                            <div class="form-group" style="display: flex; align-items: end;">
                                {{-- <button class="btn btn-success btn-w" style="margin: 0 0px 1px 2px;"
                                    onclick="filterTasks()">
                                    <i class="fa-light fa-filter-list"></i>
                                    Lọc
                                </button> --}}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <table id="plots-table" class="table table-bordered text-nowrap w-100">
                    <div id="buttons-container" class="d-flex justify-content-end gap-2">
                        <button id="edit-selected-btn" class="btn btn-warning"
                            style="border-radius: 30px; color: #FFFFFF; display: none">Không/Hoạt
                            Động</button>
                        <button id="delete-selected-btn" class="btn btn-danger"
                            style="border-radius: 30px; display: none;">
                            Xóa
                        </button>
                        <button id="add-selected-btn" class="btn btn-success" style="border-radius: 7px;">
                            <a href="{{route('plots.add')}}" class="text-white">
                                <i class="fa fa-plus"></i>Tạo Lô</a>
                        </button>
                    </div>
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                <input class="form-check-input check-all" type="checkbox" id="select-all-plots" value=""
                                    aria-label="...">
                            </th>
                            {{-- <th scope="col">STT</th> --}}
                            <th scope="col">Mã Lô</th>
                            <th scope="col">Tên Lô</th>
                            {{-- <th scope="col">Vườn</th> --}}
                            <th scope="col">Diện Tích</th>
                            <th scope="col">Số Lượng Cây</th>
                            <th scope="col">Trạng Thái</th>
                            <th scope="col">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this section -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            {{-- <th scope="col">STT</th> --}}
                            <th scope="col">Mã Lô</th>
                            <th scope="col">Tên Lô</th>
                            <th scope="col">Diện Tích</th>
                            <th scope="col">Số Lượng Cây</th>
                            <th scope="col">Trạng Thái</th>
                            <th scope="col">Thao Tác</th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Xác Nhận Cập Nhật</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn chắc chắn muốn cập nhật trạng thái đã chọn?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmUpdateBtn" class="btn btn-primary">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Xoa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác Nhận Xóa</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn chắc chắn muốn xóa vườn cây đã chọn?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-primary">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
            var selectedRows = new Set();
            var dataTable = $('#plots-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                    "emptyTable": "Không có dữ liệu",
                },
                 dom: 'lrtip',
                processing: true,
                serverSide: true,
                // responsive: true,
                columnDefs: [{
                    className: 'dtr-control',
                    orderable: false,
                    targets: 0,
                }],
                order: [1, 'asc'],
                responsive: {
                    details: {
                        type: 'column',
                        renderer: function(api, rowIdx, columns) {
                            var data = $.map(columns, function(col, i) {
                                return col.hidden ?
                                    '<li data-dtr-index="' + i + '" data-dt-row="' + rowIdx +
                                    '" data-dt-column="' + col.columnIndex + '">' +
                                    '<span class="dtr-title">' + col.title + ':</span> ' +
                                    '<span class="dtr-data">' + col.data + '</span>' +
                                    '</li>' :
                                    '';
                            }).join('');

                            return data ? $('<ul data-dtr-index="' + rowIdx + '" class="dtr-details"/>')
                                .append(data) : false;
                        }
                    }
                },
                ajax: {
                    url: '{{ route('plots.index') }}',
                    type: 'GET'
                },
                columns: [
                    {
                        data: null,
                        name: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '';
                        }

                    },
                    {
                        data: 'check',
                        name: 'check',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'plotCode',
                        name: 'plotCode'
                    },
                    {
                        data: 'plotName',
                        name: 'plotName'
                    },
                    // {
                    //     data: 'gardenID',
                    //     name: 'gardenID'
                    // },
                    {
                        data: 'plotArea',
                        name: 'plotArea'
                    },
                    {
                        data: 'plants_count',
                        name: 'plants_count'
                    },
                    
                    {
                        data: 'status',
                        name: 'status'
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                rowCallback: function(row, data) {
                    $(row).attr('data-id', data.id);
                }
            });
            let t;
            $('.search-inputs').on('input', function () {
                clearTimeout(t);
                const v = this.value;
                t = setTimeout(() => dataTable.search(v).draw(), 250);
            });
            $('#select-all-plots').on('change', function() {
                var checked = $(this).prop('checked');
                $('#plots-table tbody .form-check-input').each(function() {
                    var plotId = $(this).data('id');
                    if (checked) {
                        selectedRows.add(plotId);
                    } else {
                        selectedRows.delete(plotId);
                    }
                    $(this).prop('checked', checked);
                });
                toggleButtons();

                console.log([...selectedRows]);
            });
            $('#plots-table tbody').on('change', '.form-check-input', function() {
                var plotId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(plotId);
                } else {
                    selectedRows.delete(plotId);
                }
                console.log([...selectedRows]);
            });

            $('#plots-table').on('draw.dt', function() {
                $('#plots-table tbody .form-check-input').each(function() {
                    var plotId = $(this).data('id');
                    if (selectedRows.has(plotId)) {
                        $(this).prop('checked', true);
                    }
                });
            });
            $('#edit-selected-btn').on('click', function() {
                $('#confirmModal').modal('show');
                $('#confirmUpdateBtn').on('click', function() {
                    $.ajax({
                        url: '/plots/edit-multiple',
                        type: 'POST',
                        data: {
                            ids: [...selectedRows], // Chuyển Set thành mảng
                            status: 'Không hoạt động'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Trạng thái đã được cập nhật.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                selectedRows.clear(); // Reset danh sách đã chọn
                                $('#confirmModal').modal('hide');
                                // $('#buttons-container').hide();
                                $('#edit-selected-btn').hide();
                                $('#delete-selected-btn').hide();
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Có lỗi khi cập nhật trạng thái.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });
                $('#confirmModal').on('hidden.bs.modal', function() {
                    $('#confirmUpdateBtn').off('click');
                });
            });
            $('#delete-selected-btn').on('click', function() {
                $('#deleteModal').modal('show');
                $('#confirmDeleteBtn').on('click', function() {
                    $.ajax({
                        url: '/plots/delete-multiple',
                        type: 'POST',
                        data: {
                            ids: [...selectedRows]
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Dữ liệu đã được xóa thành công.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                selectedRows.clear();
                                $('#deleteModal').modal('hide');
                                // $('#buttons-container').hide();
                                $('#edit-selected-btn').hide();
                                $('#delete-selected-btn').hide();
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Có lỗi khi xóa dữ liệu.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });

                $('#deleteModal').on('hidden.bs.modal', function() {
                    $('#confirmDeleteBtn').off('click');
                });
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function toggleButtons() {
                var selected = $('#plots-table tbody .form-check-input:checked').length;
                // if (selected > 0) {
                //     $('#buttons-container').css('visibility', 'visible');
                // } else {
                //     $('#buttons-container').css('visibility', 'hidden');
                // }
                if (selected > 0) {
                    $('#edit-selected-btn').show();
                    $('#delete-selected-btn').show();
                } else {
                    $('#edit-selected-btn').hide();
                    $('#delete-selected-btn').hide();
                }
                $('#add-selected-btn').show();
            }

        });
</script>

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.css" rel="stylesheet">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.js"></script>

<script>
    $(document).on('click', '.toggle-status', function(e) {
            e.preventDefault();

            let button = $(this);
            let id = button.data('id');

            Swal.fire({
                title: "Xác nhận thay đổi",
                text: "Bạn có chắc chắn muốn thay đổi trạng thái của vườn cây này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Thay đổi",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('plots.status') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                if (response.status === 'Hoạt động') {
                                    button.removeClass('bg-danger').addClass('bg-success').text(
                                        'Hoạt động');
                                } else {
                                    button.removeClass('bg-success').addClass('bg-danger').text(
                                        'Không hoạt động');
                                }

                                Swal.fire({
                                    text: 'Trạng thái của vườn cây đã được cập nhật.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    text: response.message ||
                                        'Không thể thay đổi trạng thái của vườn cây.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                text: 'Không thể thay đổi trạng thái, vui lòng thử lại.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                timer: 3000
                            });
                        }
                    });
                }
            });
        });
</script>
<style>
    .card {
        background: white;
        border-radius: 0.75rem;
        /* box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); */
        margin-bottom: 1.5rem;
    }

    .card-header {
        padding: 1.5rem 1.5rem 0;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .card-description {
        color: #64748b;
        font-size: 0.875rem;
    }

    @media (min-width: 1400px) {

        .container {
            max-width: 900px !important;
        }
    }

    #map-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #map-modal .modal-content {
        position: relative;
        background: #fff;
        padding: 10px;
        border-radius: 8px;
        max-width: 90%;
        max-height: 90%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .close-btn {
        position: absolute;
        top: 8px;
        top: -15px;
        right: 10px;
        font-size: 40px;
        color: #ff0000;
        z-index: 10000;
        font-weight: bold;
    }

    .close-btn:hover {
        color: red;
    }

    @media (max-width: 768px) {
        #plantFilters {
            /* display: block !important; */
        }
    }

    @media (max-width: 500px) {
        .map-toolbar {
            display: block !important;
        }

        .form-control {
            width: 90% !important;
        }

        #filterStatus {
            margin: 3px 0px 3px 0px;
            width: 90% !important;
        }

        #filterVariety {
            width: 90% !important;
            margin-bottom: 3px;
        }

        #filterYear {
            width: 90% !important;
        }

        #btnZoomVisible {
            width: 20% !important;
        }

        #btnResetFilters {
            width: 20% !important;
        }
    }
</style>
@endsection