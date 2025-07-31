<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContractType;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;


class ContractTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        return view('contract-types.index');
    }

    public function getData(Request $request)
    {
        $types = ContractType::query();

        return DataTables::of($types)
            ->addColumn('check', function ($row) {
                return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
            })
            ->editColumn('contract_type_type', function ($row) {
                return '<a href="' . route('contract-types.edit', $row->id) . '">' . $row->contract_type_type . '</a>';
            })
            ->addColumn('actions', function ($row) {
                $action = '
                    <div class="d-flex gap-1">
                        <a href="' . route('contract-types.edit', $row->id) . '" class="btn btn-sm btn-primary">
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
                                    Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->farm_name ?? 'N/A') . '</span>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <a href="' . route('contract-types.destroy', $row->id) . '" class="btn btn-primary">Xóa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
                return $action;
            })
            // ->addColumn('actions', function ($row) {
            //     return '
            //         <a href="' . route('contract-types.edit', $row->id) . '" class="edit-btn"><i class="fa-solid fa-pencil"></i></a>
            //         <form action="' . route('contract-types.destroy', $row->id) . '" method="POST" style="display:inline;">
            //             ' . csrf_field() . '
            //             ' . method_field('DELETE') . '
            //             <button type="submit" class="delete-btn text-danger" style="border:none;background: none;" onclick="return confirm(\'Bạn có chắc chắn muốn xóa?\')"><i class="fa-sharp fa-regular fa-trash"></i></button>
            //         </form>
            //     ';
            // })
            ->rawColumns(['check', 'actions', 'contract_type_type'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contract-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'contract_type_code' => 'required|unique:contract_types,contract_type_code',
                'contract_type_name' => 'required',
                'contract_type_type' => 'required',
            ], [
                'contract_type_code.required' => 'Loại hợp đồng không được để trống',
                'contract_type_code.unique' => 'Loại hợp đồng đã tồn tại',
                'contract_type_name.required' => 'Mã loại hợp đồng không được để trống',
                'contract_type_type.required' => 'Tên loại hợp đồng không để trống'

            ]);

            ContractType::create([
                'contract_type_code' => $request->contract_type_code,
                'contract_type_name' => $request->contract_type_name,
                'contract_type_type' => $request->contract_type_type,
            ]);
            return redirect()->route('contract-types.index')->with('message', 'Loại hợp đồng đã được tạo!');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
            // return redirect()->back()->with('error', 'Tạo loại hợp đồng thất bại!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $contractType = ContractType::findOrFail($id);
        return view('contract-types.edit', compact('contractType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try {
            $validatedData = $request->validate([
                'contract_type_code' => 'required|unique:contract_types,contract_type_code,' . $id,
                'contract_type_name' => 'required',
                'contract_type_type' => 'required',
            ], [
                'contract_type_code.required' => 'Loại hợp đồng không được để trống',
                'contract_type_code.unique' => 'Loại hợp đồng đã tồn tại',
                'contract_type_name.required' => 'Mã loại hợp đồng không được để trống',
                'contract_type_type.required' => 'Tên loại hợp đồng không để trống'

            ]);

            $contractType = ContractType::findOrFail($id);
            $contractType->update($validatedData);

            return redirect()->route('contract-types.index')->with('message', 'Loại hợp đồng đã được cập nhật!');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
            // return redirect()->back()->with('error', 'Vui lòng kiểm tra lại thông tin!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $contractType = ContractType::findOrFail($id);

        $contractType->delete();

        return redirect()->route('contract-types.index')->with('success', 'Loại hợp đồng đã được xóa!');
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        ContractType::whereIn('id', $request->ids)->delete();
        return response()->json([
            'message' => 'Xóa thành công.',
            'deleted_ids' => $request->ids
        ]);
    }
}
