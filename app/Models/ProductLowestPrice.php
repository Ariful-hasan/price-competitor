<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ProductLowestPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'vendor_name', 
        'price',
        'fetched_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'fetched_at' => 'datetime'
    ];
}
