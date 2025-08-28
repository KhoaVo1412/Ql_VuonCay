@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa: {{ $comments->name }}</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comments.index') }}">Danh Sách</a></li>
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
            <form id="form-comments" action="{{ route('comments.update', ['id' => $comments->id]) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">

                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Mã Đánh Giá</label>
                                <input type="text" class="form-control" name="name" placeholder="Nhập mã"
                                    value="{{ $comments->name }}" required>
                            </div>

                            <!-- workerID -->
                            <div class="col-md-6">
                                <label for="workerID" class="form-label">Nhân Viên</label>
                                <select name="workerID" id="workerID" class="form-select">
                                    @foreach ($workers as $worker)
                                    <option value="{{ $worker->id }}" data-count-work="{{ $worker->countWork }}"
                                        data-count-cofirm="{{ $worker->countCofirm }}"
                                        data-count-un="{{ $worker->countUn }}" {{ $worker->id == $comments->workerID ?
                                        'selected' : '' }}
                                        >
                                        {{ $worker->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- deductionPoints -->
                            <div class="col-md-6">
                                <label for="deductionPoints" class="form-label">Điểm Đánh Giá</label>
                                <input type="number" step="0.1" class="form-control" name="deductionPoints"
                                    value="{{ $comments->deductionPoints ?? 0 }}">
                            </div>

                            <!-- rating -->
                            <div class="col-md-6">
                                <label for="rating" class="form-label">Xếp Hạng</label>
                                <input type="text" class="form-control" name="rating" value="{{ $comments->rating }}">
                            </div>
                            <div class="col-xl-6">
                                <label class="form-label mb-1">Số Lượng Công Việc Hiện</label>
                                <input type="number" class="form-control" id="countWork_display" value="0" readonly>
                                <input type="hidden" name="countWork" id="countWork" value="0" required>
                            </div>
                            <div class="col-xl-6">
                                <label class="form-label mb-1">Hoàn Thành Đúng Hạn</label>
                                <input type="number" class="form-control" id="countCofirm_display" value="0" readonly>
                                <input type="hidden" name="countCofirm" id="countCofirm" value="0" required>
                            </div>
                            <div class="col-xl-6">
                                <label class="form-label mb-1">Hoàn Thành Không Đúng Hạn</label>
                                <input type="number" class="form-control" id="countUn_display" value="0" readonly>
                                <input type="hidden" name="countUn" id="countUn" value="0" required>
                            </div>
                            <script>
                                (function () {
                                const sel = document.getElementById('workerID');

                                function number(n, d = 0) {
                                    return Number.isFinite(n) ? Number(n) : d;
                                }
                                function updateFromOption(opt) {
                                    const work   = number(+opt.dataset.countWork, 0);
                                    const ontime = number(+opt.dataset.countCofirm, 0);
                                    const late   = number(+opt.dataset.countUn, 0);

                                    // fill displays
                                    document.getElementById('countWork_display').value   = work;
                                    document.getElementById('countCofirm_display').value = ontime;
                                    document.getElementById('countUn_display').value     = late;

                                    // hidden for submit
                                    document.getElementById('countWork').value   = work;
                                    document.getElementById('countCofirm').value = ontime;
                                    document.getElementById('countUn').value     = late;

                                    // on-time rate
                                    const rate = work > 0 ? Math.round((ontime / work) * 100) : 0;
                                    document.getElementById('onTimeRate_display').value = rate + '%';
                                }

                                // init for selected
                                if (sel && sel.selectedOptions.length) {
                                    updateFromOption(sel.selectedOptions[0]);
                                }
                                // on change
                                sel.addEventListener('change', function () {
                                    if (this.selectedOptions.length) updateFromOption(this.selectedOptions[0]);
                                });
                            })();
                            </script>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng Thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Hoạt động" @if($comments->status == 'Hoạt động') selected @endif>Hoạt
                                        động
                                    </option>
                                    <option value="Không hoạt động" @if($comments->status == 'Không hoạt động') selected
                                        @endif>Không
                                        hoạt động</option>
                                </select>
                            </div>
                            <!-- note -->
                            <div class="col-md-12">
                                <label for="note" class="form-label">Ghi chú</label>
                                <textarea class="form-control" name="note" rows="3">{{ $comments->note }}</textarea>
                            </div>
                            @hasanyrole('Công Nhân')
                            <div class="col-md-12">
                                <label for="note" class="form-label">Ý Kiến</label>
                                <textarea class="form-control" name="opinion"
                                    rows="3">{{ $comments->opinion }}</textarea>
                            </div>
                            @endhasanyrole
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