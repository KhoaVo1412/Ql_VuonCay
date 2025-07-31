<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Category;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = WareHouse::all();
        $categories = Category::with('warehouse')->orderBy('id', 'desc')->get();
        // dd($all_categories);
        if ($request->ajax()) {
            return DataTables::of($categories)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->editColumn('warehouseID', function ($row) {
                    return $row->warehouse->name;
                })
                ->editColumn('parentID', function ($row) {
                    return $row->parentID;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-categories/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/categories/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'name', 'warehouseID', 'parentID', 'status', 'action'])
                ->make(true);
        }
        return view('category.all_categories', compact('categories', 'warehouses'));
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'warehouseID' => 'required',
            'parentID' => 'nullable',
            'status' => 'nullable',
        ]);
        $existingCode = Category::where('name', $request->name)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Mã danh mục vật tư này đã tồn tại!']);
        }
        Category::create([
            'name' => $request->name,
            'warehouseID' => $request->warehouseID,
            'parentID' => $request->parentID,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Category',
            'details' => "Đã tạo danh mục vật tư: " . $request->name . " với mã: " . $request->code,
        ]);
        session()->flash('message', 'Tạo danh mục vật tư thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $warehouses = WareHouse::all();
        $categories = Category::find($id);
        $allCategories = Category::where('id', '!=', $categories->id)->get();
        return view('category.edit_categories', compact('categories', 'warehouses', 'allCategories'));
    }
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        // Kiểm tra trùng tên (trừ chính nó)
        $existingName = Category::where('name', $request->name)
            ->where('id', '!=', $id)
            ->first();
        if ($existingName) {
            return redirect()->back()->with('error', 'Tên danh mục vật tư này đã tồn tại!');
        }

        $existingCode = Category::where('name', $request->name)
            ->where('id', '!=', $id)
            ->first();
        if ($existingCode) {
            return redirect()->back()->with('error', 'Mã danh mục này đã tồn tại!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'warehouseID' => 'required',
            'parentID' => 'nullable',
            'status' => 'nullable',
        ]);

        $category->update([
            'name' => $request->name,
            'code' => $request->code,
            'warehouseID' => $request->warehouse_id,
            'parentID' => $request->parentID,
            'status' => $request->status,
        ]);

        // Ghi log lịch sử
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Category',
            'details' => "Đã cập nhật danh mục: " . $category->name . " (ID: " . $category->id . ")",
        ]);

        return redirect()->route('categories.index')->with('message', 'Cập nhật danh mục thành công');
    }

    public function destroy($id)
    {
        $categories = Category::find($id);
        $categories->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $categories = Category::whereIn('id', $request->ids)->get();

        foreach ($categories as $c) {
            $c->status = ($c->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $c->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $categoriesToDelete = Category::whereIn('id', $request->ids)->get();

        Category::whereIn('id', $request->ids)->delete();

        foreach ($categoriesToDelete as $c) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Category',
                'details' => "Đã xóa danh mục vật tư: " . $c->name . " với mã: " . $c->c_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các danh mục vật tư được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $c = Category::find($request->id);
        if ($c) {
            $c->status = $c->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $c->save();
            return response()->json(['success' => true, 'status' => $c->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
