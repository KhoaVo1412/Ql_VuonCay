@extends('layouts.app')
@section('content')
    <section>
        <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
            <h3 class="page-title fw-semibold fs-18 mb-0">
            </h3>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0 padding">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tải Mã Qr</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <form id="form-account" action="{{ route('generate_qr') }}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="card custom-card">
                        <div class="card-header justify-content-between d-flex">
                            <h5>Tải Mã QR</h5>
                        </div>
                        <div class="card-body">
                            {{-- <div class="row modal-body gy-4"> --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="" class="form-label">Mã Lô Cần Xuất</label>
                                    <select id="batches_id" name="batches_id[]" multiple>
                                        <option value="all"> Chọn tất cả </option>
                                        @foreach ($batches as $val)
                                            <option value="{{ $val->id }}">{{ $val->batch_code }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary mt-3">Tải QR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            $('#batches_id').select2({
                placeholder: "Chọn Mã Lô",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
            $('#batches_id').on('select2:select', function(e) {
                if (e.params.data.id === "all") {
                    // Xóa tất cả selection khác, chỉ giữ 'all'
                    $('#batches_id').val(['all']).trigger('change');
                } else {
                    // Nếu chọn thêm cái gì đó mà đã chọn "all" thì loại bỏ "all"
                    let selected = $('#batches_id').val();
                    let index = selected.indexOf('all');
                    if (index !== -1) {
                        selected.splice(index, 1);
                        $('#batches_id').val(selected).trigger('change');
                    }
                }
            });
            $('#form-account').submit(function(e) {
                if ($('#batches_id').val().length === 0) {
                    e.preventDefault(); // Ngăn chặn form submit
                    alert('Vui lòng chọn ít nhất một mã lô để tải QR!');
                }
            });

        });
    </script>
    <style>
        .form-label {
            font-weight: bold;
        }

        .select2-container--default .select2-selection--single {
            height: 37px;
        }

        .select2-container .select2-search--inline .select2-search__field {
            height: 25px;
        }

        html,
        body {
            height: 100%;
        }
    </style>
@endsection
