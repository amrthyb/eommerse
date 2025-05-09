@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{__('order.list orders')}}</h2>

    <div class="button-action" style="margin-bottom: 20px">
        <!-- Tombol untuk ekspor -->
        <a href="{{ route('orders.export') }}" class="btn btn-success">Export</a>
    </div>

    <table id="table" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>{{ __('order.user') }}</th>
                <th>{{ __('order.total price') }}</th>
                <th>Status</th>
                <th>{{ __('order.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>
                        @if(in_array('pesanan.lihat', Auth::user()->roles->permissions ?? []))
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info">View</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
