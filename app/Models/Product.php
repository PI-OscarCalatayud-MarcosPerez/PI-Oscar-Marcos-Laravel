<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'sku', 'stock', 'img', 'categoria'];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Calcula la media de estrellas automáticamente
    public function getMediaEstrellasAttribute()
    {
        return round($this->reviews()->avg('estrellas'), 1) ?? 0;
    }
}