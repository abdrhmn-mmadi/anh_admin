<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockLog extends Model
{
    use HasFactory;

    // Table name (optional if it follows Laravel convention)
    protected $table = 'product_stock_logs';

    // Mass assignable columns
    protected $fillable = [
        'product_id',
        'region_id',
        'quantity',
        'type',      // 'addition' or 'sale'
        'log_date',
    ];

    // Cast columns
    protected $casts = [
        'log_date' => 'date',
        'quantity' => 'integer',
    ];

    /**
     * Get the product related to this stock log
     */
    public function product()
    {
        return $this->belongsTo(ProductType::class, 'product_id');
    }

    /**
     * Get the region related to this stock log
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Scope for sales only
     */
    public function scopeSales($query)
    {
        return $query->where('type', 'sale');
    }

    /**
     * Scope for additions only
     */
    public function scopeAdditions($query)
    {
        return $query->where('type', 'addition');
    }
}
