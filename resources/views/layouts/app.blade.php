<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Vườn Cây</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



    <link rel="stylesheet" href="/mazer-1.0.0/dist/assets/css/bootstrap.css">

    <link rel="stylesheet" href="/mazer-1.0.0/dist/assets/vendors/iconly/bold.css">

    <link rel="stylesheet" href="/mazer-1.0.0/dist/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="/mazer-1.0.0/dist/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/mazer-1.0.0/dist/assets/css/app.css">
    <link rel="stylesheet" href="/mazer-1.0.0/dist/assets/css/all_l.css">
    <link rel="stylesheet" href="/FontAwesome6.4Pro/css/all.css">
    <link rel="shortcut icon" href="/imgs/logo_vc.jpg" type="image/x-icon">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.tailwindcss.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    {{-- Chartjs --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <!-- Tempus Dominus Datepicker -->
    <!-- Popperjs -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/js/tempus-dominus.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/css/tempus-dominus.min.css"
        crossorigin="anonymous">

</head>

<body>
    <div class="loading-wrapper">
        <span class="loader"></span>
    </div>
    <style>
        .dt-type-numeric {
            text-align: left !important
        }
    </style>
    <style>
        .loading-wrapper {
            position: fixed;
            background-color: rgb(248, 248, 248);
            z-index: 1000;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center
        }

        .loader {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-block;
            border-top: 4px solid #FFF;
            border-right: 4px solid transparent;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        .loader::after {
            content: '';
            box-sizing: border-box;
            position: fixed;
            left: 0;
            top: 0;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border-bottom: 4px solid #31b131;
            border-left: 4px solid transparent;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active" style="background-color: #D4F7D1 !important;color: #697a8d; ">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo text-center" style="display: flex; justify-content: center; align-items: center;
                            padding: 15px; background-color: #ffffff; border-radius: 50%; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                            transition: all 0.3s ease-in-out;
                            width: 115px; height: 115px; margin: 0 auto;">
                            {{-- <a href="/"><img src="/imgs/HRC-removebg-preview.png" alt="Logo" srcset=""
                                    style="width:100px; height: auto"></a> --}}
                            <a href="/"><img src="/imgs/logo_vc.jpg" alt="Logo" srcset=""
                                    style="width:96%; height: auto;"></a>
                            {{-- <p class="">HOA BINH</p>
                            <p>TRACEBILITY</p> --}}
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        {{-- <div class="search-box">
                            <input type="text" class="search-input" placeholder="Tìm kiếm...">
                        </div> --}}
                        <li class="sidebar-item">
                            <a href="/" class='sidebar-link' onclick="toggleActive(this)">
                                <i class="fa-solid fa-house"></i>
                                <span>Trang Chủ</span>
                            </a>
                        </li>
                        {{-- Quản Lý Nông trường --}}
                        <li
                            class="sidebar-item has-sub {{ request()->is('plots*') || request()->is('add-plots*') || request()->is('edit-plots*') || 
                                request()->is('seedgardens*') || request()->is('edit-seedgardens*') || 
                                request()->is('crops*') || request()->is('edit-crops*') || 
                                request()->is('ingredients*') || request()->is('farms*') || request()->is('vehicles*') || request()->is('plantingareas*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link {{ request()->is(' add-plots*') ||
                                request()->is('edit-plots*') ||
                                request()->is('seedgardens*') ||
                                request()->is('edit-seedgardens*') ||
                                request()->is('vehicles*') ||
                                request()->is('factorys*') ||
                                request()->is('import-ing*') ||
                                request()->is('add-ingredients*') ||
                                request()->is('save-ingredients*') ||
                                request()->is('edit-ingredients*') ||
                                request()->is('edit-plantingareas*') ||
                                request()->is('add-excel*') ||
                                request()->is('edit-excel*') ||
                                request()->is('add-plantingareas*')
                                ? 'active'
                                : '' }}'
                                onclick="toggleActive(this)">
                                <i class="fa-solid fa-house-tree"></i>
                                <span>Vườn Cây</span>
                            </a>
                            <ul class="submenu {{ Route::is('plots.index') || Route::is('add-plots') || Route::is('edit-plots') ||
                                    Route::is('add-ingredients') || Route::is('edit-ingredients') ||
                                    Route::is('farms.edit') ||
                                    Route::is('importIng.index') ||
                                    Route::is('farms.index') ||
                                    Route::is('crops.index') || 
                                    Route::is('seedgardens.index') || Route::is('edit-seedgardens') ||
                                    Route::is('add-excel') ||
                                    Route::is('edit-excel')
                                        ? 'active' : '' }}">
                                {{-- <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('farms.index') || Route::is('farms.add') || Route::is('farms.edit') ? 'active' : '' }}">
                                    <i class="fa-solid fa-tree text-green"></i>
                                    <a href="{{ route('farms.index') }}">Vườn Cây</a>
                                </li> --}}
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('plots.index') || Route::is('plots.add') || Route::is('plots.edit') ? 'active' : '' }}">
                                    <i class="fa-solid fa-chart-scatter text-green"></i>
                                    <a href="{{ route('plots.index') }}">Lô</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('seedgardens.index') || Route::is('seedgardens.edit') ? 'active' : '' }}">
                                    <i class="fa-solid fa-bag-seedling text-green"></i>
                                    <a href="{{ route('seedgardens.index') }}">Vườn Giống</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('crops.index') || Route::is('crops.edit') ? 'active' : '' }}">
                                    <i class="fa-solid fa-trees text-green"></i>
                                    <a href="{{ route('crops.index') }}">Cây Trồng</a>
                                </li>

                            </ul>
                        </li>
                        {{-- <li class="sidebar-item one-sub {{ request()->is('workers*') ? 'active' : '' }}">
                            <a href="{{route('workers.index')}}" class='sidebar-link' onclick="toggleActive(this)">
                                <i class="fas fa-users"></i>
                                <span>Công Nhân</span>
                            </a>
                        </li> --}}
                        <li
                            class="sidebar-item has-sub {{ request()->is('duty*') || request()->is('edit-duty*') || request()->is('teams*') || request()->is('edit-teams*') 
                                || request()->is('workers*') || request()->is('edit-workers*') || request()->is('add-workers*')  ? 'active' : '' }}">
                            <a href="#" class='sidebar-link {{ request()->is(' duty*') || request()->is('edit-duty*') ||
                                request()->is(' teams*') || request()->is('edit-teams*') ||
                                request()->is('workers*') || request()->is('edit-workers*') ||
                                request()->is('add-workers*')
                                ? 'active' : '' }}'
                                onclick="toggleActive(this)">
                                <i class="fas fa-users"></i>
                                <span>Quản Lý Nhân Sự</span>
                            </a>
                            <ul
                                class="submenu {{ request()->is('workers*') || request()->is('add-workers*') || request()->is('edit-workers*') || request()->is('outputs*') ? 'active' : '' }}">
                                <li
                                    class="submenu-item d-flex d-flex align-items-center ms-3 {{Route::is('duty.index') || Route::is('duty.edit') ? 'active' : '' }}">
                                    <i class="fa-solid fa-list-check text-green"></i>
                                    <a href="{{route('duty.index')}}">Chức Vụ</a>
                                </li>
                                <li
                                    class="submenu-item d-flex d-flex align-items-center ms-3 {{ Route::is('teams.index') || Route::is('teams.edit') ? 'active' : '' }}">
                                    <i class="fa-solid fa-people-group text-green"></i>
                                    <a href="{{ route('teams.index')}}">Tổ</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('workers.index') || Route::is('workers.add') || Route::is('workers.edit') ? 'active' : '' }}">
                                    <i class="fa-solid fa-user-plus text-green"></i>
                                    <a href="{{ route('workers.index')}}">Công Nhân</a>
                                </li>
                            </ul>
                        </li>
                        {{-- Quản Lý Công việc --}}
                        <li class="sidebar-item has-sub {{ request()->is('works*') || request()->is('edit-works*') || request()->is('add-works*') ||
                                request()->is('aworks*') || request()->is('edit-aworks*') ||
                                request()->is('workps*') || request()->is('add-workps*') || request()->is('edit-workps*') ||
                                request()->is('comments*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link {{ request()->is(' works*') || request()->is('edit-works*')
                                || request()->is('add-works*') ||
                                request()->is('workps*') || request()->is('add-workps*') ||
                                request()->is('edit-workps*') ||
                                request()->is('aworks*') || request()->is('edit-aworks*')||
                                request()->is('comments*') || request()->is('outputs*') ?
                                'active' : '' }}'
                                onclick="toggleActive(this)">
                                <img src="/imgs/spade.png" width="25px" height="25px">
                                <span>Công Việc</span>
                            </a>
                            <ul
                                class="submenu {{ request()->is('works*') || request()->is('workps*') || request()->is('comments*') || request()->is('outputs*') ? 'active' : '' }}">
                                <li
                                    class="submenu-item d-flex d-flex align-items-center ms-3 {{ Route::is('aworks.index') || Route::is('aworks.edit') ? 'active' : '' }}">
                                    <i class="fa-solid fa-list-check text-green"></i>
                                    <a href="{{ route('aworks.index')}}">Loại Công Việc</a>
                                </li>
                                <li
                                    class="submenu-item d-flex d-flex align-items-center ms-3 {{ Route::is('works.index') || Route::is('works.add') || Route::is('works.edit') ? 'active' : '' }}">

                                    <i class="fa-solid fa-briefcase text-green"></i>
                                    <a href="{{ route('works.index')}}">Phân Công Công Việc</a>
                                </li>
                                <li
                                    class="submenu-item d-flex d-flex align-items-center ms-3 {{ Route::is('workps.index') || Route::is('workps.add') || Route::is('workps.edit') ? 'active' : '' }}">
                                    <i class="fas fa-box-open text-green"></i>
                                    <a href="{{ route('workps.index')}}">Đề Xuất Vật Tư</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('comments.index') ? 'active' : '' }}">
                                    <i class="fas fa-clipboard-check text-green"></i>
                                    <a href="{{ route('comments.index')}}">Đánh Giá</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('outputs.index') ? 'active' : '' }}">
                                    <i class="fa-solid fa-industry text-green"></i>
                                    <a href="{{ route('outputs.index')}}">Sản Lượng</a>
                                </li>
                            </ul>
                        </li>
                        {{-- Quản Lý cây bệnh --}}
                        <li class="sidebar-item has-sub {{ request()->is('diseaseplans*') || request()->is('add-diseaseplans*') ||  request()->is('edit-diseaseplans*') ||
                            request()->is('treatmentslips*') || request()->is('add-treatmentslips') || request()->is('edit-treatmentslips') ||
                            request()->is('materialproposals*') || request()->is('add-materialproposals') || request()->is('edit-materialproposals') ||
                            request()->is('treatmentslip*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link {{ request()->is(' diseaseplans*') ||
                                request()->is('add-diseaseplans*') || request()->is('edit-diseaseplans*') ||
                                request()->is('materialproposals*') || request()->is('add-materialproposals') ||
                                request()->is('edit-materialproposals') ||
                                request()->is('treatmentslips*') || request()->is('add-treatmentslips') ||
                                request()->is('edit-treatmentslips') ? 'active' : '' }}' onclick="toggleActive(this)">
                                <img src="/imgs/caybenh.png" width="25px" height="25px">
                                <span>Cây Bệnh</span>
                            </a>
                            <ul class="submenu {{ Route::is('diseaseplans.index') || Route::is('materialproposals.index') 
                                || Route::is('treatmentslips.index') ? 'active' : '' }}">
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('diseaseplans.index') || Route::is('diseaseplans.add')  ? 'active' : '' }}">
                                    <i class="fas fa-file-medical text-green"></i>
                                    <a href="{{ route('diseaseplans.index')}}">Phiếu Cây Bệnh</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('treatmentslips.index') || Route::is('treatmentslips.add') ? 'active' : '' }}">
                                    <i class="fas fa-stethoscope text-green"></i>
                                    <a href="{{ route('treatmentslips.index') }}">Phiếu Trị</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('materialproposals.index') || Route::is('materialproposals.add') ? 'active' : '' }}">
                                    <i class="fas fa-clipboard-check text-green"></i>
                                    <a href="{{ route('materialproposals.index') }}">Đề Xuất Vật Tư</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Quản Lý cây đỗ --}}
                        <li class="sidebar-item one-sub {{ request()->is('treesfelleds*') ? 'active' : '' }}">
                            <a href="{{ route('treesfelleds.index') }}" class='sidebar-link'
                                onclick="toggleActive(this)">
                                <img src="/imgs/caydo.png" alt="Cây gãy" width="22px" height="20px">
                                <span>Cây Gãy/Đổ</span>
                            </a>
                        </li>

                        {{-- Quản Lý kho --}}
                        <li
                            class="sidebar-item has-sub {{ request()->is('units*') || request()->is('edit-units*') 
                            || request()->is('warehouses*') || request()->is('edit-warehouses*') 
                            || request()->is('categories*') || request()->is('edit-categories*')
                            || request()->is('products*') || request()->is('edit-products*')
                            || request()->is('decomposes*') 
                            || request()->is('stocks*') || request()->is('add-stocks*') || request()->is('edit-stocks*')
                            || request()->is('pwarehouses*') || request()->is('add-pwarehouses*') || request()->is('edit-pwarehouses*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link {{ request()->is(' units*') || request()->is('edit-units*')
                                || request()->is('warehouses*') || request()->is('edit-warehouses*')
                                || request()->is('products*') || request()->is('edit-products*')
                                || request()->is('decomposes*')
                                || request()->is('categories*') || request()->is('edit-categories*')
                                || request()->is('pwarehouses*') || request()->is('add-pwarehouses*')
                                || request()->is('stocks*') || request()->is('add-stocks*') ||
                                request()->is('edit-stocks*') ||

                                request()->is('edit-pwarehouses*') ? 'active' : '' }} ' onclick="toggleActive(this)">
                                <i class="fa-solid fa-warehouse-full"></i>
                                <span>Quản Lý Kho</span>
                            </a>
                            <ul
                                class="submenu {{ Route::is('all_units') || request()->is('all_warehouses') || request()->is('all_categories') || request()->is('all_decomposes') || request()->is('all_pwarehouses') ? 'active' : '' }}">
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('warehouses.index') ? 'active' : '' }}">
                                    <i class="fa-solid fa-list-check text-green"></i>
                                    <a href="{{route('warehouses.index')}}">Kho</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('categories.index') || Route::is('categories.edit') ? 'active' : '' }}">

                                    <i class="fa-solid fa-list-dropdown text-green"></i>
                                    <a href="{{route('categories.index')}}">Danh Mục Vật Tư</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('units.index') || Route::is('units.edit') ? 'active' : '' }}">
                                    <i class="fas fa-box-open text-green"></i>
                                    <a href="{{ route('units.index')}}">Đơn Vị Vật Tư</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('products.index') || Route::is('products.edit') ? 'active' : '' }}">
                                    <span><img src="/imgs/pick-list.png" width="25px" height="25px"></span>
                                    <a href="{{route('products.index')}}">Danh Sách Vật Tư</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{Route::is('pwarehouses.index') || Route::is('pwarehouses.add') || Route::is('pwarehouses.edit') ? 'active' : ''}}">
                                    <span><img src="/imgs/import.png" width="25px" height="25px"></span>
                                    <a href="{{route('pwarehouses.index')}}">Phiếu Kho</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('decomposes.index') || Route::is('decomposes.add') ? 'active' : '' }}">
                                    <i class="fa-solid fa-industry text-green"></i>
                                    <a href="{{route('decomposes.index')}}">Phân Rã Vật Tư</a>
                                </li>
                                <li
                                    class="submenu-item d-flex align-items-center ms-3 {{ Route::is('stocks.index') || Route::is('stocks.add') ? 'active' : '' }}">
                                    <i class="fa-regular fa-garage-car text-green"></i>
                                    <a href="{{route('stocks.index')}}">Tồn Kho</a>
                                </li>
                                <li class="submenu-item d-flex align-items-center ms-3">
                                    <i class="fa-solid fa-cash-register text-green"></i>
                                    <a href="">Thu Mua Sản Lượng</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Quản Lý Nhà Máy --}}
                        {{-- @hasanyrole(' Nhà Máy XNCB|Admin|Danh Sách Nhà Máy XNCB|Quản Lý Mã Lô|Danh Sách Mã Lô|Quản
                        Lý Kết Nối TTNL|Danh Sách TTNL|Quản Lý LXH|Danh Sách LXH') <li
                            class="sidebar-item has-sub {{ request()->is('edit-orderbatchs*') || request()->is('add-orderbatchs*') || request()->is('edit-batches*') || request()->is('add-batches*') || request()->is('edit-batchesB*') || request()->is('add-batchesB*') || request()->is('qrCode*') || request()->is('import-batchIng*') || request()->is('batchesB*') || request()->is('batches*') || request()->is('orderbatch*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link {{ request()->is(' qrCode*') || request()->is('import
                                -batchIng*') ||
                                request()->is(' batchesB*') ||
                                request()->is('batches*') ||
                                request()->is('orderbatch*') ||
                                request()->is('add-batchesB*') ||
                                request()->is('add-batches*') ||
                                request()->is('edit-batches*') ||
                                request()->is('add-orderbatchs*') ||
                                request()->is('edit-orderbatchs*')
                                ? 'active'
                                : '' }}'
                                onclick="toggleActive(this)">
                                <i class="fa-sharp-duotone fa-thin fa-industry-windows text-green"></i>
                                <span>Nhà Máy XNCB</span>
                            </a>
                            <ul class="submenu {{ Route::is('batchesB.index') ||
                                    Route::is('add-batchesB') ||
                                    Route::is('edit-batchesB') ||
                                    Route::is('batches.index') ||
                                    Route::is('add-batches') ||
                                    Route::is('edit-batches') ||
                                    Route::is('orderbatchs.index') ||
                                    Route::is('add-orderbatchs') ||
                                    Route::is('edit-orderbatchs') ||
                                    Route::is('index_qr.index') ||
                                    Route::is('importBatchIng.index')
                                        ? 'active'
                                        : '' }}">

                                @hasanyrole('Nhà Máy XNCB|Admin|Quản Lý Mã Lô|Danh Sách Mã Lô')
                                <li
                                    class="submenu-item d-flex d-flex  align-items-center ms-3 {{ Route::is('batchesB.index') ? 'active' : '' }}">
                                    <i class="fa-solid fa-octagon-plus text-green"></i>
                                    <a href="{{ route('batchesB.index') }}">Tạo Mã Lô Hàng</a>
                                </li>
                                @endhasanyrole

                                @hasanyrole('Nhà Máy XNCB|Admin|Danh Sách Nhà Máy XNCB|Kết Nối TTNL|Danh Sách TTNL')
                                <li
                                    class="submenu-item d-flex  align-items-center ms-3 {{ Route::is('batches.index') ? 'active' : '' }}">
                                    <i class="bi bi-list-check text-green"></i>
                                    <a href="{{ route('batches.index') }}">Danh Sách Lô Hàng</a>
                                </li>
                                @endhasanyrole
                                <li
                                    class="submenu-item d-flex  align-items-center ms-3 {{ Route::is('importBatchIng.index') ? 'active' : '' }}">
                                    <i class="fa-regular fa-cloud-arrow-down text-green"></i>
                                    <a href="{{ route('importBatchIng.index') }}">Nhập Excel KNTTNL</a>
                                </li>
                                <li
                                    class="submenu-item d-flex  align-items-center ms-3 {{ Route::is('index_qr.index') ? 'active' : '' }}">
                                    <i class="fa-regular fa-download text-green"></i>
                                    <a href="{{ route('index_qr.index') }}">Tải Mã Lô</a>
                                </li>
                                @hasanyrole('Nhà Máy XNCB|Admin|Quản Lý Lệnh Xuất Hàng|Danh Sách LXH')
                                <li
                                    class="submenu-item d-flex  align-items-center ms-3 {{ Route::is('orderbatchs.index') ? 'active' : '' }}">
                                    <i class="bi bi-file-earmark-binary text-green"></i>
                                    <a href="{{ route('orderbatchs.index') }}">Mã Lệnh Xuất Hàng</a>
                                </li>
                                @endhasanyrole
                            </ul>
                        </li>
                        @endhasanyrole --}}

                        {{-- Quản Lý Chất LƯợng --}}
                        {{-- @hasanyrole('Quản Lý Chất Lượng|Admin|Danh Sách Quản Lý Chất Lượng')
                        <li
                            class="sidebar-item has-sub {{ Route::is('testing.*') || Route::is('untested') || Route::is('showun') || Route::is('import.*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link {{ Route::is(' testing.*') || Route::is('untested')
                                ? 'active' : '' }}' onclick="toggleActive(this)">
                                <i class="fa-brands fa-bandcamp"></i>
                                <span>Quản Lý Chất Lượng</span>
                            </a>
                            <ul
                                class="submenu {{ (Route::is('testing.*') && !Route::is('testing.show')) || Route::is('untested') || Route::is('import.*') || Route::is('showun') ? 'active' : '' }}">
                                <li
                                    class="submenu-item d-flex d-flex  align-items-center ms-3 {{ Route::is('testing.*') ? 'active' : '' }}">
                                    <i class="bi bi-bookmark-check text-green"></i>
                                    <a href="{{ route('testing.index') }}">Lô Đã Kiểm Nghiệm</a>
                                </li>
                                <li
                                    class="submenu-item d-flex  align-items-center ms-3 {{ Route::is('untested') || Route::is('showun') ? 'active' : '' }}">
                                    <i class="bi bi-bookmark-x text-green"></i>
                                    <a href="/untested">Lô Chưa Kiểm Nghiệm</a>
                                </li>
                                <li
                                    class="submenu-item d-flex  align-items-center ms-3 {{ Route::is('import.*') ? 'active' : '' }}">
                                    <i class="fa fa-file-text text-green"></i>
                                    <a href="{{ route('import.files') }}">File</a>
                                </li>
                            </ul>
                        </li>
                        @endhasanyrole --}}

                        {{-- Quản Lý Hợp đồng --}}
                        {{-- @hasanyrole('Admin|Danh Sách Hợp Đồng|Quản Lý Hợp Đồng|Quản Lý Loại Hợp Đồng|Danh Sách
                        Loại
                        Hợp
                        Đồng')
                        <li class="sidebar-item has-sub {{ Route::is('contract-types.*') ||
                                Route::is('customers.*') ||
                                Route::is('contracts.*') ||
                                Route::is('contract-files.*') ||
                                Route::is('edit.index') ||
                                Route::is('create-file.index') ||
                                Route::is('cont') ||
                                Route::is('duedilistate.index')
                                    ? 'active'
                                    : '' }}">
                            <a href="#" class='sidebar-link {{ Route::is(' contract-types.*') ||
                                Route::is('customers.*') || Route::is('contract-files.*') || Route::is('edit.index') ||
                                Route::is('create-file.index') || Route::is('contracts.*') || Route::is('cont') ||
                                Route::is('duedilistate.index')
                                ? "
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        active"
                                : '' }}' onclick="toggleActive(this)">
                                <i class="bi bi-file-earmark-medical-fill text-green"></i>
                                <span>Hợp Đồng</span>
                            </a>
                            <ul class="submenu {{ Route::is('contract-types.*') ||
                                    Route::is('customers.*') ||
                                    Route::is('contracts.*') ||
                                    Route::is('contract-files.*') ||
                                    Route::is('edit.index') ||
                                    Route::is('create-file.index') ||
                                    Route::is('cont') ||
                                    Route::is('duedilistate.index')
                                        ? 'active'
                                        : '' }}">
                                @hasanyrole('Admin|Quản Lý Loại Hợp Đồng|Danh Sách Loại Hợp Đồng')
                                <li
                                    class="submenu-item d-flex d-flex  align-items-center ms-3 {{ Route::is('contract-types.*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-copy text-green"></i>
                                    <a href="{{ route('contract-types.index') }}">Loại Hợp Đồng</a>
                                </li>
                                @endhasanyrole

                                @hasanyrole('Admin|Quản Lý Khách Hàng|Danh Sách Khách Hàng')
                                <li
                                    class="submenu-item d-flex  align-items-center ms-3 {{ Route::is('customers.*') ? 'active' : '' }}">
                                    <i class="bi bi-people text-green"></i>
                                    <a href="{{ route('customers.index') }}">Khách Hàng</a>
                                </li>
                                @endhasanyrole

                                @hasanyrole('Admin|Quản Lý Hợp Đồng|Danh Sách Hợp Đồng')
                                <li
                                    class="submenu-item d-flex d-flex  align-items-center ms-3 {{ Route::is('contracts.*') || Route::is('cont') ? 'active' : '' }}">
                                    <i class="bi bi-list-check text-green"></i>
                                    <a href="{{ route('cont') }}">Danh Sách Hợp Đồng</a>
                                </li>
                                @endhasanyrole
                                <li
                                    class="submenu-item d-flex d-flex  align-items-center ms-3 {{ Route::is('contract-files.*') ? 'active' : '' }}">
                                    <i class="bi bi-file-earmark-binary text-green"></i>
                                    <a href="{{ route('contract-files.index') }}">Mã Lệnh
                                    </a>
                                </li>
                                <li
                                    class="submenu-item d-flex d-flex  align-items-center ms-3 {{ Route::is('duedilistate.index') ? 'active' : '' }}">
                                    <i class="bi bi-file-earmark-binary text-green"></i>
                                    <a href="{{ route('duedilistate.index') }}">Due Diligence Statement</a>
                                </li>
                            </ul>
                        </li>
                        @endhasanyrole --}}

                        {{-- Quản Lý Thông tin khác --}}
                        {{-- @hasanyrole('Admin|Danh Sách Thông Tin Khác')
                        <li
                            class="sidebar-item has-sub {{ Route::is('certi.*') || Route::is('report.index') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link {{ Route::is(' certi.*') }}' onclick="toggleActive(this)">
                                <i class="fa-thin fa-square-info"></i>
                                <span>Thông Tin Khác</span>
                            </a>
                            <ul class="submenu {{ Route::is('certi.*') || Route::is('report.index') ? 'active' : '' }}">
                                <li
                                    class="submenu-item d-flex d-flex  align-items-center ms-3 {{ Route::is('certi.*') ? 'active' : '' }}">
                                    <i class="bi bi-book text-green"></i>
                                    <a href="{{ route('certi.index') }}">Chứng Chỉ</a>
                                </li>
                                <li
                                    class="submenu-item d-flex d-flex  align-items-center ms-3 {{ Route::is('report.index') ? 'active' : '' }}">
                                    <i class="fa fa-pie-chart text-green"></i>
                                    <a href="{{ route('report.index') }}">Báo Cáo</a>
                                </li>
                            </ul>
                        </li>
                        @endhasanyrole --}}
                        {{-- <li class="sidebar-item has-sub {{Route::is('certificates.index') ? " active" : "" }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Thông tin khác</span>
                            </a>
                            <ul class="submenu {{Route::is('certificates.*') ? " active" : "" }}">
                                <li class="submenu-item {{Route::is('certificates.*') ? " active" : "" }} ">
                                    <a href=" {{route('certificates.index')}}">Chứng chỉ</a>
                                </li>
                            </ul>
                        </li> --}}

                        {{-- Quản Lý Tài Khoản --}}
                        @hasanyrole('Admin|Quản Lý Tài Khoản')
                        <li
                            class="sidebar-item has-sub {{ request()->is('edit-*') || request()->is('give-*') || request()->is('all-*') ? 'active' : '' }}">
                            {{-- <a href="/account" class='sidebar-link '> --}}
                                <a href="#" class='sidebar-link {{ request()->is(' edit-*') || request()->is('give-*')
                                    || request()->is('all-*') ? 'active' : '' }}'
                                    onclick="toggleActive(this)">
                                    <i class="fa-solid fa-user"></i>
                                    <span>Thông Tin Tài Khoản</span>
                                </a>
                                <ul class="submenu {{ Route::is('all.permissions') ||
                                    Route::is('all.roles') ||
                                    Route::is('all.users') ||
                                    Route::is('show.per') ? 'active' : '' }}">
                                    <li
                                        class="submenu-item d-flex align-items-center {{ Route::is('all.roles') || Route::is('show.roles') || Route::is('show.per') ? 'active' : '' }}">
                                        <i class="bi bi-grid-fill text-green"></i>
                                        <a href=" {{ route('all.roles') }}">Vai Trò</a>
                                    </li>
                                    <li
                                        class="submenu-item d-flex align-items-center {{ Route::is('all.permissions') ||Route::is('show.permissions') ? 'active' : '' }}">
                                        <i class="bi bi-grid-fill text-green"></i>
                                        <a href=" {{ route('all.permissions') }}">Quyền</a>
                                    </li>
                                    <li
                                        class="submenu-item d-flex align-items-center {{ Route::is('all.users') || Route::is('show.users') ? 'active' : '' }}">
                                        <i class="fa-solid fa-octagon-plus text-green"></i>
                                        <a href=" {{ route('all.users') }}">Tài Khoản</a>
                                    </li>
                                </ul>
                        </li>
                        @endhasanyrole
                        @hasanyrole('Admin|Cấu Hình Trang Chủ|Cấu Hình Đăng Nhập|Cấu Hình Map')
                        <li class="sidebar-item">
                            <a href="{{ route('setting.index') }}" class='sidebar-link' onclick="toggleActive(this)">
                                <i class="fa-solid fa-gear"></i>
                                <span>Cài đặt</span>
                            </a>
                        </li>
                        @endhasanyrole
                    </ul>
                </div>

                <style>
                    .text-green {
                        color: #22573e !important
                    }

                    .search-box {
                        padding: 1rem;
                    }

                    .search-input {
                        width: 100%;
                        padding: 0.75rem 1rem 0.75rem 2.5rem;
                        border: 1px solid #d1d5db;
                        border-radius: 0.5rem;
                        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z'/%3e%3c/svg%3e");
                        background-position: 0.75rem center;
                        background-repeat: no-repeat;
                        background-size: 1rem;
                    }

                    .sidebar-item.one-sub .submenu {
                        display: none;
                    }

                    .sidebar-item.one-sub.active .submenu {
                        display: block !important;
                    }

                    .sidebar-item.has-sub .submenu {
                        display: none;
                    }

                    .sidebar-item.has-sub.active .submenu {
                        display: block !important;
                    }

                    .submenu-item.active {

                        color: #000000;
                        font-weight: bold;
                    }

                    .submenu-item.active i {
                        color: #000000;
                    }

                    #sidebar .sidebar-wrapper .menu .sidebar-link {
                        color: #22573E;
                        border-radius: 0;
                        padding: 1rem 2rem;
                        font-weight: 700;
                    }

                    #sidebar .sidebar-wrapper .menu .sidebar-link i,
                    .sidebar-wrapper .menu .sidebar-link svg {
                        color: #000;
                    }

                    #sidebar .sidebar-wrapper .menu {
                        padding: 1rem 0;
                    }

                    #sidebar .sidebar-wrapper .menu .sidebar-item.active .sidebar-link {
                        /* background-color: transparent; */
                        background-color: rgb(255 255 255);
                        border-radius: 10px;
                        margin-bottom: 3px;
                    }

                    #sidebar .sidebar-wrapper .menu .sidebar-item.has-sub .sidebar-link:after {

                        content: "\f107";
                        font-weight: 600;
                        font-family: "Font Awesome 6 Pro";
                    }

                    #sidebar .sidebar-wrapper .menu .sidebar-title {
                        padding: 0 1rem;
                        margin: 0;
                        color: #fff;
                    }

                    #sidebar .sidebar-wrapper .menu .submenu .submenu-item a {
                        color: #000;
                        padding: 0.7rem 10px;
                        font-size: 13px;
                        font-weight: 700;
                    }

                    #main {
                        /* background: rgb(25 135 84); */
                        background: #f5f5f9;
                        /* background: linear-gradient(to top, #a2d18ab8, #d4f6c3b8, #d4f6c3b8); */
                    }

                    #sidebar .sidebar-wrapper .menu .sidebar-link {
                        padding: 0.5rem 1rem;
                    }

                    .sidebar-wrapper {
                        width: 260px;
                    }

                    #main,
                    #main2 {
                        margin-left: 260px;
                        /* padding: 10px; */
                    }

                    @media screen and (max-width: 1199px) {

                        #main,
                        #main2 {
                            margin-left: 0;
                        }
                    }

                    ul.submenu {
                        background: #F3FFF2;
                    }

                    #sidebar .sidebar-wrapper .menu .submenu .submenu-item.active>a {
                        color: #00b560;
                        font-weight: 700;
                    }

                    .submenu-item.active a::after {
                        content: "|";
                        color: #00b560;
                        position: absolute;
                        right: 0;
                        top: 50%;
                        transform: translateY(-50%);
                        font-weight: bold;
                    }

                    .sidebar-wrapper .menu .sidebar-link {

                        font-size: 15px;

                    }

                    .sidebar-header.position-relative {
                        /* background: #fff; */
                        padding: 1rem;
                    }

                    .sidebar-link::after {
                        transition: transform 0.3s ease;
                        display: inline-block;
                        transform: rotate(0deg);
                    }

                    .sidebar-item.has-sub.active>.sidebar-link::after {
                        transform: translateY(-10%) rotate(180deg);
                    }
                </style>
                <button class="sidebar-toggler BtnOut x"><i data-feather="x"></i></button>
            </div>
        </div>
        <div id="main">
            <div class="page-content">
                <div class="page-title1">
                    <div class="row align-items-center">
                        <a href="#" class="burger-btn d-block d-xl-none" style="margin-left: 0%">
                            <i class="bi bi-justify fs-3 text-dark"></i>
                        </a>
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <div class="dropdown-custom">
                                <h5 class="text-dark d-inline me-2">Xin chào,
                                    <button class="btn btn-sm p-0 border-0 dropdown-toggle-custom" type="button"
                                        id="userDropdownButton" onclick="toggleDropdown()">
                                        <img src="/imgs/logo_vc.jpg" alt="Avatar" class="avatar-img">
                                    </button> {{ Auth::user()->name }}
                                </h5>

                                <ul class="dropdown-menu-custom" id="userDropdownMenu">
                                    <li>
                                        <a>Thông Tin</a>
                                    </li>
                                    <li>
                                        <form id="logout-form" action="/logout" method="POST">
                                            @csrf
                                            <button type="submit">
                                                <div class="sign">
                                                    Đăng Xuất
                                                </div>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item text-dark">Quản Lý Vườn Cây Joint stock Company</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="" id="main2">
            @yield('content')
        </div>
    </div>
    <script src="/mazer-1.0.0/dist/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="/mazer-1.0.0/dist/assets/js/bootstrap.bundle.min.js"></script>

    <script src="/mazer-1.0.0/dist/assets/vendors/apexcharts/apexcharts.js"></script>
    <script src="/mazer-1.0.0/dist/assets/js/pages/dashboard.js"></script>

    <script src="/mazer-1.0.0/dist/assets/js/main.js"></script>
    <script src="/js/certificate.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    {{-- DataTable --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.4/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.4/js/responsive.bootstrap5.js"></script>
</body>


<script>
    function toggleActive(element) {
        element.classList.toggle("active");
    }
</script>
<style>
    .dt-orderable-none .dt-column-order {
        display: none !important;
    }

    .sidebar-item .submenu {
        display: none;
    }

    .sidebar-item.active .submenu {
        display: block;
    }

    .sidebar-item.active .sidebar-link {
        background-color: #f0f0f0;
        color: #000;
    }

    .BtnOut {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 45px;
        height: 45px;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition-duration: .3s;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
        background-color: rgb(117 1 2);
    }

    .sign {
        width: 100%;
        transition-duration: .3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sign svg {
        width: 17px;
    }

    .sign svg path {
        fill: white;
    }

    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #dce7f1;
        border-radius: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #444;
        line-height: 37px;
    }

    .swal-footer {
        text-align: center !important;
    }
</style>
<style>
    .datepicker-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .calendar-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #555;
    }

    .calendar-icon:hover {
        color: #000;
    }

    .page-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .page-title {
        color: #059669;
        font-size: 1.2rem;
        font-weight: bold;
    }
</style>

{{-- Responsive datatable --}}
<style>
    table.dataTable td,
    table.dataTable th {
        white-space: normal !important;
        word-wrap: break-word;

    }

    table.dataTable.dtr-column>tbody>tr>td.dtr-control:before,
    table.dataTable.dtr-column>tbody>tr>th.dtr-control:before,
    table.dataTable.dtr-column>tbody>tr>td.control:before,
    table.dataTable.dtr-column>tbody>tr>th.control:before {
        box-sizing: border-box !important;
        content: "+" !important;
        background-color: #31b131 !important;
        border-radius: 50% !important;
        color: white !important;
        display: inline-block !important;
        text-align: center !important;
        width: 22px !important;
        height: 22px !important;
        border: .1em solid white !important;
        box-shadow: 0 0 .2em #444 !important;
        font-size: 13px !important;
    }


    table.dataTable.dtr-column>tbody>tr.dtr-expanded td.dtr-control:before,
    table.dataTable.dtr-column>tbody>tr.dtr-expanded th.dtr-control:before,
    table.dataTable.dtr-column>tbody>tr.dtr-expanded td.control:before,
    table.dataTable.dtr-column>tbody>tr.dtr-expanded th.control:before {
        content: "-" !important;
        background-color: #d33333 !important;
        box-sizing: border-box !important;
        border-radius: 50% !important;
        color: white !important;
        display: inline-block !important;
        text-align: center !important;
        width: 22px !important;
        border: .1em solid white !important;
        box-shadow: 0 0 .2em #444 !important;
        font-size: 13px !important;
    }
</style>

{{-- button-container --}}
<style>
    #buttons-container {
        /* visibility: hidden; */
        padding-bottom: 4px;
    }
</style>
{{-- custom input type file --}}
<style>
    .avatar-img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #22573E;
        transition: 0.3s;
    }

    .dropdown-custom {
        position: relative;
        display: inline-block;
    }

    .dropdown-toggle-custom {
        background: none;
        border: none;
        cursor: pointer;
        outline: none;
    }

    .dropdown-menu-custom {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        min-width: 120px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .dropdown-menu-custom.show {
        display: block;
    }

    .dropdown-menu-custom li {
        padding: 8px 12px;
    }

    .choose-file-btn {
        background-color: #E6EDF7;
        border-color: #E6EDF7;
        color: #7E8B91;
    }

    .file-name {
        background-color: white !important;
        cursor: pointer;
    }
</style>

@if (Session::has('message'))
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
    swal("Thông báo", "{{ Session::get('message') }}", 'success', {
            button: true,
            button: "OK",
            timer: 3000,
            dangerMode: true,
        });
</script>
@php
Session::put('message', null);
@endphp
@elseif(Session::has('error'))
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
    swal("Thông báo", "{{ Session::get('error') }}", 'error', {
            button: true,
            button: "OK",
            timer: 15000,
            dangerMode: true,
        });
</script>
@php
Session::put('error', null);
@endphp
@endif

<script>
    $(window).on('load', function() {
        $('.loading-wrapper').fadeOut('slow');
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

    /* 1. GHI NHỚ mục one-sub đang ACTIVE lúc page load */
    const currentActiveOneSub = document.querySelector(
        '.sidebar-item.one-sub.active'
    );

    /* 2. Xử lý cho menu đa cấp (.has-sub) */
    const hasSubLinks = document.querySelectorAll(
        '.sidebar-item.has-sub > .sidebar-link'
    );

    hasSubLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();               // chỉ bung/thu menu, không chuyển trang

            /* 2a. Bỏ active các .has-sub khác */
            hasSubLinks.forEach(other => {
                if (other !== this) {
                    other.parentElement.classList.remove('active');
                }
            });

            /* 2b. Toggle chính nó */
            this.parentElement.classList.toggle('active');

            /* 2c. KHÔNG xoá, mà đảm bảo trang hiện tại vẫn sáng */
            if (currentActiveOneSub) {
                currentActiveOneSub.classList.add('active');
            }
        });
    });

    /* 3. KHÔNG gắn listener cho .one-sub → click là chuyển trang bình thường */
});
</script>

{{-- has-one sub --}}
{{-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebarLinks = document.querySelectorAll(".sidebar-item.has-sub > .sidebar-link");

        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const parentItem = this.parentElement;
                const isCurrentlyActive = parentItem.classList.contains('active');

                // Đóng tất cả submenu khác trước khi mở submenu đang click
                sidebarLinks.forEach(otherLink => {
                    if (otherLink.parentElement !== parentItem) {
                        otherLink.parentElement.classList.remove("active");
                    }
                });

                // Toggle submenu hiện tại
                if (isCurrentlyActive) {
                    parentItem.classList.remove("active");
                } else {
                    parentItem.classList.add("active");
                }
            });
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebarLinks = document.querySelectorAll(".sidebar-item.one-sub > .sidebar-link");

        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const parentItem = this.parentElement;
                const isCurrentlyActive = parentItem.classList.contains('active');

                // Đóng tất cả submenu khác trước khi mở submenu đang click
                sidebarLinks.forEach(otherLink => {
                    if (otherLink.parentElement !== parentItem) {
                        otherLink.parentElement.classList.remove("active");
                    }
                });

                // Toggle submenu hiện tại
                if (isCurrentlyActive) {
                    parentItem.classList.remove("active");
                } else {
                    parentItem.classList.add("active");
                }
            });
        });
    });
</script> --}}

<script>
    const menu = document.querySelector("#sidebar");
    const icon = document.querySelector(".burger-btn");
    const wrapper = document.querySelector(".sidebar-wrapper");

    icon.addEventListener("click", (e) => {
        e.stopPropagation();
        menu.classList.add("active");
    });

    document.addEventListener("click", (e) => {
        if (!wrapper.contains(e.target) && menu.classList.contains("active") && window.innerWidth < 1200) {
            menu.classList.remove("active");
        }
    });
</script>
<script>
    function initDateTimePicker(selector) {
        const input = document.querySelector(selector);
        console.log("Selector:", selector); // Kiểm tra giá trị trước khi truyền vào
        if (!input) return; // Kiểm tra nếu không tìm thấy input

        const wrapper = input.closest(".datepicker-wrapper"); // Tìm div bọc input
        const icon = wrapper?.querySelector(".calendar-icon"); // Tìm icon trong wrapper

        // Khởi tạo DateTimePicker
        const picker = new tempusDominus.TempusDominus(input, {
            localization: {
                format: "dd/MM/yyyy", // Hiển thị ngày/tháng/năm
                locale: "vi",
                dayViewHeaderFormat: {
                    month: '2-digit',
                    year: 'numeric'
                }
            },
            display: {
                viewMode: "calendar",
                components: {
                    clock: false,
                    date: true,
                    month: true,
                    year: true
                }
            }
        });

        // Khi click vào icon -> Mở DatePicker
        if (icon) {
            icon.addEventListener("click", function(event) {
                event.stopPropagation(); // Ngăn sự kiện lan ra ngoài
                if (picker.display.isVisible) {
                    picker.hide();
                } else {
                    picker.show();
                }
            });
        }
    }
    document.addEventListener("DOMContentLoaded", function() {
        // // Khởi tạo DatePicker cho tất cả input có class .datetimepicker
        document.querySelectorAll(".datetimepicker").forEach((element) => {
            initDateTimePicker(`#${element.id}`);
        });
    });
    document.querySelectorAll(".datetimepicker").forEach(function(input) {
        input.addEventListener("keydown", function(event) {
            if (event.key === "Backspace" || event.key === "Delete") {
                this.value = "";
                this.dispatchEvent(new Event("change"));
            }
        });
    });
</script>
@if (session('toast_error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
            Toast.fire({
                icon: 'error',
                title: '{{ session('toast_error') }}'
            });
            // Hoặc dùng SweetAlert2:
            // Swal.fire('Lỗi', '{{ session('toast_error') }}', 'error');
        });
</script>
@endif
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Khởi tạo Tempus Dominus với hỗ trợ mobile tốt hơn
        const id = document.getElementById("delivery_month");
        if (!id) return;
        const picker = new tempusDominus.TempusDominus(id, {
            localization: {
                format: 'MM/yyyy',
                locale: 'vi',

            },
            display: {
                viewMode: 'months', // Chỉ hiển thị tháng
                components: {
                    clock: false, // Ẩn giờ phút
                    date: false, // Ẩn ngày, chỉ chọn tháng
                    month: true, // Hiển thị tháng
                    year: true // Hiển thị năm
                }
            }
        });
        const icon = document.querySelector(".calendar-icon");

        if (icon) {
            icon.addEventListener("click", function(event) {
                event.stopPropagation(); // Ngăn sự kiện lan ra ngoài
                if (picker.display.isVisible) {
                    picker.hide();
                } else {
                    picker.show();
                }
            });
        }
        document.getElementById("delivery_month").addEventListener("keydown", function(event) {
            if (event.key === "Backspace" || event.key === "Delete") {
                this.value = "";
                this.dispatchEvent(new Event("change"));
            }
        });
    });
</script>
{{-- custom input type file --}}
<script>
    // Lấy tất cả các phần tử theo class
    let fileInputs = document.querySelectorAll(".import_excel");
    let fileNameInputs = document.querySelectorAll(".file-name");
    let chooseFileBtns = document.querySelectorAll(".choose-file-btn");

    // Cập nhật tên file vào ô text sau khi người dùng chọn tệp
    fileInputs.forEach((fileInput, index) => {
        fileInput.addEventListener("change", function() {
            let fileName = this.files.length > 0 ? this.files[0].name : "Không có tệp nào được chọn";
            fileNameInputs[index].value = fileName;
        });
    });

    // Khi click vào ô text, sẽ tự động kích hoạt input file
    fileNameInputs.forEach((fileNameInput, index) => {
        fileNameInput.addEventListener("click", function() {
            fileInputs[index].click();
        });
    });

    // Khi click vào label "Chọn tệp", kích hoạt input file
    chooseFileBtns.forEach((chooseFileBtn, index) => {
        chooseFileBtn.addEventListener("click", function() {
            fileInputs[index].click();
        });
    });
</script>
<!-- Tải ngôn ngữ tiếng Việt của Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/vi.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const dropdownButton = document.getElementById('userDropdownButton');
    const dropdownMenu = document.getElementById('userDropdownMenu');

    // Toggle dropdown khi click vào nút
    dropdownButton.addEventListener('click', function (event) {
        event.stopPropagation(); // Ngăn sự kiện click lan ra ngoài
        dropdownMenu.classList.toggle('show');
    });

    // Ẩn dropdown khi click ra ngoài
    document.addEventListener('click', function (event) {
        if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });
});
</script>

</html>