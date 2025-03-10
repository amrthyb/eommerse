<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CartItem;

class CartItemsTableSeeder extends Seeder
{
    public function run()
    {
        CartItem::create([
            'user_id' => 1,  // Admin user
            'product_id' => 1,  // Laptop
            'quantity' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        CartItem::create([
            'user_id' => 2,  // Regular user
            'product_id' => 2,  // T-shirt
            'quantity' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

