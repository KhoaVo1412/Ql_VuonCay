<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    use HasFactory;
    protected $table = 'plants';

    protected $fillable = [
        'plantCode',
        'plotID',
        'varietyID',
        'RF_id',
        'year',
        'status',
        'statusTree',
    ];

    public function plot()
    {
        return $this->belongsTo(Plot::class, 'plotID', 'id');
    }

    public function variety()
    {
        return $this->belongsTo(Variety::class, 'varietyID', 'id');
    }

    public function rf()
    {
        // return $this->belongsTo(RF::class); // chỉnh lại RF::class nếu cần
    }
    public function diseaseReports()
    {
        return $this->hasMany(DiseasePlant::class);
    }
    public function genTasks()
    {
        return $this->belongsToMany(GenTask::class, 'gen_task_plant', 'plantID', 'taskID');
    }
}
