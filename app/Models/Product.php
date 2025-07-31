<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    protected $fillable = [
        'name',
        'categoryID',
        'unitID',
        'type',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryID');
    }

    public function unit()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unitID');
    }

    public function productPickings()
    {
        return $this->hasMany(ProductPicking::class);
    }

    public function taskProposalProducts()
    {
        return $this->hasMany(TaskProductProposalProduct::class, 'productID', 'productID');
    }
}
