<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();

        $recentOrders = Order::with('user')
                            ->whereHas('user', function($query) {
                            $query->where('role', 'user');
                            })
                            ->latest()
                            ->take(5)
                            ->get();
                            //  dd($recentOrders);

        return view('admin.dashboard', compact('totalUsers', 'totalProducts', 'totalOrders', 'recentOrders'));
    }
}
