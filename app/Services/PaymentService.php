<?php

// app/Services/PaymentService.php

namespace App\Services;
use App\Models\Order;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Facades\Log;
class PaymentService
{
    public function __construct()
    {

        // Set konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

    }

    // Membuat transaksi Midtrans
    public function createTransaction(Order $order)
    {
        // Pastikan relasi user dimuat
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }

        // Bisa juga tambahkan item details (optional)
        $itemDetails = $order->orderItems->map(function ($item) {
            return [
                'id' => $item->product_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name ?? 'Product',
            ];
        })->toArray();

        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
            ],
            'item_details' => $itemDetails,

            // 'transaction_details' => [
            //     'order_id' => $order->order_id,
            //     'gross_amount' => $order->price,
            // ],
            // 'item_details' => $this->mapItemsToDetails($order),
            // 'customer_details' => $this->getCustomerDetails($order),

        ];

        // Mengambil token pembayaran dari Midtrans
        try {
            Log::info('buat transaksi midtrans dengan params: ', $params);
            $snapToken = Snap::getSnapToken($params);
            Log::info('generate snap token midtrans: ', ['token' => $snapToken]);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('midtrans snap error: '. $e->getMessage());
            throw new \Exception('Error while processing transaction: ' . $e->getMessage());
        }
    }

    // protected function mapItemsToDetails(Order $order): array
    // {
    //     return $order->items()->get()->map(function ($item) {
    //         return [
    //             'id' => $item->id,
    //             'price' => $item->price,
    //             'quantity' => $item->quantity,
    //             'name' => $item->product_name,
    //         ];
    //     })->toArray();
    // }
    // protected function getCustomerDetails(Order $order): array
    // {
    //     // Sesuaikan data customer dengan informasi yang dimiliki oleh aplikasi Anda
    //     return [
    //         'first_name' => $order->user->name, // Ganti dengan data nyata
    //         'email' => $order->user->email, // Ganti dengan data nyata
    //     ];
    // }
}

