<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\ContractType;
use App\Models\Contract;
use App\Models\OrderExport;
use Yajra\DataTables\Facades\DataTables;

use App\Exports\ContractsExport;
use App\Exports\DDS3ContractExport;
use App\Exports\DDS2ContractExport;
use App\Exports\DDSContractExport;
use App\Models\ActionHistory;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     return view("contracts.index");
    // }

    // public function getData(Request $request)
    // {
    //     $contracts = Contract::with('contractType', 'customer', 'orderExports')
    //         ->select('contracts.*')->orderByDesc('id');

    //     if ($request->day) {
    //         $contracts->whereDay('delivery_date', $request->day);
    //     }

    //     if ($request->month) {
    //         $contracts->whereMonth('delivery_date', $request->month);
    //     }

    //     if ($request->year) {
    //         $contracts->whereYear('delivery_date', $request->year);
    //     }

    //     if ($request->ajax()) {

    //         return DataTables::of($contracts)
    //             ->addColumn('check', function ($row) {
    //                 return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
    //             })
    //             ->editColumn('contract_code', function ($contract) {
    //                 return $contract->contract_code;
    //             })
    //             ->editColumn('contract_type_name', function ($contract) {
    //                 // return $contract->contractType ? $contract->contractType->contract_type_name : 'Trống';
    //                 return $contract->contractType->isNotEmpty()
    //                     ? $contract->contractType->pluck('contract_type_name')->implode(', ')
    //                     : 'Chưa có tên hợp đồng';
    //             })
    //             ->editColumn('customer_id', function ($contract) {
    //                 return $contract->customer ? $contract->customer->company_name : 'Chưa có công ty';
    //             })
    //             ->addColumn('delivery_date', function ($contract) {
    //                 return $contract->delivery_date ? Carbon::parse($contract->delivery_date)->format('d/m/Y') : "";
    //             })
    //             ->addColumn('contract_days', function ($contract) {
    //                 return $contract->contract_days;
    //             })
    //             ->editColumn('quantity', function ($contract) {
    //                 return $contract->quantity;
    //             })
    //             ->filterColumn('delivery_date', function ($query, $keyword) {
    //                 $parts = explode('/', $keyword);
    //                 if (count($parts) == 2) {
    //                     $month = (int) $parts[0];
    //                     $year = (int) $parts[1];
    //                     $query->whereMonth('delivery_date', $month)
    //                         ->whereYear('delivery_date', $year);
    //                 } elseif (strlen($keyword) === 4) {
    //                     $query->whereYear('delivery_date', (int) $keyword);
    //                 }
    //             })
    //             ->editColumn('order_export_id', function ($contract) {
    //                 return $contract->orderExports ? implode(", ", $contract->orderExports->pluck('code')->toArray()) : 'Chưa có lệnh xuất hàng';
    //             })
    //             ->addColumn('actions', function ($row) {
    //                 $action = '
    //                 <div class="d-flex gap-1">
    //                     <a href="' . route('contracts.edit', $row->id) . '" class="btn btn-sm btn-primary">
    //                         <i class="fas fa-edit"></i>
    //                     </a>
    //                     <!-- <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
    //                         <i class="fas fa-trash-alt"></i>
    //                     </a> -->
    //                 </div>
    //                 <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
    //                     <div class="modal-dialog">
    //                         <div class="modal-content">
    //                             <div class="modal-header">
    //                                 <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác Nhận Xóa</h5>
    //                                 <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    //                             </div>
    //                             <div class="modal-body">
    //                                 Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->farm_name ?? 'Không có nông trường') . '</span>?
    //                             </div>
    //                             <div class="modal-footer">
    //                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
    //                                 <a href="/farms/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
    //                             </div>
    //                         </div>
    //                     </div>
    //                 </div>
    //             ';
    //                 return $action;
    //             })
    //             ->rawColumns(['check', 'contract_code', 'contract_type_name', 'customer_id', 'original_contract_number', 'delivery_date', 'contract_days', 'quantity', 'order_export_id', 'actions'])
    //             ->make(true);
    //     }
    // }
    public function index()
    {
        return view("contracts.index");
    }

    public function getData(Request $request)
    {
        $contracts = Contract::with('contractType', 'customer', 'orderExports')
            ->select('contracts.*')->orderByDesc('id');

        if ($request->month) {
            $contracts->whereMonth('delivery_date', $request->month);
        }

        if ($request->year) {
            $contracts->whereYear('delivery_date', $request->year);
        }

        if ($request->ajax()) {

            return DataTables::of($contracts)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->editColumn('contract_code', function ($contract) {
                    return $contract->contract_code;
                })
                ->editColumn('contract_type_id', function ($contract) {
                    return $contract->contractType ? $contract->contractType->contract_type_name : 'Trống';
                })
                ->editColumn('customer_id', function ($contract) {
                    return $contract->customer ? $contract->customer->company_name : 'Trống';
                })
                ->addColumn('order_export_id', function ($contract) {
                    return $contract->orderExports ? implode(", ", $contract->orderExports->pluck('code')->toArray()) : 'Trống';
                })
                ->addColumn('delivery_date', function ($contract) {
                    return $contract->delivery_date ? Carbon::parse($contract->delivery_date)->format('d/m/Y') : "";
                })
                // ->filterColumn('delivery_date', function ($query, $keyword) {
                //     $date = Carbon::createFromFormat('m', $keyword);
                //     $query->whereMonth('delivery_date', $date);
                // })
                // ->filterColumn('delivery_date', function ($query, $keyword) {
                //     $date = Carbon::createFromFormat('Y', $keyword);
                //     $query->whereYear('delivery_date', $date);
                // })
                ->filterColumn('delivery_date', function ($query, $keyword) {
                    // Expect keyword là chuỗi như "04/2025" hoặc "4/2025"

                    $parts = explode('/', $keyword);
                    if (count($parts) == 2) {
                        $month = (int) $parts[0];
                        $year = (int) $parts[1];
                        $query->whereMonth('delivery_date', $month)
                            ->whereYear('delivery_date', $year);
                    } elseif (strlen($keyword) === 4) {
                        // Nếu chỉ gõ năm, ví dụ: "2025"
                        $query->whereYear('delivery_date', (int) $keyword);
                    }
                })
                ->addColumn('actions', function ($row) {
                    $action = '
                    <div class="d-flex gap-1">
                        <a href="' . route('contracts.edit', $row->id) . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <!-- <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
                            <i class="fas fa-trash-alt"></i>
                        </a> -->
                    </div>
                    <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác Nhận Xóa</h5>
                                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->farm_name ?? 'Trống') . '</span>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <a href="/farms/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
                    return $action;
                })
                ->rawColumns(['check', 'actions', 'order_export_id'])
                ->make(true);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $contractTypes = ContractType::all();
        $batches = Batch::where('status', '=', 1)
            ->whereDoesntHave('orderExport')
            ->get();
        return view('contracts.create', compact('batches', 'customers', 'contractTypes'));
    }
    // public function storeContract(Request $request)
    // {
    //     $validated = $request->validate([
    //         'contract_type_ids' => 'required|array|min:1',
    //         'contract_type_ids.*' => 'exists:contract_types,id',
    //         'contract_code' => 'required|string|unique:contracts,contract_code',
    //         'customer_id' => 'required',
    //         'original_contract_number' => 'required',
    //         'delivery_month' => 'required',
    //         'quantity' => 'required|numeric|min:0',
    //         'contract_days' => 'required|numeric|min:0',
    //         'product_type_name' => 'required',
    //         'delivery_date' => 'required|date_format:d/m/Y|after_or_equal:container_closing_date',
    //         'packaging_type' => 'required',
    //         'container_closing_date' => 'required|date_format:d/m/Y',
    //         'market' => 'required',
    //         'production_or_trade_unit' => 'required',
    //         'third_party_sale' => 'required',
    //         'orders' => 'nullable|array',
    //         'orders.*.code' => 'required|string|max:255',
    //         'orders.*.batch' => 'nullable|exists:batches,id',
    //         // 'orderExports.code' => 'required|string|unique:orderExports, code',
    //     ], [
    //         'contract_type_ids.required' => 'Loại hợp đồng là bắt buộc.',
    //         'customer_id.required' => 'Khách hàng là bắt buộc.',
    //         'original_contract_number.required' => 'Số hợp đồng gốc là bắt buộc.',
    //         'delivery_month.required' => 'Tháng giao hàng là bắt buộc.',
    //         'quantity.required' => 'Khối lượng là bắt buộc.',
    //         'quantity.numeric' => 'Khối lượng phải là một số.',
    //         'contract_days.required' => 'Số ngày hợp đồng là bắt buộc.',
    //         'contract_days.numeric' => 'Số ngày hợp đồng phải là một số.',
    //         'product_type_name.required' => 'Tên chủng loại sản phẩm là bắt buộc.',
    //         'delivery_date.required' => 'Ngày giao hàng là bắt buộc.',
    //         'delivery_date.after_or_equal' => 'Ngày giao hàng phải bằng hoặc sau ngày đóng container.',
    //         'packaging_type.required' => 'Dạng đóng gói là bắt buộc.',
    //         'container_closing_date.required' => 'Ngày đóng container là bắt buộc.',
    //         'market.required' => 'Thị trường là bắt buộc.',
    //         'production_or_trade_unit.required' => 'Đơn vị sản xuất thương mại là bắt buộc.',
    //         'third_party_sale.required' => 'Bán cho bên thứ 3 là bắt buộc.',
    //         'orders.*.code.required' => 'Mã lệnh xuất hàng là bắt buộc.',
    //         'orders.*.batches.*.exists' => 'Lô hàng không hợp lệ.',
    //     ]);
    //     try {
    //         $validated['delivery_month'] = Carbon::createFromFormat('m/Y', $validated['delivery_month'])->format('Y-m');
    //         $validated['container_closing_date'] = Carbon::createFromFormat('d/m/Y', $validated['container_closing_date'])->format('Y-m-d');
    //         $validated['delivery_date'] = Carbon::createFromFormat('d/m/Y', $validated['delivery_date'])->format('Y-m-d');
    //         $contract = Contract::create($validated);
    //         $contractTypeIds = $validated['contract_type_ids'];
    //         unset($validated['contract_type_ids']);
    //         $contract->contractType()->attach($contractTypeIds);
    //         if ($request->has('orders')) {
    //             foreach ($request->orders as $orderData) {
    //                 if (!isset($orderData['code']) || empty($orderData['code'])) {
    //                     continue;
    //                 }
    //                 // $existingOrder = OrderExport::where('code', $orderData['code'])
    //                 //     ->where('contract_id', $contract->id)
    //                 //     ->first();
    //                 $existingOrder = OrderExport::where('code', $orderData['code'])
    //                     ->where('contract_id', '!=', $contract->id)
    //                     ->first();
    //                 if ($existingOrder) {
    //                     return redirect()->back()->withErrors(['orders' => 'Mã lệnh xuất hàng "' . $orderData['code'] . '" đã tồn tại trong hợp đồng.'])->withInput();
    //                 }
    //                 $orderExport = OrderExport::create([
    //                     'code' => $orderData['code'],
    //                     'contract_id' => $contract->id
    //                 ]);
    //                 if (!empty($orderData['batches']) && is_array($orderData['batches'])) {
    //                     foreach ($orderData['batches'] as $batchId) {
    //                         Batch::where('id', $batchId)->update(['order_export_id' => $orderExport->id]);
    //                     }
    //                 }
    //             }
    //         }
    //         return redirect()->route('cont')->with('message', 'Hợp đồng đã được tạo thành công!');
    //     } catch (\Exception $e) {
    //         Log::error("Lỗi khi tạo hợp đồng: " . $e->getMessage());
    //         return redirect()->back()
    //             ->withErrors(['error' => 'Có lỗi xảy ra khi tạo hợp đồng: ' . $e->getMessage()])
    //             ->withInput();
    //     }
    // }
    public function storeContract(Request $request)
    {
        $validated = $request->validate([
            'contract_code' => 'required|string|unique:contracts,contract_code',
            'contract_type_id' => 'required',
            'customer_id' => 'required',
            'original_contract_number' => 'required',
            'delivery_month' => 'required',
            'quantity' => 'required|numeric|min:0',
            'contract_days' => 'required|numeric|min:0',
            'product_type_name' => 'required',
            // 'delivery_date' => 'required|date|after_or_equal:container_closing_date',
            'delivery_date' => 'required|date_format:d/m/Y|after_or_equal:container_closing_date',
            'packaging_type' => 'required',
            // 'container_closing_date' => 'required|date',
            'container_closing_date' => 'required|date_format:d/m/Y',
            'market' => 'required',
            'production_or_trade_unit' => 'required',
            'third_party_sale' => 'required',
            'orders' => 'nullable|array',
            'orders.*.code' => 'required|string|max:255',
            'orders.*.batch' => 'nullable|exists:batches,id',
        ], [
            'contract_type_id.required' => 'Loại hợp đồng là bắt buộc.',
            'contract_code.unique' => 'Mã hợp đồng đã tồn tại.',
            'customer_id.required' => 'Khách hàng là bắt buộc.',
            'original_contract_number.required' => 'Số hợp đồng gốc là bắt buộc.',
            'delivery_month.required' => 'Tháng giao hàng là bắt buộc.',
            'quantity.required' => 'Khối lượng là bắt buộc.',
            'quantity.numeric' => 'Khối lượng phải là một số.',
            'contract_days.required' => 'Số ngày hợp đồng là bắt buộc.',
            'contract_days.numeric' => 'Số ngày hợp đồng phải là một số.',
            'product_type_name.required' => 'Tên chủng loại sản phẩm là bắt buộc.',
            'delivery_date.required' => 'Ngày giao hàng là bắt buộc.',
            'delivery_date.after_or_equal' => 'Ngày giao hàng phải bằng hoặc sau ngày đóng container.',
            'packaging_type.required' => 'Dạng đóng gói là bắt buộc.',
            'container_closing_date.required' => 'Ngày đóng container là bắt buộc.',
            'market.required' => 'Thị trường là bắt buộc.',
            'production_or_trade_unit.required' => 'Đơn vị sản xuất thương mại là bắt buộc.',
            'third_party_sale.required' => 'Bán cho bên thứ 3 là bắt buộc.',
        ]);
        // dd($request->all());
        try {
            DB::beginTransaction();
            $validated['delivery_month'] = Carbon::createFromFormat('m/Y', $validated['delivery_month'])->format('Y-m');
            $validated['container_closing_date'] = Carbon::createFromFormat('d/m/Y', $validated['container_closing_date'])->format('Y-m-d');
            $validated['delivery_date'] = Carbon::createFromFormat('d/m/Y', $validated['delivery_date'])->format('Y-m-d');
            $contract = Contract::create($validated);

            if ($request->has('orders')) {
                foreach ($request->orders as $orderData) {
                    if (!isset($orderData['code']) || empty($orderData['code'])) {
                        continue;
                    }
                    $existingOrder = OrderExport::where('code', $orderData['code'])
                        ->where('contract_id', $contract->id)
                        ->first();

                    if ($existingOrder) {
                        return redirect()->back()->withErrors(['orders' => 'Mã lệnh xuất hàng "' . $orderData['code'] . '" đã tồn tại trong hợp đồng.'])->withInput();
                    }
                    $orderExport = OrderExport::create([
                        'code' => $orderData['code'],
                        'contract_id' => $contract->id
                    ]);
                    if (!empty($orderData['batches']) && is_array($orderData['batches'])) {
                        foreach ($orderData['batches'] as $batchId) {
                            Batch::where('id', $batchId)->update(['order_export_id' => $orderExport->id]);
                        }
                    }
                }
            }


            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Tạo mới',
                'model_type' => 'Hợp Đồng',
                'details' => "Tạo hợp đồng mới với mã hợp đồng: {$contract->contract_code}",
            ]);

            DB::commit();
            return redirect()->route('cont')->with('message', 'Hợp đồng đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollback();

            Log::error("Lỗi khi tạo hợp đồng: " . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra khi tạo hợp đồng: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $contract = Contract::findOrFail($id);

        $batches = Batch::where('status', '=', 1)
            // ->whereDoesntHave('orderExport')
            ->get();
        $contractTypes = ContractType::all();
        $customers = Customer::all();


        return view('contracts.edit', compact('contract', 'contractTypes', 'customers', 'batches'));
    }
    // public function updateContract(Request $request, $id)
    // {
    //     $validatedData = $request->validate([
    //         'contract_code' => 'required|string|max:255',
    //         'contract_type_ids' => 'required|exists:contract_types,id',
    //         'customer_id' => 'required|exists:customers,id',
    //         'original_contract_number' => 'required|string|max:255',
    //         'contract_days' => 'required|numeric|min:0',
    //         'delivery_month' => 'required|date_format:m/Y',
    //         'quantity' => 'required|numeric|min:0',
    //         'product_type_name' => 'required|string|max:255',
    //         'container_closing_date' => 'required|date_format:d/m/Y',
    //         'delivery_date' => 'required|date_format:d/m/Y|after_or_equal:container_closing_date',
    //         'packaging_type' => 'required|string|max:255',
    //         'market' => 'required|string|max:255',
    //         'production_or_trade_unit' => 'required|string|max:255',
    //         'third_party_sale' => 'required|string',
    //         'orders.*.code' => 'required|string|max:255',
    //         'orders.*.batches' => 'nullable|array',
    //         'orders.*.batches.*' => 'exists:batches,id',
    //     ], [
    //         'contract_type_ids.required' => 'Loại hợp đồng là bắt buộc.',
    //         'customer_id.required' => 'Khách hàng là bắt buộc.',
    //         'original_contract_number.required' => 'Số hợp đồng gốc là bắt buộc.',
    //         'delivery_month.required' => 'Tháng giao hàng là bắt buộc.',
    //         'quantity.required' => 'Khối lượng là bắt buộc.',
    //         'quantity.numeric' => 'Khối lượng phải là một số.',
    //         'contract_days.required' => 'Số ngày hợp đồng là bắt buộc.',
    //         'contract_days.numeric' => 'Số ngày hợp đồng phải là một số.',
    //         'product_type_name.required' => 'Tên chủng loại sản phẩm là bắt buộc.',
    //         'delivery_date.required' => 'Ngày giao hàng là bắt buộc.',
    //         'delivery_date.after_or_equal' => 'Ngày giao hàng phải bằng hoặc sau ngày đóng container.',
    //         'packaging_type.required' => 'Dạng đóng gói là bắt buộc.',
    //         'container_closing_date.required' => 'Ngày đóng container là bắt buộc.',
    //         'market.required' => 'Thị trường là bắt buộc.',
    //         'production_or_trade_unit.required' => 'Đơn vị sản xuất thương mại là bắt buộc.',
    //         'third_party_sale.required' => 'Bán cho bên thứ 3 là bắt buộc.',
    //         'orders.*.code.required' => 'Mã lệnh xuất hàng là bắt buộc.',
    //         'orders.*.batches.*.exists' => 'Lô hàng không hợp lệ.',
    //     ]);

    //     $contract = Contract::findOrFail($id);
    //     $checkContractCode = Contract::where('contract_code', $request->contract_code)
    //         ->where('id', '!=', $id)
    //         ->exists();

    //     if ($checkContractCode) {
    //         return redirect()->back()->withErrors(['contract_code' => 'Mã hợp đồng đã tồn tại, vui lòng nhập mã hợp đồng khác.']);
    //     }

    //     try {
    //         $validatedData['delivery_month'] = Carbon::createFromFormat('m/Y', $validatedData['delivery_month'])->format('Y-m');
    //         $validatedData['container_closing_date'] = Carbon::createFromFormat('d/m/Y', $validatedData['container_closing_date'])->format('Y-m-d');
    //         $validatedData['delivery_date'] = Carbon::createFromFormat('d/m/Y', $validatedData['delivery_date'])->format('Y-m-d');

    //         $contract->update($validatedData);
    //         $contract->contractType()->sync($request->contract_type_ids);

    //         if ($request->has('orders')) {
    //             foreach ($request->orders as $orderData) {
    //                 if (!isset($orderData['code']) || empty($orderData['code'])) {
    //                     continue;
    //                 }

    //                 // Kiểm tra mã lệnh xuất hàng đã tồn tại trong hợp đồng khác
    //                 $existingOrder = OrderExport::where('code', $orderData['code'])
    //                     ->where('contract_id', '!=', $contract->id)
    //                     ->first();

    //                 if ($existingOrder) {
    //                     return redirect()->back()->withErrors([
    //                         'error' => "Mã lệnh xuất '{$orderData['code']}' đã tồn tại trong hợp đồng khác."
    //                     ])->withInput();
    //                 }

    //                 // Tạo hoặc cập nhật orderExport
    //                 $orderExport = OrderExport::updateOrCreate(
    //                     ['code' => $orderData['code'], 'contract_id' => $contract->id],
    //                     ['contract_id' => $contract->id]
    //                 );

    //                 // Đồng bộ hóa batches với orderExport
    //                 $batchIds = !empty($orderData['batches']) && is_array($orderData['batches']) ? $orderData['batches'] : [];
    //                 Batch::whereIn('id', $batchIds)->update(['order_export_id' => $orderExport->id]);
    //                 // Đặt lại order_export_id cho các batch không còn được chọn
    //                 Batch::where('order_export_id', $orderExport->id)
    //                     ->whereNotIn('id', $batchIds)
    //                     ->update(['order_export_id' => null]);
    //             }
    //         }

    //         return redirect()->route('cont')->with('message', 'Hợp đồng đã được cập nhật thành công!');
    //     } catch (\Exception $e) {
    //         return redirect()->back()
    //             ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
    //             ->withInput();
    //     }
    // }
    public function updateContract(Request $request, $id)
    {
        $validatedData = $request->validate([
            // 'code' => 'required|unique:order_exports,code,' . $request->id,
            'contract_code' => 'required|string|max:255',
            'contract_type_id' => 'required|exists:contract_types,id',
            'customer_id' => 'required|exists:customers,id',
            'original_contract_number' => 'required|string|max:255',
            'contract_days' => 'required|numeric|min:0',
            'delivery_month' => 'required|date_format:m/Y',
            'quantity' => 'required|numeric|min:0',
            'product_type_name' => 'required|string|max:255',
            'container_closing_date' => 'required|date_format:d/m/Y',
            'delivery_date' => 'required|date_format:d/m/Y|after_or_equal:container_closing_date',
            'packaging_type' => 'required|string|max:255',
            'market' => 'required|string|max:255',
            'production_or_trade_unit' => 'required|string|max:255',
            'third_party_sale' => 'required|string',
            // 'orders.*.code' => 'required|string|max:255', // Validate order codes
            'orders.*.batches' => 'nullable|array', // Validate batches (optional)
            'orders.*.batches.*' => 'exists:batches,id',
        ], [
            // 'code.unique' => 'Mã Lệnh đã tồn tại.',
            'contract_type_id.required' => 'Loại hợp đồng là bắt buộc.',
            'customer_id.required' => 'Khách hàng là bắt buộc.',
            'original_contract_number.required' => 'Số hợp đồng gốc là bắt buộc.',
            'delivery_month.required' => 'Tháng giao hàng là bắt buộc.',
            'quantity.required' => 'Khối lượng là bắt buộc.',
            'quantity.numeric' => 'Khối lượng phải là một số.',
            'contract_days.required' => 'Số ngày hợp đồng là bắt buộc.',
            'contract_days.numeric' => 'Số ngày hợp đồng phải là một số.',
            'product_type_name.required' => 'Tên chủng loại sản phẩm là bắt buộc.',
            'delivery_date.required' => 'Ngày giao hàng là bắt buộc.',
            'delivery_date.after_or_equal' => 'Ngày giao hàng phải bằng hoặc sau ngày đóng container.',
            'packaging_type.required' => 'Dạng đóng gói là bắt buộc.',
            'container_closing_date.required' => 'Ngày đóng container là bắt buộc.',
            'market.required' => 'Thị trường là bắt buộc.',
            'production_or_trade_unit.required' => 'Đơn vị sản xuất thương mại là bắt buộc.',
            'third_party_sale.required' => 'Bán cho bên thứ 3 là bắt buộc.',
            // 'orders.*.code.required' => 'Mã lệnh xuất hàng là bắt buộc.',
            'orders.*.batches.*.exists' => 'Lô hàng không hợp lệ.',
        ]);

        $contract = Contract::findOrFail($id);
        $checkContractCode = Contract::where('contract_code', $request->contract_code)
            ->where('id', '!=', $id)
            ->exists();

        if ($checkContractCode) {
            return redirect()->back()->withErrors(['contract_code' => 'Mã hợp đồng đã tồn tại, vui lòng nhập mã hợp đồng khác.']);
        }

        try {
            DB::beginTransaction();
            $validatedData['delivery_month'] = Carbon::createFromFormat('m/Y', $validatedData['delivery_month'])->format('Y-m');
            $validatedData['container_closing_date'] = Carbon::createFromFormat('d/m/Y', $validatedData['container_closing_date'])->format('Y-m-d');
            $validatedData['delivery_date'] = Carbon::createFromFormat('d/m/Y', $validatedData['delivery_date'])->format('Y-m-d');

            $oldContractData = $contract->getOriginal();
            $contract->update($validatedData);
            $newContractData = $contract->getAttributes();

            $details = '';

            if ($oldContractData['contract_code'] !== $newContractData['contract_code']) {
                $details .= "Mã hợp đồng: {$oldContractData['contract_code']} ➝ {$newContractData['contract_code']}, ";
            }
            if ($oldContractData['contract_type_id'] !== $newContractData['contract_type_id']) {
                $oldType = ContractType::find($oldContractData['contract_type_id']);
                $newType = ContractType::find($newContractData['contract_type_id']);

                $oldTypeName = $oldType ? $oldType->contract_type_name : 'Không xác định';
                $newTypeName = $newType ? $newType->contract_type_name : 'Không xác định';

                $details .= "Loại hợp đồng: {$oldTypeName} ➝ {$newTypeName}, ";
            }

            if ($oldContractData['quantity'] !== $newContractData['quantity']) {
                $details .= "Khối lượng: {$oldContractData['quantity']} ➝ {$newContractData['quantity']}, ";
            }
            if ($oldContractData['delivery_month'] !== $newContractData['delivery_month']) {
                $details .= "Tháng giao hàng: {$oldContractData['delivery_month']} ➝ {$newContractData['delivery_month']}, ";
            }
            if ($oldContractData['container_closing_date'] !== $newContractData['container_closing_date']) {
                $oldDate = Carbon::parse($oldContractData['container_closing_date'])->format('d/m/Y');
                $newDate = Carbon::parse($newContractData['container_closing_date'])->format('d/m/Y');
                $details .= "Ngày đóng container: {$oldDate} ➝ {$newDate}, ";
            }
            if ($oldContractData['delivery_date'] !== $newContractData['delivery_date']) {
                $oldDate = Carbon::parse($oldContractData['delivery_date'])->format('d/m/Y');
                $newDate = Carbon::parse($newContractData['delivery_date'])->format('d/m/Y');
                $details .= "Ngày giao hàng: {$oldDate} ➝ {$newDate}, ";
            }

            $details = rtrim($details, ', ');


            // if ($request->has('orders')) {
            //     foreach ($request->orders as $orderData) {
            //         if (!isset($orderData['code']) || empty($orderData['code'])) {
            //             continue;
            //         }
            //         $existingOrder = OrderExport::where('code', $orderData['code'])
            //             ->where('contract_id', '!=', $contract->id)
            //             ->first();

            //         if ($existingOrder) {
            //             return redirect()->back()->withErrors([
            //                 'error' => "Mã lệnh xuất '{$orderData['code']}' đã tồn tại trong hợp đồng khác."
            //             ])->withInput();
            //         }
            //         $orderExport = OrderExport::updateOrCreate(
            //             ['code' => $orderData['code'], 'contract_id' => $contract->id],
            //             ['contract_id' => $contract->id]
            //         );

            //         if (!empty($orderData['batches']) && is_array($orderData['batches'])) {
            //             foreach ($orderData['batches'] as $batchId) {
            //                 Batch::where('id', $batchId)->update(['order_export_id' => $orderExport->id]);
            //             }
            //         }
            //     }
            // }
            if ($request->has('orders')) {
                foreach ($request->orders as $orderData) {
                    if (!isset($orderData['code']) || empty($orderData['code'])) {
                        continue;
                    }

                    // Kiểm tra mã lệnh xuất hàng đã tồn tại trong hợp đồng khác
                    $existingOrder = OrderExport::where('code', $orderData['code'])
                        ->where('contract_id', '!=', $contract->id)
                        ->first();

                    if ($existingOrder) {
                        return redirect()->back()->withErrors([
                            'error' => "Mã lệnh xuất '{$orderData['code']}' đã tồn tại trong hợp đồng khác."
                        ])->withInput();
                    }

                    // Tạo hoặc cập nhật orderExport
                    $orderExport = OrderExport::updateOrCreate(
                        ['code' => $orderData['code'], 'contract_id' => $contract->id],
                        ['contract_id' => $contract->id]
                    );

                    // Đồng bộ hóa batches với orderExport
                    $batchIds = !empty($orderData['batches']) && is_array($orderData['batches']) ? $orderData['batches'] : [];
                    Batch::whereIn('id', $batchIds)->update(['order_export_id' => $orderExport->id]);
                    // Đặt lại order_export_id cho các batch không còn được chọn
                    Batch::where('order_export_id', $orderExport->id)
                        ->whereNotIn('id', $batchIds)
                        ->update(['order_export_id' => null]);
                }
            }
            if (!empty($details)) {
                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Cập Nhật',
                    'model_type' => 'Hợp Đồng',
                    'model_id' => $contract->id,
                    'details' => $details, // Log the specific changes
                ]);
            }
            // Commit các thay đổi vào cơ sở dữ liệu
            DB::commit();

            return redirect()->route('cont')->with('message', 'Hợp đồng đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                ->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $contract = Contract::findOrFail($id);

        $contract->delete();

        return redirect()->route('cont')->with('success', 'Hợp đồng đã được xóa!');
    }

    public function delete_order(Request $request)
    {

        $validated = $request->validate([
            'code' => 'nullable',
        ]);

        $order = OrderExport::where('code', $validated['code'])->first();

        // dd($request->batches);

        if ($request->batches) {
            foreach ($request->batches as $item) {
                $batch = Batch::find($item);
                if ($batch) {
                    $batch->order_export_id = null;
                    $batch->save();
                }
            }
        }

        if ($order) {

            $order->delete();

            return response()->json(['success' => true]);
        } else {

            return response()->json(['success' => false, 'message' => 'Order not found']);
        }
    }
    public function deleteMultiple1(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $contracts = Contract::whereIn('id', $request->ids)->get();
        $deletedContractCodes = $contracts->pluck('contract_code')->toArray();
        $contractCodesString = implode(', ', $deletedContractCodes);
        // $contractsWithBatchCodes = $contracts->filter(function ($contract) {
        //     return $contract->batchCodes()->exists();
        // });

        // if ($contractsWithBatchCodes->isNotEmpty()) {
        //     return response()->json([
        //         'message' => 'Không thể xóa hợp đồng vì đã có mã lô hàng.',
        //         'contracts' => $contractsWithBatchCodes->pluck('contract_code')
        //     ], 400);
        // }
        Contract::whereIn('id', $request->ids)->delete();

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'Xóa',
            'model_type' => 'Hợp Đồng',
            'details' => "Xóa các hợp đồng với mã: {$contractCodesString}",
        ]);

        return response()->json([
            'message' => 'Xóa thành công.',
            'deleted_ids' => $request->ids
        ]);
    }


    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $contracts = Contract::whereIn('id', $request->ids)->get();

        $contractsWithDependencies = $contracts->filter(function ($contract) {
            foreach ($contract->orderExports as $orderExport) {
                if ($orderExport->batches()->exists()) {
                    return true;
                }
            }
            return false;
        });

        if ($contractsWithDependencies->isNotEmpty()) {
            return response()->json([
                'message' => 'Không thể xóa hợp đồng vì đã có mã lô trong lệnh xuất hàng.',
                'contracts' => $contractsWithDependencies->pluck('contract_code')
            ], 400);
        }

        try {
            $deletedContractCodes = $contracts->pluck('contract_code')->toArray();
            Contract::whereIn('id', $request->ids)->delete();

            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Xóa',
                'model_type' => 'Hợp Đồng',
                'details' => "Xóa các hợp đồng với mã: " . implode(', ', $deletedContractCodes),
            ]);

            return response()->json([
                'message' => 'Xóa thành công.',
                'deleted_ids' => $request->ids
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể xóa hợp đồng vì đã có mã lô trong lệnh xuất hàng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function getList(Request $request)
    // {
    //     $contracts = Contract::all();

    //     if (true) {
    //         return response()->json([
    //             'success' => true,
    //             'data' => $contracts
    //         ]);
    //     }
    //     return response()->json([
    //         'success' => false,
    //         'data' => 'ko biết lỗi gì, nào biết ghi vô sau'
    //     ]);
    // }
    // public function getList(Request $request)
    // {
    //     $contracts = Contract::with('contractType', 'customer', 'orderExports')->get();

    //     if ($contracts->isNotEmpty()) {
    //         return response()->json([
    //             'success' => true,
    //             'data' => $contracts->map(function ($contract) {
    //                 return [
    //                     'id' => $contract->id,
    //                     'contract_code' => $contract->contract_code,
    //                     'contract_type_name' => $contract->contractType ? $contract->contractType->contract_type_name : null,
    //                     'customer_id' => $contract->customer ? $contract->customer->company_name : null,
    //                     // 'order_export_id' => $contract->order_export_id ? $contract->order_export_id : null,
    //                     // 'order_export_id' => $contract->orderExport ? $contract->orderExport->code : null,
    //                     'original_contract_number' => $contract->original_contract_number,
    //                     'delivery_month' => $contract->delivery_month,
    //                     'quantity' => $contract->quantity,
    //                     'contract_days' => $contract->contract_days,
    //                     'product_type_name' => $contract->product_type_name,
    //                     'delivery_date' => $contract->delivery_date,
    //                     'packaging_type' => $contract->packaging_type,
    //                     'container_closing_date' => $contract->container_closing_date,
    //                     'market' => $contract->market,
    //                     'production_or_trade_unit' => $contract->production_or_trade_unit,
    //                     'third_party_sale' => $contract->third_party_sale,
    //                     'created_at' => $contract->created_at,
    //                     'updated_at' => $contract->updated_at,
    //                 ];
    //             }),
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'data' => 'Không có hợp đồng nào.',
    //     ]);
    // }
    public function getList1(Request $request)
    {
        $authUser = Auth::user();
        $userId = $authUser ? $authUser->id : $request->input('user_id');
        $customerId = $request->input('customer_id');
        $contractsQuery = Contract::with('contractType', 'customer', 'orderExports');
        if (!empty($customerId)) {
            $contractsQuery->where('customer_id', $customerId);
        }

        if (!$authUser || !$authUser->hasRole('Admin')) {
            if (!empty($userId)) {
                $contractsQuery->whereHas('customer', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu user_id hoặc chưa đăng nhập.'
                ], 400);
            }
        }

        $contracts = $contractsQuery->get();

        if ($contracts->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'data' => $contracts->map(function ($contract) {
                    return [
                        'id' => $contract->id,
                        'contract_code' => $contract->contract_code,
                        'contract_type_name' => optional($contract->contractType)->contract_type_name,
                        'customer_name' => optional($contract->customer)->company_name,
                        'original_contract_number' => $contract->original_contract_number,
                        'delivery_month' => $contract->delivery_month,
                        'quantity' => $contract->quantity,
                        'contract_days' => $contract->contract_days,
                        'product_type_name' => $contract->product_type_name,
                        'delivery_date' => $contract->delivery_date,
                        'packaging_type' => $contract->packaging_type,
                        'container_closing_date' => $contract->container_closing_date,
                        'market' => $contract->market,
                        'production_or_trade_unit' => $contract->production_or_trade_unit,
                        'third_party_sale' => $contract->third_party_sale,
                        'created_at' => $contract->created_at,
                        'updated_at' => $contract->updated_at,
                        // 'order_exports' => $contract->orderExports->map(function ($orderExport) {
                        //     return [
                        //         'order_code' => $orderExport->code,
                        //         'batches' => $orderExport->batches->map(function ($batch) use ($orderExport) {
                        //             // Trả về link tải DDS cho từng batch
                        //             $batch->dds_link = $this->getDdsLinkForBatch($orderExport->code, $batch->id);
                        //             return $batch;
                        //         })
                        //     ];
                        // }),
                    ];
                }),
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => [],
            'error' => 'Không có hợp đồng nào.',
        ]);
    }

    public function getList(Request $request)
    {
        $authUser = Auth::user();
        $userId = $authUser ? $authUser->id : $request->input('user_id');
        $customerId = $request->input('customer_id');
        $contractsQuery = Contract::with('contractType', 'customer', 'orderExports');

        if (!empty($customerId)) {
            $contractsQuery->where('customer_id', $customerId);
        }

        if (!$authUser || !$authUser->hasRole('Admin')) {
            if (!empty($userId)) {
                $contractsQuery->whereHas('customer', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu user_id hoặc chưa đăng nhập.'
                ], 400);
            }
        }

        $contracts = $contractsQuery->get();

        if ($contracts->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'data' => $contracts->map(function ($contract) {
                    return [
                        'id' => $contract->id,
                        'contract_code' => $contract->contract_code,
                        'contract_type_name' => [$contract->contractType->first()->contract_type_name ?? null],

                        // 'contract_type_name1' => optional($contract->contractType)->contract_type_name,
                        'customer_name' => optional($contract->customer)->company_name,
                        'original_contract_number' => $contract->original_contract_number,
                        'delivery_month' => $contract->delivery_month,
                        'quantity' => $contract->quantity,
                        'contract_days' => $contract->contract_days,
                        'product_type_name' => $contract->product_type_name,
                        'delivery_date' => $contract->delivery_date,
                        'packaging_type' => $contract->packaging_type,
                        'container_closing_date' => $contract->container_closing_date,
                        'market' => $contract->market,
                        'production_or_trade_unit' => $contract->production_or_trade_unit,
                        'third_party_sale' => $contract->third_party_sale,
                        'created_at' => $contract->created_at,
                        'updated_at' => $contract->updated_at,
                        'order_exports' => $contract->orderExports->map(function ($orderExport) {
                            return [
                                'order_code' => $orderExport->code,
                                'batches' => $orderExport->batches->map(function ($batch) use ($orderExport) {
                                    $batch->dds_link = route('export.dds.order', ['code' => $orderExport->code]);
                                    $batch->dds2_link = route('export.dds2.order', ['code' => $orderExport->code]);
                                    $batch->dds3_link = route('export.dds3.order', ['code' => $orderExport->code]);
                                    return $batch;
                                })
                            ];
                        }),
                    ];
                }),
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => [],
            'error' => 'Không có hợp đồng nào.',
        ]);
    }

    public function getDetail($id)
    {
        try {
            $contract = Contract::with([
                'orderExports.batches',
                'orderExports'
            ])->findOrFail($id);

            $contractDetails = [
                'id' => $contract->id,
                'customer_name' => $contract->customer->company_name ?? 'Trống',
                'contract_code' => $contract->contract_code,
                // 'contract_type_name' => $contract->contractType->contract_type_name ?? 'Trống',
                'contract_type_name' => [$contract->contractType->first()->contract_type_name ?? null],

                'contract_day' => $contract->contract_days,
                'delivery_date' => $contract->delivery_date,
                'customer_id' => $contract->customer_id,
                'product_type_name' => $contract->product_type_name,
                'quantity' => $contract->quantity,
                'market' => $contract->market,
                'packaging_type' => $contract->packaging_type,
                'production_or_trade_unit' => $contract->production_or_trade_unit,
                'third_party_sale' => $contract->third_party_sale,
                'original_contract_number' => $contract->original_contract_number,
                'delivery_month' => $contract->delivery_month,
                'container_closing_date' => $contract->container_closing_date,
                // 'created_at' => $contract->created_at,
                // 'updated_at' => $contract->updated_at,
            ];

            // Prepare order export data with associated batches
            $orderExports = $contract->orderExports->map(function ($orderExport) {
                return [
                    'id' => $orderExport->id,
                    'code' => $orderExport->code,
                    'batches' => $orderExport->batches->map(function ($batch) {
                        return [
                            'id' => $batch->id,
                            'batch_code' => $batch->batch_code,
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => ['Chi tiết hợp đồng.'],
                'data' => [
                    'contract_details' => $contractDetails,
                    'order_export_list' => $orderExports,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hợp đồng hoặc có lỗi khác: ' . $e->getMessage(),
            ]);
        }
    }


    public function getDetailOrder(string $id)
    {
        try {
            $orderDetail = OrderExport::with('contract', 'batches')->findOrFail($id);

            $contract = $orderDetail->contract;
            $contract->quantity = $contract->quantity . ' tons';
            // $contract->shipping_order = $orderDetail->code;

            $order = $orderDetail;
            $order->amount = null;
            $order->zip_name = null;
            $order->zip_file = null;
            $order->url_download = null;
            $order->url_download_v2 = null;
            $order->url_download_v3 = null;
            $order->url_download_pdf = null;
            $order->url_download_pdf_detail = null;

            $order->url_download = route('export.dds.order', ['code' => $orderDetail->code]);
            $order->url_download_v2 = route('export.dds2.order', ['code' => $orderDetail->code]);
            $order->url_download_v3 = route('export.dds3.order', ['code' => $orderDetail->code]);
            return response()->json([
                'success' => true,
                'message' => ['Chi tiết lệnh xuất hàng.'],
                'data' => [
                    // 'contract' => $contract,
                    'order' => $order,
                    'batches' => $orderDetail->batches->map(function ($batch) use ($orderDetail) {
                        $batch->batch_weight = $batch->batch_weight . ' kg';
                        $batch->banh_weight = $batch->banh_weight . ' kg';
                        $batch->dds_link = route('export.dds.order', ['code' => $orderDetail->code]);
                        $batch->dds2_link = route('export.dds2.order', ['code' => $orderDetail->code]);
                        $batch->dds3_link = route('export.dds3.order', ['code' => $orderDetail->code]);
                        return [
                            'info' => $batch,
                            'ingredients' => $batch->ingredients->map(function ($ingredient) {
                                return [
                                    'id' => $ingredient->id,
                                    'plantingAreas' => $ingredient->plantingAreas,
                                ];
                            }),
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hợp đồng hoặc có lỗi khác: ' . $e->getMessage(),
            ]);
        }
    }

    public function getContractType()
    {
        $types = ContractType::all();

        if ($types) {
            return response()->json([
                'success' => true,
                'data' => $types,
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'không tìm thấy'
        ]);
    }

    // public function export(Request $request)
    // {
    //     $year = $request->input('year', date('Y'));

    //     return Excel::download(new ContractsExport($year), "HopDong_{$year}.xlsx");
    // }

    public function export(Request $request)
    {
        $filters = $request->only(['year', 'month', 'day']);

        return Excel::download(new ContractsExport($filters), "HopDong.xlsx");
    }

    public function ddsExportExcel($code)
    {
        $order = OrderExport::where('code', $code)->with('batches')->firstOrFail();

        $export = new DDSContractExport($order);
        return $export->download();
    }
    public function dds2ExportExcel($code)
    {
        $order = OrderExport::where('code', $code)->with('batches')->firstOrFail();

        $export = new DDS2ContractExport($order);
        return $export->download();
    }
    public function dds3ExportExcel($code)
    {
        $order = OrderExport::where('code', $code)->with('batches')->firstOrFail();

        $export = new DDS3ContractExport($order);
        return $export->download();
    }
    public function getBatchDDS($order_code, $batch_id)
    {
        $order = OrderExport::where('code', $order_code)->firstOrFail();

        $batch = $order->batches()->findOrFail($batch_id);
        $filePath = storage_path("app/exports/{$order_code}_batch_{$batch_id}_dds.xlsx");

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'DDS file for this batch does not exist.'
            ], 404);
        }

        return response()->download($filePath);
    }

    public function index_customer()
    {
        $customers = Customer::all();

        if ($customers->isNotEmpty()) {
            $responseData = $customers->map(function ($c) {
                $customerTypes = $c->customerTypes;

                $customerTypeData = $customerTypes->map(function ($type) use ($c) {
                    return $type->name;
                });

                $customerTypesString = $customerTypeData->implode(', ');

                return [
                    'id' => $c->id,
                    'company_name' => $c->company_name,
                    'customer_types' => $customerTypesString,
                    'phone' => $c->phone,
                    'email' => $c->email,
                    'address' => $c->address,
                    'description' => $c->description,
                ];
            });

            return response()->json([
                'data' => $responseData,
                'status' => 200,
                'success' => true,
                'message' => 'Danh sách khách hàng.',
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    // public function index_customer()
    // {
    //     // Lấy tất cả các khách hàng
    //     $customers = Customer::all();

    //     if ($customers->isNotEmpty()) {
    //         $responseData = $customers->map(function ($c) {
    //             $customerTypes = $c->customerTypes;

    //             $customerTypeData = $customerTypes->map(function ($type) use ($c) {
    //                 $customerTypeDetail = DB::table('customer_customer_type')
    //                     ->where('customer_id', $c->id)
    //                     ->where('customer_type_id', $type->id)
    //                     ->first(); 
    //                 return [
    //                     'type_name' => $type->name,
    //                 ];
    //             });

    //             return [
    //                 'id' => $c->id,
    //                 'company_name' => $c->company_name,
    //                 'customer_types' => $customerTypeData, 
    //                 'phone' => $c->phone,
    //                 'email' => $c->email,
    //                 'address' => $c->address,
    //                 'description' => $c->description,
    //             ];
    //         });

    //         return response()->json([
    //             'data' => $responseData,
    //             'status' => 200,
    //             'success' => true,
    //             'message' => 'Danh sách khách hàng.',
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'data' => 'Không tìm thấy dữ liệu.',
    //     ]);
    // }
}
