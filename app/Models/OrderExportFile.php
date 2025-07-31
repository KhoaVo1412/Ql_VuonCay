<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderExportFile extends Model
{
    use HasFactory;
    protected $table = 'order_exports_files';
    protected $fillable = ['order_export_id', 'name', 'file_name'];
    public function orderExports()
    {
        return $this->belongsTo(OrderExport::class, 'order_export_id', 'id');
    }
}