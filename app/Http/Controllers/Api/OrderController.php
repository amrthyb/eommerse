<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use App\Notifications\NewOrder;
use App\Notifications\OrderStatusChanged;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Melihat daftar order yang telah dilakukan oleh pengguna
    public function getOrders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->with('orderItems.product')->get();

        return response()->json([
            'success' => true,
            'message' => __('messageApi.orders fetched'),
            'data' => $orders,
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
            return response()->json(
                [
                    'success' => false,
                    'message' => __('messageApi.cart empty'),
                ],
                400,
            );
        }

        // Periksa stok produk
        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->product_id);
            if (!$product) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => __('messageApi.no orders found'),
                    ],
                    400,
                );
            }

            if ($product->stock < $cartItem->quantity) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => __('messageApi.Insufficient stock for the selected product') . $product->name,
                    ],
                    400,
                );
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
            $order->load('user', 'orderItems.product');
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewOrder($order));
            }

            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            $snapToken = $paymentService->createTransaction($order);

            return response()->json(
                [
                    'success' => true,
                    'message' => __('messageApi.Order placed successfully. Please proceed to payment.'),
                    'order_id' => $order->id,
                    'payment_url' => "https://app.sandbox.midtrans.com/snap/v2/vtweb/$snapToken",
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => __('messageApi.An error occurred while processing checkout: ') . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function handleCallback(Request $request)
    {
        $payload = $request->all();
        \Log::info('Midtrans Notification Payload:', $payload);

        $transactionStatus = $payload['transaction_status'] ?? null;
        $orderId = $payload['order_id'] ?? null;
        $transactionId = $payload['transaction_id'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? 0;

        if (!$transactionStatus || !$orderId || !$transactionId) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Invalid Midtrans notification payload',
                ],
                400,
            );
        }

        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Order not found',
                ],
                404,
            );
        }

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            try {
                $existingPayment = Payment::where('transaction_id', $transactionId)->first();
                if (!$existingPayment) {
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_status' => 'completed',
                        'amount' => $grossAmount,
                        'transaction_id' => $transactionId,
                        'payment_date' => now(),
                    ]);
                }
                // dd($payload, $transactionStatus, $orderId, $order);

                $order->status = 'completed';
                $order->save();

                $order->load('user', 'orderItems.product');
                $pdf = PDF::loadView('pdf.invoice', ['order' => $order])->output();
                $order->user->notify(new OrderStatusChanged($order, $pdf));
                $admins = User::where('role', 'admin')->get();

                foreach ($admins as $admin) {
                    if ($admin->id !== $order->user->id) {
                        $admin->notify(new OrderStatusChanged($order, $pdf));
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Transaction captured and order marked as paid.',
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to process payment callback: ' . $e->getMessage());
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Failed to update payment or order',
                    ],
                    500,
                );
            }
        }

        $order->status = 'pending';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Transaction received but not marked as paid.',
        ]);
    }
}
