<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentsTableSeeder extends Seeder
{
    public function run()
    {
        Payment::create([
            'order_id' => 1,  // Order ID 1
            'payment_status' => 'completed',
            'payment_date' => now(),
            'amount' => 10150000.00,
            'transaction_id' => 'TXN123456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Payment::create([
            'order_id' => 2,  // Order ID 2
            'payment_status' => 'pending',
            'payment_date' => now(),
            'amount' => 150000.00,
            'transaction_id' => 'TXN789012',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

