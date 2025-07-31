@extends('layouts.app')
@section('content')
<style>
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border-radius: 5px;
        border: 1px solid #ced4da;
        padding: 6px 12px;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        font-size: 16px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
    }

    .file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        margin-bottom: 5px;
        border-radius: 5px;
        font-size: 14px;
        background-color: #f8f9fa;
    }

    .file-item i {
        margin-right: 8px;
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

    /* plus sign */
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

    /* text */
    .text {
        position: absolute;
        right: 0%;
        width: 0%;
        opacity: 0;
        color: white;
        font-size: 13px;
        font-weight: 600;
        transition-duration: .3s;
    }

    /* hover effect on button width */
    .BtnOut:hover {
        width: 100px;
        border-radius: 40px;
        transition-duration: .3s;
    }

    .BtnOut:hover .sign {
        width: 35%;
        transition-duration: .3s;
        padding-left: 20px;
    }

    /* hover effect button's text */
    .BtnOut:hover .text {
        opacity: 1;
        width: 70%;
        transition-duration: .3s;
        padding-right: 10px;
    }

    /* button click effect*/
    .BtnOut:active {
        transform: translate(2px, 2px);
    }
</style>

<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/contract-files">Danh Sách</a></li>
                    <li class="breadcrumb-item">Gắn File
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    {{-- <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h4>Gắn File Vào Lệnh Xuất Hàng</h4>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/contract-files">File</a></li>
                    <li class="breadcrumb-item"><a href="/contract-files/create-file">Gắn File Vào Lệnh Xuất Hàng</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div> --}}

    @include('layouts.alert')
</section>

<div class="row">
    <form id="uploadForm" action="{{ route('create-file.createfile') }}" method="POST" enctype="multipart/form-data">
        <div class="card custom-card">
            <div class="card-header justify-content-between d-flex">
                <h5>Gắn File Vào Lệnh Xuất Hàng</h5>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Gắn file</button>
            </div>
            <div class="card-body">

                @csrf
                <div class="mb-3">
                    <label class="form-label text-dark"><b>Mã lệnh xuất hàng</b></label>
                    <select id="code" class="form-control select2">
                        <option value="">-- Chọn lệnh xuất hàng --</option>
                        @foreach ($orderExports as $xh)
                        {{-- <option value="{{ $xh->id }}">{{ $xh->code }}</option> --}}
                        @if (!$xh->exportFile->whereNotNull('file_name')->count())
                        <option value="{{ $xh->id }}">{{ $xh->code }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div id="fileContainer" class="mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><b>Tên File</b></label>
                            <input type="text" id="tenFile" class="form-control" placeholder="Nhập tên file">
                        </div>
                        {{-- <div class="col-md-6 mb-2">
                            <label><b>Chọn file nhập</b></label>
                            <div class="d-flex gap-2">
                                <input type="file" id="fileInput" class="form-control" accept=".pdf">
                                <button type="button" id="addFileBtn" class="btn btn-success">
                                    Thêm</button>
                            </div>
                        </div> --}}
                        <div class="col-md-6 mb-3">

                            <label for="import_ing" class="form-label text-black" style="cursor: pointer;">
                                <b>Chọn Tài Liệu (Pdf):</b>
                            </label>

                            <div class="input-group">
                                <label class="btn choose-file-btn"
                                    style="cursor: pointer; background-color: #E6EDF7; border-color: #E6EDF7; color: #7E8B91">
                                    Chọn tệp
                                </label>
                                <!-- Sử dụng opacity: 0 thay vì display: none để vẫn hiển thị thông báo lỗi mặc định -->
                                <input type="file" class="import_excel" name="file" accept=".pdf" id="fileInput"
                                    style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; z-index: 1; cursor: pointer;">
                                <input type="text" class="form-control file-name" id="fileInput"
                                    placeholder="Không có tệp nào được chọn" readonly>
                                <div class="d-flex gap-2" style="margin-left: 10px;">
                                    <button type="button" id="addFileBtn" class="btn btn-success">
                                        Thêm</button>
                                </div>

                            </div>



                        </div>
                        {{-- <div class="col mb-2" style="padding-top: 2rem !important;">
                            <button type="button" id="addFileBtn" class="btn btn-success">
                                Thêm</button>
                        </div> --}}
                        {{-- <div class="col-md-1 d-flex align-items-end mb-2">
                            <button type="button" id="addFileBtn" class="btn btn-success">
                                Thêm</button>
                        </div> --}}
                    </div>
                </div>

                <div id="fileList"></div>

                {{-- <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Gắn</button>
                    {{-- <a href="/contract-files" class="btn btn-danger"><i class="fas fa-times"></i> Hủy</a>
                </div> --}}
    </form>
</div>
</div>


</div>

<script>
    $(document).ready(function() {

            function updateSubmitButtonState() {
                $("#submitBtn").prop("disabled", $(".file-item").length === 0);
            }

            let fileArray = []; // Mảng lưu trữ file

            $("#addFileBtn").click(function() {
                let fileName = $("#tenFile").val().trim();
                let fileInput = $("#fileInput")[0].files[0];

                if (!fileName || !fileInput) {
                    alert("Vui lòng nhập tên file và chọn file!");
                    return;
                }

                let isDuplicate = fileArray.some(file => file.name === fileName);

                if (isDuplicate) {
                    swal({
                        title: "Thông báo",
                        text: "Tên file đã tồn tại trên hệ thống.",
                        icon: "error",
                        button: true,
                        button: "OK",
                        timer: 3000,
                        dangerMode: true,
                    });
                    return;
                    // alert("Tên file đã tồn tại trong danh sách!");
                    // return;
                }

                if (fileInput.name.split('.').pop().toLowerCase() !== "pdf") {
                    alert("Chỉ được chọn file PDF!");
                    return;
                }

                // let maxSize = 20 * 1024 * 1024;
                // if (fileInput.size > maxSize) {
                //     alert("File vượt quá dung lượng cho phép (tối đa 20MB)!");
                //     return;
                // }

                let maxSize = 20 * 1024 * 1024;
                if (fileInput.size > maxSize) {
                    swal({
                        title: "Thông báo",
                        text: "File vượt quá dung lượng cho phép (tối đa 20MB).",
                        icon: "error",
                        button: true,
                        button: "OK",
                        timer: 3000,
                        dangerMode: true,
                    });
                    return;
                }

                // Lưu file vào mảng
                fileArray.push({
                    name: fileName,
                    file: fileInput
                });

                let fileList = $("#fileList");

                let fileItem = `
            <div class="file-item">
                <span>${fileName} - ${fileInput.name}</span>
                <button type="button" class="btn btn-danger btn-sm remove-file" data-index="${fileArray.length - 1}">
                    Xóa
                </button>
            </div>`;

                fileList.append(fileItem);
                $("#tenFile, #fileInput").val("");
                updateSubmitButtonState();
            });

            $(document).on("click", ".remove-file", function() {
                let index = $(this).data("index");
                fileArray.splice(index, 1);
                $(this).closest(".file-item").remove();
                updateSubmitButtonState();
            });

            $("#uploadForm").submit(function(e) {
                e.preventDefault();

                let formData = new FormData();
                let selectedCode = $("#code").val().trim();

                if (!selectedCode) {
                    alert("Vui lòng chọn Mã lệnh xuất hàng!");
                    return;
                }

                formData.append("code", selectedCode);

                if (fileArray.length === 0) {
                    alert("Vui lòng thêm ít nhất một file!");
                    return;
                }

                fileArray.forEach((item, index) => {
                    formData.append("ten_file[]", item.name);
                    formData.append("files_uploaded[]", item.file);
                });

                // Lấy CSRF token từ meta
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                formData.append('_token', csrfToken); // Thêm CSRF token vào formData

                $.ajax({
                    url: "{{ route('create-file.createfile') }}", // Đảm bảo route đúng
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken // Thêm vào header (phòng khi Laravel kiểm tra header)
                    },
                    success: function(response) {
                        swal({
                            title: "Thông báo",
                            text: response.message,
                            icon: "success",
                            button: true,
                            button: "OK",
                            timer: 3000,
                            dangerMode: true,
                        }).then(() => {
                            window.location.href = response
                                .redirect; // Chuyển hướng ngay sau khi bấm OK
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.message || "Có lỗi xảy ra!";
                        swal({
                            title: "Thông báo",
                            text: rerrorMessage,
                            icon: "error",
                            button: true,
                            button: "OK",
                            timer: 3000,
                            dangerMode: true,
                        }).then(() => {
                            window.location.href = response
                                .redirect; // Chuyển hướng ngay sau khi bấm OK
                        });
                    }
                });
            });

            updateSubmitButtonState();
        });
</script>
@endsection