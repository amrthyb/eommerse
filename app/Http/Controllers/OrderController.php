<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:pesanan.lihat')->only(['show']);
        $this->middleware('permission:pesanan')->only(['index']);
    }
    public function index()
    {
        // Ambil pesanan yang hanya dimiliki oleh user dengan role 'user'
        $orders = Order::with('user')
                        ->whereHas('user', function($query) {
                            $query->where('role', 'user');
                        })
                        ->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        // Ambil pesanan berdasarkan ID
        $order = Order::with('orderitems')->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }
    public function export()
    {
        // Menyediakan opsi untuk mengekspor data
        return Excel::download(new OrderExport, 'orders.xlsx');
    }

    public function downloadInvoice(Order $order, $id)
    {
        $order = Order::with('orderitems')->findOrFail($id);
        $order->load('user', 'orderItems.product');
        $pdf = Pdf::loadView('pdf.invoice', ['order' => $order]);

        return $pdf->stream('invoice_order_' . $order->id . '.pdf');
    }
}
