<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Ambil data pesanan yang sudah ada
        return Order::with('user', 'orderItems.product')->get();
    }

    public function headings(): array
    {
        // Menentukan header untuk file Excel
        return [
            'Order ID',
            'User Name',
            'Total Price',
            'Status',
            'Items',
        ];
    }

    public function map($order): array
    {
        // Menyusun data untuk setiap baris
        $items = $order->orderItems->map(function ($item) {
            return $item->product->name . ' (' . $item->quantity . ')';
        })->implode(', ');

        return [
            $order->id,
            $order->user->name,
            'Rp ' . number_format($order->total_amount, 0, ',', '.'),
            ucfirst($order->status),
            $items,
        ];
    }
}

