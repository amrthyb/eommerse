<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrder;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Melihat daftar order yang telah dilakukan oleh pengguna
    public function getOrders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
                       ->with('orderItems.product')
                       ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Order',
            'data' => $orders
        ]);
    }

    // Melakukan checkout dan memproses pesanan
    public function checkout(Request $request, PaymentService $paymentService)
    {
        $request->validate([
            'shipping_address' => 'required|string',
        ]);

        $user = User::find(Auth::user()->id);
        $cartItems = CartItem::where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang Anda kosong.',
            ], 400);
        }

        // Periksa stok produk
        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->product_id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.',
                ], 400);
            }

            if ($product->stock < $cartItem->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak cukup untuk produk: ' . $product->name,
                ], 400);
            }
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_amount' => 0,
                'shipping_address' => $request->shipping_address,
            ]);

            $totalAmount = 0;

            foreach ($cartItems as $cartItem) {
                $product = Product::find($cartItem->product_id);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->price,
                ]);

                $product->stock -= $cartItem->quantity;
                $product->save();

                $totalAmount += $product->price * $cartItem->quantity;
            }

            $order->total_amount = $totalAmount;
            $order->save();
            // Kirim notifikasi ke user & admin
            $admins = User::where('role','admin')->get();
            foreach($admins as $admin){
                // sementara no cc email
                $admin->notify(new NewOrder($order,));
            }

            // Hapus item dari keranjang
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            // Buat transaksi Midtrans
            $snapToken = $paymentService->createTransaction($order);

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil, order Anda sedang diproses.',
                'order_id' => $order->id,
                'payment_url' => "https://app.midtrans.com/snap/v2/vtweb/$snapToken"
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses checkout: ' . $e->getMessage(),
            ], 500);
        }
    }
}
