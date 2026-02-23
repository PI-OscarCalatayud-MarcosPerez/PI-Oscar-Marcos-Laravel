<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCode extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'code', 'is_sold'];

    protected $casts = [
        'is_sold' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
