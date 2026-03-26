<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'name',
        'email',
        'address',
        'city',
        'postalCode',
        'country',
        'cart',
        'total',
        'paid',
    ];

    protected $casts = [
        'cart' => 'array',
        'paid' => 'boolean',
    ];
}
