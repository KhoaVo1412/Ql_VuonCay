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
<style>
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
</style>

<div id="map-modal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn" onclick="hideMapModal()">&times;</span>
        <div id="viewMap" style="width:100%; height:500px;"></div>
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
    const plot = allPlots.find(p => p.id === id_plot);
    if (!plot) { alert("Không tìm thấy plot!"); return; }

    $('#loading-message').fadeIn();
    $('#map-modal').fadeIn();

    // Xóa view cũ (nếu có) để tránh rò bộ nhớ khi mở nhiều lần
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
      "esri/symbols/TextSymbol",
      "esri/geometry/Point",
      "esri/geometry/geometryEngine",
      "esri/geometry/support/webMercatorUtils",
      "esri/geometry/projection"
    ], function (
      WebMap, MapView, esriConfig, GraphicsLayer, Graphic, Polygon, TextSymbol, Point,
      geometryEngine, webMercatorUtils, projection
    ) {

        esriConfig.apiKey = 'AAPTxy8BH1VEsoebNVZXo8HurOadC8u-UIHoZRb-lXA-3rkDu_-XvNmTAkDyub3lRUmC8opcXCyL0s2ZXRCaabr-0W_mTQODDQjoxFA7bzeneym8cc7T6retjmqiAgVD52KNfFwO9aDBzBNwGpXLnZxmDumfBBo5NSsT3CPe8mcayFVA4-iaFGgByWubANLOFhj3AgrYBaWsfyCBJSz76VDY6OFtZElcdmA7m9bmHkvGfOo.AT1_x0a0pTBz';
      const map = new WebMap({ portalItem: { id: "5ec8ed782e5146d1909f76e14e293059" } });
      const view = new MapView({
        container: "viewMap",
        map,
        zoom: 12,
        center: [107.3877, 10.6695]
      });
      window.__arcgisView = view; // lưu để lần sau có thể destroy

      // Layers
      const otherPlotsLayer = new GraphicsLayer({ id: "otherPlotsLayer" });
      const highlightLayer = new GraphicsLayer({ id: "highlightLayer" });
      const treeLayer      = new GraphicsLayer({ id: "treeLayer" });
      map.addMany([otherPlotsLayer, highlightLayer, treeLayer]);

      // === Helpers: chuyển SR & tạo lưới điểm trong polygon ===
      function projectToWebMercator(geom) {
        if (geom.spatialReference && geom.spatialReference.isWGS84) {
          return webMercatorUtils.geographicToWebMercator(geom);
        }
        if (geom.spatialReference && geom.spatialReference.isWebMercator) {
          return geom;
        }
        return projection.project(geom, { wkid: 3857 });
      }

      function generateGridPointsInPolygon(polygonGeom, spacingMeters = 120, jitterRatio = 0.12) {
        return projection.load().then(function () {
          const polyWM = projectToWebMercator(polygonGeom);
          const extent = polyWM.extent;
          const step = spacingMeters; // mét trong WebMercator
          const ptsWM = [];

          for (let y = extent.ymin + step / 2; y <= extent.ymax; y += step) {
            for (let x = extent.xmin + step / 2; x <= extent.xmax; x += step) {
              const jx = jitterRatio ? (Math.random() - 0.5) * step * jitterRatio * 2 : 0;
              const jy = jitterRatio ? (Math.random() - 0.5) * step * jitterRatio * 2 : 0;
              const pt = new Point({ x: x + jx, y: y + jy, spatialReference: polyWM.spatialReference });
              if (geometryEngine.contains(polyWM, pt)) ptsWM.push(pt);
            }
          }

          // Trả về đúng SR ban đầu
          if (polygonGeom.spatialReference && polygonGeom.spatialReference.isWGS84) {
            return ptsWM.map(p => webMercatorUtils.webMercatorToGeographic(p));
          }
          if (polygonGeom.spatialReference && polygonGeom.spatialReference.isWebMercator) {
            return ptsWM;
          }
          return ptsWM.map(p => projection.project(p, polygonGeom.spatialReference));
        });
      }
      // === Hết helpers ===

      view.when(async () => {
        otherPlotsLayer.removeAll();
        highlightLayer.removeAll();
        treeLayer.removeAll();

        // Parse GeoJSON lô đang chọn
        let geojson;
        try { geojson = JSON.parse(plot.mapJs); }
        catch (err) { console.error("Lỗi parse GeoJSON:", err); $('#loading-message').fadeOut(); return; }

        // Vẽ tất cả lô khác mờ hơn
        for (const p of allPlots) {
          if (!p.mapJs || p.id === plot.id) continue;
          let data;
          try { data = JSON.parse(p.mapJs); } catch { continue; }
          if (data.type !== "FeatureCollection" || !data.features.length) continue;

          const f = data.features[0];
          let geom;
          if (f.geometry.type === "Polygon") {
            geom = new Polygon({ rings: f.geometry.coordinates });
          } else if (f.geometry.type === "MultiPolygon") {
            geom = new Polygon({ rings: f.geometry.coordinates.flat() });
          } else continue;

          otherPlotsLayer.add(new Graphic({
            geometry: geom,
            symbol: {
              type: "simple-fill",
              color: [255, 255, 255, 0.08],
              outline: { color: [120, 120, 120, 0.8], width: 1 }
            }
          }));
        }

        // Vẽ lô đang chọn + đi tới
        if (geojson.type === "FeatureCollection" && geojson.features.length > 0) {
          const feature = geojson.features[0];
          let polygonGeometry;
          if (feature.geometry.type === "Polygon") {
            polygonGeometry = new Polygon({ rings: feature.geometry.coordinates });
          } else if (feature.geometry.type === "MultiPolygon") {
            polygonGeometry = new Polygon({ rings: feature.geometry.coordinates.flat() });
          } else {
            alert("GeoJSON không phải Polygon/MultiPolygon"); $('#loading-message').fadeOut(); return;
          }

          // Nền đỏ mờ + viền trắng dày cho nổi bật
          highlightLayer.add(new Graphic({
            geometry: polygonGeometry,
            symbol: {
              type: "simple-fill",
              color: [255, 0, 0, 0.3],
              outline: { color: [255, 255, 255, 1], width: 3 }
            }
          }));

          await view.goTo({ target: polygonGeometry, zoom: 16 }, { duration: 400 });

          const TREE_SPACING_M = 60;  
          const JITTER = 0.12;     
          const points = await generateGridPointsInPolygon(polygonGeometry, TREE_SPACING_M, JITTER);

          for (const pt of points) {
            treeLayer.add(new Graphic({
              geometry: pt,
              symbol: {
                type: "simple-marker",
                style: "circle",
                size: 6,
                color: [0, 128, 0, 1],                  // xanh lá
                outline: { color: [255, 255, 255, 1], width: 0.5 }
              }
              // Muốn icon PNG:
              // symbol: { type: "picture-marker", url: "/images/tree-icon.png", width: "16px", height: "16px" }
            }));
          }

          // Ẩn cây khi zoom quá xa để đỡ rối/nặng
          view.watch("scale", s => { treeLayer.visible = s < 30000; });
        } else {
          alert("Plot chưa có dữ liệu GeoJSON hợp lệ!");
        }

        $('#loading-message').fadeOut();
      });
    });
  });
</script>

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
</style>
@endsection