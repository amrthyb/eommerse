@extends('layouts.admin')

@section('content')

<div class="container">
    <h2>{{ __('user.user detail') }} : {{ $user->name }}</h2>
    <p><strong>Email :</strong> {{ $user->email }}</p>
    <p><strong>{{ __('user.role') }} :</strong> {{ $user->role }}</p>

    <h4>{{ __('user.order history') }}</h4>
    <table id="table" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>{{ __('user.total price') }}</th>
                <th>Status</th>
                <th>{{ __('user.created at') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order) <!-- Pastikan menggunakan $orders -->
            <tr>
                <td>{{ $order->id }}</td>
                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>{{ $order->created_at }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4">{{ __('user.no orders found.') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('user.back to list') }}</a>
</div>
@endsection
