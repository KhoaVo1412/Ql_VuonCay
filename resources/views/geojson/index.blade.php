<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOA BINH RUBBER</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/imgs/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="/mazer-1.0.0/dist/assets/css/bootstrap.css">
    <link rel="stylesheet" href="/mazer-1.0.0/dist/assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="/mazer-1.0.0/dist/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="/mazer-1.0.0/dist/assets/vendors/bootstrap-icons/bootstrap-icons.css">
</head>

<body>
    <style>
    body {
        background-image: url("{{ asset('/imgs/background-geojson.jpg') }}")
    }

    .content {
        padding-bottom: 80px;
    }

    /* .footer {
            background: linear-gradient(to top, #126f09, #277414, #d4f6c3b8);
            color: white;
            padding: 20px;
            text-align: center;
        } */

    .custom-footer a {
        color: white;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .custom-footer a:hover {
        color: black;
    }
    </style>
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand ms-md-4 ms-lg-5" href="https://app.horuco.com.vn/">
                <img src="/imgs/logo_bl.png" alt="Logo" style="width:110px; height: auto;">
            </a>
        </div>
    </nav>

    <div class="container mt-5 content">
        @if (!empty($batch))
        <h2 class="text-center text-black">Mã nhà máy {{ optional($batch)->batch_code }}
        </h2>
        <div class="row">
            <div class="col pt-2" style="background-color: #C9C9C9; border-radius: 10px;">
                <h4 class="mb-2 text-black">Coordinate (long/lat)</h4>
                {{-- <textarea class="form-control" id="coordinate" rows="10" readonly>
                                                                            {{ $coordinates->map(fn($coord) => "{$coord['x']}, {$coord['y']}")->join("\n\n") }}
                </textarea> --}}
                <textarea class="form-control" id="coordinate" rows="10"
                    readonly>{{ collect($coordinates)->map(fn($feature) => collect((array) $feature)->map(fn($point) => $point['x'] . ', ' . $point['y'])->implode("\n"))->implode("\n\n") }}</textarea>
                <button type="button" class="btn btn-success my-2" onclick="copyText('coordinate')">Copy</button>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col pt-2" style="background-color: #C9C9C9; border-radius: 10px;">
                <h4 class="mb-2 text-black">Geojson</h4>
                {{-- <textarea class="form-control" id="geojson" rows="10" readonly>{!! $jsonContent !!}</textarea> --}}
                <textarea class="form-control" id="geojson" rows="10"
                    readonly>{{ json_encode($mergedGeoJSON, JSON_UNESCAPED_UNICODE) }}</textarea>

                <button type="button" class="btn btn-success my-2" onclick="copyText('geojson')">Copy</button>
                <a href="{{ route('download.geojson', ['batch_code' => $batch->batch_code]) }}"
                    class="btn btn-success my-2">
                    Download Geojson
                </a>
            </div>
        </div>
        @elseif (!empty($plantingarea))
        <h2 class="text-center text-black"> {{ optional($plantingarea)->ma_lo }}
        </h2>
        <div class="row">
            <div class="col pt-2" style="background-color: #C9C9C9; border-radius: 10px;">
                <h4 class="mb-2 text-black">Coordinate (long/lat)</h4>
                {{-- <textarea class="form-control" id="coordinate" rows="10" readonly>
                                                            @foreach ($coordinates as $index => $feature)
                                                                @foreach ($feature as $point)
                                                                    {{ $point['x'] }}, {{ $point['y'] }}
                @endforeach
                @if (!$loop->last && $loop->count == 1)
                @endif
                @endforeach
                </textarea> --}}
                <textarea class="form-control" id="coordinate" rows="10"
                    readonly>{{ collect($coordinates)->map(fn($feature) => collect((array) $feature)->map(fn($point) => $point['x'] . ', ' . $point['y'])->implode("\n"))->implode("\n\n") }}</textarea>
                <button type="button" class="btn btn-success my-2" onclick="copyText('coordinate')">Copy</button>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col pt-2" style="background-color: #C9C9C9; border-radius: 10px;">
                <h4 class="mb-2 text-black">Geojson</h4>
                {{-- <textarea class="form-control" id="geojson" rows="10" readonly>{!! $jsonContent !!}</textarea> --}}
                <textarea class="form-control" id="geojson" rows="10"
                    readonly>{{ json_encode($mergedGeoJSON, JSON_UNESCAPED_UNICODE) }}</textarea>
                <button type="button" class="btn btn-success my-2" onclick="copyText('geojson')">Copy</button>
                <a href="{{ route('download.geojson', ['ma_lo' => $plantingarea->ma_lo]) }}"
                    class="btn btn-success my-2">
                    Download Geojson
                </a>
            </div>
        </div>
        @elseif (!empty($orderexport))
        <h2 class="text-center text-black">Lệnh xuất hàng {{ optional($orderexport)->code }}
        </h2>
        <div class="row">
            <div class="col pt-2" style="background-color: #C9C9C9; border-radius: 10px;">
                <h4 class="mb-2 text-black">Coordinate (long/lat)</h4>
                {{-- <textarea class="form-control" id="coordinate" rows="10" readonly>
                                                                            {{ $coordinates->map(fn($coord) => "{$coord['x']}, {$coord['y']}")->join("\n\n") }}
                </textarea> --}}
                <textarea class="form-control" id="coordinate" rows="10"
                    readonly>{{ collect($coordinates)->map(fn($feature) => collect((array) $feature)->map(fn($point) => $point['x'] . ', ' . $point['y'])->implode("\n"))->implode("\n\n") }}</textarea>
                <button type="button" class="btn btn-success my-2" onclick="copyText('coordinate')">Copy</button>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col pt-2" style="background-color: #C9C9C9; border-radius: 10px;">
                <h4 class="mb-2 text-black">Geojson</h4>
                {{-- <textarea class="form-control" id="geojson" rows="10" readonly>{!! $jsonContent !!}</textarea> --}}
                <textarea class="form-control" id="geojson" rows="10"
                    readonly>{{ json_encode($mergedGeoJSON, JSON_UNESCAPED_UNICODE) }}</textarea>

                <button type="button" class="btn btn-success my-2" onclick="copyText('geojson')">Copy</button>
                <a href="{{ route('download.geojson', ['code' => $orderexport->code]) }}" class="btn btn-success my-2">
                    Download Geojson
                </a>
            </div>
        </div>
        @endif

    </div>

    {{-- <footer class="custom-footer text-center text-white py-3 fixed-bottom bg-success" style="position: relative;">
        <p class="mb-0">© 2025 Copyrights <a href="https://app.horuco.com.vn/">Hòa Bình Rubber</a></p>
    </footer> --}}


    <footer class="custom-footer text-center text-white py-3 fixed-bottom" style="background: #1B710E;">
        <p class="mb-0">© 2025 Copyrights <a href="https://app.horuco.com.vn/">Hòa Bình Rubber</a></p>
    </footer>

    <script>
    function copyText(elementId) {
        let textarea = document.getElementById(elementId);
        textarea.select();
        textarea.setSelectionRange(0, 99999); // Dành cho mobile
        document.execCommand("copy");

        // Hiển thị thông báo copy thành công
        alert("Đã sao chép " + elementId + " vào bảng tạm!");
    }
    </script>

</body>

</html>