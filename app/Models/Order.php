<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'status', 'total_amount', 'price', 'shipping_address'
    ];

    // Relasi ke tabel user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan model OrderItem
        public function orderItems()
        {
            return $this->hasMany(OrderItem::class);
        }

    // Relasi ke tabel payment
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
