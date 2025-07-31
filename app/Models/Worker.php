<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $table = 'workers';

    protected $fillable = [
        'image',
        'name',
        'code_name',
        'bdate',
        'cccd',
        'address',
        'team_id',
        'duty_id',
        'gender',
        'phone',
        'status'
    ];
    public function evaluations()
    {
        return $this->hasMany(Evaluate::class, 'id');
    }

    // public function team()
    // {
    //     return $this->belongsTo(Team::class, 'id');
    // }
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function duty()
    {
        return $this->belongsTo(Duty::class, 'duty_id');
    }
    public function genTasks()
    {
        return $this->hasMany(GenTask::class, 'workerID', 'id');
    }
    public function diseaseReports()
    {
        return $this->hasMany(DiseasePlant::class);
    }
}
