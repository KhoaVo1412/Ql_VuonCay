@extends('layouts.app')

@section('content')
<div id="mapWrap" style="position:relative; height: 100%; min-height: 560px;">
  <div id="plantFilters"
    style="gap:6px; align-items:center; position:absolute; z-index:10; top:8px; left:8px; background:#ffffffd9; padding:5px 10px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.12)">
    <input id="searchPlot" list="plotHints" class="form-input" placeholder="Tìm lô (mã/tên)...">
    <datalist id="plotHints"></datalist>
    <div style="width:1px; height:24px; background:#ddd; margin:0 4px;"></div>
    <input class="form-input" id="searchPlant" placeholder="Tìm mã cây...">
    <select id="filterStatus" class="form-select">
      <option value="">Trạng thái</option>
      <option>tốt</option>
      <option>sâu bệnh</option>
      <option>gãy đổ</option>
      <option>chết</option>
    </select>
    <button id="btnResetFilters" class="btn btn-sm btn-outline-secondary" title="Xoá lọc"><i
        class="fa-solid fa-delete-left"></i></button>
    <button id="btnZoomVisible" class="btn btn-sm btn-primary" title="Zoom cây đang hiển thị"><i
        class="fa-solid fa-magnifying-glass"></i></button>
    <span id="filterCount" style="margin-left:8px; font-weight:600; width:250px"></span>
  </div>
  <div id="viewMap" style="height:100%"></div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const allPlots = @json($all_plots);
    const apiKey   = @json($apikeys);

    require([
      "esri/WebMap",
      "esri/views/MapView",
      "esri/config",
      "esri/layers/GraphicsLayer",
      "esri/Graphic",
      "esri/geometry/Polygon",
      "esri/geometry/Point",
      "esri/geometry/geometryEngine",
      "esri/geometry/support/webMercatorUtils"
    ], function (
      WebMap, MapView, esriConfig, GraphicsLayer, Graphic, Polygon, Point, geometryEngine, webMercatorUtils
    ) {
      // ===== Preload icon =====
      ["/imgs/treemapp.png","/imgs/treemap.png","/imgs/cay_benh.png","/imgs/cay_nga.png","/imgs/tree-dead.png"]
        .forEach(src => { const i = new Image(); i.src = src; });

      // ===== Map init =====
      esriConfig.apiKey = apiKey;
      const map = new WebMap({ portalItem: { id: "5ec8ed782e5146d1909f76e14e293059" } });
      const view = new MapView({
        container: "viewMap",
        map,
        zoom: 11,
        center: [107.39, 10.67],
        constraints: { minZoom: 8, snapToZoom: false },
        requestRenderMode: "on-demand",
        highlightOptions: { color: [255,0,0,0.5] }
      });
      window.__arcgisView = view;

      // ===== Layers =====
      const otherPlotsLayer = new GraphicsLayer({ id: "otherPlotsLayer" });
      const highlightLayer  = new GraphicsLayer({ id: "highlightLayer"  });
      const treeLayer       = new GraphicsLayer({ id: "treeLayer"       });
      const labelLayer      = new GraphicsLayer({ id: "labelLayer"      });
      map.addMany([otherPlotsLayer, highlightLayer, treeLayer, labelLayer]);
      map.layers.reorder(otherPlotsLayer,  map.layers.length - 4);
      map.layers.reorder(highlightLayer,   map.layers.length - 3);
      map.layers.reorder(treeLayer,        map.layers.length - 2);
      map.layers.reorder(labelLayer,       map.layers.length - 1);

      map.when(() => {
        view.popup.autoOpenEnabled = false;
        map.allLayers.forEach(l => {
          if (!["treeLayer","highlightLayer","otherPlotsLayer","labelLayer"].includes(l.id) && "popupEnabled" in l) l.popupEnabled = false;
          if ("allSublayers" in l && l.allSublayers) l.allSublayers.forEach(sl => { if ("popupEnabled" in sl) sl.popupEnabled = false; });
        });
      });

      // ===== Icons & helpers =====
      const ICONS = { normal:"/imgs/treemapp.png", disease:"/imgs/cay_benh.png", broken:"/imgs/cay_nga.png", dead:"/imgs/tree-dead.png", default:"/imgs/treemap.png" };
      const ANCHOR_K = 0.25;
      function treeSymbol(sizePx = 25, url = ICONS.default) { return { type:"picture-marker", url, width:`${sizePx}px`, height:`${sizePx}px`, yoffset:`${Math.round(sizePx*ANCHOR_K)}px` }; }
      function iconUrlByStatus(status) {
        const s = (status||"").trim().toLowerCase();
        const sNorm = s.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d');
        if (s.includes("sâu bệnh") || sNorm.includes("sau benh") || s.includes("bệnh") || sNorm.includes("benh")) return ICONS.disease;
        if (s.includes("gãy") || sNorm.includes("gay") || sNorm.includes("gay do") || s.includes("đổ") || sNorm === "do") return ICONS.broken;
        if (s.includes("chết") || sNorm.includes("chet")) return ICONS.dead;
        return ICONS.normal;
      }
      function sizeByScale(scale){ if (scale > 120000) return 0; if (scale > 60000) return 22; if (scale > 30000) return 26; return 30; }
      function stripVN(s=''){ return s.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/gi,'d').toLowerCase(); }

      // ===== Popup template =====
      function makePlotPopupTemplate() {
        return {
          title: "Lô {plotCode}",
          content: [{ type:"fields", fieldInfos: [
            { fieldName:"plotCode",   label:"Mã lô" },
            { fieldName:"plotName",   label:"Tên lô" },
            { fieldName:"area",       label:"Diện tích", format:{ places:2 } },
            { fieldName:"year",       label:"Năm trồng" },
            { fieldName:"status",     label:"Hiện trạng" },
            { fieldName:"plantCount", label:"Số cây" }
          ]}]
        };
      }

      // ===== Helpers =====
      function buildPolygonFromMapJs(mapJs) {
        try {
          const data = JSON.parse(mapJs);
          if (!(data.type === "FeatureCollection" && data.features.length)) return null;
          const f = data.features[0];
          if (f.geometry.type === "Polygon")
            return new Polygon({ rings: f.geometry.coordinates, spatialReference: { wkid: 4326 } });
          if (f.geometry.type === "MultiPolygon")
            return new Polygon({ rings: f.geometry.coordinates.flat(), spatialReference: { wkid: 4326 } });
        } catch(e) {}
        return null;
      }

      // ===== State =====
      const plotGfxMap = new Map();
      const plantCountByPlot = new Map();
      const loadedPlots = new Set();
      let currentPlotGraphic = null;
      let currentPlotId = null;

      // NEW: trạng thái “focus” để thay checkbox
      let focusPlotId = null; // khi khác null → chỉ hiện cây của lô này

      // chặn auto-fit khi người dùng đang thao tác
      let ignoreAutoGoTo = false, autoGoToTimer = null;
      ["drag","mouse-wheel","double-click","key-down","key-up"].forEach(evt=>{
        view.on(evt, ()=>{ ignoreAutoGoTo = true; clearTimeout(autoGoToTimer); autoGoToTimer = setTimeout(()=> ignoreAutoGoTo = false, 700); });
      });

      // ===== Vẽ các lô =====
      function addPlotGraphic(p) {
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
        return g.geometry;
      }
      function renderAllPlotsAndGetGeoms() {
        otherPlotsLayer.removeAll();
        plotGfxMap.clear();
        const geoms = [];
        for (const p of allPlots) {
          if (!p.mapJs) continue;
          const geom = addPlotGraphic(p);
          if (geom) geoms.push(geom);
        }
        return geoms;
      }

      // ===== Highlight lô =====
      async function setHighlightPlotById(plotId, openAt=null, doFit=true, forceFit=false) {
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
        if (doFit && (!ignoreAutoGoTo || forceFit)) {
          await view.goTo({ target: geom }, { duration: 350 });
        }
        if (openAt) {
          view.popup.open({ features: [currentPlotGraphic], location: openAt, updateLocationEnabled: true });
        }
      }

      // ===== Cây =====
      function updateGraphicVisibility(g) {
        const a = g.attributes || {};
        const byFilter = a.__matchFilter !== false;
        const byScale  = a.__matchScale  !== false;
        g.visible = byFilter && byScale;
      }

      function addPlantGraphic(pObj, plant) {
        const lat = +plant.lat, lng = +plant.lng;
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
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
            content: [{ type:"fields", fieldInfos: [
              { fieldName: "plantCode", label: "Mã cây" },
              { fieldName: "varietyName", label: "Giống" },
              { fieldName: "year",        label: "Năm trồng" },
              { fieldName: "statusTree",  label: "Trạng thái" },
              { fieldName: "lat",         label: "Lat" },
              { fieldName: "lng",         label: "Lng" },
            ]}]
          }
        });
        treeLayer.add(g);
        updateGraphicVisibility(g);
        plantCountByPlot.set(pObj.id, (plantCountByPlot.get(pObj.id) || 0) + 1);
      }

      async function loadPlantsForPlot(pObj) {
        if (!pObj || loadedPlots.has(pObj.id)) return;
        try {
          const res = await fetch(`/plots/${pObj.id}/plants`, { headers: { 'Accept': 'application/json' }});
          if (!res.ok) throw new Error('HTTP ' + res.status);
          const plants = await res.json();
          if (Array.isArray(plants) && plants.length) {
            plants.forEach(pl => addPlantGraphic(pObj, pl));
            loadedPlots.add(pObj.id);
            const cnt = plantCountByPlot.get(pObj.id) || 0;
            if (currentPlotGraphic && currentPlotGraphic.attributes.plotID === pObj.id) currentPlotGraphic.attributes.plantCount = cnt;
            const otherG = plotGfxMap.get(pObj.id); if (otherG) otherG.attributes.plantCount = cnt;
          } else { loadedPlots.add(pObj.id); }
        } catch (e) { console.warn('Load plants failed for plot', pObj?.id, e); }
      }

      // Load tất cả cây (giới hạn song song)
      async function loadAllPlants(concurrency = 6) {
        const queue = allPlots.filter(p => p.mapJs && !loadedPlots.has(p.id));
        let i = 0;
        async function worker() { while (i < queue.length) { const p = queue[i++]; await loadPlantsForPlot(p); } }
        await Promise.all(Array.from({length: Math.max(1, concurrency)}, worker));
        applyFiltersGoTo(false);
        updatePlotLabels();
      }

      // Lazy-load theo extent
      async function loadPlantsInView() {
        const ext = view.extent; if (!ext) return;
        if (view.scale > 60000) return;
        let ext4326 = ext;
        try {
          if (ext.spatialReference?.isWebMercator) ext4326 = webMercatorUtils.webMercatorToGeographic(ext);
        } catch (e) { console.warn('Extent transform failed', e); }
        const candidates = allPlots.filter(p => {
          if (!p.mapJs) return false;
          const poly4326 = buildPolygonFromMapJs(p.mapJs);
          if (!poly4326) return false;
          try { return geometryEngine.intersects(poly4326, ext4326); } catch { return false; }
        });
        for (const p of candidates) { await loadPlantsForPlot(p); }
        applyFiltersGoTo(false);
        updatePlotLabels();
      }

      // ===== Filters & UI =====
      function goToVisibleTrees(duration=300){
        const geoms = treeLayer.graphics.filter(g=>g.visible).map(g=>g.geometry);
        if (geoms.length) view.goTo(geoms, { duration });
      }

      function applyFiltersGoTo(doFit = false){
        const sQ = document.getElementById('searchPlant');
        const sS = document.getElementById('filterStatus');
        const cL = document.getElementById('filterCount');

        const q  = sQ ? stripVN(sQ.value.trim()) : "";
        const st = sS ? sS.value : "";

        let visibleCount = 0;
        treeLayer.graphics.forEach(g=>{
          const a = g.attributes || {};
          let ok = true;

          // NEW: nếu đang focus lô thì chỉ hiện cây của lô đó
          if (focusPlotId != null) ok = String(a.plotID) === String(focusPlotId);

          if (ok && q)  ok = stripVN(a.plantCode||'').includes(q);
          if (ok && st) ok = (a.statusTree||'') === st;

          a.__matchFilter = ok;
          g.attributes = a;
          updateGraphicVisibility(g);
          if (g.visible) visibleCount++;
        });
        if (cL) cL.textContent = `${visibleCount}/${treeLayer.graphics.length} cây`;
        if (doFit && visibleCount > 0 && !ignoreAutoGoTo) goToVisibleTrees(300);
        updatePlotLabels();
      }

      function updatePlotLabels(){
        labelLayer.removeAll();
        const countByPlot = new Map();
        treeLayer.graphics.forEach(g=>{
          if (!g.visible) return;
          const pid = g.attributes?.plotID;
          countByPlot.set(pid, (countByPlot.get(pid) || 0) + 1);
        });
        otherPlotsLayer.graphics.forEach(pg=>{
          const geom = pg.geometry;
          const pid  = pg.attributes.plotID;
          const cnt  = countByPlot.get(pid) || 0;
          if (!cnt) return;
          labelLayer.add(new Graphic({
            geometry: geom.extent.center,
            symbol: { type: "text", text: `${cnt} cây`, haloColor: "white", haloSize: 1, font: { size: 10, family: "sans-serif", weight:"bold" } }
          }));
        });
      }
      // ===== Search: cây (tự focus lô + fit như checkbox) =====
      async function goToPlantByCode(input) {
        const raw = (input || '').trim();
        const q = stripVN(raw);
        if (!q) return;
        const loaded = treeLayer.graphics.toArray();// 1) tìm trong cây đã load
        const exact = loaded.find(g => stripVN(g.attributes?.plantCode || '') === q);
        const partial = exact || loaded.find(g => stripVN(g.attributes?.plantCode || '').includes(q));
        if (partial) {
          const pid = partial.attributes.plotID;
          focusPlotId = pid;                           // bật chế độ chỉ hiện cây của lô này
          await setHighlightPlotById(pid, partial.geometry, true, true);
          applyFiltersGoTo(true);                      // fit như khi tick checkbox
          view.popup.open({ features: [partial], location: partial.geometry, updateLocationEnabled: true });
          return;
        }
        try {// 2) fallback: hỏi server
          const res = await fetch(`/plants/find?code=${encodeURIComponent(raw)}`, { headers: { 'Accept': 'application/json' }});
          if (!res.ok) throw new Error('HTTP ' + res.status);
          const plant = await res.json();
          if (!plant || !plant.plotID) return;

          const plotObj = allPlots.find(p => String(p.id) === String(plant.plotID));
          if (plotObj) {
            focusPlotId = plotObj.id;                 // chỉ hiện cây của lô tìm được
            await setHighlightPlotById(plotObj.id, null, true, true);
            await loadPlantsForPlot(plotObj);
          }
          let g = treeLayer.graphics.find(gg => String(gg.attributes?.id) === String(plant.id));
          if (!g && Number.isFinite(+plant.lat) && Number.isFinite(+plant.lng)) {
            g = new Graphic({
              geometry: new Point({ latitude: +plant.lat, longitude: +plant.lng, spatialReference: { wkid: 4326 } }),
              symbol: treeSymbol(sizeByScale(view.scale) || 26, iconUrlByStatus(plant.statusTree)),
              attributes: { ...plant, __matchFilter: true, __matchScale: sizeByScale(view.scale) > 0 }
            });
            treeLayer.add(g);
          }
          if (g) {
            applyFiltersGoTo(true);                   // fit như checkbox
            view.popup.open({ features: [g], location: g.geometry, updateLocationEnabled: true });
          }
        } catch (e) { console.warn('Server plant search failed', e); }
      }
      // ===== Wire UI =====
      let filtersWired = false;
      function ensureFiltersWired(){
        if (filtersWired) return;
        const el = id => document.getElementById(id);
        if (!el('searchPlant')) return;
        let timer;
        const debounced = ()=>{ clearTimeout(timer); timer=setTimeout(()=>applyFiltersGoTo(true), 180); };
        el('searchPlant').addEventListener('input', debounced);
        el('searchPlant').addEventListener('keydown', async (e) => {
          if (e.key !== 'Enter') return;
          await goToPlantByCode(e.target.value);
        });
        el('filterStatus').addEventListener('change', ()=>applyFiltersGoTo(true));
        // Reset filters → cũng bỏ focus lô
        el('btnResetFilters').addEventListener('click', ()=>{
          el('searchPlant').value = '';
          el('filterStatus').value = '';
          const sp = document.getElementById('searchPlot'); if (sp) sp.value = '';
          focusPlotId = null;                          // NEW
          applyFiltersGoTo(true);
        });
        el('btnZoomVisible').addEventListener('click', ()=> goToVisibleTrees(250));
        filtersWired = true;
      }
      // ===== Tìm lô (focus lô + fit như checkbox) =====
      function normKey(s=''){
        return String(s).normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/gi,'d').replace(/[^a-zA-Z0-9]/g,'').toLowerCase();
      }
      let plotIndex = [];
      function buildPlotIndex(){
        plotIndex = otherPlotsLayer.graphics.map(g => {
          const code = String(g.attributes?.plotCode || '').trim();
          const name = String(g.attributes?.plotName || '').trim();
          return {
            id: g.attributes?.plotID,
            code, name,
            codeL: code.toLowerCase(),
            nameL: name.toLowerCase(),
            codeN: normKey(code),
            nameN: normKey(name),
          };
        }).filter(x => x.id != null);
      }
      function setupPlotSearch(){
        const input = document.getElementById('searchPlot');
        const dl    = document.getElementById('plotHints');
        if (!input) return;
        if (dl) {
          dl.innerHTML = plotIndex.map(p => {
            const v = p.code || p.name || '';
            return v ? `<option value="${v}">` : '';
          }).join('');
        }
        async function goToPlotByText(txt){
          const raw = (txt||'').trim(); if (!raw) return;
          const rawL = raw.toLowerCase(), rawN = normKey(raw);
          let hit = plotIndex.find(p => p.codeL === rawL) ||
                    plotIndex.find(p => p.nameL === rawL) ||
                    plotIndex.find(p => p.codeL.startsWith(rawL)) ||
                    plotIndex.find(p => p.nameL.startsWith(rawL)) ||
                    plotIndex.find(p => p.codeL.includes(rawL)) ||
                    plotIndex.find(p => p.nameL.includes(rawL));
          if (!hit && rawN) {
            hit = plotIndex.find(p => p.codeN === rawN) ||
                  plotIndex.find(p => p.nameN === rawN) ||
                  plotIndex.find(p => p.codeN.startsWith(rawN)) ||
                  plotIndex.find(p => p.nameN.startsWith(rawN)) ||
                  plotIndex.find(p => p.codeN.includes(rawN)) ||
                  plotIndex.find(p => p.nameN.includes(rawN));
          }
          if (!hit) return;
          focusPlotId = hit.id; // NEW: thay cho checkbox
          await setHighlightPlotById(hit.id, null, true, true);
          const pObj = allPlots.find(x => String(x.id) === String(hit.id));
          if (pObj) await loadPlantsForPlot(pObj);
          applyFiltersGoTo(true); // fit như checkbox
        }
        input.addEventListener('keydown', async (e)=>{ if (e.key === 'Enter') await goToPlotByText(input.value); });
        input.addEventListener('change',  async ()=>{ await goToPlotByText(input.value); });
      }
      // ===== Events =====
      view.when(async () => {
        const geoms = renderAllPlotsAndGetGeoms();
        if (geoms.length) await view.goTo(geoms, { duration: 300 });

        buildPlotIndex();
        setupPlotSearch();

        await loadAllPlants(6);

        view.watch("stationary", (stopped) => { if (stopped) loadPlantsInView(); });
        view.watch("scale", () => {
          const size = sizeByScale(view.scale);
          const byScale = size > 0;
          treeLayer.graphics.forEach(g => {
            if (g.symbol?.type === "picture-marker" && byScale) {
              g.symbol.width   = `${size}px`;
              g.symbol.height  = `${size}px`;
              g.symbol.yoffset = `${Math.round(size * ANCHOR_K)}px`;
            }
            const a = g.attributes || {};
            a.__matchScale = byScale;
            g.attributes = a;
            updateGraphicVisibility(g);
          });
          updatePlotLabels();
        });

        // Click: cây hoặc polygon lô (giữ nguyên hành vi hiện tại)
        view.on("immediate-click", async (event) => {
          let { results } = await view.hitTest(event, { include: [treeLayer] });
          if (results?.length) {
            const g = results[0].graphic;
            // vẫn zoom sâu khi click cây
            await view.goTo({ target: g.geometry, zoom: Math.max(view.zoom, 18) }, { duration: 250 });
            view.popup.open({ features: [g], location: g.geometry, updateLocationEnabled: true });
            return;
          }
          ({ results } = await view.hitTest(event, { include: [otherPlotsLayer] }));
          if (results?.length) {
            const g = results[0].graphic;
            await setHighlightPlotById(g.attributes.plotID, event.mapPoint, true);
            const pObj = allPlots.find(x => x.id === g.attributes.plotID);
            if (pObj) await loadPlantsForPlot(pObj);
            return;
          }
          view.popup.close();
        });

        // Hover: con trỏ
        let hoverT = 0;
        view.on("pointer-move", async (evt) => {
          const now = performance.now(); if (now - hoverT < 80) return; hoverT = now;
          const t = await view.hitTest(evt, { include: [treeLayer, highlightLayer, otherPlotsLayer] });
          view.container.style.cursor = t?.results?.length ? "pointer" : "default";
        });

        ensureFiltersWired();
        applyFiltersGoTo(false);
      });
    });
  });
</script>

<style>
  #mapWrap {
    height: 85vh;
    position: relative;
  }

  .esri-ui-corner {
    display: none !important;
  }

  #plantFilters {
    display: flex;
    gap: 6px;
    align-items: center;
    position: absolute;
    z-index: 1;
    top: 8px;
    left: 0px;
    background: #ffffffd9;
    padding: 6px 8px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
    flex-wrap: wrap;
    max-width: calc(100vw - 16px);
  }

  #plantFilters input,
  #plantFilters select {
    height: 34px;
    flex: 1 1 160px;
    min-width: 0;
  }

  #plantFilters button {
    flex: 0 0 auto;
  }

  #filterCount {
    flex: 1 1 100%;
    text-align: right;
    font-size: 12px;
  }

  @media (max-width: 576px) {
    #plantFilters {
      left: 50%;
      padding: 6px;
      gap: 4px;
    }

    #plantFilters input,
    #plantFilters select {
      flex: 1 1 120px;
    }
  }

  @media (max-width: 420px) {
    #plantFilters {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
  }
</style>
@endsection