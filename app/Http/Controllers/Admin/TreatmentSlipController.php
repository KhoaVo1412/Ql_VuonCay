<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\DiseasePlant;
use App\Models\Product;
use App\Models\TreatmentSessions;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class TreatmentSlipController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $treatmentslips = TreatmentSessions::with(['diseasePlant', 'assignedWorker'])
            ->whereHas('diseasePlant') // giữ điều kiện của bạn
            ->when(!($user?->hasRole('Admin')), function ($q) use ($user) {
                $q->whereHas('assignedWorker', fn($w) => $w->where('user_id', $user->id));
                // nếu cột là userID thì đổi 'user_id' -> 'userID'
            })
            ->latest()
            ->get();
        // $treatmentslips = TreatmentSessions::with('diseasePlant', 'assignedWorker')->whereHas('diseasePlant')->get();
        if ($request->ajax()) {
            return DataTables::of($treatmentslips)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('code', function ($row) {
                    return $row->diseasePlant->code;
                })
                ->editColumn('name', function ($row) {
                    return $row->diseasePlant->name ?? 'Không rõ';
                })
                ->addColumn('plantCode', function ($row) {
                    return $row->plant ? $row->plant->plantCode : 'Không rõ';
                })
                ->addColumn('sessionStart', function ($row) {
                    return $row->sessionStart
                        ? Carbon::parse($row->sessionStart)->format('d/m/Y')
                        : null;
                })
                ->editColumn('sessionEnd', function ($row) {
                    return $row->sessionEnd
                        ? Carbon::parse($row->sessionEnd)->format('d/m/Y')
                        : null;
                })
                ->editColumn('assigned_to', function ($row) {
                    return $row->assignedWorker->name;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-treatmentslips/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->code ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/treatmentslips/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'assigned_to', 'code', 'stt', 'sessionEnd', 'name', 'sessionStart', 'plantName', 'status', 'action'])
                ->make(true);
        }
        return view('treatmentslip.all_treatmentslip');
    }
    public function add(Request $request)
    {
        $diseasePlants = DiseasePlant::all();
        $products = Product::all();
        $workers = Worker::all();
        return view('treatmentslip.add_treatmentslip', compact('diseasePlants', 'products', 'workers'));
    }
    public function save(Request $request)
    {
        $validated = $request->validate([
            'sessionStart' => 'required|date',
            'sessionEnd' => 'nullable|date',
            'assigned_to' => 'required',
            'priority' => 'required',
            'results' => 'nullable',
            'steps' => 'required|array|min:1',
            'steps.*.productID' => 'required',
            'steps.*.dose' => 'required|string',
            'steps.*.execution_date' => 'required|date',
            'steps.*.instructions' => 'required|string',
        ]);
        $session = TreatmentSessions::create([
            'diseasePlantID' => $request->diseasePlantID,
            'sessionStart' => $validated['sessionStart'],
            'sessionEnd' => $validated['sessionEnd'],
            'assigned_to' => $validated['assigned_to'],
            'priority' => $validated['priority'],
        ]);
        foreach ($validated['steps'] as $step) {
            $session->treatmentSteps()->create($step);
        }
        return redirect()->route('treatmentslips.index')->with('message', 'Đã tạo phiếu trị mới thành công.');
    }

    public function edit($id)
    {
        $treatmentslips = TreatmentSessions::with(['treatmentSteps', 'diseasePlant'])->findOrFail($id);
        $products = Product::all();
        $workers = Worker::all();

        return view('treatmentslip.edit_treatmentslip', compact('treatmentslips', 'products', 'workers'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sessionStart' => 'required|date',
            'sessionEnd' => 'nullable|date',
            'assigned_to' => 'required|exists:workers,id',
            'priority' => 'required',
            'results' => 'nullable',
            'steps' => 'required|array|min:1',
            'steps.*.id' => 'nullable|exists:treatment_steps,id',
            'steps.*.productID' => 'required|exists:products,id',
            'steps.*.dose' => 'required|string',
            'steps.*.execution_date' => 'required|date',
            'steps.*.instructions' => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $id) {
            $session = TreatmentSessions::findOrFail($id);
            $session->update([
                'sessionStart' => $validated['sessionStart'],
                'sessionEnd' => $validated['sessionEnd'],
                'assigned_to' => $validated['assigned_to'],
                'priority' => $validated['priority'],
                'results' => $validated['results'] ?? null,
            ]);

            $existingStepIds = $session->treatmentSteps()->pluck('id')->toArray();
            $submittedStepIds = [];

            foreach ($validated['steps'] as $step) {
                if (!empty($step['id'])) {
                    $session->treatmentSteps()->where('id', $step['id'])->update($step);
                    $submittedStepIds[] = $step['id'];
                } else {
                    $newStep = $session->treatmentSteps()->create($step);
                    $submittedStepIds[] = $newStep->id;
                }
            }
            $toDelete = array_diff($existingStepIds, $submittedStepIds);
            if (!empty($toDelete)) {
                $session->treatmentSteps()->whereIn('id', $toDelete)->delete();
            }
        });
        return redirect()->route('treatmentslips.index', TreatmentSessions::find($id)->diseasePlantID)->with('message', 'Cập nhật phiếu trị thành công.');
    }
    public function destroy($id)
    {
        $dis = TreatmentSessions::find($id);
        $dis->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $dis = TreatmentSessions::whereIn('id', $request->ids)->get();

        foreach ($dis as $TreatmentSessions) {
            $TreatmentSessions->status = ($TreatmentSessions->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $TreatmentSessions->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $disToDelete = TreatmentSessions::whereIn('id', $request->ids)->get();

        TreatmentSessions::whereIn('id', $request->ids)->delete();

        foreach ($disToDelete as $TreatmentSessions) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'TreatmentSessions',  // Model "TreatmentSessions"
                'details' => "Đã xóa nông trường: " . $TreatmentSessions->TreatmentSessions_name . " với mã: " . $TreatmentSessions->TreatmentSessions_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các nông trường được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $TreatmentSessions = TreatmentSessions::find($request->id);
        if ($TreatmentSessions) {
            $TreatmentSessions->status = $TreatmentSessions->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $TreatmentSessions->save();
            return response()->json(['success' => true, 'status' => $TreatmentSessions->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
