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
        // Mengambil total pengguna ,product, order
        $totalUsers = User::where('role', 'user')->count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();

        // Mengambil 5 pesanan terbaru dengan relasi 'user'
        $recentOrders = Order::with('user')
                             ->whereHas('user', function($query) {
                                 $query->where('role', 'user');
                             })
                             ->latest()
                             ->take(5)
                             ->get();
                            //  dd($recentOrders);

        // Mengirim data ke view
        return view('admin.dashboard', compact('totalUsers', 'totalProducts', 'totalOrders', 'recentOrders'));
    }
}
