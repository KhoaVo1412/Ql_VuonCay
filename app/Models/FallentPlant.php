<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FallentPlant extends Model
{
    use HasFactory;
    protected $table = 'fallent_plants';

    protected $fillable = [
        'detectionDate',
        'cause',
        'plantID',
        'specificLocation',
        'reportStatus',
        'treeCondition',
        'workerID',
        'status',
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plantID', 'id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'workerID', 'id');
    }
}
