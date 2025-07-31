<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Farm;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ChartController extends Controller
{
    public function getFarmAndVehicleStatistics()
    {

        $batches = DB::table('batches')
            ->selectRaw('MONTH(date_sx) as month, COUNT(*) as total_batches')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as completed_batches')
            ->selectRaw('SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending_batches')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $countContractWithCustomers = $this->countContractWithCustomers();
        $countTripByTypeOfPusFromPlantation = $this->countTripByTypeOfPusFromPlantation();
        $countBatchesCreateConnect = $this->countBatchesCreateConnect();

        return response()->json([
            // 'farms' => $farms,
            // 'vehicles' => $vehicles,
            'batches' => $batches,
            'countContractWithCustomers' => $countContractWithCustomers,
            'countTripByTypeOfPusFromPlantation' => $countTripByTypeOfPusFromPlantation,
            'countBatchesCreateConnect' => $countBatchesCreateConnect
        ]);
    }

    public function countTripByTypeOfPusFromPlantation()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = DB::table('ingredients')
            ->join('farm', 'ingredients.farm_id', '=', 'farm.id')
            ->join('typeofpus', 'ingredients.type_of_pus_id', '=', 'typeofpus.id')
            ->join('units', 'farm.unit_id', '=', 'units.id') // join thêm bảng đơn vị
            ->select(
                'farm.farm_code',
                'farm.farm_name',
                'units.unit_name',
                'typeofpus.name_pus',
                DB::raw('COUNT(*) as total_pus')
            )
            ->groupBy(
                'farm.farm_code',
                'farm.farm_name',
                'units.unit_name',
                'typeofpus.name_pus'
            )
            ->orderBy('farm.farm_name');

        if ($user && $user->farms()->exists()) {
            $farmIds = $user->farms->pluck('id')->toArray();
            $query->whereIn('farm.id', $farmIds);
        }

        $results = $query->get();


        return [
            'results' => $results->toArray()
        ];
    }

    public function countBatchesCreateConnect()
    {
        $totalBatches = Batch::count();
        $linkedBatches = DB::table('batch_ingredient')
            ->distinct('batch_id')
            ->count('batch_id');
        return [
            'totalBatches' => $totalBatches,
            'linkedBatches' => $linkedBatches
        ];
    }

    private function countContractWithCustomers()
    {
        return [
            'contracts' => Contract::count(),
            'customers' => Customer::count()
        ];
    }


}