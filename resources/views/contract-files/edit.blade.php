@extends('layouts.app')
@section('content')
<style>
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
<div class="page-title my-3">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/contract-files">Danh Sách</a></li>
                    <li class="breadcrumb-item">Chỉnh Sửa</li>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    @include('layouts.alert')
</div>

<div class="row">
    <form action="{{ route('contract-files.edit', $xuathang->id) }}" id="uploadForm" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card custom-card">
            <div class="card-header justify-content-between d-flex">
                <h5>Chỉnh Sửa File</h5>
                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
            </div>
            <div class="card-body">
                <input type="hidden" name="contract_id" value="{{ $xuathang->id }}">
                <div class="mb-2">
                    <label for="code"><b>Mã lệnh xuất hàng</b></label>
                    <input type="text" id="code" class="form-control" value="{{ $xuathang->code }}" readonly>
                    <input type="hidden" name="code" value="{{ $xuathang->id }}">
                </div>

                <div id="fileContainer">
                    <div class="file-group mb-2">
                        <div id="fileContainer" class="mb-2">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label"><b>Tên File</b></label>
                                    <input type="text" id="tenFile" class="form-control" placeholder="Nhập tên file">
                                </div>
                                {{-- <div class="col-md-6 mb-2">
                                    <label><b>Chọn file nhập</b></label>
                                    <div class="d-flex gap-2">
                                        <input type="file" id="fileInput" class="form-control" accept=".pdf">
                                        <button type="button" id="addFileBtn" class="btn btn-success">Thêm</button>
                                    </div>

                                </div> --}}
                                <div class="col-md-6 mb-2">

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
                                {{-- <div class="col d-flex align-items-end mb-2">
                                    <button type="button" id="addFileBtn" class="btn btn-success">Thêm</button>
                                </div> --}}
                            </div>
                        </div>
                        <div id="fileList"></div>
                        <hr style=" border: 1px solid #333; margin: 20px 0; ">
                        @foreach ($xuathang->exportFile as $index => $file)
                        <div class="row file-row" data-id="{{ $file->id }}">

                            <div class="col-md-6 mb-2">

                                <label class="form-label"><b>Tên File</b></label>
                                <input type="text" name="ten_file[{{ $index }}]" class="form-control"
                                    value="{{ $file->name }}" required>
                            </div>
                            {{-- <div class="col-md-6 mb-2">
                                <label><b>Chọn File mới</b></label>
                                <div class="d-flex gap-2">
                                    <input type="file" name="file_input[{{ $index }}]" class="form-control"
                                        accept=".pdf">
                                    <button type="button" class="btn btn-danger remove-file d-inline-block"
                                        data-id="{{ $file->id }}">
                                        Xoá
                                    </button>
                                </div>

                            </div> --}}
                            <div class="col-md-6 mb-2">

                                <label for="import_ing" class="form-label text-black" style="cursor: pointer;">
                                    <b>Chọn Tài Liệu (Pdf):</b>
                                </label>

                                <div class="input-group">
                                    <label class="btn choose-file-btn"
                                        style="cursor: pointer; background-color: #E6EDF7; border-color: #E6EDF7; color: #7E8B91">
                                        Chọn tệp
                                    </label>
                                    <!-- Sử dụng opacity: 0 thay vì display: none để vẫn hiển thị thông báo lỗi mặc định -->
                                    <input type="file" class="import_excel" name="file_input[{{ $index }}]"
                                        accept=".pdf" id="fileInput"
                                        style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; z-index: 1; cursor: pointer;">
                                    <input type="text" class="form-control file-name" id="fileInput"
                                        placeholder="Không có tệp nào được chọn" name="file_input[{{ $index }}]"
                                        readonly>
                                    <div class="d-flex gap-2" style="margin-left: 10px;">
                                        <button type="button" class="btn btn-danger remove-file d-inline-block"
                                            data-id="{{ $file->id }}">
                                            Xoá
                                        </button>
                                    </div>

                                </div>



                            </div>
                            {{-- <div class="col d-flex align-items-end mb-2">
                                <button type="button" class="btn btn-danger remove-file d-inline-block"
                                    data-id="{{ $file->id }}">
                                    Xoá
                                </button>
                            </div> --}}

                            @if ($file)
                            <div class="mb-2">
                                <label><b>Tệp hiện tại: </b></label>
                                <br>
                                <div class="file-item">
                                    <a href="{{ asset('contracts/' . $xuathang->id . '/' . $file->file_name) }}"
                                        target="_blank">
                                        {{ $file->file_name }}
                                    </a>
                                    <input type="hidden" name="deleted_files[]" class="deleted-file" value="">
                                    {{-- <button type="button" class="btn btn-danger btn-sm remove-file d-inline-block"
                                        data-id="{{ $file->id }}">
                                        Xóa
                                    </button> --}}
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                        {{-- <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Xác nhận</button>
                            <a href="/contract-files" class="btn btn-danger">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
            document.addEventListener("click", function(event) {
                let button = event.target.closest(
                    ".remove-file"); // Tìm phần tử cha gần nhất có class .remove-file
                // console.log('button:', button);
                if (button) {
                    let fileId = button.getAttribute("data-id");
                    // console.log('Xóa file ID:', fileId);

                    let row = button.closest(".file-row");
                    // console.log("row", row);
                    if (row) {
                        row.querySelector(".deleted-file").value = fileId; // Ghi ID vào input hidden
                        row.style.display = "none"; // Ẩn dòng
                    }
                }
            });
        });
</script>

<!-- Script xử lý thêm file -->
<script>
    let fileArray = [];

        $(document).ready(function() {
            $("#addFileBtn").click(function() {
                let fileName = $("#tenFile").val().trim();
                let fileInput = $("#fileInput")[0].files[0];
                let existingFiles = @json($xuathang->exportFile->pluck('name'));
                if (!fileName || !fileInput) {
                    alert("Vui lòng nhập tên file và chọn file!");
                    return;
                }

                let isDuplicate = fileArray.some(file => file.name === fileName);

                if (isDuplicate) {
                    alert("Tên file đã tồn tại trong danh sách!");
                    return;
                }

                if (existingFiles.includes(fileName)) {
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
                    // alert("Tên file đã tồn tại trên hệ thống!");
                    // return;
                }

                if (fileInput.name.split('.').pop().toLowerCase() !== "pdf") {
                    alert("Chỉ được chọn file PDF!");
                    return;
                }

                let maxSize = 20 * 1024 * 1024;
                if (fileInput && fileInput.size > maxSize) {
                    swal({
                        title: "Thông báo",
                        text: "File vượt quá dung lượng cho phép (tối đa 20MB).",
                        icon: "error",
                        button: true,
                        button: "OK",
                        timer: 3000,
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
                <div class="file-item" data-index="${fileArray.length - 1}">
                    <span>${fileName} - ${fileInput.name}</span>
                    <button type="button" class="btn btn-danger btn-sm remove-file">
                        Xóa
                    </button>
                </div>`;

                fileList.append(fileItem);
                $("#tenFile, #fileInput").val("");
            });
        });
</script>

<!-- Script xử lý xóa file -->
<script>
    $(document).on("click", ".remove-file", function() {
            let fileItem = $(this).closest(".file-item");
            let index = fileItem.data("index");

            // Xóa file khỏi mảng
            fileArray.splice(index, 1);

            // Xóa phần tử HTML
            fileItem.remove();

            // Cập nhật lại index sau khi xóa
            $(".file-item").each(function(i) {
                $(this).attr("data-index", i);
            });
        });
</script>

<!-- Script xử lý gửi form -->
<script>
    $(document).ready(function() {
            $("#uploadForm").submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                let maxSize = 20 * 1024 * 1024;

                let isFileTooLarge = false;
                formData.append('_method', 'PUT');

                fileArray.forEach((item, index) => {
                    formData.append("new_file[]", item.name);
                    formData.append("new_files_uploaded[]", item.file);
                });

                // for (let pair of formData.entries()) {
                //     console.log(pair[0] + ": ", pair[1]);
                // }
                // for (let pair of formData.entries()) {
                //     if (pair[1] instanceof File) {
                //         if (pair[1].size > maxSize) {
                //             isFileTooLarge = true;
                //         }
                //     }
                // }

                let largeFile = [...formData.entries()].find(([key, file]) =>
                    key.includes("file_input") && file instanceof File && file.size > maxSize
                );
                // console.log("largeFile", largeFile);
                if (largeFile) {
                    swal({
                        title: "Thông báo",
                        text: "File vượt quá dung lượng cho phép (tối đa 20MB).",
                        icon: "error",
                        button: true,
                        button: "OK",
                        timer: 3000,
                        dangerMode: true,
                        // timer: 3000,
                        dangerMode: true,
                    });
                    return;
                }

                $.ajax({
                    url: "/contract-files/edit/" + $('input[name="contract_id"]').val(),
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                            window.location.href = response.redirect;
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.message || "Có lỗi xảy ra!";
                        swal({
                            title: "Thông báo",
                            text: errorMessage,
                            icon: "error",
                            button: true,
                            button: "OK",
                            timer: 3000,
                            dangerMode: true,
                        });
                    }
                });
            });
        });
</script>
@endsection