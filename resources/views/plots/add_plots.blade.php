@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Tạo Lô</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('plots.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Lô</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('plots.save') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <!-- Vườn (gardenID) -->
                            <div class="col-xl-6">
                                <label for="plantCode" class="form-label">Mã Lô</label>
                                <input type="text" class="form-control" name="plantCode" id="plantCode" required
                                    placeholder="Mã lô">
                            </div>
                            <!-- Vườn (gardenID) -->
                            {{-- <div class="col-xl-6">
                                <label for="gardenID" class="form-label">Vườn</label>
                                <select class="form-control" name="gardenID" id="gardenID" required>
                                    <option value="">Chọn vườn</option>
                                    @foreach($gardens as $garden)
                                    <option value="{{ $garden->id }}">{{ $garden->gardenName }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <!-- Tên lô -->
                            <div class="col-xl-6">
                                <label for="plotName" class="form-label">Tên Lô</label>
                                <input type="text" class="form-control" name="plotName" id="plotName" required
                                    placeholder="Tên lô">
                            </div>
                            <!-- Diện tích lô -->
                            <div class="col-xl-6">
                                <label for="plotArea" class="form-label">Diện Tích (m2)</label>
                                <input type="number" min="0" step="0.01" class="form-control" name="plotArea"
                                    id="plotArea" required placeholder="Diện tích">
                            </div>
                            <!-- Số lượng cây -->
                            <div class="col-xl-6">
                                <label for="plantCount" class="form-label">Số Lượng Cây</label>
                                <input type="number" class="form-control" placeholder="0" readonly>
                            </div>
                            <div class="col-xl-6">
                                <label for="plantCount" class="form-label">Năm Trồng</label>
                                <input type="number" min="2000" class="form-control" name="year" id="year" required
                                    placeholder="Năm">
                            </div>
                            <div class="col-xl-6">
                                <label for="plantCount" class="form-label">Tình Trạng Cây</label>
                                <input type="text" class="form-control" name="statusTree" id="statusTree" required
                                    placeholder="Tình trạng cây">
                            </div>
                            <div class="col-xl-12">
                                <label for="plantCount" class="form-label">Map</label>
                                <textarea class="form-control" name="mapJs" id="mapJs" required
                                    placeholder=""></textarea>
                            </div>
                            <div class="prism-toggle d-grid gap-2 d-md-flex p-">
                                <button type="submit" class="btn btn-success">Lưu Thông Tin</button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</section>
<script>
    function autoResize(textarea) {
        textarea.style.height = 'auto'; 
        textarea.style.height = textarea.scrollHeight + 'px';
    }
</script>

<style>
    .form-label {
        font-weight: bold;
    }

    .select2-container--default .select2-selection--single {
        height: 37px;
    }

    .modal-dialog {
        max-width: 90% !important;
        margin: 1.75rem auto;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        color: #059669;
        font-size: 1rem;
        font-weight: bold;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 1rem;
        }

        .form-group,
        .form-group.wide {
            flex: none;
        }
    }
</style>
@endsection