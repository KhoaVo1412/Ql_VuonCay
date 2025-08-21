<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Duty;
use App\Models\Team;
use App\Models\Worker;

class WorkerControllerApi extends Controller
{
    public function dutys()
    {

        $all_dutys = Duty::all();
        if ($all_dutys->isNotEmpty()) {

            return response()->json([
                'data' => $all_dutys,
                'status' => 200,
                'message' => 'Danh sách chức vụ.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function teams()
    {

        $all_teams = Team::all();
        if ($all_teams->isNotEmpty()) {

            return response()->json([
                'data' => $all_teams,
                'status' => 200,
                'message' => 'Danh sách tổ.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function workers()
    {
        $all_workers = Worker::with('team', 'duty')->orderBy('id', 'desc')->get();
        if ($all_workers->isNotEmpty()) {

            return response()->json([
                'data' => $all_workers->map(function ($worker) {
                    $worker->dutyName = $worker->duty->dutyName;
                    $worker->teamName = $worker->team->teamName;
                    return $worker;
                }),
                'status' => 200,
                'message' => 'Danh sách công nhân.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function detail($id)
    {
        $all_workers = Worker::with('team', 'duty')->find($id);

        if (!$all_workers) {
            return response()->json([
                'message' => 'Công nhân không tồn tại.'
            ], 404);
        }
        $all_workers->dutyName = $all_workers->duty ? $all_workers->duty->dutyName : null;
        $all_workers->teamName = $all_workers->team ? $all_workers->team->teamName : null;
        return response()->json([
            'status' => 200,
            'data' => $all_workers,
            'message' => 'Chi tiết công nhân.'
        ], 200);
    }
}
