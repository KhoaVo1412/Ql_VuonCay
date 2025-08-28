<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Duty;
use App\Models\Evaluate;
use App\Models\GenTask;
use App\Models\TaskProductProposal;
use App\Models\Team;
use App\Models\Work;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

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
