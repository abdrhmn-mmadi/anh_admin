<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieEquipement extends Model
{
    protected $fillable = ["name"];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('ordered', function ($builder) {
            $builder->orderBy('name');
        });
    }
}
