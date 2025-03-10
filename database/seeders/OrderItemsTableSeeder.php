<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;

class OrderItemsTableSeeder extends Seeder
{
    public function run()
    {
        OrderItem::create([
            'order_id' => 1,  // Order ID 1
            'product_id' => 1,  // Laptop
            'quantity' => 1,
            'price' => 10000000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => 2,  // Order ID 2
            'product_id' => 2,  // T-shirt
            'quantity' => 2,
            'price' => 150000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

