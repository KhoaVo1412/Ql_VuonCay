<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duty extends Model
{
    use HasFactory;
    protected $table = 'duties';
    protected $fillable = ['dutyName', 'status'];
    public function workers()
    {
        return $this->hasMany(Worker::class, 'duty_id');
    }
}
