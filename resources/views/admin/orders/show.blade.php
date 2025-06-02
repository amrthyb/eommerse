@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>{{ __('order.order detail') }} : {{ $order->id }}</h2>
    <p><strong>{{ __('order.user') }}:</strong> {{ $order->user->name }}</p>
    <p><strong>{{ __('order.total price') }}:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>{{ __('order.created at') }}:</strong> {{ $order->created_at->format('d-m-Y') }}</p>

    <h4>{{ __('order.Order Items') }}</h4>

    <!-- Pengecekan untuk memastikan items ada -->
    @if($order->orderitems->isNotEmpty())
        <table id="table" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>{{ __('order.Product') }}</th>
                    <th>{{ __('order.Quantity') }}</th>
                    <th>{{ __('order.Price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderitems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>{{ __('order.No items available for this order.') }}</p>
    @endif

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection
