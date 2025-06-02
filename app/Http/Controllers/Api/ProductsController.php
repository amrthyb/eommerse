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

        $query = Product::query();

    if ($categoryId) {
        $query->where('category_id', $categoryId);
    }

    if ($sort) {
        $query->orderBy('price', $sort);
    }
        $products = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => __('messageApi.products list fetched'),
            'data' => $products
        ]);
    }

}

