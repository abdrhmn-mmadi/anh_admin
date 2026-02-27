<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'region_id', // <-- important
    ];

    public function product()
    {
        return $this->belongsTo(ProductType::class, 'product_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
