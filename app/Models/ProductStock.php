<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

  protected $fillable = [
        'product_id', 'region_id', 'total_quantity'
    ];

    public function product()
    {
        return $this->belongsTo(ProductType::class, 'product_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
