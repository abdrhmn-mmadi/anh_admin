<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'batch_id',
        'agent_id',
        'region_id',
        'type',
        'quantity',
        'reference',
    ];

    public function product()
    {
        return $this->belongsTo(ProductType::class, 'product_id');
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
