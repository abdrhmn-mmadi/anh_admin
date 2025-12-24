<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'agent_id', 'region_id', 'quantity_available', 'cost_price', 'expiry_date'
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
