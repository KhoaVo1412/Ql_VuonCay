<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiseasePlant;
use App\Models\Duty;
use App\Models\Evaluate;
use App\Models\FallentPlant;
use App\Models\GenTask;
use App\Models\MaterialProposal;
use App\Models\TaskProductProposal;
use App\Models\Team;
use App\Models\TreatmentSessions;
use App\Models\Work;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

class DiseaseControllerApi extends Controller
{
    public function diseasePlants()
    {
        $all_dis = DiseasePlant::with('disease', 'plant', 'worker')->get();
        if ($all_dis->isNotEmpty()) {

            return response()->json([
                'data' => $all_dis,
                'status' => 200,
                'message' => 'Danh sách phiếu cây bệnh.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function detailD($id)
    {
        $all_dis = DiseasePlant::with('disease', 'plant', 'worker')->find($id);

        if (!$all_dis) {
            return response()->json([
                'message' => 'Phiếu cây bệnh không tồn tại.'
            ], 404);
        }
        // $all_dis->typeWork = $all_dis->work ? $all_dis->work->workName : null;
        // $all_dis->nameWorker = $all_dis->worker ? $all_dis->worker->name : null;
        return response()->json([
            'status' => 200,
            'data' => $all_dis,
            'message' => 'Chi tiết phiếu cây bệnh.'
        ], 200);
    }
    public function treatmentslips()
    {
        $treatmentslips = TreatmentSessions::with('diseasePlant', 'assignedWorker')->whereHas('diseasePlant')->get();
        if ($treatmentslips->isNotEmpty()) {

            return response()->json([
                'data' => $treatmentslips,
                'status' => 200,
                'message' => 'Danh sách phiếu trị.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function detailT($id)
    {
        $treatmentslips = TreatmentSessions::with('diseasePlant', 'assignedWorker')->find($id);

        if (!$treatmentslips) {
            return response()->json([
                'message' => 'Phiếu trị không tồn tại.'
            ], 404);
        }
        // $all_dis->typeWork = $all_dis->work ? $all_dis->work->workName : null;
        // $all_dis->nameWorker = $all_dis->worker ? $all_dis->worker->name : null;
        return response()->json([
            'status' => 200,
            'data' => $treatmentslips,
            'message' => 'Chi tiết phiếu trị.'
        ], 200);
    }
    public function materialproposals()
    {
        $all_materialproposals = MaterialProposal::with(['creator', 'items.product'])->orderBy('id', 'desc')->get();

        if ($all_materialproposals->isNotEmpty()) {
            $data = $all_materialproposals->map(function ($proposal) {
                return [
                    'id' => $proposal->id,
                    'proposaName' => $proposal->proposaName,
                    'proposalDate' => $proposal->proposalDate,
                    'approvalDate' => $proposal->approvalDate,
                    'status' => $proposal->status,
                    'creator' => $proposal->creator,
                    'items' => $proposal->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'materialQuantity' => $item->materialQuantity,
                            'unitID' => $item->unit->name,
                            'note' => $item->note,
                            'status' => $item->status,
                            'productName' => $item->product ? $item->product->name : null,
                        ];
                    }),
                ];
            });

            return response()->json([
                'data' => $data,
                'status' => 200,
                'message' => 'Danh sách cây gãy đổ.',
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }

    public function detailM($id)
    {
        $materialproposals = MaterialProposal::with(['creator', 'items.product'])->find($id);

        if (!$materialproposals) {
            return response()->json([
                'message' => 'Phiếu trị không tồn tại.'
            ], 404);
        }

        $materialproposals->items = $materialproposals->items->map(function ($item) {
            return [
                'id' => $item->id,
                'materialQuantity' => $item->materialQuantity,
                'unitID' => $item->unitID,
                'note' => $item->note,
                'status' => $item->status,
                'productName' => $item->product ? $item->product->name : null,
                'unitName' => $item->unit ? $item->unit->name : null,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $materialproposals,
            'message' => 'Chi tiết đề xuất vật tư (CB).'
        ], 200);
    }
    public function fallentplants()
    {
        $fallentplants = FallentPlant::with(['plant.plot', 'worker'])->orderBy('id', 'desc')->get();

        if ($fallentplants->isNotEmpty()) {
            // $data = $fallentplants->map(function ($proposal) {
            //     return [
            //         'id' => $proposal->id,
            //         'proposaName' => $proposal->proposaName,
            //         'proposalDate' => $proposal->proposalDate,
            //         'approvalDate' => $proposal->approvalDate,
            //         'status' => $proposal->status,
            //         'creator' => $proposal->creator,
            //         'items' => $proposal->items->map(function ($item) {
            //             return [
            //                 'id' => $item->id,
            //                 'materialQuantity' => $item->materialQuantity,
            //                 'unitID' => $item->unit->name,
            //                 'note' => $item->note,
            //                 'status' => $item->status,
            //                 'productName' => $item->product ? $item->product->name : null,
            //             ];
            //         }),
            //     ];
            // });

            return response()->json([
                'data' => $fallentplants,
                'status' => 200,
                'message' => 'Danh sách cây gãy đổ.',
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
}
