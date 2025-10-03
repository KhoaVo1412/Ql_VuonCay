<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Duty;
use App\Models\Evaluate;
use App\Models\GenTask;
use App\Models\Picking;
use App\Models\Plant;
use App\Models\ProductPicking;
use App\Models\TaskProductProposal;
use App\Models\TaskProductProposalProduct;
use App\Models\Team;
use App\Models\Work;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class WorkControllerApi extends Controller
{
    public function typeWorks()
    {

        $all_typeWs = Work::all();
        if ($all_typeWs->isNotEmpty()) {

            return response()->json([
                'data' => $all_typeWs,
                'status' => 200,
                'message' => 'Loại công việc.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function gentasks()
    {

        $all_gentask = GenTask::with('work', 'worker', 'worker.team', 'worker.duty')->orderBy('id', 'desc')->get();
        if ($all_gentask->isNotEmpty()) {

            return response()->json([
                'data' => $all_gentask,
                'data' => $all_gentask->map(function ($g) {
                    $g->typeWork = $g->work->workName;
                    $g->nameWorker = $g->worker->name;
                    return $g;
                }),
                'status' => 200,
                'message' => 'Danh sách công việc.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function store(Request $request)
    {
        try {
            // --- Chuẩn hoá priority
            if ($p = $request->input('priority')) {
                $map = [
                    'cao' => 'Cao',
                    'high' => 'Cao',
                    'trung bình' => 'Trung bình',
                    'trungbinh' => 'Trung bình',
                    'medium' => 'Trung bình',
                    'khẩn cấp' => 'Khẩn cấp',
                    'khan cap' => 'Khẩn cấp',
                    'urgent' => 'Khẩn cấp',
                    'thấp' => 'Thấp',
                    'thap' => 'Thấp',
                    'low' => 'Thấp',
                ];
                $request->merge(['priority' => $map[mb_strtolower($p)] ?? $p]);
            }

            // --- Validate
            $data = $request->validate([
                'workID'      => 'required|integer|exists:works,id',
                'workerID'    => 'required|integer|exists:workers,id',
                'workName'    => 'required|string|max:255',
                'workDate'    => 'required|date',
                'dateEnd'     => 'required|date|after_or_equal:workDate',
                'plotID'      => 'required|integer|exists:plots,id',
                'priority'    => 'required|string|in:Cao,Trung bình,Khẩn cấp,Thấp',
                'description' => 'nullable|string',
                'plantIDs'    => 'required|array|min:1',
                'plantIDs.*'  => 'integer|distinct|exists:plants,id',

                // type = ID loại công việc (1 = yêu cầu đề xuất vật tư)
                'type'        => 'required|integer|in:0,1',

                // Khai thác (nếu Work là "Khai thác")
                'code'        => 'nullable|string|max:255',
                'name'        => 'nullable|string|max:255',
                'warehouseID' => 'nullable|integer|exists:ware_houses,id',
                'createName'  => 'nullable|string|max:255',
                'createDate'  => 'nullable|date',
                'desc'        => 'nullable|string',
                'active'      => 'nullable|string',
                'materials'               => 'nullable|array',
                'materials.*.productID'   => 'nullable|integer|exists:products,id',
                'materials.*.quantity'    => 'nullable|numeric|min:0',
                'materials.*.unitID'      => 'nullable|integer|exists:unit_of_measures,id',

                // (Tuỳ chọn) Đề xuất gửi kèm (khi type = 1)
                'proposalName'                => 'nullable|string|max:255',
                'proposalDate'           => 'nullable|date',
                'approvalDate'          => 'nullable|date',
                'reason'          => 'nullable',
                'proposalProducts'            => 'nullable|array|min:1',
                'proposalProducts.*.productID' => 'required_with:proposalProducts|integer|exists:products,id',
                'proposalProducts.*.warehouseID' => 'nullable|integer|exists:ware_houses,id',
                'proposalProducts.*.unitID'   => 'nullable|integer|exists:unit_of_measures,id',
                'proposalProducts.*.quantity' => 'required_with:proposalProducts|numeric|min:0.000001',
                'proposalProducts.*.note'     => 'nullable|string',
            ], [], [
                'workID' => 'Mã công việc',
                'workerID' => 'Người thực hiện',
                'plantIDs' => 'Danh sách cây'
            ]);

            // --- Work tồn tại + cây thuộc đúng lô
            $work = Work::find($data['workID']);
            $invalidPlants = Plant::whereIn('id', $data['plantIDs'])->where('plotID', '!=', $data['plotID'])->count();
            if (!$work) {
                return response()->json(['status' => 'error', 'message' => 'Công việc không tồn tại.', 'data' => []], 404);
            }
            if ($invalidPlants > 0) {
                return response()->json(['status' => 'error', 'message' => 'Một hoặc nhiều cây không thuộc lô đã chọn.', 'data' => []], 422);
            }

            $requiresProposal = ((int)$data['type'] === 1);
            // --- Sinh code
            $prefix = '#' . Str::slug((string)$data['workID'], '_') . '_' . Str::slug((string)$data['workerID']);
            do {
                $randomCode = $prefix . random_int(100, 999);
            } while (GenTask::where('code', $randomCode)->exists());
            // --- Transaction
            [$task, $picking, $proposal, $proposalItems] = DB::transaction(function () use ($data, $randomCode, $work, $requiresProposal) {

                // 1) Tạo Task
                $task = GenTask::create([
                    'code'        => $randomCode,
                    'workID'      => $data['workID'],
                    'workName'    => $data['workName'],
                    'workerID'    => $data['workerID'],
                    'workDate'    => $data['workDate'],
                    'dateEnd'     => $data['dateEnd'],   // mutator endOfDay() sẽ xử lý
                    'plotID'      => $data['plotID'],
                    'type'        => (int)$data['type'],  // LƯU type (ID)
                    'priority'    => $data['priority'],
                    'description' => $data['description'] ?? null,
                    'workStatus'  => GenTask::STATUS_PENDING,
                ]);
                $task->plants()->sync($data['plantIDs']);
                // 2) Nếu Work là "Khai thác"
                $isHarvest = ((int)$data['workID'] === 2);
                $picking = null;
                if ($isHarvest) {
                    // if ($work->workType === 'Khai thác') {
                    $picking = Picking::create([
                        'taskID'      => $task->id,
                        'code'        => $data['code']        ?? null,
                        'name'        => $data['name']        ?? null,
                        'type'        => 'Khai thác',
                        'warehouseID' => $data['warehouseID'] ?? null,
                        'createName'  => $data['createName']  ?? (optional(Auth::user())->name),
                        'createDate'  => $data['createDate']  ?? Carbon::today()->toDateString(),
                        'desc'        => $data['desc']        ?? null,
                        'active'      => $data['active']      ?? 'Chưa hoàn thành',
                        'status'      => 'Hoạt động',
                    ]);
                    foreach (($data['materials'] ?? []) as $m) {
                        ProductPicking::create([
                            'pickingID' => $picking->id,
                            'productID' => $m['productID'] ?? null,
                            'quantity'  => isset($m['quantity']) ? (float)$m['quantity'] : 0,
                        ]);
                    }
                }
                // 3) Nếu type=1
                $proposal = null;
                $proposalItems = collect();
                if ($requiresProposal && !empty($data['proposalProducts'])) {
                    $proposal = TaskProductProposal::create([
                        'proposaName'  => $data['proposalName'] ?? ('DXVT-' . $task->code . '-' . now()->format('Ymd-His')),
                        'proposalDate' => $data['proposalDate'] ?? now()->toDateString(),
                        'approvalDate' => $data['proposalApprovalDate'] ?? ($data['approvalDate'] ?? null),
                        'reason'       => $data['proposalReason']       ?? ($data['reason'] ?? null),
                        'taskID'       => $task->id,
                        'treatmentID'  => null,
                        'status'       => 'Chờ duyệt',
                        'created_by'   => Auth::id(),
                    ]);
                    foreach ($data['proposalProducts'] as $it) {
                        $proposalItems->push(TaskProductProposalProduct::create([
                            'taskproposalID'   => $proposal->id,
                            'warehouseID'      => $it['warehouseID'] ?? null,
                            'productID'        => $it['productID'],
                            'materialQuantity' => $it['quantity'],
                            'unitID'           => $it['unitID'] ?? null,
                            'note'             => $it['note'] ?? null,
                            'status'           => 'Chờ duyệt',
                        ]));
                    }
                }
                ActionHistory::create([
                    'user_id'     => Auth::id(),
                    'action_type' => 'create',
                    'model_type'  => 'GenTask',
                    'details'     => "Tạo công việc: {$data['workName']} ({$randomCode})",
                ]);

                return [$task, $picking, $proposal, $proposalItems];
            });
            $task->load(['worker', 'plot.garden', 'plants']);
            return response()->json([
                'status'  => 'success',
                'message' => 'Tạo công việc thành công.',
                'data'    => [[
                    'task'                => $task,
                    'picking'             => $picking,
                    'proposal'            => $proposal,
                    'proposal_items'      => $proposal ? $proposalItems : null,
                    'proposal_required'   => $requiresProposal && !$proposal,
                    'proposal_create_url' => ($requiresProposal && !$proposal) ? url("/api/tasks/{$task->id}/proposals") : null,
                ]],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.', 'errors' => $e->errors(), 'data' => []], 422);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['status' => 'error', 'message' => 'Có lỗi xảy ra khi tạo công việc.', 'data' => []], 500);
        }
    }
    // public function store(Request $request)
    // {
    //     try {
    //         // Chuẩn hoá priority
    //         $priorityInput = trim((string) $request->input('priority', ''));
    //         if ($priorityInput !== '') {
    //             $map = [
    //                 'cao' => 'Cao',
    //                 'high' => 'Cao',
    //                 'trung bình' => 'Trung bình',
    //                 'trungbinh' => 'Trung bình',
    //                 'medium' => 'Trung bình',
    //                 'khẩn cấp' => 'Khẩn cấp',
    //                 'khan cap' => 'Khẩn cấp',
    //                 'urgent' => 'Khẩn cấp',
    //                 'thấp' => 'Thấp',
    //                 'thap' => 'Thấp',
    //                 'low' => 'Thấp',
    //             ];
    //             $key = mb_strtolower($priorityInput);
    //             $request->merge(['priority' => $map[$key] ?? $priorityInput]);
    //         }

    //         // ===== Validate cơ bản
    //         $rules = [
    //             'workID'      => 'required|integer|exists:works,id',
    //             'workerID'    => 'required|integer|exists:workers,id',
    //             'workName'    => 'required|string|max:255',
    //             'workDate'    => 'required|date',
    //             'dateEnd'     => 'required|date|after_or_equal:workDate',
    //             'plotID'      => 'required|integer|exists:plots,id',
    //             'priority'    => 'required|string|in:Cao,Trung bình,Khẩn cấp,Thấp',
    //             'description' => 'nullable|string',
    //             'plantIDs'    => 'required|array|min:1',
    //             'plantIDs.*'  => 'integer|distinct|exists:plants,id',

    //             // Loại công việc THẬT: là ID (vd 1 = có đề xuất)
    //             // Nếu có bảng types: dùng exists:types,id
    //             'type'        => 'required|integer',

    //             // Nếu có gửi đề xuất kèm theo:
    //             'proposalProducts'                 => 'nullable|array|min:1',
    //             'proposalProducts.*.productID'     => 'required_with:proposalProducts|integer|exists:products,id',
    //             'proposalProducts.*.warehouseID'   => 'nullable|integer|exists:ware_houses,id',
    //             'proposalProducts.*.unitID'        => 'nullable|integer|exists:unit_of_measures,id',
    //             'proposalProducts.*.quantity'      => 'required_with:proposalProducts|numeric|min:0.000001',
    //             'proposalProducts.*.note'          => 'nullable|string',

    //             // Phiếu khai thác (nếu workType = 'Khai thác')
    //             'code'        => 'nullable|string|max:255',
    //             'name'        => 'nullable|string|max:255',
    //             'warehouseID' => 'nullable|integer|exists:ware_houses,id',
    //             'createName'  => 'nullable|string|max:255',
    //             'createDate'  => 'nullable|date',
    //             'desc'        => 'nullable|string',
    //             'active'      => 'nullable|string',

    //             'materials'               => 'nullable|array',
    //             'materials.*.productID'   => 'nullable|integer|exists:products,id',
    //             'materials.*.quantity'    => 'nullable|numeric|min:0',
    //             'materials.*.unitID'      => 'nullable|integer|exists:unit_of_measures,id',
    //         ];

    //         $messages = [
    //             'required'              => ':attribute là bắt buộc.',
    //             'after_or_equal'        => ':attribute phải lớn hơn hoặc bằng ngày bắt đầu.',
    //             'plantIDs.min'          => 'Vui lòng chọn ít nhất một cây.',
    //             'plantIDs.*.distinct'   => 'Danh sách cây chứa ID trùng lặp.',
    //         ];

    //         $attributes = [
    //             'workID'   => 'Mã công việc',
    //             'workerID' => 'Người thực hiện',
    //             'workName' => 'Tên công việc',
    //             'workDate' => 'Ngày bắt đầu',
    //             'dateEnd'  => 'Ngày kết thúc',
    //             'plotID'   => 'Lô',
    //             'priority' => 'Độ ưu tiên',
    //             'plantIDs' => 'Danh sách cây',
    //         ];

    //         $data = $request->validate($rules, $messages, $attributes);

    //         // Xác định có yêu cầu đề xuất không: type == 1
    //         $requiresProposal = ((int)$data['type'] === 1);

    //         // ===== Kiểm tra Work và cây thuộc đúng lô
    //         $work = Work::find($data['workID']);
    //         if (!$work) {
    //             return response()->json(['status' => 'error', 'message' => 'Công việc không tồn tại.', 'data' => []], 404);
    //         }

    //         $invalidPlants = Plant::whereIn('id', $data['plantIDs'])
    //             ->where('plotID', '!=', $data['plotID'])
    //             ->count();
    //         if ($invalidPlants > 0) {
    //             return response()->json(['status' => 'error', 'message' => 'Một hoặc nhiều cây không thuộc lô đã chọn.', 'data' => []], 422);
    //         }

    //         // ===== Sinh mã task
    //         $taskSlug  = Str::slug((string)$data['workID'], '_');
    //         $taskSlug1 = Str::slug((string)$data['workerID']);
    //         $prefix = "#" . $taskSlug . "_" . $taskSlug1;
    //         do {
    //             $randomCode = $prefix . random_int(100, 999);
    //         } while (GenTask::where('code', $randomCode)->exists());

    //         // ===== Tạo task (+ phiếu nếu Khai thác) + (đề xuất nếu type=1 và có dữ liệu)
    //         [$task, $picking] = DB::transaction(function () use ($data, $randomCode, $work, $requiresProposal, $request) {

    //             // GenTask (LƯU type = ID loại công việc)
    //             $task = GenTask::create([
    //                 'code'        => $randomCode,
    //                 'workID'      => $data['workID'],
    //                 'workName'    => $data['workName'],
    //                 'workerID'    => $data['workerID'],
    //                 'workDate'    => $data['workDate'],
    //                 'dateEnd'     => $data['dateEnd'],
    //                 'plotID'      => $data['plotID'],
    //                 'type'        => (int)$data['type'],   // <--- LƯU type (ID)
    //                 'priority'    => $data['priority'],
    //                 'description' => $data['description'] ?? null,
    //                 'workStatus'  => GenTask::STATUS_PENDING,
    //             ]);

    //             // Attach plants
    //             $task->plants()->sync($data['plantIDs']);

    //             // Phiếu khai thác (theo workType)
    //             $picking = null;
    //             $materials = $data['materials'] ?? [];
    //             if ($work->workType === 'Khai thác') {
    //                 $picking = Picking::create([
    //                     'taskID'      => $task->id,
    //                     'code'        => $data['code']        ?? null,
    //                     'name'        => $data['name']        ?? null,
    //                     'type'        => 'Khai thác',
    //                     'warehouseID' => $data['warehouseID'] ?? null,
    //                     'createName'  => $data['createName']  ?? (optional(Auth::user())->name),
    //                     'createDate'  => $data['createDate']  ?? Carbon::today()->toDateString(),
    //                     'desc'        => $data['desc']        ?? null,
    //                     'active'      => $data['active']      ?? 'Chưa hoàn thành',
    //                     'status'      => 'Hoạt động',
    //                 ]);

    //                 foreach ($materials as $m) {
    //                     ProductPicking::create([
    //                         'pickingID' => $picking->id,
    //                         'productID' => $m['productID'] ?? null,
    //                         'quantity'  => isset($m['quantity']) ? (float)$m['quantity'] : 0,
    //                         // 'unitID' => $m['unitID'] ?? null,
    //                     ]);
    //                 }
    //             }

    //             // Nếu type=1 và client đã gửi kèm proposalProducts → tạo luôn đề xuất
    //             if ($requiresProposal && $request->filled('proposalProducts')) {
    //                 $ppRules = [
    //                     'proposalProducts'                 => 'required|array|min:1',
    //                     'proposalProducts.*.productID'     => 'required|integer|exists:products,id',
    //                     'proposalProducts.*.warehouseID'   => 'nullable|integer|exists:ware_houses,id',
    //                     'proposalProducts.*.unitID'        => 'nullable|integer|exists:unit_of_measures,id',
    //                     'proposalProducts.*.quantity'      => 'required|numeric|min:0.000001',
    //                     'proposalProducts.*.note'          => 'nullable|string',
    //                 ];
    //                 $request->validate($ppRules); // validate riêng cho block đề xuất

    //                 foreach ($request->input('proposalProducts', []) as $item) {
    //                     \App\Models\TaskProductProposal::create([
    //                         'taskID'      => $task->id,
    //                         'productID'   => $item['productID'],
    //                         'warehouseID' => $item['warehouseID'] ?? null,
    //                         'unitID'      => $item['unitID'] ?? null,
    //                         'quantity'    => $item['quantity'],
    //                         'note'        => $item['note'] ?? null,
    //                     ]);
    //                 }
    //             }

    //             // Lịch sử
    //             ActionHistory::create([
    //                 'user_id'     => Auth::id(),
    //                 'action_type' => 'create',
    //                 'model_type'  => 'GenTask',
    //                 'details'     => "Đã tạo công việc: {$data['workName']} với mã: {$randomCode}",
    //             ]);

    //             return [$task, $picking];
    //         });

    //         $task->load(['worker', 'plot.garden', 'plants']);

    //         return response()->json([
    //             'status'  => 'success',
    //             'message' => 'Tạo công việc thành công.',
    //             'data'    => [[
    //                 'task'                => $task,
    //                 'picking'             => $picking,
    //                 'proposal_required'   => $requiresProposal, // type==1
    //                 'proposal_create_url' => $requiresProposal ? url("/api/tasks/{$task->id}/proposals") : null,
    //             ]],
    //         ], 201);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => 'Dữ liệu không hợp lệ.',
    //             'errors'  => $e->errors(),
    //             'data'    => [],
    //         ], 422);
    //     } catch (\Throwable $e) {
    //         report($e);
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => 'Có lỗi xảy ra khi tạo công việc.',
    //             'data'    => [],
    //         ], 500);
    //     }
    // }
    public function detailG($id)
    {
        $all_gentask = GenTask::with('work', 'worker', 'plot', 'plants')->find($id);

        if (!$all_gentask) {
            return response()->json([
                'message' => 'Công việc không tồn tại.'
            ], 404);
        }
        $all_gentask->typeWork = $all_gentask->work ? $all_gentask->work->workName : null;
        $all_gentask->nameWorker = $all_gentask->worker ? $all_gentask->worker->name : null;
        return response()->json([
            'status' => 200,
            'data' => $all_gentask,
            'message' => 'Chi tiết công việc.'
        ], 200);
    }
    public function workps()
    {
        $all_workps = TaskProductProposal::with('task', 'creator')->orderBy('id', 'desc')->get();
        if ($all_workps->isNotEmpty()) {

            return response()->json([
                'data' => $all_workps->map(function ($worker) {
                    // $worker->dutyName = $worker->task->dutyName;
                    $worker->creatorName = $worker->creator->name;
                    return $worker;
                }),
                'status' => 200,
                'message' => 'Danh sách đề xuất vật tư.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function detailW($id)
    {
        $all_workps = TaskProductProposal::with('task', 'creator')->find($id);

        if (!$all_workps) {
            return response()->json([
                'message' => 'Đề xuất vật tư không tồn tại.'
            ], 404);
        }
        $all_workps->creatorName = $all_workps->creator ? $all_workps->creator->name : null;
        return response()->json([
            'status' => 200,
            'data' => $all_workps,
            'message' => 'Chi tiết đề xuất vật tư.'
        ], 200);
    }

    public function evaluate()
    {
        $all_comments = Evaluate::with(['worker.genTasks' => function ($query) {
            $query->selectRaw('workerID, 
                            count(*) as countWork, 
                            sum(case when workStatus = "Hoàn thành" then 1 else 0 end) as countComplete, 
                            sum(case when workStatus = "Hoàn thành" and dateEnd >= now() then 1 else 0 end) as countOnTime, 
                            sum(case when workStatus = "Hoàn thành" and dateEnd < now() then 1 else 0 end) as countLate')
                ->groupBy('workerID');
        }])
            ->orderBy('id', 'desc')
            ->get();

        foreach ($all_comments as $comment) {
            $worker = $comment->worker;
            $worker->countWork = $worker->genTasks->first()->countWork ?? 0;
            $worker->countComplete = $worker->genTasks->first()->countComplete ?? 0;
            $worker->countOnTime = $worker->genTasks->first()->countOnTime ?? 0;
            $worker->countLate = $worker->genTasks->first()->countLate ?? 0;
        }

        return response()->json([
            'status' => 200,
            'data' => $all_comments,
            'message' => 'Danh sách đánh giá.'
        ], 200);
    }
}
