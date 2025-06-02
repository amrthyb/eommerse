<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 8px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h2>Invoice Order #{{ $order->id }}</h2>
    <p>Nama Pelanggan: {{ $order->user->name }}</p>
    <p>Tanggal Order: {{ $order->created_at->format('d M Y') }}</p>
    <p>Status: {{ ucfirst($order->status) }}</p>
    
    <h4>Detail Produk:</h4>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $item)
                <tr>
                    <td>{{ $item->product->name}}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->price) }}</td>
                    <td>Rp {{ number_format($item->price * $item->quantity) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total Bayar: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h3>
</body>
</html>
