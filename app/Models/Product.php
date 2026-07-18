<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['sku', 'name', 'price'];

    public function branches()
    {
        return $this->belongsToMany(Branch::class)->withPivot('stock_quantity')->withTimestamps();
    }
}
