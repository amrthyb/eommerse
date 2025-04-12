<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Menambahkan produk ke keranjang
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $user = Auth::user();

        $cartItem = CartItem::where('user_id', $user->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
        ]);
    }

    // Menghapus produk dari keranjang
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Menemukan produk yang ingin dihapus dari keranjang
        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if ($cartItem) {
            $cartItem->delete();
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari keranjang',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan di keranjang',
        ]);
    }

    // Mengupdate kuantitas produk di keranjang
    public function updateQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Update kuantitas produk di keranjang
        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if ($cartItem) {
            $cartItem->quantity = $request->quantity;
            $cartItem->save();
            return response()->json([
                'success' => true,
                'message' => 'Kuantitas produk berhasil diperbarui',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan di keranjang',
        ]);
    }

    // Menampilkan keranjang pengguna
    public function getCart()
    {
        // Menampilkan produk di keranjang untuk user yang sedang login
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)
                             ->with('product')
                             ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Keranjang',
            'data' => $cartItems
        ]);
    }

    // Menampilkan daftar produk di keranjang (index)
    public function index()
    {
        // Mendapatkan semua produk yang ada di keranjang pengguna yang sedang login
        $user = Auth::user();

        // Mengambil semua cart item yang berhubungan dengan produk di keranjang
        $cartItems = CartItem::where('user_id', $user->id)
                             ->with('product') 
                             ->get();

        // Mengembalikan response JSON dengan data produk di keranjang
        return response()->json([
            'success' => true,
            'message' => 'Daftar produk di keranjang',
            'data' => $cartItems
        ]);
    }
}
