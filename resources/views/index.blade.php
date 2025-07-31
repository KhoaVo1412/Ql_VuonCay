<!DOCTYPE html>
<html lang="en">
{{-- lang="{{ str_replace('_', '-', app()->getLocale()) }}" --}}

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Truy xuất lô hàng</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Jost:ital,wght@0,100..900;1,100..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="/FontAwesome6.4Pro/css/all.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

    <link rel="stylesheet" href="https://js.arcgis.com/4.32/esri/themes/light/main.css">
    <script src="https://js.arcgis.com/4.32/"></script>

    {{--
    <link rel="stylesheet" href="https://js.arcgis.com/4.30/esri/themes/light/main.css" /> --}}
    {{--
    <script src="https://js.arcgis.com/4.21/"></script> --}}
    {{--
    <script src="https://js.arcgis.com/4.30/"></script> --}}
    {{-- <arcgis-map item-id=>{{ $idMap }}</arcgis-map> --}}



</head>
<style>
    .esri-popup__field-label,
    .esri-popup__field-description {
        font-family: 'Arial', sans-serif;
        /* Thay thế bằng font khác */
    }
</style>

<body class="position-relative">
    <div class="bg-overlay"></div>


    <!-- Modal thông báo đang tải bản đồ -->
    <div id="map-modal" class="map-modal" style="display: none;">
        <div class="modal-content">
            <button id="close-map-modal" class="close-btn">&times;</button>
            <p id="loading-message" style="text-align: center">{{ __('index.load_map') }}</p>
            <div id="viewMap" style="width: 100%; height: 100%; margin-top: 20px;"></div>
        </div>
    </div>


    <div id="plot-code-modal" class="plot-code-modal" style="display: none;">
        <button id="close-plot-code-modal" class="close-btn text-white">&times;</button>
        <div class="d-flex justify-content-center align-items-center my-2">
            <span class="loader loader-plot-code" style="opacity: 1; transition: opacity 0.3s;"></span>
        </div>
        <div id="plot-code-result">
        </div>
    </div>

    <header>
        <div class="container">
            <div class="row align-items-center">
                <!-- Bên trái: Xin chào -->
                <div class="col-12 col-md-auto mb-2 mb-md-0">
                    <div
                        class="border rounded px-3 py-2 d-flex align-items-center justify-content-center justify-content-md-start">
                        <i class="fa-solid fa-user me-2"></i>
                        <span>{{ __('index.hello') }}, {{ Auth::user()->name }}</span>
                    </div>
                </div>

                @php
                    $languages = [
                        'vi' => ['label' => 'Tiếng Việt', 'flag' => 'imgs/vietnam.png'],
                        'en' => ['label' => 'English', 'flag' => 'imgs/united-kingdom.png'],
                        'de' => ['label' => 'Deutsch', 'flag' => 'imgs/germany.png'],
                        'zh' => ['label' => '中文', 'flag' => 'imgs/china.png'],
                    ];
                @endphp

                <!-- Bên phải: Ngôn ngữ + Logout -->
                <div class="col-12 col-md d-flex justify-content-center justify-content-md-end gap-2">
                    <!-- Dropdown đổi ngôn ngữ -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 w-md-auto" type="button"
                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"
                            style="background-color: #f0f0f0; border-color: #ccc; color: #333;">

                            <img src="/{{ $languages[App::getLocale()]['flag'] }}" width="22" height="22"
                                class="me-2 align-middle" style="object-fit: contain;" alt="flag">

                            {{ $languages[App::getLocale()]['label'] }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li>
                                <a class="dropdown-item" href="{{ route('change-language', ['lang' => 'vi']) }}" {{ App::getLocale() === 'vi' ? 'selected' : '' }} data-flag="vn">
                                    Tiếng Việt
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('change-language', ['lang' => 'en']) }}" {{ App::getLocale() === 'en' ? 'selected' : '' }} data-flag="us">
                                    English
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="{{ route('change-language', ['lang' => 'de']) }}" {{ App::getLocale() === 'de' ? 'selected' : '' }} data-flag="de">
                                    Deutsch (German)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('change-language', ['lang' => 'zh']) }}" {{ App::getLocale() === 'zh' ? 'selected' : '' }} data-flag="cn">
                                    中文 (Chinese)
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Nút logout -->
                    <form action="/logout" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 w-md-auto"><i
                                class="fa-solid fa-right-from-bracket me-2"></i>{{ __('index.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <div class="d-flex justify-content-center align-items-center">
            <div class="border-container">
                <div class="logo mb-3">
                    <img src="/imgs/logo_circle.png" alt="" class="">
                </div>
                <div class="position-relative" style="color: white; ">
                    <h2 class="text-center text-uppercase" style="font-weight: bolder;">
                        {{ __('index.truyxuatnguongoc') }}
                    </h2>
                    <h6 class="text-center text-uppercase " style="font-weight: bolder;">{{ __('index.caosuhrc') }}
                    </h6>
                    <p class="text-white text-center" style="max-width: 700px; margin: 0 auto;">
                        {{ __('index.decription') }}
                    </p>

                </div>

                <hr class="mt-5" style="border: none; border-top: 3px solid white;">

            </div>
        </div>




    </div>

    <div class="container mt-5 overflow-hidden">

        <div class="d-flex justify-content-center">
            <nav class="menu">
                <div class="button-container">
                    <a href="#" class="menu-item">
                        <i class="fas fa-search me-2"></i>{{ __('index.track_shipment') }}
                    </a>
                    <a href="#" class="menu-item">
                        <i class="fas fa-file-contract me-2"></i>{{ __('index.contracts_list') }}
                    </a>
                    <a href="#" class="menu-item">
                        <i class="fas fa-file-certificate me-2"></i>{{ __('index.certificates_list') }}
                    </a>
                </div>
            </nav>
        </div>

        <div class="search position-relative">
            <div class="text-white back backSearch btn btn-success text-nowrap">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('index.back') }}
            </div>

            <div class="card" style="padding: 10px 10px;">
                <div class="card-title-box text-white text-center fw-bold info-title">
                    <div class="text-center fw-bold info-title">{{ __('index.track_shipment') }}</div>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2">
                    <input type="text" name="batch_code" id="searchInput" class="form-control"
                        placeholder="{{ __('index.nhapmalohang') }}">

                    <button id="searchButton" class="btn text-nowrap"
                        style="background-color: #f0f0f0; color: #333; border: 1px solid #ccc;"
                        onmouseover="this.style.backgroundColor='#e0e0e0'"
                        onmouseout="this.style.backgroundColor='#f0f0f0'">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center my-2">
                <span class="loader" style="opacity: 0; transition: opacity 0.3s;"></span>
            </div>
            <div class="results text-white">
                <!-- Dữ liệu lô hàng sẽ được hiển thị ở đây -->
            </div>
        </div>
        <!-- Dữ liệu contract sẽ được hiển thị ở đây -->
        <div class="contracts position-relative" style="opacity: 0; transform: translateX(100%);">
            <div class="text-white back backContract btn btn-success">
                <i class="fa-solid fa-arrow-left"></i> {{ __('index.back') }}
            </div>
            <div class="card" style="padding: 10px 10px;">
                <div class="card-title-box text-white text-center fw-bold info-title">
                    <div class="text-center fw-bold info-title">{{ __('index.contracts_list') }}</div>
                </div>
                <div class="search-container d-flex justify-content-between align-items-center gap-3 mt-2">
                    <select id="contract-type-filter" class="form-select">
                        <option value="">{{ __('index.allcontracts') }}</option>
                        <option value="Hợp đồng nội địa dài hạn">{{ __('index.domestic_long_term') }}</option>
                        <option value="Hợp đồng nội địa chuyến">{{ __('index.domestic_single_trip') }}</option>
                        <option value="Hợp đồng nội địa nguyên tắc">{{ __('index.domestic_principle_based') }}
                        </option>
                        <option value="Hợp đồng xuất khẩu dài hạn">{{ __('index.export_long_term') }}</option>
                        <option value="Hợp đồng xuất khẩu chuyến">{{ __('index.export_single_trip') }}</option>
                        <option value="Hợp đồng xuất khẩu nguyên tắc">{{ __('index.export_principle_based') }}
                        </option>
                    </select>
                    <input type="text" class="form-control" id="search-contract"
                        placeholder="{{ __('index.search') }}..." />
                </div>
                <div id="contract-count" class="mt-3" style="color: #34A853; text-align:right">0
                    {{ __('index.found') }}
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center my-2">
                <span class="loader loader-contract" style="opacity: 0; transition: opacity 0.3s;"></span>
            </div>
            <div class="list text-white"></div>
        </div>

        <div class="certificates position-relative" style="opacity: 0; transform: translateX(100%);">
            <div class="text-white back backCertificate btn btn-success">
                <i class="fa-solid fa-arrow-left"></i> {{ __('index.back') }}
            </div>
            <div class="card" style="padding: 10px 10px;">
                <div class="card-title-box text-white text-center fw-bold info-title">
                    <div class="text-center fw-bold info-title">{{ __('index.certificates_list') }}</div>
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center my-2">
                <span class="loader loader-certificate" style="opacity: 0; transition: opacity 0.3s;"></span>
            </div>
            <div class="certificate_list text-white"></div>
        </div>

    </div>

</body>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    const tokenMap = @json($tokenMap);
    const apiToken = "{{ Auth::user()->remember_token }}";
    const webMapId = @json($idMap);
</script>
<script>
    $(document).ready(function () {
        const loader = $('.loader');
        const resultsDiv = $('.results');
        $('#searchButton').on('click', function () {
            const batchCode = $('#searchInput').val().trim();
            if (!batchCode) {
                alert('Vui lòng nhập mã lô hàng!');
                return;
            }
            loader.css('opacity', 1);
            resultsDiv.html('');
            $.ajax({
                url: '{{ route('batches.indexapi') }}',
                method: 'GET',
                headers: {
                    'token': apiToken
                },
                data: {
                    batch_code: batchCode
                },
                success: function (response) {
                    loader.css('opacity', 0);
                    window.batchResponse = response;
                    if (response.status === 200) {
                        const batch = response.batch;
                        const ingredient = batch.ingredients;
                        // console.log(ingredient);
                        const webMap = ingredient?.length ? [...new Set(ingredient.flatMap(
                            i => i.planting_areas.map(m => m
                                .webmap)))] // Dùng flatMap để làm phẳng mảng
                            .join(', ') :
                            '';

                        const plantingAreas = ingredient.length ?
                            ingredient.map(i => i.planting_areas).flat() : [];

                        function groupByPlantationAndUnit(plantingAreas) {
                            return plantingAreas.reduce((groups, plot) => {
                                // Nhóm theo plantation và unit
                                const plantationKey = plot.plantation ||
                                    ''; // Nếu không có plantation thì nhóm vào 'other'
                                const unitKey = plot.unit ||
                                    ''; // Nếu không có unit thì nhóm vào 'other'

                                // Nếu nhóm plantation chưa tồn tại, tạo nhóm mới
                                if (!groups[plantationKey]) {
                                    groups[plantationKey] = {};
                                }

                                // Nếu nhóm unit chưa tồn tại trong nhóm plantation, tạo nhóm mới
                                if (!groups[plantationKey][unitKey]) {
                                    groups[plantationKey][unitKey] = [];
                                }

                                // Thêm plot vào nhóm tương ứng
                                groups[plantationKey][unitKey].push(plot);

                                return groups;
                            }, {});
                        }
                        const groupedPlantingAreas = groupByPlantationAndUnit(
                            plantingAreas);
                        // console.log(groupedPlantingAreas);

                        const receivedFactory = ingredient?.length ? [...new Set(ingredient
                            .map(i => i.receiving_factory))].join(
                                ', ') :
                            '';
                        // console.log(receivedFactory);
                        const contract = batch.contract;
                        const deliveryMonthRaw = contract?.delivery_month;
                        let deliveryMonth = '';

                        if (deliveryMonthRaw) {
                            const date = new Date(deliveryMonthRaw);
                            const month = ('0' + (date.getMonth() + 1)).slice(-
                                2); // Thêm số 0 nếu < 10
                            const year = date.getFullYear();
                            deliveryMonth = `${month}/${year}`;
                        }

                        function formatDate(dateString) {
                            if (!dateString) return '';
                            const date = new Date(dateString);
                            const day = ('0' + date.getDate()).slice(-
                                2); // Lấy ngày 2 chữ số
                            const month = ('0' + (date.getMonth() + 1)).slice(-
                                2); // Lấy tháng 2 chữ số
                            const year = date.getFullYear();
                            return `${day}/${month}/${year}`;
                        }

                        const containerClosingDate = formatDate(contract
                            ?.container_closing_date);
                        const deliveryDate = formatDate(contract?.delivery_date);
                        const dateSX = formatDate(batch?.date_sx);
                        const testingResult = response.batch.testing_results;
                        // Map từ value sang tên có dấu
                        const svrColorMap = {
                            dacam: "Da cam",
                            xanhlacaynhat: "Xanh lá cây nhạt",
                            nau: "Nâu",
                            do: "Đỏ"
                        };
                        let html = `

                            <div class="text-center fw-bold mb-3 info-title">{{ __('index.sanpham') }}: ${batch.batch_code}</div>
                            
                            <div class="row">
                                <!-- Batch Info -->
                                <div class="col-lg-6">
                                    <div class="item-wrap">
                                         <div class="card-title-info text-white text-center fw-bold info-title">
                                            <div class="info-title" style="color: white; font-weight: bold">{{ __('index.thongtinnhamay') }}</div>
                                        </div>
                                        <ul class="m-0 p-0">
                                            <li>{{ __('index.factory') }}: <span class="info factory">${receivedFactory || ''}</span></li>
                                            <li>{{ __('index.manufacturing_date') }}: <span class="info manufacturing-date">${dateSX || ''}</span></li>
                                            <li>{{ __('index.bale_weight') }}: <span class="info weight">${batch.batch_weight || ''}</span></li>
                                            <li>{{ __('index.lot_weight') }}: <span class="info batch-weight">${batch.banh_weight || ''}</span></li>
                                        </ul>
                                    </div>
                                </div>
 `;
                        if (batch.ingredients && batch.ingredients.length > 0) {

                            html += `
                                <div class="col-lg-6">
                                    <div class="item-wrap">
                                        <div class="card-title-info text-white text-center fw-bold info-title">
                                            <div class="info-title" style="color: white; font-weight: bold">{{ __('index.nguonnguyenlieu') }}
                                                 <span class="all-map" >
                                                    <i class="fa-sharp fa-solid fa-map-location-dot"></i>{{ __('index.map') }}
                                                </span>
                                            </div>
                                        </div>
                                         <div class="area">`

                            batch.ingredients.forEach(function (ingredient) {
                                html += `
                                <div class="tag">
                                    <div class="tag-ingredient d-flex justify-content-between align-items-center">
                                        ${ingredient.farm_name || ''} - ${ingredient.unit || ''}
                                        <i class="fa-solid fa-angle-down icon-toggle-ingredient"></i>
                                    </div>
                                    <ul class="m-0 p-2 tag-detail-ingredient mt-2 hidden">
                                        <div class="mb-3">                                        
                                        <div class="detail-title">{{ __('index.ttvc') }}</div>
                                        <li>{{ __('index.soxe') }}: <span class="info factory">${ingredient.vehicle ? ingredient.vehicle.vehicle_number : ''}</span></li>
                                        </div>
                                        <div class="mb-3">    
                                        <div class="detail-title">{{ __('index.nt') }}</div>
                                        <li>{{ __('index.giong_cay') }}: <span class="info code">${ingredient.tree_type || ''}</span></li>
                                        <li>{{ __('index.loai_mu') }}: <span class="info">${ingredient.type_of_pus ? ingredient.type_of_pus.name_pus : ''}</span></li>
                                        <li>{{ __('index.ngay_nhan') }}: <span class="info">${formatDate(ingredient.received_date) || ''}</span></li>
                                        <li>{{ __('index.ngay_cao') }}: <span class="info factory">${formatDate(ingredient.harvesting_date) || ''}</span></li>
                                        <li>{{ __('index.ngay_kt_cao') }}: <span class="info factory">${formatDate(ingredient.end_harvest_date) || ''}</span></li>
                                        </div>
                                        <div class="mb-3"> 
                                        <div class="detail-title">{{ __('index.ctvc') }}</div>
                                        <li>{{ __('index.ban_do_vuon_cay') }}: <span class="info factory"><a target="_blank" href="${webMap}">${webMap}</a></span></li>
                                        <li>{{ __('index.lovuoncay') }}: <span class="info factory"></span></li>
                                        
                                        <div class="plots">
                                            ${renderPlantPlots(groupedPlantingAreas, ingredient)}
                                        </div>
                                        </div>
                                    </ul>
                                </div>
                                    `;
                            });
                            html += `</div>
                                </div>
                                </div>`;

                            function renderPlantPlots(groupedPlantingAreas, ingredient) {
                                let html = ''; // Khởi tạo biến html để chứa nội dung

                                // Lọc ra nhóm plantation và unit tương ứng với ingredient.farm_name và ingredient.unit
                                const plantationKey = ingredient.farm_name ||
                                    ''; // Nếu không có farm_name thì nhóm vào 'other'
                                const unitKey = ingredient.unit ||
                                    ''; // Nếu không có unit thì nhóm vào 'other'

                                // Kiểm tra nếu groupedPlantingAreas có plantationKey và unitKey
                                if (groupedPlantingAreas[plantationKey] &&
                                    groupedPlantingAreas[plantationKey][unitKey]) {
                                    const plots = groupedPlantingAreas[plantationKey][
                                        unitKey
                                    ];

                                    // Tạo HTML cho các plots trong nhóm
                                    html += `
        <div class="plantation-group">
            ${plots.map(plot => `
                <div class="plot-wrap d-flex align-items-center">
                    <div class="plot w-100">
                        <div class="plot-name d-flex justify-content-between align-items-center">
                            ${plot.id_plot || ''}
                            <i class="fa-solid fa-angle-down icon-toggle-ingredient"></i>
                        </div>
                         <div class="plot-detail-ingredient hidden"> <!-- Toggle hidden ở đây -->
                            <div class="row m-0 p-0">

                            <div class="col-6 text-black">fid: <span class="info">${plot.fid || ''}</span></div>
                            <div class="col-6 text-black">idPlot: <span class="info">${plot.id_plot || ''}</span></div>
                            <div class="col-6 text-black">country: <span class="info">${plot.quocgia || ''}</span></div>
                            <div class="col-6 text-black">plot: <span class="info">${plot.plot || ''}</span></div>
                            <div class="col-6 text-black">tappingY: <span class="info">${plot.tapping_y || ''}</span></div>
                            <div class="col-6 text-black">find: <span class="info">${plot.find || ''}</span></div>
                            <div class="col-6 text-black">idmap: <span class="info">${plot.idmap || ''}</span></div>
                            <div class="col-6 text-black">producer: <span class="info">${plot.nhasx || ''}</span></div>
                            <div class="col-6 text-black">plantation: <span class="info">${plot.plantation || ''}</span></div>
                            <div class="col-6 text-black">plantingY: <span class="info">${plot.planting_y || ''}</span></div>
                            <div class="col-6 text-black">areaHa: <span class="info">${plot.area_ha || ''}</span></div>
                            <div class="col-6 text-black">repTime: <span class="info">${plot.repl_time || ''}</span></div>
                            <div class="col-12 text-center">
                                <div class="map mt-2 text-center" data-id_plot="${plot.id_plot || ''}">
                                    <i class="fa-sharp fa-solid fa-map-location-dot"></i> {{ __('index.map') }}
                                </div>
                            </div>
                            </div>

                        </div>
                    </div>
                    <div class="d-flex copyjson justify-content-center align-items-center"
                         title="Copy GeoJson" style="width:40px; color: #198754"
                         data-json='${JSON.stringify(plot.geo_json || '')}'>
                        <i class="fa-solid fa-copy"></i>
                    </div>
                </div>
            `).join('')}
        </div>
        `;
                                }
                                return html;
                            }

                            initToggleEvents();
                        } else {
                            html += '<p class="text-black">Không có nguồn nguyên liệu.</p>';

                        }

                        if (contract) {
                            html += `
                                    <div class="col-lg-6">
                                        <div class="item-wrap">
                                            <div class="card-title-info text-white text-center fw-bold info-title">
                                                <div class="info-title" style="color: white; font-weight: bold">{{ __('index.chitiet_hopdong') }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="detail-title">{{ __('index.tthd') }}</div>
                                                <ul class="m-0 p-0">
                                                    <li>{{ __('index.mahopdong') }}: <span class="info code">${contract.contract_code || ''}</span></li>
                                                    <li>{{ __('index.original_contract_number') }}: <span class="info">${contract.original_contract_number || ''}</span></li>
                                                    <li>{{ __('index.ngaygiaohang') }}: <span class="info factory">${deliveryDate || ''}</span></li>
                                                </ul>
                                            </div>
                                            <div class="mb-3">
                                                <div class="detail-title">{{ __('index.lohang') }}</div>
                                                <ul class="m-0 p-0">
                                                    <li>{{ __('index.product_type') }}: <span class="info">${contract.product_type_name || ''}</span></li>
                                                    <li>{{ __('index.production_or_trade_unit') }}: <span class="info">${contract.production_or_trade_unit || ''}</span></li>
                                                    <li>{{ __('index.market') }}: <span class="info">${contract.market || ''}</span></li>
                                                    <li>{{ __('index.third_party_sale') }}: <span class="info">${contract.third_party_sale || ''}</span></li>
                                                    <li>{{ __('index.delivery_month') }}: <span class="info">${deliveryMonth}</span></li>
                                                    <li>{{ __('index.quantity') }}: <span class="info">${contract.quantity || ''}</span></li>
                                                    <li>{{ __('index.container_closing_date') }}: <span class="info">${containerClosingDate || ''}</span></li>
                                                    <li>{{ __('index.packaging_type') }}: <span class="info">${contract.packaging_type || ''}</span></li>  
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                        } else {
                            html += `<div class="col-lg-6">
                                    <div class="item-wrap">
                                        <div class="card-title-info text-white text-center fw-bold info-title">
                                            <div class="info-title" style="color: white; font-weight: bold">{{ __('index.chitiet_hopdong') }}</div>
                                        </div>
                                        <p class="text-black">Không có chi tiết hợp đồng.</p>
                                        <div>
                                            </div>
                                                `;
                        }

                        if (testingResult) {
                            html += `
                                    <div class="col-lg-6">
                                        <div class="item-wrap">
                                            <div class="card-title-info text-white text-center fw-bold info-title">
                                                <div class="info-title" style="color: white; font-weight: bold">{{ __('index.kqkn') }}</div>
                                            </div>

                            `;
                            if (Array.isArray(testingResult)) {
                                testingResult.forEach(result => {
                                    html += `
                                    <ul class="m-0 p-0">
                                        <li>{{ __('index.ngayguimau') }}: <span class="info code">${formatDate(result.ngay_gui_mau) || ''}</span></li>
                                        <li>{{ __('index.ngaykn') }}: <span class="info weight">${formatDate(result.ngay_kiem_nghiem) || ''}</span></li>
                                        <li>{{ __('index.xephang') }}: <span class="info batch-weight">${(result.rank || '').toUpperCase()}</span></li>
                                    </ul>
                                     <ul class="m-0 p-0 targets row mt-3">
                                        <li><strong>Rank:</strong> <span class="info">${(result.rank || '').toUpperCase()}</span></li>
                                        <li><strong>Ngày gửi mẫu:</strong> <span class="info">${formatDate(result.ngay_gui_mau) || ''}</span></li>
                                        <li><strong>Ngày kiểm nghiệm:</strong> <span class="info">${formatDate(result.ngay_kiem_nghiem) || ''}</span></li>
                                        <li><strong>Impurities:</strong> <span class="info">${result.svr_impurity || 'N/A'}</span></li>
                                        <li><strong>Ash:</strong> <span class="info">${result.svr_ash || 'N/A'}</span></li>
                                        <li><strong>Volatile:</strong> <span class="info">${result.svr_volatile || 'N/A'}</span></li>
                                        <li><strong>Nitrogen:</strong> <span class="info">${result.svr_nitrogen || 'N/A'}</span></li>
                                        <li><strong>PO:</strong> <span class="info">${result.svr_po || 'N/A'}</span></li>
                                        <li><strong>PRI:</strong> <span class="info">${result.svr_pri || 'N/A'}</span></li>
                                        <li><strong>Color:</strong> <span class="info">${svrColorMap[testingResult.svr_color] || 'N/A'}</span></li>
                                        <li><strong>Lovibond:</strong> <span class="info">${result.svr_vr || 'N/A'}</span></li>
                                        <li><strong>Viscous:</strong> <span class="info">${result.svr_viscous || 'N/A'}</span></li>
                                        <li><strong>Vul:</strong> <span class="info">${result.svr_vul || 'N/A'}</span></li>
                                    </ul>
                                    `;
                                });
                            } else {
                                html += `
                                    <ul class="m-0 p-0">
                                        <li>{{ __('index.ngayguimau') }}: <span class="info code">${formatDate(testingResult.ngay_gui_mau) || ''}</span></li>
                                        <li>{{ __('index.ngaykn') }}: <span class="info weight">${formatDate(testingResult.ngay_kiem_nghiem) || ''}</span></li>
                                        <li>{{ __('index.xephang') }}: <span class="info batch-weight">${(testingResult.rank || '').toUpperCase()}</span></li>
                                    </ul>
                                    <div class="targets row mt-3">
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Impurities</div>
                                                <div class="target-value">${testingResult.svr_impurity || ''}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Ash</div>
                                                <div class="target-value">${testingResult.svr_ash || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Volatile</div>
                                                <div class="target-value">${testingResult.svr_volatile || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Nitrogen</div>
                                                <div class="target-value">${testingResult.svr_nitrogen || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">PO</div>
                                                <div class="target-value">${testingResult.svr_po || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">PRI</div>
                                                <div class="target-value">${testingResult.svr_pri || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Color</div>
                                                <div class="target-value">${svrColorMap[testingResult.svr_color] || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Lovibond</div>
                                                <div class="target-value">${testingResult.svr_vr || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Viscous</div>
                                                <div class="target-value">${testingResult.svr_viscous || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Vul</div>
                                                <div class="target-value">${testingResult.svr_vul || 'N/A'}</div>
                                            </div>
                                        </div>
                                    </div>
                                </ul>
                                `;
                            }
                            html += `
                                            </div>
                                        </div>


                                        `;
                        } else {
                            html += `
                            <div class="col-lg-6">
                                        <div class="item-wrap">
                                            <div class="card-title-info text-white text-center fw-bold info-title">
                                                <div class="info-title" style="color: white; font-weight: bold">{{ __('index.kqkn') }}</div>
                                            </div>
                                            <p class="text-black">Không có kết quả kiểm nghiệm.</p>
                                        </div>
                                    </div>

                            `;
                        }

                        resultsDiv.html(html);

                    } else {
                        resultsDiv.html('<p>' + response.message + '</p>');
                    }
                },
                error: function (xhr) {
                    loader.css('opacity', 0);
                    const errorMsg = xhr.status === 404 || xhr.status === 403 ?
                        'Không tìm thấy lô hàng!' :
                        'Đã xảy ra lỗi, vui lòng thử lại!';
                    resultsDiv.html('<p>' + errorMsg + '</p>');
                }
            });
        });
        $('#searchInput').on('keypress', function (e) {
            if (e.which === 13) {
                $('#searchButton').click();
            }
        });



        $(document).on('click', '.plot-code', function () {
            const loader = $('.loader-plot-code');
            const resultsDiv = $('#plot-code-result');
            const batchCode = $(this).data('code');

            loader.css('opacity', 1);
            resultsDiv.html('');

            $.ajax({
                url: '{{ route('batches.indexapi') }}',
                method: 'GET',
                headers: {
                    'token': apiToken
                },
                data: {
                    batch_code: batchCode
                },
                success: function (response) {
                    loader.css('opacity', 0);
                    window.batchResponse = response;
                    if (response.status === 200) {
                        const batch = response.batch;
                        const ingredient = batch.ingredients;
                        // console.log(ingredient);
                        const webMap = ingredient?.length ? [...new Set(ingredient.flatMap(
                            i => i.planting_areas.map(m => m
                                .webmap)))] // Dùng flatMap để làm phẳng mảng
                            .join(', ') :
                            '';

                        const plantingAreas = ingredient.length ?
                            ingredient.map(i => i.planting_areas).flat() : [];

                        function groupByPlantationAndUnit(plantingAreas) {
                            return plantingAreas.reduce((groups, plot) => {
                                // Nhóm theo plantation và unit
                                const plantationKey = plot.plantation ||
                                    ''; // Nếu không có plantation thì nhóm vào 'other'
                                const unitKey = plot.unit ||
                                    ''; // Nếu không có unit thì nhóm vào 'other'

                                // Nếu nhóm plantation chưa tồn tại, tạo nhóm mới
                                if (!groups[plantationKey]) {
                                    groups[plantationKey] = {};
                                }

                                // Nếu nhóm unit chưa tồn tại trong nhóm plantation, tạo nhóm mới
                                if (!groups[plantationKey][unitKey]) {
                                    groups[plantationKey][unitKey] = [];
                                }

                                // Thêm plot vào nhóm tương ứng
                                groups[plantationKey][unitKey].push(plot);

                                return groups;
                            }, {});
                        }
                        const groupedPlantingAreas = groupByPlantationAndUnit(
                            plantingAreas);
                        // console.log(groupedPlantingAreas);

                        const receivedFactory = ingredient?.length ? [...new Set(ingredient
                            .map(i => i.receiving_factory))].join(
                                ', ') :
                            '';
                        // console.log(receivedFactory);
                        const contract = batch.contract;
                        const deliveryMonthRaw = contract?.delivery_month;
                        let deliveryMonth = '';

                        if (deliveryMonthRaw) {
                            const date = new Date(deliveryMonthRaw);
                            const month = ('0' + (date.getMonth() + 1)).slice(-
                                2); // Thêm số 0 nếu < 10
                            const year = date.getFullYear();
                            deliveryMonth = `${month}/${year}`;
                        }

                        function formatDate(dateString) {
                            if (!dateString) return '';
                            const date = new Date(dateString);
                            const day = ('0' + date.getDate()).slice(-
                                2); // Lấy ngày 2 chữ số
                            const month = ('0' + (date.getMonth() + 1)).slice(-
                                2); // Lấy tháng 2 chữ số
                            const year = date.getFullYear();
                            return `${day}/${month}/${year}`;
                        }

                        const containerClosingDate = formatDate(contract
                            ?.container_closing_date);
                        const deliveryDate = formatDate(contract?.delivery_date);
                        const dateSX = formatDate(batch?.date_sx);
                        const testingResult = response.batch.testing_results;
                        // Map từ value sang tên có dấu
                        const svrColorMap = {
                            dacam: "Da cam",
                            xanhlacaynhat: "Xanh lá cây nhạt",
                            nau: "Nâu",
                            do: "Đỏ"
                        };
                        let html = `

                            <div class="text-center fw-bold mb-3 info-title">{{ __('index.sanpham') }}: ${batch.batch_code}</div>
                            
                            <div class="row">
                                <!-- Batch Info -->
                                <div class="col-lg-6">
                                    <div class="item-wrap">
                                         <div class="card-title-info text-white text-center fw-bold info-title">
                                            <div class="info-title" style="color: white; font-weight: bold">{{ __('index.thongtinnhamay') }}</div>
                                        </div>
                                        <ul class="m-0 p-0">
                                            <li>{{ __('index.factory') }}: <span class="info factory">${receivedFactory || ''}</span></li>
                                            <li>{{ __('index.manufacturing_date') }}: <span class="info manufacturing-date">${dateSX || ''}</span></li>
                                            <li>{{ __('index.bale_weight') }}: <span class="info weight">${batch.batch_weight || ''}</span></li>
                                            <li>{{ __('index.lot_weight') }}: <span class="info batch-weight">${batch.banh_weight || ''}</span></li>
                                        </ul>
                                    </div>
                                </div>
 `;
                        if (batch.ingredients && batch.ingredients.length > 0) {

                            html += `
                                <div class="col-lg-6">
                                    <div class="item-wrap">
                                        <div class="card-title-info text-white text-center fw-bold info-title">
                                            <div class="info-title" style="color: white; font-weight: bold">{{ __('index.nguonnguyenlieu') }}
                                                 <span class="all-map" >
                                                    <i class="fa-sharp fa-solid fa-map-location-dot"></i>{{ __('index.map') }}
                                                </span>
                                            </div>
                                        </div>
                                         <div class="area">`

                            batch.ingredients.forEach(function (ingredient) {
                                html += `
                                <div class="tag">
                                    <div class="tag-ingredient d-flex justify-content-between align-items-center">
                                        ${ingredient.farm_name || ''} - ${ingredient.unit || ''}
                                        <i class="fa-solid fa-angle-down icon-toggle-ingredient"></i>
                                    </div>
                                    <ul class="m-0 p-2 tag-detail-ingredient mt-2 hidden">
                                        <div class="mb-3">                                        
                                        <div class="detail-title">{{ __('index.ttvc') }}</div>
                                        <li>{{ __('index.soxe') }}: <span class="info factory">${ingredient.vehicle ? ingredient.vehicle.vehicle_number : ''}</span></li>
                                        </div>
                                        <div class="mb-3">    
                                        <div class="detail-title">{{ __('index.nt') }}</div>
                                        <li>{{ __('index.giong_cay') }}: <span class="info code">${ingredient.tree_type || ''}</span></li>
                                        <li>{{ __('index.loai_mu') }}: <span class="info">${ingredient.type_of_pus ? ingredient.type_of_pus.name_pus : ''}</span></li>
                                        <li>{{ __('index.ngay_nhan') }}: <span class="info">${formatDate(ingredient.received_date) || ''}</span></li>
                                        <li>{{ __('index.ngay_cao') }}: <span class="info factory">${formatDate(ingredient.harvesting_date) || ''}</span></li>
                                        <li>{{ __('index.ngay_kt_cao') }}: <span class="info factory">${formatDate(ingredient.end_harvest_date) || ''}</span></li>
                                        </div>
                                        <div class="mb-3"> 
                                        <div class="detail-title">{{ __('index.ctvc') }}</div>
                                        <li>{{ __('index.ban_do_vuon_cay') }}: <span class="info factory"><a target="_blank" href="${webMap}">${webMap}</a></span></li>
                                        <li>{{ __('index.lovuoncay') }}: <span class="info factory"></span></li>
                                        
                                        <div class="plots">
                                            ${renderPlantPlots(groupedPlantingAreas, ingredient)}
                                        </div>
                                        </div>
                                    </ul>
                                </div>
                                    `;
                            });
                            html += `</div>
                                </div>
                                </div>`;

                            function renderPlantPlots(groupedPlantingAreas, ingredient) {
                                let html = ''; // Khởi tạo biến html để chứa nội dung

                                // Lọc ra nhóm plantation và unit tương ứng với ingredient.farm_name và ingredient.unit
                                const plantationKey = ingredient.farm_name ||
                                    ''; // Nếu không có farm_name thì nhóm vào 'other'
                                const unitKey = ingredient.unit ||
                                    ''; // Nếu không có unit thì nhóm vào 'other'

                                // Kiểm tra nếu groupedPlantingAreas có plantationKey và unitKey
                                if (groupedPlantingAreas[plantationKey] &&
                                    groupedPlantingAreas[plantationKey][unitKey]) {
                                    const plots = groupedPlantingAreas[plantationKey][
                                        unitKey
                                    ];

                                    // Tạo HTML cho các plots trong nhóm
                                    html += `
        <div class="plantation-group">
            ${plots.map(plot => `
                <div class="plot-wrap d-flex align-items-center">
                    <div class="plot w-100">
                        <div class="plot-name d-flex justify-content-between align-items-center">
                            ${plot.id_plot || ''}
                            <i class="fa-solid fa-angle-down icon-toggle-ingredient"></i>
                        </div>
                         <div class="plot-detail-ingredient hidden"> <!-- Toggle hidden ở đây -->
                            <div class="row m-0 p-0">

                            <div class="col-6 text-black">fid: <span class="info">${plot.fid || ''}</span></div>
                            <div class="col-6 text-black">idPlot: <span class="info">${plot.id_plot || ''}</span></div>
                            <div class="col-6 text-black">country: <span class="info">${plot.quocgia || ''}</span></div>
                            <div class="col-6 text-black">plot: <span class="info">${plot.plot || ''}</span></div>
                            <div class="col-6 text-black">tappingY: <span class="info">${plot.tapping_y || ''}</span></div>
                            <div class="col-6 text-black">find: <span class="info">${plot.find || ''}</span></div>
                            <div class="col-6 text-black">idmap: <span class="info">${plot.idmap || ''}</span></div>
                            <div class="col-6 text-black">producer: <span class="info">${plot.nhasx || ''}</span></div>
                            <div class="col-6 text-black">plantation: <span class="info">${plot.plantation || ''}</span></div>
                            <div class="col-6 text-black">plantingY: <span class="info">${plot.planting_y || ''}</span></div>
                            <div class="col-6 text-black">areaHa: <span class="info">${plot.area_ha || ''}</span></div>
                            <div class="col-6 text-black">repTime: <span class="info">${plot.repl_time || ''}</span></div>
                            <div class="col-12 text-center">
                                <div class="map mt-2 text-center" data-id_plot="${plot.id_plot || ''}">
                                    <i class="fa-sharp fa-solid fa-map-location-dot"></i> {{ __('index.map') }}
                                </div>
                            </div>
                            </div>

                        </div>
                    </div>
                    <div class="d-flex copyjson justify-content-center align-items-center"
                         title="Copy GeoJson" style="width:40px; color: #198754"
                         data-json='${JSON.stringify(plot.geo_json || '')}'>
                        <i class="fa-solid fa-copy"></i>
                    </div>
                </div>
            `).join('')}
        </div>
        `;
                                }
                                return html;
                            }

                            initToggleEvents();
                        } else {
                            html += '<p class="text-black">Không có nguồn nguyên liệu.</p>';

                        }

                        if (contract) {
                            html += `
                                    <div class="col-lg-6">
                                        <div class="item-wrap">
                                            <div class="card-title-info text-white text-center fw-bold info-title">
                                                <div class="info-title" style="color: white; font-weight: bold">{{ __('index.chitiet_hopdong') }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="detail-title">{{ __('index.tthd') }}</div>
                                                <ul class="m-0 p-0">
                                                    <li>{{ __('index.mahopdong') }}: <span class="info code">${contract.contract_code || ''}</span></li>
                                                    <li>{{ __('index.original_contract_number') }}: <span class="info">${contract.original_contract_number || ''}</span></li>
                                                    <li>{{ __('index.ngaygiaohang') }}: <span class="info factory">${deliveryDate || ''}</span></li>
                                                </ul>
                                            </div>
                                            <div class="mb-3">
                                                <div class="detail-title">{{ __('index.lohang') }}</div>
                                                <ul class="m-0 p-0">
                                                    <li>{{ __('index.product_type') }}: <span class="info">${contract.product_type_name || ''}</span></li>
                                                    <li>{{ __('index.production_or_trade_unit') }}: <span class="info">${contract.production_or_trade_unit || ''}</span></li>
                                                    <li>{{ __('index.market') }}: <span class="info">${contract.market || ''}</span></li>
                                                    <li>{{ __('index.third_party_sale') }}: <span class="info">${contract.third_party_sale || ''}</span></li>
                                                    <li>{{ __('index.delivery_month') }}: <span class="info">${deliveryMonth}</span></li>
                                                    <li>{{ __('index.quantity') }}: <span class="info">${contract.quantity || ''}</span></li>
                                                    <li>{{ __('index.container_closing_date') }}: <span class="info">${containerClosingDate || ''}</span></li>
                                                    <li>{{ __('index.packaging_type') }}: <span class="info">${contract.packaging_type || ''}</span></li>  
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                        } else {
                            html += `<div class="col-lg-6">
                                    <div class="item-wrap">
                                        <div class="card-title-info text-white text-center fw-bold info-title">
                                            <div class="info-title" style="color: white; font-weight: bold">{{ __('index.chitiet_hopdong') }}</div>
                                        </div>
                                        <p class="text-black">Không có chi tiết hợp đồng.</p>
                                        <div>
                                            </div>
                                                `;
                        }

                        if (testingResult) {
                            html += `
                                    <div class="col-lg-6">
                                        <div class="item-wrap">
                                            <div class="card-title-info text-white text-center fw-bold info-title">
                                                <div class="info-title" style="color: white; font-weight: bold">{{ __('index.kqkn') }}</div>
                                            </div>

                            `;
                            if (Array.isArray(testingResult)) {
                                testingResult.forEach(result => {
                                    html += `
                                    <ul class="m-0 p-0">
                                        <li>{{ __('index.ngayguimau') }}: <span class="info code">${formatDate(result.ngay_gui_mau) || ''}</span></li>
                                        <li>{{ __('index.ngaykn') }}: <span class="info weight">${formatDate(result.ngay_kiem_nghiem) || ''}</span></li>
                                        <li>{{ __('index.xephang') }}: <span class="info batch-weight">${(result.rank || '').toUpperCase()}</span></li>
                                    </ul>
                                     <ul class="m-0 p-0 targets row mt-3">
                                        <li><strong>Rank:</strong> <span class="info">${(result.rank || '').toUpperCase()}</span></li>
                                        <li><strong>Ngày gửi mẫu:</strong> <span class="info">${formatDate(result.ngay_gui_mau) || ''}</span></li>
                                        <li><strong>Ngày kiểm nghiệm:</strong> <span class="info">${formatDate(result.ngay_kiem_nghiem) || ''}</span></li>
                                        <li><strong>Impurities:</strong> <span class="info">${result.svr_impurity || 'N/A'}</span></li>
                                        <li><strong>Ash:</strong> <span class="info">${result.svr_ash || 'N/A'}</span></li>
                                        <li><strong>Volatile:</strong> <span class="info">${result.svr_volatile || 'N/A'}</span></li>
                                        <li><strong>Nitrogen:</strong> <span class="info">${result.svr_nitrogen || 'N/A'}</span></li>
                                        <li><strong>PO:</strong> <span class="info">${result.svr_po || 'N/A'}</span></li>
                                        <li><strong>PRI:</strong> <span class="info">${result.svr_pri || 'N/A'}</span></li>
                                        <li><strong>Color:</strong> <span class="info">${svrColorMap[testingResult.svr_color] || 'N/A'}</span></li>
                                        <li><strong>Lovibond:</strong> <span class="info">${result.svr_vr || 'N/A'}</span></li>
                                        <li><strong>Viscous:</strong> <span class="info">${result.svr_viscous || 'N/A'}</span></li>
                                        <li><strong>Vul:</strong> <span class="info">${result.svr_vul || 'N/A'}</span></li>
                                    </ul>
                                    `;
                                });
                            } else {
                                html += `
                                    <ul class="m-0 p-0">
                                        <li>{{ __('index.ngayguimau') }}: <span class="info code">${formatDate(testingResult.ngay_gui_mau) || ''}</span></li>
                                        <li>{{ __('index.ngaykn') }}: <span class="info weight">${formatDate(testingResult.ngay_kiem_nghiem) || ''}</span></li>
                                        <li>{{ __('index.xephang') }}: <span class="info batch-weight">${(testingResult.rank || '').toUpperCase()}</span></li>
                                    </ul>
                                    <div class="targets row mt-3">
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Impurities</div>
                                                <div class="target-value">${testingResult.svr_impurity || ''}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Ash</div>
                                                <div class="target-value">${testingResult.svr_ash || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Volatile</div>
                                                <div class="target-value">${testingResult.svr_volatile || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Nitrogen</div>
                                                <div class="target-value">${testingResult.svr_nitrogen || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">PO</div>
                                                <div class="target-value">${testingResult.svr_po || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">PRI</div>
                                                <div class="target-value">${testingResult.svr_pri || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Color</div>
                                                <div class="target-value">${svrColorMap[testingResult.svr_color] || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Lovibond</div>
                                                <div class="target-value">${testingResult.svr_vr || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Viscous</div>
                                                <div class="target-value">${testingResult.svr_viscous || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-6" title="">
                                            <div class="target text-center">
                                                <div class="target-name">Vul</div>
                                                <div class="target-value">${testingResult.svr_vul || 'N/A'}</div>
                                            </div>
                                        </div>
                                    </div>
                                </ul>
                                `;
                            }
                            html += `
                                            </div>
                                        </div>


                                        `;
                        } else {
                            html += `
                            <div class="col-lg-6">
                                        <div class="item-wrap">
                                            <div class="card-title-info text-white text-center fw-bold info-title">
                                                <div class="info-title" style="color: white; font-weight: bold">{{ __('index.kqkn') }}</div>
                                            </div>
                                            <p class="text-black">Không có kết quả kiểm nghiệm.</p>
                                        </div>
                                    </div>

                            `;
                        }

                        resultsDiv.html(html);

                    } else {
                        resultsDiv.html('<p>' + response.message + '</p>');
                    }
                },
                error: function (xhr) {
                    loader.css('opacity', 0);
                    const errorMsg = xhr.status === 404 || xhr.status === 403 ?
                        'Không tìm thấy lô hàng!' :
                        'Đã xảy ra lỗi, vui lòng thử lại!';
                    resultsDiv.html('<p>' + errorMsg + '</p>');
                }
            });
        });
        // $('#searchInput').on('keypress', function (e) {
        //     if (e.which === 13) {
        //         $('#searchButton').click();
        //     }
        // });
    });
</script>



<script>
    $(document).ready(function () {

        $('.menu-item').first().on('click', function (e) {
            e.preventDefault();

            $('.menu-item').each(function (index) {
                $(this).delay(index * 100).animate({
                    opacity: 0,
                    margin: '0px auto 20px auto'
                }, 300);
            });

            setTimeout(function () {
                $('.menu').animate({
                    height: '0px'
                }, 300, function () {

                    $('.search').css('opacity', 1).css('transform', 'translateX(0)');
                });
            }, 600);
        });

        $('.back').on('click', function () {

            $('.search').css('opacity', 0).css('transform', 'translateX(-100%)');
            $('.results').empty();


            setTimeout(function () {
                $('.menu').animate({
                    height: '100%'
                }, 300);
                $('.menu-item').each(function (index) {
                    $(this).delay(index * 100).animate({
                        opacity: 1,
                        margin: '0px auto 20px auto'
                    }, 300);
                });
            }, 300);
        });



    });
</script>


<script type="module">
    import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/5.0.375/pdf.min.mjs';
    // Cấu hình workerSrc
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/5.0.375/pdf.worker.mjs';

    $('.menu-item').eq(2).on('click', function (e) {
        e.preventDefault();
        $('.menu-item').each(function (index) {
            $(this)
                .delay(index * 100)
                .animate({
                    opacity: 0,
                    margin: '0px auto 20px auto'
                }, 300);
        });

        setTimeout(function () {
            $('.menu').animate({
                height: '0px'
            }, 300, function () {
                $('.search').animate({
                    height: '0px'
                }, 300, function () {
                    $('.contracts').animate({
                        // minHeight: '0px', // Thu nhỏ contracts với min-height
                        height: '0px'
                    }, 300, function () {
                        // Đảm bảo các phần tử chuyển trạng thái sau khi ẩn
                        $('.certificates').css('opacity', 1).css('transform',
                            'translateX(0)');
                        fetchCertificate();
                    });
                });
            });
        }, 600);
    });

    $('.backCertificate').on('click', function () {
        $('.search').css('height', 'auto');
        $('.contracts').css('height', 'auto');
        // Ẩn certificates
        $('.certificates').css('opacity', 0).css('transform', 'translateX(100%)');
        // Dọn sạch certificate_list
        $('.certificate_list').empty();

        setTimeout(function () {
            // Hiển thị lại menu và các mục trong menu
            $('.menu').animate({
                height: 'auto' // Hoặc '100%' nếu muốn chiều cao cố định
            }, 300);
            $('.search').animate({
                height: 'auto' // Điều chỉnh chiều cao cho search nếu cần
            }, 300);
            // $('.contracts').animate({
            //     minHeight: '500px',
            //     height: 'auto' // Hiển thị lại contracts nếu cần
            // }, 300);

            // Mở lại các mục menu với hiệu ứng mượt mà
            $('.menu-item').each(function (index) {
                $(this)
                    .delay(index * 100) // Điều chỉnh độ trễ giữa các mục
                    .animate({
                        opacity: 1,
                        margin: '0px auto 20px auto'
                    }, 300);
            });
        }, 300); // Thời gian delay trước khi thực hiện các animation
    });



    function fetchCertificate() {
        $('.loader').css('opacity', 1);
        $.ajax({
            url: '/api/certificates/list',
            method: 'GET',
            headers: {
                'token': apiToken
            },
            success: function (response) {
                if (response.success) {
                    const certificates = response.data;
                    const tags = certificates.map((item) => {
                        const pdfTag = loadPdf(item); // Tạo HTML cho chứng chỉ
                        // Gọi render PDF vào canvas ngay sau khi tạo HTML
                        renderPdfPage(item.file_name, `pdf-canvas-${item.id}`);
                        return pdfTag;
                    });
                    $('.certificate_list').html(tags.join('')); // Chèn HTML vào .list

                    const certificateList = $('.certificate_list');
                    const items = certificateList.find('.certificate-item');

                    // Kiểm tra xem số lượng item có phải là 1
                    if (items.length % 2 === 1) {
                        certificateList.css('justify-content', 'flex-start');
                    } else {
                        certificateList.css('justify-content', 'center');
                    }
                } else {
                    alert(response.message || 'Lỗi không xác định!');
                }
            },
            error: function () {
                alert('Lỗi kết nối đến server!');
            },
            complete: function () {
                $('.loader').css('opacity', 0);
            }
        });
    }

    function loadPdf(item) {
        return `
                                    <div class="certificate-item" data-pdf-url="${item.file_name}">
                                        <canvas id="pdf-canvas-${item.id}" class="pdf-canvas"></canvas>
                                        <p class="certificate-name">${item.name}</p>
                                    </div>
                                    `;
    }

    $(document).on('click', '.certificate-item', function () {
        const pdfUrl = $(this).data('pdf-url');
        openPdf(pdfUrl);
    });

    function openPdf(pdfUrl) {
        // console.log("pdfUrl", pdfUrl);
        window.open(pdfUrl, '_blank'); // Mở trang PDF trong cửa sổ mới
    }

    function renderPdfPage(pdfUrl, canvasId) {
        // Kiểm tra xem URL có hợp lệ không (có thể thay thế bằng cách kiểm tra đường dẫn tệp trước khi gọi render)
        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        loadingTask.promise.then(function (pdf) {
            pdf.getPage(1).then(function (page) {
                const canvas = document.getElementById(canvasId);
                const context = canvas.getContext('2d');
                const viewport = page.getViewport({
                    scale: 1
                });
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                page.render({
                    canvasContext: context,
                    viewport: viewport
                });
            }).catch(function (error) {
                console.error("Lỗi khi lấy trang PDF:", error);
                $(`#${canvasId}`).closest('.certificate-item').remove(); // Loại bỏ chứng chỉ nếu lỗi
            });
        }).catch(function (error) {
            console.error("Lỗi tải PDF:", error);
            $(`#${canvasId}`).closest('.certificate-item').remove(); // Loại bỏ chứng chỉ nếu lỗi
        });
    }
</script>

<script>
    $(document).ready(function () {

        $('.menu-item').eq(1).on('click', function (e) {
            e.preventDefault();

            $('.menu-item').each(function (index) {
                $(this)
                    .delay(index * 100)
                    .animate({
                        opacity: 0,
                        margin: '0px auto 20px auto'
                    }, 300);
            });

            setTimeout(function () {
                $('.menu').animate({
                    height: '0px'
                }, 300);
                $('.search').animate({
                    height: '0px'
                }, 300, function () {
                    $('.contracts').css('opacity', 1).css('transform', 'translateX(0)');
                    fetchContracts();
                },

                );
            }, 600);
        });

        $('.backContract').on('click', function () {
            $('.search').css('height', 'auto');
            // Hide contracts
            $('.contracts').css('opacity', 0).css('transform', 'translateX(100%)');
            $('.list').empty();
            setTimeout(function () {
                $('.menu').animate({
                    height: 'auto'
                }, 300);
                $('.search').animate({
                    height: 'auto'
                }, 300);
                $('.menu-item').each(function (index) {
                    $(this)
                        .delay(index * 100)
                        .animate({
                            opacity: 1,
                            margin: '0px auto 20px auto'
                        }, 300);
                });
            }, 300);
        });
    });


    function fetchContracts() {
        $('.loader').css('opacity', 1);
        $.ajax({
            url: '/api/contracts/list',
            method: 'GET',
            headers: {
                'token': apiToken
            },
            success: function (response) {
                if (response.success) {
                    const contracts = response.data;
                    // console.log(contracts)
                    // console.log(response.count);
                    const tags = contracts.map((item) => createContractTag(item));
                    $('.list').html(tags.join(''));
                    $('.tag-detail').hide(); // Hide contract details initially
                    $('#contract-count').text(contracts.length + ' {{ __('index.found') }}');

                    // $('.icon-toggle').on('click', function() {
                    //     const tagDetail = $(this).closest('.tag').find('.tag-detail');
                    //     tagDetail.slideToggle(); // Toggle visibility of contract details
                    //     $(this).toggleClass('fa-angle-down fa-angle-up'); // Toggle the icon
                    // });
                } else {
                    alert(response.message || 'Lỗi không xác định!');
                }
            },
            error: function () {
                alert('Lỗi kết nối đến server!');
            },
            complete: function () {
                $('.loader').css('opacity', 0);
            }
        });
    }

    function createContractTag(contract) {
        return `
                                    <div class="tag tag-contract" data-type="${contract.contract_type_name}" id="${contract.id}">
                                        <div class="tag-name d-flex justify-content-between align-items-center">
                                            <div style="color: #34A853">
                                                ${contract.contract_code} (<span class="contract-type">${String(contract.contract_type_name).replace(/,/g, ', ')}</span>)
                                            </div>
                                            <div class="d-flex justify-content-center align-items-center">
                                                <span class="loader loader-detail" style="opacity: 0; transition: opacity 0.3s"></span>
                                            </div>
                                            <i class="fa-solid fa-angle-down icon-toggle" style="color: #34A853"></i>
                                        </div>
                                        <div class="tag-detail contract-detail">
                                        </div>
                                    </div>
        `;
    }
</script>

<script>
    $(document).ready(function () {

        $('#search-contract').on('input', function () {
            const searchTerm = $(this).val().toLowerCase();

            filterContracts(searchTerm);
        });

        function filterContracts(searchTerm) {

            let visibleCount = 0;

            $('.tag').each(function () {
                const contractCode = $(this).text().toLowerCase();

                // console.log(contractCode);

                if (contractCode.includes(searchTerm)) {
                    $(this).show();
                    visibleCount++;

                } else {
                    $(this).hide();
                }
            });

            $('#contract-count').text(visibleCount + ' hợp đồng được tìm thấy');
        }
    });

    $(document).ready(function () {

        $('#contract-type-filter').on('change', function () {
            $('#search-contract').val('');
            const contractType = $(this).val().toLowerCase();
            filterContracts(contractType, $('#search-contract').val().toLowerCase());
        });

        function filterContracts(contractType, searchTerm) {
            let visibleCount = 0;

            $('.tag').each(function () {
                const contractCategory = $(this).data('type').toLowerCase();
                const matchType = contractCategory.includes(contractType) || contractType === '';
                if (matchType) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            $('#contract-count').text(visibleCount + ' hợp đồng được tìm thấy');
        }
    });
</script>

<script>
    $(document).ready(function () {
        $(".list").on("click", ".tag-name", function (e) {
            if ($(e.target).closest(".plot-code").length > 0) {
                return; // Không làm gì nếu click vào plot-code
            }
            const $tag = $(this).closest(".tag-contract");
            const id = $tag.attr("id");
            const detailDiv = $(`#${id} .tag-detail`);

            if (detailDiv.length > 0 && detailDiv.html().trim() === "") {
                // console.log("dsadsadsadasdas", detailDiv.html().trim());
                $tag.find(".loader-detail").css("opacity", 1);

                $.ajax({
                    url: `/api/contracts/get/${id}`, // Use the correct API endpoint
                    type: "GET",
                    dataType: "json",
                    headers: {
                        'token': apiToken
                    },
                    success: function (response) {
                        if (response.success) {
                            // console.log(response);

                            const data = response.data;
                            const contractDetails = data.contract_details ??
                                {}; // Contract details
                            // console.log('aaaaaa112', contractDetails);
                            const orderExports = data.order_export_list ?? [];
                            // console.log('aaaaaa', orderExports);

                            function formatDate(dateString) {
                                if (!dateString) return 'Không có';
                                const date = new Date(dateString);
                                const day = ('0' + date.getDate()).slice(-
                                    2); // Lấy ngày 2 chữ số
                                const month = ('0' + (date.getMonth() + 1)).slice(-
                                    2); // Lấy tháng 2 chữ số
                                const year = date.getFullYear();
                                return `${day}/${month}/${year}`;
                            }

                            const deliveryMonthRaw = contractDetails?.delivery_month;
                            let deliveryMonth = '';

                            if (deliveryMonthRaw) {
                                const date = new Date(deliveryMonthRaw);
                                const month = ('0' + (date.getMonth() + 1)).slice(-
                                    2); // Thêm số 0 nếu < 10
                                const year = date.getFullYear();
                                deliveryMonth = `${month}/${year}`;
                            }

                            let html = `
                                <div class="mb-3">
                                    <div class="detail-title">{{ __('index.tthd') }}</div>
                                    <ul class="m-0 p-0" style="color: black">
                                        <li>{{ __('index.mahopdong') }}: <span class="info info-contract">${contractDetails.contract_code || ''}</span></li>
                                        <li>{{ __('index.original_contract_number') }}: <span class="info">${contractDetails.original_contract_number || ''}</span></li>
                                        <li>{{ __('index.ngaygiaohang') }}: <span class="info info-contract">${formatDate(contractDetails.delivery_date) || ''}</span></li>
                                    </ul>
                                </div>
                                <div class="mb-3">
                                    <div class="detail-title">{{ __('index.lohang') }}</div>
                                    <ul class="m-0 p-0" style="color: black">
                                        <li>{{ __('index.product_type') }}: <span class="info">${contractDetails.product_type_name || ''}</span></li>
                                        <li>{{ __('index.loaihd') }}: <span class="info info-contract">${String(contractDetails.contract_type_name).replace(/,/g, ', ') || ''}</span></li>
                                        
                                        <li>{{ __('index.production_or_trade_unit') }}: <span class="info">${contractDetails.production_or_trade_unit || ''}</span></li>
                                        <li>{{ __('index.market') }}: <span class="info info-contract">${contractDetails.market || ''}</span></li>
                                        <li>{{ __('index.third_party_sale') }}: <span class="info info-contract">${contractDetails.third_party_sale || ''}</span></li>
                                        <li>{{ __('index.delivery_month') }}: <span class="info info-contract">${deliveryMonth || ''}</span></li>
                                        <li>{{ __('index.quantity') }}: <span class="info info-contract">${contractDetails.quantity || ''}</span></li>
                                        <li>{{ __('index.container_closing_date') }}: <span class="info info-contract">${formatDate(contractDetails.container_closing_date) || ''}</span></li>
                                        <li>{{ __('index.packaging_type') }}: <span class="info info-contract">${contractDetails.packaging_type || ''}</span></li>
                                    </ul>
                                </div>
                            `;
                            if (orderExports.length > 0) {
                                html += `
                                    <div class="mb-3">
                                        <div class="detail-title">{{ __('index.lenhxuathang') }}</div>
                                            ${orderExports.map(orderExport => {
                                    return `
                                                    <div class="tag tag-contract" style="padding-right: 20px; width:98%">
                                                        <div class="tag-name d-flex justify-content-between align-items-center" style="color: white">
                                                            <div class="detail-title" style="color: black">{{ __('index.ma_lenh') }}
                                                                <span class="info"> ${orderExport.code}</span>
                                                            </div>
                                                        </div>

                                                        
                                                            <div class="plots d-flex gap-2 align-items-center">
                                                                ${orderExport.batches.map(batch => {

                                        return `
        <div class="plot-code btn btn-danger" data-code="${batch.batch_code}">
            ${batch.batch_code}
        </div>
                                         

    `;


                                    })
                                            .join("")}
                                            
                                                            </div>
                                                       
                                                    ${orderExport.batches.some(batch => batch.batch_code) ? `
                                                            <div class="detail-title" style="color: black">{{ __('index.dds') }}</div>

                                                    <div class="dds-button d-flex gap-2" data-id="${orderExport.id}"></div>
` : ''}
 </div>                                              
`;

                                }).join("")}
                                    </div>
                                `;
                            }

                            // Append the HTML content to the detailDiv

                            detailDiv.html(html).slideDown();
                            detailDiv.find('.dds-button').each(function () {
                                const id = $(this).data('id');
                                fetchOrderDetails(id, $(this));
                            });

                            $tag.find(".icon-toggle").removeClass("fa-angle-down").addClass(
                                "fa-angle-up");
                        } else {
                            alert("Không thể lấy thông tin hợp đồng. Vui lòng thử lại.");
                        }
                    },
                    error: function () {
                        alert("Lỗi kết nối đến server!");
                    },
                    complete: function () {
                        $tag.find(".loader-detail").css("opacity", 0);
                    }
                });
            } else {
                // console.log("testesfsafdasfas");
                detailDiv.slideToggle();
                $tag.find('.icon-toggle').toggleClass('fa-angle-down fa-angle-up');
            }
        });

    });


    function fetchOrderDetails(id, $element) {

        $.ajax({
            url: `/api/contracts/get-detail-order/${id}`,
            method: 'GET',
            headers: {
                'token': apiToken
            },
            success: function (response) {
                if (response.success) {
                    const orderexports = response.data;

                    const ddsLinks = orderexports.order.batches.map(batch => batch.dds_link);
                    const dds2Links = orderexports.order.batches.map(batch => batch.dds2_link);
                    const dds3Links = orderexports.order.batches.map(batch => batch.dds3_link);
                    // console.log('aaa12345', ddsLinks);
                    const html = `
                    <a href="${ddsLinks}">
                        <div class="btn btn-primary"><i class="fa-solid fa-download"></i> DDS</div>
                    </a>
                    <a href="${dds2Links}">
                        <div class="btn btn-primary"><i class="fa-solid fa-download"></i> DDS-2</div>
                    </a>
                    <a href="${dds3Links}">
                        <div class="btn btn-primary"><i class="fa-solid fa-download"></i> DDS-3</div>
                    </a>
                    `;
                    $element.html(html); // gắn kết quả vào div.dds-button
                } else {
                    $element.html('<span style="color:red">Không có DDS</span>');
                }
            },
            error: function () {
                $element.html('<span style="color:red">Lỗi kết nối</span>');
            }

        });
    }
</script>

<script>
    $(document).ready(function () {
        $(document).on('click', '.plot-code', function (e) {
            $('.loader-plot-code').css('opacity', '1');
            $('#plot-code-modal').show();
        });

        $('#close-plot-code-modal').on('click', function () {
            $('#plot-code-result').empty();
            $('#plot-code-modal').hide();
        });

    });
</script>
<script>
    $('#close-map-modal').on('click', function () {
        $('#map-modal').fadeOut();

        if (!$('#map-container').hasClass("not-delete")) {
            $('#map-container').empty();
        }
    });
</script>
<script>
    function applyFilters(filters) {
        let orConditions = [];

        filters.forEach(filter => {
            let andConditions = [];
            let find = filter.find;
            let plantation = filter.plantation;
            let namTrong = filter.plantingY;
            let congTy = filter.unit;

            if (find && find.trim() !== '') {
                andConditions.push(`Ten_lo = '${find}'`);
            }

            if (plantation && plantation.trim() !== '') {
                let result = plantation.replace("Nông Trường", "NT");
                andConditions.push(`NT_Doi = '${result}'`);
            }

            if (namTrong && namTrong.trim() !== '') {
                andConditions.push(`Nam_trong = '${namTrong}'`);
            }

            if (congTy && congTy.trim() !== '') {
                andConditions.push(`Cong_ty = '${congTy}'`);
            }

            if (andConditions.length > 0) {
                orConditions.push(`(${andConditions.join(" AND ")})`);
            }
        });

        // Join the OR conditions together or default to "1=1"
        let definitionExpression = orConditions.length > 0 ? orConditions.join(" OR ") : "1=1";
        return definitionExpression;
    }

    $(document).on('click', '.all-map', function () {
        const batchResponse = window.batchResponse;
        const batch = batchResponse.batch;
        const allPantingArea = batch.ingredients.flatMap(ingredient => ingredient.planting_areas);


        const expression = applyFilters(allPantingArea);
        $('#loading-message').fadeIn();
        $('#map-modal').fadeIn();

        require(["esri/WebMap", "esri/views/MapView", "esri/config", "esri/widgets/Search"], function (WebMap,
            MapView,
            esriConfig,
            Search) {
            esriConfig.apiKey = tokenMap; // Gán API key nếu cần

            const webMap = new WebMap({
                portalItem: {
                    id: webMapId // ID của WebMap bạn muốn tải
                }
            });

            const view = new MapView({
                container: "viewMap", // ID của div chứa bản đồ
                map: webMap,
                zoom: 12,
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        // buttonEnabled: false,
                        breakpoint: false,
                        position: "bottom-left",
                    }
                }
            });

            // Đảm bảo kích thước map được tính đúng
            view.when(() => {

                view.container.style.height = "100%";
                view.container.style.width = "100%";
                window.dispatchEvent(new Event("resize"));
                const layers = view.map.allLayers;

                // Duyệt qua tất cả layers trong bản đồ
                layers.forEach(function (layer) {
                    if (layer.type ===
                        "feature") { // Kiểm tra nếu là một layer có tính năng

                        layer.definitionExpression = expression; // Áp dụng filter
                    }
                });

                const featureLayer = layers.find(layer => layer.title ===
                    "data_map_cao_su_hoa_binh_feature");

                const searchWidget = new Search({
                    view: view,
                    includeDefaultSources: false,
                    locationEnabled: false,
                    searchAllEnabled: false,
                    sources: [{
                        layer: featureLayer,
                        searchFields: ["Ten_lo"],
                        displayField: "Ten_lo",
                        name: "Lô Trồng",
                        placeholder: "Tìm theo tên lô...",
                        suggestionTemplate: "{Ten_lo}",
                        outFields: ["*"],
                        suggestionsEnabled: true,
                        minSuggestCharacters: 0
                    }]
                });

                view.ui.add(searchWidget, "top-right");
                $('#loading-message').fadeOut();

            });


        });

    });
</script>

<script>
    function applyFilters(filters) {
        let orConditions = [];

        filters.forEach(filter => {
            let andConditions = [];
            let find = filter.find;
            let plantation = filter.plantation;
            let namTrong = filter.plantingY;
            let congTy = filter.unit;

            if (find && find.trim() !== '') {
                andConditions.push(`Ten_lo = '${find}'`);
            }

            if (plantation && plantation.trim() !== '') {
                let result = plantation.replace("Nông Trường", "NT");
                andConditions.push(`NT_Doi = '${result}'`);
            }

            if (namTrong && namTrong.trim() !== '') {
                andConditions.push(`Nam_trong = '${namTrong}'`);
            }

            if (congTy && congTy.trim() !== '') {
                andConditions.push(`Cong_ty = '${congTy}'`);
            }

            if (andConditions.length > 0) {
                orConditions.push(`(${andConditions.join(" AND ")})`);
            }
        });

        // Join the OR conditions together or default to "1=1"
        let definitionExpression = orConditions.length > 0 ? orConditions.join(" OR ") : "1=1";
        return definitionExpression;
    }

    $(document).on('click', '.map', function () {
        const batchResponse = window.batchResponse;
        const batch = batchResponse.batch;
        const id_plot = $(this).data('id_plot') || '';

        const allPlantingAreas = batch.ingredients.flatMap(ingredient => ingredient.planting_areas || []);

        // Lọc theo farm_name và unit
        const filteredPlantingAreas = allPlantingAreas.filter(plot => plot.id_plot === id_plot);


        const expression = applyFilters(filteredPlantingAreas);
        $('#loading-message').fadeIn();
        $('#map-modal').fadeIn();

        require(["esri/WebMap", "esri/views/MapView", "esri/config"], function (WebMap,
            MapView,
            esriConfig,
            Search) {
            esriConfig.apiKey = tokenMap; // Gán API key nếu cần

            const webMap = new WebMap({
                portalItem: {
                    id: webMapId // ID của WebMap bạn muốn tải
                }
            });

            const view = new MapView({
                container: "viewMap", // ID của div chứa bản đồ
                map: webMap,
                zoom: 12,
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        // buttonEnabled: false,
                        breakpoint: false,
                        position: "bottom-left",
                    }
                }
            });

            // Đảm bảo kích thước map được tính đúng
            view.when(() => {

                view.container.style.height = "100%";
                view.container.style.width = "100%";
                window.dispatchEvent(new Event("resize"));
                const layers = view.map.allLayers;

                // Duyệt qua tất cả layers trong bản đồ
                layers.forEach(function (layer) {
                    if (layer.type ===
                        "feature") { // Kiểm tra nếu là một layer có tính năng

                        layer.definitionExpression = expression; // Áp dụng filter
                        layer.queryFeatures({
                            where: expression,
                            returnGeometry: true,
                            outFields: ["*"]
                        }).then(function (results) {
                            if (results.features.length > 0) {
                                const feature = results.features[0];

                                view.goTo({
                                    target: feature.geometry,
                                    zoom: 16,
                                    animate: false
                                }).catch(function (error) {
                                    console.error("Zoom lỗi: ", error);
                                });
                            } else {
                                console.warn("Không tìm thấy plot!");
                            }
                            $('#loading-message').fadeOut();
                        });
                    }
                });
            });


        });

    });
</script>


<script>
    function initToggleEvents() {
        $(document).off('click', '.icon-toggle-ingredient');
        $(document).off('click', '.fa-copy');


        $(document).on('click', '.icon-toggle-ingredient', function () {
            $(this).parent().next('.tag-detail-ingredient, .plot-detail-ingredient').toggleClass('hidden');
        });

        $(document).on('click', '.fa-copy', function () {
            const geoJson = $(this).closest('.copyjson')
                .data('json');

            navigator.clipboard.writeText(JSON.stringify(geoJson)).then(() => {
                alert("Đã sao chép GeoJSON!");
            }).catch(() => {
                alert("Không thể sao chép!");
            });
        });
    }
</script>

<script>
    $(document).ready(function () {

        $(document).on('click', '.tag-ingredient', function () {
            $(this).siblings('.tag-detail-ingredient').slideToggle(300);
            $(this).find('.icon-toggle-ingredient').toggleClass('rotate');
        });
    });
</script>

<script>
    $(document).ready(function () {
        $(document).on('click', '.plot-name', function () {

            $(this).siblings('.plot-detail-ingredient').slideToggle(300);

            $(this).find('.icon-toggle-ingredient').toggleClass('rotate');
        });
    });
</script>

</html>