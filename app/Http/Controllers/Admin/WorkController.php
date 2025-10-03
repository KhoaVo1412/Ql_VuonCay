<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Category;
use App\Models\Garden;
use App\Models\GenTask;
use App\Models\Picking;
use App\Models\Plant;
use App\Models\Plot;
use App\Models\Product;
use App\Models\ProductPicking;
use App\Models\UnitOfMeasure;
use App\Models\User;
use App\Models\WareHouse;
use App\Models\Work;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class WorkController extends Controller
{
    public function taskStats($workerId, Request $request)
    {
        $asOf = $request->filled('as_of')
            ? Carbon::parse($request->as_of)->endOfDay()
            : null;
        $base = GenTask::where('workerID', $workerId);
        if ($asOf) {
            $base->where('created_at', '<=', $asOf);
        }
        $countWork  = (clone $base)->count();                         // tổng task
        $doneOnTime = (clone $base)->completedOnTime()->count();      // Hoàn thành
        $doneLate   = (clone $base)->completedLate()->count();        // Hoàn thành trễ
        $notDone    = (clone $base)->pending()->count();              // Đang chờ
        // (Tuỳ chọn) Tách đang chờ — đúng hạn vs quá hạn
        $pendingOnTrack = (clone $base)->pendingOnTrack()->count();
        $pendingOverdue = (clone $base)->pendingOverdue()->count();
        return response()->json([
            'success'         => true,
            'countWork'       => $countWork,
            'doneOnTime'      => $doneOnTime,
            'doneLate'        => $doneLate,
            'notDone'         => $notDone,
            'countCofirm'     => $doneOnTime,
            'countLate'       => $doneLate,
            'countUn'         => $notDone,
            'pendingOnTrack'  => $pendingOnTrack,
            'pendingOverdue'  => $pendingOverdue,
        ]);
    }

    public function index(Request $request)
    {
        $works = Work::all();
        $gardens = Garden::with('plots')->get();
        $workers = Worker::all();
        $plots = Plot::with('garden')->get();
        // dd($all_gentask);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // $all_gentask = GenTask::with(['work', 'worker'])
        //     ->when(!$user->hasRole('Admin'), function ($q) use ($user) {
        //         $q->whereHas('worker', fn($w) => $w->where('user_id', $user->id));
        //     })
        //     ->latest()
        //     ->get();

        if ($request->ajax()) {
            $all_gentask = GenTask::with([
                'work:id,workName,workType',
                'worker:id,name,user_id',
                'plot:id,plotName',
            ])
                ->when(!$user->hasRole('Admin'), function ($q) use ($user) {
                    $q->whereHas('worker', fn($w) => $w->where('user_id', $user->id));
                })
                ->when($request->filled('work_id'),  fn($q) => $q->where('workID',  $request->work_id))
                ->when($request->filled('plot_id'),  fn($q) => $q->where('plotID',  $request->plot_id))
                ->when($request->filled('priority'), fn($q) => $q->where('priority', $request->priority))
                ->when($request->filled(['start_date', 'end_date']), function ($q) use ($request) {
                    $start = \Carbon\Carbon::parse($request->start_date)->toDateString();
                    $end = \Carbon\Carbon::parse($request->end_date)->toDateString();
                    $q->whereDate('workDate', '<=', $end)
                        ->whereDate('dateEnd',  '>=', $start);
                })
                ->when(
                    $request->filled('start_date') && !$request->filled('end_date'),
                    fn($q) => $q->whereDate('dateEnd', '>=', $request->start_date)
                )
                ->when(
                    $request->filled('end_date') && !$request->filled('start_date'),
                    fn($q) => $q->whereDate('workDate', '<=', $request->end_date)
                )
                ->latest();
            return DataTables::of($all_gentask)
                ->setRowClass(function ($row) {
                    if ($row->dateEnd && $row->workStatus !== 'Hoàn thành') {
                        if (\Carbon\Carbon::parse($row->dateEnd)->lt(now())) {
                            return 'bg-warning1';
                        }
                    }
                    return '';
                })
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('code', function ($row) {
                    return $row->code ?? 'Chờ kiểm duyệt';
                })
                ->addColumn('workName', function ($row) {
                    return $row->workName ?? 'Không rõ';
                })
                ->addColumn('plotName', function ($row) {
                    return $row->plot->plotName ?? 'Không rõ';
                })
                ->addColumn('workType', function ($row) {
                    return $row->work->workType ?? 'Không rõ';
                })
                ->addColumn('priority', function ($row) {
                    return $row->priority ?? 'Không rõ';
                })
                ->editColumn('workDate', function ($row) {
                    return $row->workDate ? Carbon::parse($row->workDate)->format('d/m/Y')
                        : null;
                })
                ->editColumn('dateEnd', function ($row) {
                    return $row->dateEnd ? Carbon::parse($row->dateEnd)->format('d/m/Y')
                        : null;
                })
                ->editColumn('workerID', function ($row) {
                    return $row->worker->name ?? 'Không rõ';
                })
                ->editColumn('workStatus', function ($row) {
                    $workStatusClass = $row->workStatus == 'Hoàn thành' ? 'success' : 'danger';
                    $workStatusText = $row->workStatus == 'Hoàn thành' ? 'Hoàn thành' : 'Đang chờ';
                    return '<button class="badge bg-' . $workStatusClass . ' toggle-status" data-id="' . $row->id . '">' . $workStatusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-works/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->workType ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/works/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'priority', 'plotName', 'dateEnd', 'code', 'workerID', 'stt', 'workDate', 'workName', 'workType', 'workStatus', 'action'])
                ->make(true);
        }
        return view('works.all_works', compact('gardens', 'workers', 'works', 'plots'));
        // return view('works.sanluong', compact('gardens', 'workers', 'works', 'plots'));
    }
    public function getPlantsByPlot($plotID)
    {
        $plants = Plant::where('plotID', $plotID)->get();
        return response()->json($plants);
    }

    public function add(Request $request)
    {
        $categories = Category::all();
        $products = Product::all();
        $units = UnitOfMeasure::all();
        $users = User::all();
        $warehouses = WareHouse::where('id', 2)->get();
        $works = Work::all();
        $gardens = Garden::with('plots')->get();
        $workers = Worker::all();
        $plots = Plot::with('garden')->get();
        return view('works.add_works', compact('categories', 'products', 'units', 'users', 'warehouses', 'gardens', 'workers', 'works', 'plots'));
    }
    public function save(Request $request)
    {
        try {
            $rules = [
                'workID'     => 'required',
                'workerID'   => 'required',
                'workName'   => 'required',
                'workDate'   => 'required',
                'dateEnd'    => 'required|date|after_or_equal:workDate',
                'plotID'     => 'required',
                'priority'   => 'required',
                'description' => 'nullable',
                'plantIDs'   => 'required|array',
                'code'       => 'nullable',
                'name'       => 'nullable',
                'warehouseID' => 'nullable',
                'createName' => 'nullable',
                'createDate' => 'nullable|date',
                'desc'       => 'nullable|string',
            ];
            $messages = [
                'required'                  => ':attribute là bắt buộc.',
                'date'                      => ':attribute không đúng định dạng ngày.',
                'array'                     => ':attribute phải là mảng.',
                'plantIDs.min'              => 'Vui lòng chọn ít nhất một cây.',
                'dateEnd.after_or_equal'    => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
                'plantIDs.*.integer'        => 'ID cây không hợp lệ.',
            ];
            $attributes = [
                'workID'     => 'Mã công việc',
                'workerID'   => 'Người thực hiện',
                'workName'   => 'Tên công việc',
                'workDate'   => 'Ngày bắt đầu',
                'dateEnd'    => 'Ngày kết thúc',
                'plotID'     => 'Lô',
                'priority'   => 'Độ ưu tiên',
                'plantIDs'   => 'Danh sách cây',
                'createDate' => 'Ngày tạo phiếu',
            ];
            $work = Work::find($request->workID);
            if (!$work) {
                return redirect()->back()->withErrors(['workID' => 'Công việc không tồn tại']);
            }

            if ($work->workType === 'Khai thác') {
                $rules = array_merge($rules, [
                    'materials'            => 'nullable',
                    'materials.*.productID' => 'nullable',
                    'materials.*.quantity'  => 'nullable|numeric',
                    'materials.*.unitID'    => 'nullable',
                ]);
                $messages = array_merge($messages, [
                    'materials.required'               => 'Cần chọn ít nhất 1 vật tư.',
                    'materials.min'                    => 'Cần chọn ít nhất 1 vật tư.',
                    'materials.*.productID.required'   => 'Vui lòng chọn sản phẩm cho từng vật tư.',
                    'materials.*.quantity.required'    => 'Vui lòng nhập số lượng cho từng vật tư.',
                    'materials.*.quantity.numeric'     => 'Số lượng vật tư phải là số.',
                    'materials.*.unitID.required'      => 'Vui lòng chọn đơn vị tính cho từng vật tư.',
                ]);
            } else {
                $rules = array_merge($rules, [
                    'materials'            => 'nullable|array',
                    'materials.*.productID' => 'nullable',
                    'materials.*.quantity'  => 'nullable|numeric',
                    'materials.*.unitID'    => 'nullable',
                ]);
            }

            // $request->validate($rules);
            $request->validate($rules, $messages, $attributes);

            $invalidPlants = Plant::whereIn('id', $request->plantIDs)
                ->where('plotID', '!=', $request->plotID)
                ->count();
            if ($invalidPlants > 0) {
                return redirect()->back()->withErrors(['plantIDs' => 'Một hoặc nhiều cây không thuộc lô đã chọn']);
            }

            $taskSlug  = Str::slug($request->workID, '_');
            $taskSlug1 = Str::slug($request->workerID);
            $prefix = "#" . $taskSlug . "_" . $taskSlug1;
            do {
                $randomCode = $prefix . rand(100, 999);
            } while (GenTask::where('code', $randomCode)->exists());

            $task = GenTask::create([
                'code'        => $randomCode,
                'workID'      => $request->workID,
                'workName'    => $request->workName,
                'workerID'    => $request->workerID,
                'workDate'    => $request->workDate,
                'dateEnd'     => $request->dateEnd,
                'plotID'      => $request->plotID,
                'type'        => $request->typeG,
                'priority'    => $request->priority,
                'description' => $request->description,
                'workStatus'  => 'Đang chờ',
            ]);
            $task->plants()->sync($request->plantIDs);

            if ($work->workType === 'Khai thác') {
                $picking = Picking::create([
                    'taskID'      => $task->id,
                    'code'        => $request->code,
                    'name'        => $request->name,
                    'type'        => 'Khai thác',
                    'warehouseID' => $request->warehouseID,
                    'createName'  => Auth::user()->name,
                    'createDate'  => $request->createDate,
                    'desc'        => $request->desc,
                    'active'      => $request->active ?? 'Chưa hoàn thành',
                    'status'      => 'Hoạt động',
                ]);

                foreach ($request->materials ?? [] as $material) {
                    ProductPicking::create([
                        'pickingID' => $picking->id,
                        'productID' => $material['productID'] ?? null,
                        'quantity'  => $material['quantity'] ?? 0,
                    ]);
                }
            }
            ActionHistory::create([
                'user_id'     => Auth::id(),
                'action_type' => 'create',
                'model_type'  => 'GenTask',
                'details'     => "Đã tạo công việc: {$request->workName} với mã: {$randomCode}",
            ]);

            if ((int)$request->typeG === 1) {
                return redirect()
                    ->route('workps.addProposal', ['taskID' => $task->id])
                    ->with('message', 'Đã tạo công việc. Bạn có thể tạo đề xuất vật tư ngay hoặc để sau.');
            }

            return redirect()->route('works.index')->with('message', 'Tạo công việc thành công');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();

            // dd($e->errors());
        }
    }
    public function edit($id)
    {
        $works = Work::all();
        $gardens = Garden::all();
        $workers = Worker::all();
        $plots = Plot::all();
        $warehouses = WareHouse::where('id', 2)->get();
        $categories = Category::orderBy('name')->get();
        $units     = UnitOfMeasure::orderBy('name')->get();
        $gentasks = GenTask::with('plants.variety', 'taskProductProposals.proposalProducts.product', 'taskProductProposals.proposalProducts.unit', 'pickings.productPickings.product.unit')->findOrFail($id);
        $plants = Plant::with('variety')->get();
        return view('works.edit_work', compact('gardens', 'workers', 'works', 'plots', 'gentasks', 'plants', 'warehouses', 'categories', 'units'));
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $existingGenTask = GenTask::where(function ($query) use ($request, $id) {
            $query->where('code', $request->code);
        })->where('id', '!=', $id)->first();

        if ($existingGenTask && $existingGenTask->code === $request->code) {
            return redirect()->back()->with(['error' => 'Mã công việc này đã tồn tại!']);
        }

        try {
            $gentasks = GenTask::with(['pickings.productPickings'])->find($id);
            if (!$gentasks) {
                return redirect()->back()->with('error', 'Công việc không tồn tại');
            }
            $request->validate([
                'workID'      => 'required|exists:works,id',
                'workerID'    => 'required|exists:workers,id',
                'workName'    => 'required|string|max:255',
                'workDate'    => 'required|date',
                'dateEnd'     => 'required|date|after_or_equal:workDate',
                'plotID'      => 'required|exists:plots,id',
                'type'        => 'required|in:0,1',
                'priority'    => 'required|in:Thấp,Trung bình,Cao,Khẩn cấp',
                'description' => 'nullable|string',
                'plantIDs'    => 'required|array|min:1',
                'plantIDs.*'  => 'integer|exists:plants,id',

                'pickings'    => 'sometimes|array',
                'pickings.*.warehouseID'           => 'sometimes|exists:ware_houses,id',
                'pickings.*.createDate'            => 'sometimes|date',
                'pickings.*.desc'                  => 'sometimes|nullable|string',
                'pickings.*.type'                  => 'sometimes|string',

                'pickings.*.materials'             => 'sometimes|array',
                'pickings.*.materials.*.id'        => 'sometimes|integer|exists:product_pickings,id',
                'pickings.*.materials.*.productID' => 'sometimes|exists:products,id',
                'pickings.*.materials.*.quantity'  => 'sometimes|numeric|min:0',
            ], [
                'dateEnd.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
            ]);
            //  Ràng buộc cây thuộc lô
            $invalidPlants = Plant::whereIn('id', $request->plantIDs)
                ->where('plotID', '!=', $request->plotID)
                ->count();
            if ($invalidPlants > 0) {
                return back()->withErrors(['plantIDs' => 'Một hoặc nhiều cây không thuộc lô đã chọn'])->withInput();
            }
            //(Khuyến nghị) Bổ sung check thủ công: dòng mới (không id) thì bắt buộc có productID + quantity
            $pickingsInput = $request->input('pickings', []);
            $manualErrors = [];
            foreach ($pickingsInput as $pid => $payload) {
                if (!empty($payload['materials']) && is_array($payload['materials'])) {
                    foreach ($payload['materials'] as $i => $row) {
                        $hasId = !empty($row['id']);
                        if (!$hasId) {
                            if (empty($row['productID'])) {
                                $manualErrors["pickings.$pid.materials.$i.productID"] = 'Bắt buộc khi thêm dòng mới.';
                            }
                            if ($row['quantity'] === null || $row['quantity'] === '') {
                                $manualErrors["pickings.$pid.materials.$i.quantity"] = 'Bắt buộc khi thêm dòng mới.';
                            }
                        }
                    }
                }
            }
            if (!empty($manualErrors)) {
                throw ValidationException::withMessages($manualErrors);
            }

            DB::transaction(function () use ($request, $gentasks, $pickingsInput) {
                if ($gentasks->workID != $request->workID || $gentasks->workerID != $request->workerID) {
                    $taskSlug  = Str::slug($request->workID, '_');
                    $taskSlug1 = Str::slug($request->workerID);
                    $prefix    = '#' . $taskSlug . '_' . $taskSlug1;
                    do {
                        $randomCode = $prefix . rand(100, 999);
                    } while (GenTask::where('code', $randomCode)->exists());
                    $gentasks->code = $randomCode;
                }
                $gentasks->update([
                    'workID'      => $request->workID,
                    'workName'    => $request->workName,
                    'workerID'    => $request->workerID,
                    'workDate'    => $request->workDate,
                    'dateEnd'     => $request->dateEnd,
                    'plotID'      => $request->plotID,
                    'type'        => $request->type,
                    'priority'    => $request->priority,
                    'description' => $request->description,
                    'workStatus'  => $request->workStatus ?? $gentasks->workStatus,
                ]);
                $gentasks->plants()->sync($request->plantIDs);

                if (is_array($pickingsInput)) {
                    $existingPickings = $gentasks->pickings->keyBy('id');

                    foreach ($pickingsInput as $pickingId => $payload) {
                        $pickingId = (int)$pickingId;
                        /** @var \App\Models\Picking|null $picking */
                        $picking = $existingPickings->get($pickingId);
                        if (!$picking) {
                            continue;
                        }
                        $toUpdate = [];
                        if (array_key_exists('warehouseID', $payload)) $toUpdate['warehouseID'] = $payload['warehouseID'];
                        if (array_key_exists('createDate',  $payload)) $toUpdate['createDate']  = $payload['createDate'];
                        if (array_key_exists('desc',        $payload)) $toUpdate['desc']        = $payload['desc'];
                        if (array_key_exists('type',        $payload)) $toUpdate['type']        = $payload['type'];

                        if (!empty($toUpdate)) {
                            $picking->update($toUpdate);
                        }

                        if (!empty($payload['materials']) && is_array($payload['materials'])) {
                            $ppMap = $picking->productPickings->keyBy('id');

                            foreach ($payload['materials'] as $row) {
                                $rowId = isset($row['id']) ? (int)$row['id'] : null;

                                if ($rowId && $ppMap->has($rowId)) {
                                    // UPDATE dòng cũ
                                    $ppUpdate = [];
                                    if (array_key_exists('productID', $row)) $ppUpdate['productID'] = $row['productID'];
                                    if (array_key_exists('quantity',  $row)) $ppUpdate['quantity']  = $row['quantity'];

                                    if (!empty($ppUpdate)) {
                                        $ppMap->get($rowId)->update($ppUpdate);
                                    }
                                } else {
                                    if (!empty($row['productID']) && $row['quantity'] !== null && $row['quantity'] !== '') {
                                        $picking->productPickings()->create([
                                            'productID' => (int)$row['productID'],
                                            'quantity'  => $row['quantity'],
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                ActionHistory::create([
                    'user_id'     => Auth::id(),
                    'action_type' => 'update',
                    'model_type'  => 'GenTask',
                    'details'     => "Đã cập nhật công việc: {$gentasks->workName} với mã: {$gentasks->code}",
                ]);
            });

            return redirect()->route('works.index')->with('message', 'Cập nhật công việc & sản lượng thành công');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
    }
    public function destroy($id)
    {
        $gens = GenTask::find($id);
        $gens->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $gens = GenTask::whereIn('id', $request->ids)->get();

        foreach ($gens as $g) {
            $g->status = ($g->status === 'Hoàn thành') ? 'Chưa hoàn thành' : 'Hoàn thành';
            $g->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $gensToDelete = GenTask::whereIn('id', $request->ids)->get();

        GenTask::whereIn('id', $request->ids)->delete();

        foreach ($gensToDelete as $g) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'GenTask',
                'details' => "Đã xóa công việc: " . $g->workName . " với mã: " . $g->code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các công việc được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:gen_tasks,id'],
        ]);
        $t = GenTask::findOrFail($request->id);
        if (in_array($t->workStatus, ['Hoàn thành', 'Hoàn thành trễ'], true)) {
            $t->workStatus   = 'Đang chờ';
            $t->completed_at = null;
            $t->save();
            return response()->json([
                'success'  => true,
                'status'   => $t->workStatus,   // 'Đang chờ'
                'deadline' => 'pending',
                'message'  => 'Đã chuyển về trạng thái Đang chờ.',
            ]);
        }
        $t->completed_at = $t->completed_at ?? now();
        $deadlineAt = $t->dateEnd ? \Carbon\Carbon::parse($t->dateEnd)->endOfDay() : null;
        $completedAt = $t->completed_at;
        $deadline  = 'no_deadline';
        $label     = 'Đúng hạn';
        $lateDays  = 0;
        if ($deadlineAt && $completedAt->gt($deadlineAt)) {
            $t->workStatus = 'Hoàn thành trễ';
            $deadline      = 'late';
            $label         = 'Trễ';
            $lateDays      = $deadlineAt->diffInDays($completedAt);
            $tooltip       = "Hoàn thành TRỄ {$lateDays} ngày (hoàn thành: " . $completedAt->format('d/m/Y H:i') . ", hạn: " . $deadlineAt->format('d/m/Y H:i') . ")";
        } else {
            $t->workStatus = 'Hoàn thành';
            $deadline      = $deadlineAt ? 'on_time' : 'no_deadline';
            $tooltip       = $deadlineAt
                ? "Hoàn thành ĐÚNG HẠN (hoàn thành: " . $completedAt->format('d/m/Y H:i') . ", hạn: " . $deadlineAt->format('d/m/Y H:i') . ")"
                : "Hoàn thành (không có hạn)";
        }
        $t->save();
        return response()->json([
            'success'      => true,
            'status'       => $t->workStatus, // 'Hoàn thành' | 'Hoàn thành trễ'
            'deadline'     => $deadline, // on_time | late | no_deadline
            'label'        => $label,
            'late_days'    => $lateDays,
            'completed_at' => $t->completed_at?->toIso8601String(),
            'dateEnd'      => $deadlineAt?->toIso8601String(),
            'tooltip'      => $tooltip ?? null,
            'message'      => 'Cập nhật trạng thái thành công.',
        ]);
    }

    public function index1(Request $request)
    {
        $all_AddWork = Work::orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return DataTables::of($all_AddWork)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->addColumn('workCode', function ($row) {
                    return $row->workCode ?? 'N/A';
                })
                ->addColumn('workName', function ($row) {
                    return $row->workName ?? 'N/A';
                })
                ->addColumn('workType', function ($row) {
                    return $row->workType ?? 'N/A';
                })
                ->addColumn('workDate', function ($row) {
                    return $row->workDate ?? 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-aworks/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->workType ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/aworks/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'workCode', 'stt', 'workDate', 'workType', 'workName', 'action'])
                ->make(true);
        }
        return view('Aworks.all_AddWorks');
    }
    public function save1(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'workCode' => 'required',
            // 'workName' => 'required',
            'workType' => 'required',
            'workDate' => 'required',

            'status' => 'nullable',
        ]);
        $existingCode = Work::where('workType', $request->workType)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Công việc này đã tồn tại!']);
        }

        Work::create([
            // 'workName' => $request->workName,
            'workType' => $request->workType,
            'workCode' => $request->workCode,
            'workDate' => $request->workDate,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'WorkType',
            'details' => "Đã tạo công việc: " . $request->workCode,
        ]);
        session()->flash('message', 'Tạo công việc thành công.');
        return redirect()->back();
    }
    public function edit1($id)
    {
        $aworks = Work::find($id);

        return view('Aworks.edit_Aworks', compact('aworks'));
    }
    public function update1(Request $request, $id)
    {
        $existingWork = Work::where('workType', $request->workType)->where('id', '!=', $id)->first();

        $existingWork = Work::where(function ($query) use ($request, $id) {
            $query->where('workType', $request->workType);
        })->where('id', '!=', $id)->first();

        if ($existingWork) {
            if ($existingWork->workType === $request->workType) {
                return redirect()->back()->with(['error' => 'Công việc này đã tồn tại!']);
            }
        }
        $Aworks = Work::find($id);
        if (!$Aworks) {
            return redirect()->back()->with('error', 'Công việc không tồn tại');
        }
        $request->validate([
            'workCode' => 'required',
            // 'workName' => 'required',
            'workType' => 'required',
            'workDate' => 'required',
        ]);
        $Aworks->update([
            // 'workName' => $request->workName,
            'workType' => $request->workType,
            'workCode' => $request->workCode,
            'workDate' => $request->workDate,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'WorkType',
            'details' => "Đã cập nhật công việc: " . $Aworks->workName,
        ]);
        return redirect()->route('aworks.index')->with('message', 'Cập nhật công việc thành công');
    }
    public function destroy1($id)
    {
        $Aworks = Work::find($id);
        $Aworks->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple1(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $Aworks = Work::whereIn('id', $request->ids)->get();

        foreach ($Aworks as $g) {
            $g->status = ($g->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $g->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple1(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $AworksToDelete = Work::whereIn('id', $request->ids)->get();
        Work::whereIn('id', $request->ids)->delete();
        foreach ($AworksToDelete as $g) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'WorkType',
                'details' => "Đã xóa công việc: " . $g->workName,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công công việc được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
}
