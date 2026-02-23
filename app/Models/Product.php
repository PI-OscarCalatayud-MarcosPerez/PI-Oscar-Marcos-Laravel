<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'sku', 'stock', 'imagen_url', 'categoria', 'seccion', 'category_id', 'porcentaje_descuento', 'plataforma', 'is_eco'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Calcula la media de estrellas automáticamente
    public function getMediaEstrellasAttribute()
    {
        return round($this->reviews()->avg('estrellas'), 1) ?? 0;
    }

    public function productCodes()
    {
        return $this->hasMany(ProductCode::class);
    }

    // Calcula el stock dinámicamente según los códigos disponibles
    public function getStockAttribute()
    {
        return $this->productCodes()->count();
    }
}
