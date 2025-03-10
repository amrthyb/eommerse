<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'payment_status', 'payment_date', 'amount', 'transaction_id'
    ];

    // Relasi ke tabel order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
