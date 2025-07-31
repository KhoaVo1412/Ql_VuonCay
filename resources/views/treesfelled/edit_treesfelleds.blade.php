@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa: {{ $treesfelleds->plant->plantCode }}</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('treesfelleds.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>

    <style>
        .tab-content {
            border-right: 1px solid #dee2e6;
            border-left: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            padding: 5px;
        }

        .col-xl-4 {
            padding-top: 15px;
        }

        .form-label {
            font-weight: bold;
        }
    </style>

    <div class="row">
        <div class="col-xl-12">
            <form id="form-treesfelleds" action="{{ route('treesfelleds.update', ['id' => $treesfelleds->id]) }}"
                method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">

                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- detectionDate -->
                            <div class="col-md-4">
                                <label class="form-label">Ngày phát hiện</label>
                                <input type="date" class="form-control" name="detectionDate"
                                    value="{{ $treesfelleds->detectionDate }}" required>
                            </div>

                            <!-- plantID -->
                            <div class="col-md-4">
                                <label class="form-label">Mã Cây</label>
                                <select name="plantID" class="form-control" required>
                                    <option value="">-- Chọn cây --</option>
                                    @foreach($plants as $plant)
                                    <option value="{{ $plant->id }}" {{ $plant->id == $treesfelleds->plantID ?
                                        'selected' : '' }}>
                                        {{ $plant->plantCode }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- specificLocation -->
                            <div class="col-md-4">
                                <label class="form-label">Vị trí cụ thể</label>
                                <input type="text" class="form-control" name="specificLocation"
                                    value="{{ $treesfelleds->specificLocation }}" required>
                            </div>

                            <!-- cause -->
                            <div class="col-md-6">
                                <label class="form-label">Nguyên nhân</label>
                                <input type="text" class="form-control" name="cause" value="{{ $treesfelleds->cause }}">
                            </div>

                            <!-- treeCondition -->
                            <div class="col-md-4">
                                <label class="form-label">Tình Trạng Cây</label>
                                <input type="text" class="form-control" name="treeCondition"
                                    value="{{ $treesfelleds->treeCondition }}">
                            </div>
                            <!-- workerID -->
                            <div class="col-md-4">
                                <label class="form-label">Người Phát Hiện</label>
                                <select name="workerID" class="form-control" required>
                                    <option value="">-- Chọn công nhân --</option>
                                    @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ $worker->id == $treesfelleds->workerID ?
                                        'selected' : '' }}>
                                        {{ $worker->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- reportStatus -->
                            <div class="col-md-4">
                                <label class="form-label">Trạng Thái Báo Cáo</label>
                                <select name="reportStatus" class="form-control">
                                    <option value="Chưa xử lý" {{ $treesfelleds->reportStatus == 'Chưa xử lý' ?
                                        'selected' : '' }}>
                                        Chưa xử lý
                                    </option>
                                    <option value="Đã xử lý" {{ $treesfelleds->reportStatus == 'Đã xử lý' ? 'selected' :
                                        '' }}>
                                        Đã xử lý
                                    </option>
                                </select>
                            </div>



                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng Thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Hoạt động" @if($treesfelleds->status == 'Hoạt động') selected
                                        @endif>Hoạt
                                        động
                                    </option>
                                    <option value="Không hoạt động" @if($treesfelleds->status == 'Không hoạt động')
                                        selected
                                        @endif>Không
                                        hoạt động</option>
                                </select>
                            </div>
                            <div class="p-t-10 col-sm-12" style="margin-top: 10px">
                                <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
@endsection