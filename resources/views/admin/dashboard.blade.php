@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('dashboard.admin dashboard') }}</h2>
    <div class="row">
        <!-- Total Users Card -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">{{ __('dashboard.total users') }}</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalUsers }}</h5>
                </div>
            </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">{{ __('dashboard.total products') }}</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalProducts }}</h5>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">{{ __('dashboard.total orders') }}</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalOrders }}</h5>
                </div>
            </div>
        </div>
    </div>

    <h4>{{ __('dashboard.recent orders') }}</h4>
    <table id="table" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('dashboard.user') }}</th>
                <th>{{ __('dashboard.total price') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.created at') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
