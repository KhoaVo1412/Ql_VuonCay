<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionHistory extends Model
{
    use HasFactory;

    protected $table = 'action_histories';
    protected $fillable = ['user_id', 'action_type', 'model_type', 'details'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
