<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price']; // Make sure 'price' is included
    protected $table = 'product_types'; // optional if Laravel auto-detects
}
