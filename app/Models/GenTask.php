<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenTask extends Model
{
    use HasFactory;

    protected $table = 'gen_tasks';

    protected $fillable = [
        'code',
        'workName',
        'workerID',
        'workID',
        'workDate',
        'dateEnd',
        'TaskpropID',
        'plotID',
        'active',
        'type',
        'productID',
        'workStatus',
        'description',
        'priority',
        'status',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class, 'workID', 'id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'workerID', 'id');
    }

    public function plot()
    {
        return $this->belongsTo(Plot::class, 'plotID', 'id');
    }

    public function pickings()
    {
        return $this->hasMany(Picking::class, 'taskID');
    }
    public function taskProductProposals()
    {
        return $this->hasMany(TaskProductProposal::class, 'taskID', 'id');
    }
    public function plants()
    {
        return $this->belongsToMany(Plant::class, 'gen_task_plant', 'taskID', 'plantID');
    }
}
