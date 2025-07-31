<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $table = 'works';

    protected $fillable = ['id', 'workCode', 'workName', 'workType', 'workDate'];
    public function genTasks()
    {
        return $this->hasMany(GenTask::class, 'workID', 'id');
    }
}
