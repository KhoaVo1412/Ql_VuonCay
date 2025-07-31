<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['name', 'parentID', 'warehouseID', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parentID');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parentID');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouseID');
    }
}
