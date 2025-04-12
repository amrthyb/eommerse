<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->input('category_id');
        $sort = $request->input('sort', 'asc');

        // Filter berdasarkan kategori
        $query = Product::query();

        // Filter berdasarkan kategori jika ada
    if ($categoryId) {
        $query->where('category_id', $categoryId);
    }

    // Sorting berdasarkan harga (price) jika ada
    if ($sort) {
        $query->orderBy('price', $sort);
    }
        $products = $query->paginate(10);

        // Mengembalikan response JSON
        return response()->json([
            'success' => true,
            'message' => 'Daftar Produk',
            'data' => $products
        ]);
    }

}

