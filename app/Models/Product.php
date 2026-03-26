<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price',
        'original_price',
        'description',
        'features',
        'rating',
        'reviews',
        'qty',
        'brand',
        'images',
        'in_stock',
    ];

    protected $casts = [
        'images' => 'array',
        'features' => 'array',
        'in_stock' => 'boolean',
    ];

    // Compute in_stock automatically
    public function getInStockAttribute($value)
    {
        return $this->qty > 0;
    }
}
