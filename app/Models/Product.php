<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'description', 'price', 'stock', 'category_id'
    ];

    // Relasi ke tabel category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke tabel product_images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Relasi ke tabel order_items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi ke tabel cart_items
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

}
