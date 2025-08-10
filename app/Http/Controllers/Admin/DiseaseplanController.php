<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\DiseasePlant;
use App\Models\Diseases;
use App\Models\Plant;
use App\Models\Product;
use App\Models\TreatmentSessions;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class DiseaseplanController extends Controller
{
    public function index(Request $request)
    {

        $all_dis = DiseasePlant::with('disease', 'plant', 'worker')->get();
        if ($request->ajax()) {
            return DataTables::of($all_dis)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('code', function ($row) {
                    return $row->code;
                })
                ->editColumn('diseaseName', function ($row) {
                    return $row->disease->diseaseName ?? 'Không rõ';
                })
                ->addColumn('plantCode', function ($row) {
                    return $row->plant ? $row->plant->plantCode : 'Không rõ';
                })
                ->addColumn('detectionDate', function ($row) {
                    return $row->detectionDate;
                })
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->editColumn('workerID', function ($row) {
                    return $row->worker->name;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-diseaseplans/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/diseaseplans/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'workerID', 'code', 'stt', 'diseaseName', 'name', 'detectionDate', 'plantName', 'status', 'action'])
                ->make(true);
        }
        return view('diseaseplan.all_diseaseplan');
    }
    public function add(Request $request)
    {
        $diseases = Diseases::all();
        $plants = Plant::with('variety')->get();
        $workers = Worker::all();
        $products = Product::all();
        return view('diseaseplan.add_diseaseplan', compact('products', 'diseases', 'plants', 'workers'));
    }
    public function save(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required',
            'name' => 'required',
            'plantID' => 'required',
            'workerID' => 'required',
            'diseaseID' => 'required',
            'detectionDate' => 'required',
            'symptoms' => 'nullable',
            'cause' => 'nullable',
            'status' => 'nullable',
            // 'sessionStart' => 'required|date',
            // 'sessionEnd' => 'nullable|date',
            // 'assigned_to' => 'required',
            // 'priority' => 'required',
            // 'steps' => 'required|array',
            // 'steps.*.productID' => 'required',
            // 'steps.*.dose' => 'required',
            // 'steps.*.execution_date' => 'required',
            // 'steps.*.instructions' => 'required',
        ]);
        $status = $validatedData['status'] ?? 'Hoạt động';
        $diseasePlant = DiseasePlant::create([
            'code' => $validatedData['code'],
            'name' => $validatedData['name'],
            'plantID' => $validatedData['plantID'],
            'diseaseID' => $validatedData['diseaseID'],
            'detectionDate' => $validatedData['detectionDate'],
            'symptoms' => $validatedData['symptoms'],
            'cause' => $validatedData['cause'],
            'workerID' => $validatedData['workerID'],
            'status' => $status,
        ]);

        // $treatmentSession = TreatmentSessions::create([
        //     'sessionStart' => $validatedData['sessionStart'],
        //     'sessionEnd' => $validatedData['sessionEnd'],
        //     'assigned_to' => $validatedData['assigned_to'], // Lưu người phụ trách vào trường assigned_to
        //     'priority' => $validatedData['priority'],
        //     'results' => $request->results ?? null,
        // ]);

        // foreach ($validatedData['steps'] as $step) {
        //     $treatmentSession->treatmentSteps()->create([
        //         'productID' => $step['productID'],
        //         'dose' => $step['dose'],
        //         'execution_date' => $step['execution_date'],
        //         'instructions' => $step['instructions'],
        //     ]);
        // }

        // $diseasePlant->update([
        //     'sessionID' => $treatmentSession->id,
        // ]);

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'diseasePlant',
            'details' => "Đã tạo cây bệnh: " . $request->name . " với mã: " . $request->code,
        ]);
        // return redirect()->route('treatmentslips.add', ['id' => $diseasePlant->id])
        //     ->with('message', 'Tạo cây bệnh thành công. Tiếp tục tạo phiếu trị.');

        return redirect()->route('diseaseplans.index')->with('message', 'Tạo cây bệnh thành công');
    }
    public function edit($id)
    {
        $diseases = Diseases::all();
        $plants = Plant::with('variety')->get();
        $workers = Worker::all();
        $products = Product::all();
        $diseaseplants = DiseasePlant::findOrFail($id);
        $treatmentSession = TreatmentSessions::where('id', $diseaseplants->sessionID)->first();
        return view('diseaseplan.edit_diseaseplan', compact('diseaseplants', 'products', 'diseases', 'plants', 'workers', 'treatmentSession'));
    }

    public function update(Request $request, $id)
    {
        // Xác thực dữ liệu từ form
        $validatedData = $request->validate([
            'code' => 'required',
            'name' => 'required',
            'plantID' => 'required',
            'workerID' => 'required',
            'diseaseID' => 'required',
            'detectionDate' => 'required',
            'symptoms' => 'nullable',
            'cause' => 'nullable',
            'status' => 'nullable',
            // 'sessionStart' => 'required|date',
            // 'sessionEnd' => 'nullable|date',
            // 'assigned_to' => 'required',
            // 'priority' => 'required',
            // 'steps' => 'required',
            // 'steps.*.productID' => 'required',
            // 'steps.*.dose' => 'required',
            // 'steps.*.execution_date' => 'required',
            // 'steps.*.instructions' => 'required',
        ]);

        $diseasePlant = DiseasePlant::findOrFail($id);
        $diseasePlant->update([
            'code' => $validatedData['code'],
            'name' => $validatedData['name'],
            'plantID' => $validatedData['plantID'],
            'diseaseID' => $validatedData['diseaseID'],
            'detectionDate' => $validatedData['detectionDate'],
            'symptoms' => $validatedData['symptoms'],
            'cause' => $validatedData['cause'],
            'workerID' => $validatedData['workerID'],
            'status' => $validatedData['status'] ?? 'Hoạt động',
        ]);

        // $treatmentSession = TreatmentSessions::findOrFail($diseasePlant->sessionID);
        // $treatmentSession->update([
        //     'sessionStart' => $validatedData['sessionStart'],
        //     'sessionEnd' => $validatedData['sessionEnd'],
        //     'assigned_to' => $validatedData['assigned_to'],
        //     'priority' => $validatedData['priority'],
        //     'results' => $request->results ?? null,
        // ]);

        // $existingStepIds = $treatmentSession->treatmentSteps()->pluck('id')->toArray();
        // $submittedStepIds = [];

        // foreach ($validatedData['steps'] as $step) {
        //     if (isset($step['id'])) {
        //         // Cập nhật nếu có ID
        //         $treatmentSession->treatmentSteps()->where('id', $step['id'])->update([
        //             'productID' => $step['productID'],
        //             'dose' => $step['dose'],
        //             'execution_date' => $step['execution_date'],
        //             'instructions' => $step['instructions'],
        //         ]);
        //         $submittedStepIds[] = $step['id'];
        //     } else {
        //         $newStep = $treatmentSession->treatmentSteps()->create([
        //             'productID' => $step['productID'],
        //             'dose' => $step['dose'],
        //             'execution_date' => $step['execution_date'],
        //             'instructions' => $step['instructions'],
        //         ]);
        //         $submittedStepIds[] = $newStep->id;
        //     }
        // }

        // $stepsToDelete = array_diff($existingStepIds, $submittedStepIds);
        // $treatmentSession->treatmentSteps()->whereIn('id', $stepsToDelete)->delete();

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'DiseasePlant',
            'details' => "Đã cập nhật cây bệnh: " . $diseasePlant->name . " với mã: " . $diseasePlant->code,
        ]);

        return redirect()->route('diseaseplans.index')->with('message', 'Cập nhật cây bệnh thành công!');
    }

    public function destroy($id)
    {
        $diseaseplans = DiseasePlant::find($id);
        $diseaseplans->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $diseaseplans = DiseasePlant::whereIn('id', $request->ids)->get();

        foreach ($diseaseplans as $DiseasePlant) {
            $DiseasePlant->status = ($DiseasePlant->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $DiseasePlant->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $diseaseplansToDelete = DiseasePlant::whereIn('id', $request->ids)->get();

        DiseasePlant::whereIn('id', $request->ids)->delete();

        foreach ($diseaseplansToDelete as $DiseasePlant) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'diseasePlant',
                'details' => "Đã xóa cây bệnh: " . $DiseasePlant->name . " với mã: " . $DiseasePlant->code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các cây bệnh được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $DiseasePlant = DiseasePlant::find($request->id);
        if ($DiseasePlant) {
            $DiseasePlant->status = $DiseasePlant->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $DiseasePlant->save();
            return response()->json(['success' => true, 'status' => $DiseasePlant->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
