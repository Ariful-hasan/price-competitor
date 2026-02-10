<?php

namespace App\Models;

use App\Http\Resources\ProductLowestPriceResource;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseResource(ProductLowestPriceResource::class)]
class ProductLowestPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'vendor_name',
        'price',
        'fetched_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'fetched_at' => 'datetime',
    ];
}
