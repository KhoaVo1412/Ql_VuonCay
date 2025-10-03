@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-4 ">
    <h1 class="text-xl font-semibold mb-4">Báo cáo lịch sử Nhập / Xuất / Khai thác</h1>

    {{-- Bộ lọc --}}
    <form id="filterForm" class="row g-2 bg-white rounded shadow p-4" style="border-radius: 1.25rem !important;">
        <div class="col-md-3">
            <label class="form-label">Từ ngày</label>
            <input type="date" class="form-control" name="from" id="from">
        </div>
        <div class="col-md-3">
            <label class="form-label">Đến ngày</label>
            <input type="date" class="form-control" name="to" id="to">
        </div>
        <div class="col-md-3">
            <label class="form-label">Chế độ</label>
            <select class="form-select" name="mode" id="mode">
                <option value="">Tất cả</option>
                <option value="nhap">Nhập</option>
                <option value="xuat">Xuất</option>
                <option value="khaithac">Khai thác</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Kho</label>
            <select class="form-select" name="warehouseID" id="warehouseID">
                <option value="">-- Chọn kho --</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}">{{ $w->code }} - {{ $w->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Sản phẩm</label>
            <select class="form-select" name="productID" id="productID">
                <option value="">-- Chọn sản phẩm --</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 d-flex gap-2 mt-2">
            <button type="button" id="btnFilter" class="btn btn-primary">Lọc</button>
            <a id="btnExport" class="btn btn-success" target="_blank">Xuất Excel</a>
        </div>
    </form>


    {{-- Bảng --}}
    <div class="bg-white rounded shadow p-4" style="    border-radius: 1.25rem !important;">
        <table id="historyTable" class="min-w-full display">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Loại</th>
                    <th>Mã phiếu</th>
                    <th>Sản phẩm</th>
                    <th>Kho</th>
                    <th>Đơn vị</th>
                    <th>Số lượng</th>
                    <th>Tồn sau</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Ngày</th>
                    <th>Loại</th>
                    <th>Mã phiếu</th>
                    <th>Sản phẩm</th>
                    <th>Kho</th>
                    <th>Đơn vị</th>
                    <th>Số lượng</th>
                    <th>Tồn sau</th>
                    <th>Ghi chú</th>
                    {{-- <th colspan="6" class="text-right">Tổng trang:</th>
                    <th id="sumQty"></th>
                    <th></th>
                    <th></th> --}}
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
  const $table = $('#historyTable');
  const $form  = $('#filterForm');

  const dt = $table.DataTable({
    processing: true,
    serverSide: true,
    searching: false,
    order: [[0,'desc']],
    ajax: {
      url: "{{ route('inv.history.data') }}",
      data: function (d) {
        d.from        = $form.find('[name=from]').val();
        d.to          = $form.find('[name=to]').val();
        d.warehouseID = $form.find('[name=warehouseID]').val(); // ✔ khớp tên
        d.productID   = $form.find('[name=productID]').val();   // ✔ khớp tên
        d.mode        = $form.find('[name=mode]').val();
      }
    },
    columns: [
      { data: 'date',            name: 'date' },
      { data: 'display_type',    name: 'display_type' },
      { data: 'doc_code',        name: 'doc_code' },
      { data: 'product_label',   name: 'product_label' },
      { data: 'warehouse_label', name: 'warehouse_label' },
      { data: 'unit_name',       name: 'unit_name' },
      { data: 'quantity_changed',name: 'quantity_changed', className: 'text-right' },
      { data: 'balance_after',   name: 'balance_after',    className: 'text-right' },
      { data: 'note',            name: 'note' },
    ],
    footerCallback: function (row, data) {
      let total = 0;
      data.forEach(r => {
        const raw = (r.quantity_changed || '').replace(/[^\d.\-]/g,'');
        const val = parseFloat(raw);
        if (!isNaN(val)) total += val;
      });
      const sign = total >= 0 ? '+' : '−';
      $('#sumQty').text(sign + Math.abs(total).toFixed(2));
    }
  });

  $('#btnFilter').on('click', () => dt.ajax.reload());

  function buildExportUrl() {
    const params = new URLSearchParams({
      from:        $form.find('[name=from]').val() || '',
      to:          $form.find('[name=to]').val() || '',
      warehouseID: $form.find('[name=warehouseID]').val() || '',
      productID:   $form.find('[name=productID]').val() || '',
      mode:        $form.find('[name=mode]').val() || ''
    });

    // loại bỏ key rỗng
    [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });

    return "{{ route('inventory.export') }}" + "?" + params.toString();
  }

  // set href ngay khi load và mỗi lần đổi filter
  const refreshExportHref = () => $('#btnExport').attr('href', buildExportUrl());
  refreshExportHref();
  $form.on('change', 'input,select', refreshExportHref);
});
</script>
@endsection