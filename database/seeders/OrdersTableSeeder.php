<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        Order::create([
            'user_id' => 1,  // Admin user
            'order_date' => now(),
            'status' => 'completed',  // Example status
            'total_amount' => 10150000.00,
            'shipping_address' => '123 Main St, Jakarta, Indonesia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Order::create([
            'user_id' => 2,  // Regular user
            'order_date' => now(),
            'status' => 'pending',  // Example status
            'total_amount' => 150000.00,
            'shipping_address' => '456 Another St, Jakarta, Indonesia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
