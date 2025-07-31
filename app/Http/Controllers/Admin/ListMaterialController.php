<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Category;
use App\Models\Product;
use App\Models\UnitOfMeasure;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class ListMaterialController extends Controller
{
    public function index(Request $request)
    {
        $units = UnitOfMeasure::all();
        $warehouses = WareHouse::all();
        $categories = Category::all();
        $all_products = Product::with(['unit', 'category'])->orderBy('id', 'desc')->get();

        if ($request->ajax()) {
            return DataTables::of($all_products)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->editColumn('name', function ($row) {
                    return e($row->name);
                })
                ->addColumn('category', function ($row) {
                    return $row->category->name ?? '<span class="text-muted">Không có</span>';
                })
                ->addColumn('unit', function ($row) {
                    return $row->unit->name ?? '<span class="text-muted">Không có</span>';
                })
                ->editColumn('status', function ($row) {
                    $isActive = (bool)$row->status;
                    $statusClass = $isActive ? 'success' : 'danger';
                    $statusText = $isActive ? 'Hoạt động' : 'Không hoạt động';

                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-products/' . $row->id . '" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác Nhận Xóa</h5>
                                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->name ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/products/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'status', 'action', 'category', 'unit'])
                ->make(true);
        }
        return view('products.all_products', compact('units', 'warehouses', 'categories'));
    }
    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'categoryID' => 'required',
            'unitID' => 'required',
            'type' => 'nullable',
            'status' => 'nullable',
        ]);
        $existingCode = Product::where('name', $request->name)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Tên vật tư này đã tồn tại!']);
        }

        Product::create([
            'name' => $request->name,
            'categoryID' => $request->categoryID,
            'unitID' => $request->unitID,
            'type' => $request->type,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Products',
            'details' => "Đã tạo danh mục vật tư: " . $request->name,
        ]);
        session()->flash('message', 'Tạo vật tư thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $warehouses = WareHouse::all();
        $categories = Category::all();
        $units = UnitOfMeasure::all();
        $products = Product::find($id);
        return view('products.edit_products', compact('units', 'products', 'warehouses', 'categories'));
    }
    public function update(Request $request, $id)
    {
        $existingProduct = Product::where('name', $request->name)->where('id', '!=', $id)->first();


        $existingProduct = Product::where(function ($query) use ($request, $id) {
            $query->where('name', $request->name);
        })->where('id', '!=', $id)->first();
        if ($existingProduct) {
            if ($existingProduct->name === $request->name) {
                return redirect()->back()->with(['error' => 'Đơn vị này đã tồn tại!']);
            }
        }
        $products = Product::find($id);
        if (!$products) {
            return redirect()->back()->with('error', 'Đơn vị không tồn tại');
        }
        $request->validate([
            'name' => 'required',
            'categoryID' => 'required',
            'unitID' => 'required',
            'type' => 'nullable',
            'status' => 'nullable',
        ]);
        $products->update([
            'name' => $request->name,
            'categoryID' => $request->categoryID,
            'unitID' => $request->unitID,
            'type' => $request->type,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Products',
            'details' => "Đã cập nhật danh mục: " . $products->name . " (ID: " . $products->id . ")",
        ]);
        return redirect()->route('products.index')->with('message', 'Cập nhật vật tư thành công');
    }
    public function destroy($id)
    {
        $products = Product::find($id);
        $products->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $products = Product::whereIn('id', $request->ids)->get();

        foreach ($products as $unit) {
            $unit->status = ($unit->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $unit->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        Product::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Xóa thành công các vật tư được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $unit = Product::find($request->id);
        if ($unit) {
            $unit->status = $unit->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $unit->save();
            return response()->json(['success' => true, 'status' => $unit->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
