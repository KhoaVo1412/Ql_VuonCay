<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluate extends Model
{
    protected $table = 'evaluates';

    protected $fillable = [

        'name',
        'workerID',
        'deductionPoints',
        'rating',
        'note',
        'status',
        'date_comment',
        'opinion',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'workerID', 'id');
    }
}
