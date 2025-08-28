@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0"></h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('plots.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Lô Excel</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('import-plots') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>Thêm Bằng Excel</h5>
                <br>
                <div class="mb-3">
                    <label for="excel_file" class="form-label text-black" style="cursor: pointer;">
                        <b>Chọn Tài Liệu (Excel):</b>
                    </label>
                    <div class="input-group">
                        <label class="btn choose-file-btn"
                            style="cursor: pointer; background-color: #E6EDF7; border-color: #E6EDF7; color: #7E8B91">
                            Chọn Tệp
                        </label>
                        <!-- Sử dụng opacity: 0 thay vì display: none để vẫn hiển thị thông báo lỗi mặc định -->
                        <input type="file" class="import_excel" name="excel_file" accept=".xlsx,.xls"
                            style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; z-index: 1; cursor: pointer;"
                            required>
                        <input type="text" class="form-control bg-white file-name" style="cursor: pointer;"
                            placeholder="Không có tệp nào được chọn" readonly>
                    </div>
                </div>

                {{-- <p style="color:red; font-size:13px;">Lưu ý *:
                    <br>
                    - Cột <strong>Plantation</strong> trong file Excel có thể nhập viết tắt là <code>NT</code>.
                    <br>
                    - Cột <strong>Tap_tin (Pdf)</strong> trong file Excel phải nhập đúng tên file PDF cần upload. Nếu
                    không đúng, hệ thống sẽ không nhận dạng được.
                    <br>
                    Ví dụ: Nếu trong cột <strong>Tap_tin (Pdf)</strong> bạn nhập <code>ten_file</code> hoặc
                    <code>ten_file.pdf</code>,
                    thì khi upload file, bạn phải chọn đúng file có tên <code>ten_file.pdf</code>.

                </p> --}}
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Tạo</button>
                    <a href="{{ asset('files/plant_plot_template.xlsx') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Tải File Mẫu
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- <form action="{{ route('import-plantingareas.saveData') }}" method="POST">
        @if(isset($data) && count($data))
        <div class="card mt-4">
            <div class="card-header">
                <h5>Upload File Pdf</h5>
            </div>
            <div class="card-body table-responsive">
                <table id="previewTable" class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th></th>
                            <th>Mã Lô Vùng Trồng</th>
                            <th>Tập Tin (PDF)</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                        @if(!empty($row['tap_tin_pdf']))
                        <tr>
                            <td></td>
                            <td>{{ $row['ma_lo'] ?? '' }}</td>
                            <input type="hidden" class="ma-lo-value" value="{{ $row['ma_lo'] }}">

                            <td>
                                <span class="expected-pdf">{{ $row['tap_tin_pdf'] ?? '' }}</span><br>
                                <input type="hidden" name="uploaded_pdfs[{{ $row['tap_tin_pdf'] }}]"
                                    class="uploaded-hidden" value="">
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>


                @csrf
                <input type="hidden" name="data" value="{{ base64_encode(serialize($data)) }}">
                <button type="submit" class="btn btn-primary mt-3 submit-pdf">Xác Nhận Lưu Dữ Liệu Excel</button>

            </div>
        </div>
        @endif
    </form> --}}
</section>

<script>
    $(document).ready(function () {
            // Khởi tạo DataTable
            let table = $('#previewTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                lengthChange: false,
                pageLength: 10,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json"
                },
                columnDefs: [{
                    className: 'dtr-control',
                    orderable: false,
                    targets: 0,
                }],
                order: [1, 'asc'],
                responsive: {
                    details: {
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
                                return col.hidden ?
                                    '<li data-dtr-index="' + i + '" data-dt-row="' + rowIdx +
                                    '" data-dt-column="' + col.columnIndex + '">' +
                                    '<span class="dtr-title">' + col.title + ':</span> ' +
                                    '<span class="dtr-data">' + col.data + '</span>' +
                                    '</li>' : '';
                            }).join('');

                            return data ? $('<ul data-dtr-index="' + rowIdx + '" class="dtr-details"/>')
                                .append(data) : false;
                        }
                    }
                },
                layout: {
                    topStart: {
                        buttons: [
                            {
                                text: 'Upload PDF',
                                className: 'btn btn-warning',
                                action: function () {
                                    let input = $('<input type="file" accept=".pdf" multiple style="display:none">');
                                    $('body').append(input);
                                    input.trigger('click');

                                    input.on('change', function () {
                                        let files = input[0].files;
                                        let formData = new FormData();

                                        for (let file of files) {
                                            formData.append('pdfs[]', file);
                                        }

                                        // Tạo map tên file PDF -> mã lô
                                        let pdfMaLoMap = {};
                                        let validPdfNames = [];

                                        $('#previewTable tbody tr').each(function () {
                                            let expectedPdf = $(this).find('.expected-pdf').text().trim().toLowerCase().replace('.pdf', '');
                                            let maLo = $(this).find('.ma-lo-value').val()?.trim() ?? '';

                                            if (expectedPdf && maLo) {
                                                pdfMaLoMap[expectedPdf] = maLo;
                                                validPdfNames.push(expectedPdf);
                                            }
                                        });

                                        formData.append('pdf_ma_lo_map', JSON.stringify(pdfMaLoMap));
                                        formData.append('valid_names', JSON.stringify(validPdfNames));

                                      
                                    });
                                }
                            }
                        ]
                    }
                }
            });

            // Kiểm tra khi submit: có thiếu file PDF không?
            $('form').on('submit', function (e) {
                // Kiểm tra nếu nút được bấm không phải là nút `.submit-pdf` thì bỏ qua
                let submitter = e.originalEvent?.submitter;
                if (!submitter || !$(submitter).hasClass('submit-pdf')) {
                    return; // không làm gì nếu không phải nút submit-pdf
                }

                let missingPdfs = [];

                $('#previewTable tbody tr').each(function () {
                    let expectedPdf = $(this).find('.expected-pdf').text().trim();
                    let uploadedValue = $(this).find('input.uploaded-hidden').val().trim();

                    if (expectedPdf && !uploadedValue) {
                        missingPdfs.push(expectedPdf);
                    }
                });

                if (missingPdfs.length > 0) {
                    e.preventDefault();
                    swal({
                        title: "Thiếu file PDF",
                        text: 'Bạn chưa upload đủ file PDF cho các dòng sau:\n\n' + missingPdfs.join('\n'),
                        icon: "error",
                        button: "OK",
                        dangerMode: true,
                    });
                }
            });

        });
</script>



@endsection