<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use App\Notifications\NewProduct;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::OrderBy('id','desc')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
{
    // Validasi data input
    $request->validate([
        'name' => 'required|max:225',
        'description' => 'required|max:225',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'category_id' => 'required|exists:categories,id',
        'images' => 'nullable|array',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Menyimpan data produk
    $product = Product::create([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'stock' => $request->stock,
        'category_id' => $request->category_id,
    ]);

    // Menyimpan gambar baru jika ada
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('product_images', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $imagePath,
            ]);
        }
    }

       // Mengirim notifikasi ke semua admin
       $users = User::where('role', 'user')->get();
       foreach ($users as $user) {
            sleep(2);
           $user->notify(new NewProduct($product)); // Kirim notifikasi ke admin
       }
        // Redirect ke halaman index produk dengan pesan sukses
        return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data input
        $request->validate([
            'name' => 'required|max:225',
            'description' => 'required|max:225',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Cari produk berdasarkan ID
        $product = Product::findOrFail($id);

        // Update produk dengan data baru
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        // Menyimpan gambar baru
        if ($request->hasFile('images')) {
            // Menghapus gambar lama
            if ($product->images->isNotEmpty()) {
                foreach ($product->images as $image) {
                    Storage::delete('public/' . $image->image_url);
                    $image->delete();
                }
            }

            // Menyimpan gambar baru
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('product_images', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imagePath,
                ]);
            }
        }

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Find the product by ID
        $product = Product::findOrFail($id);

        // Delete associated images from storage (if any)
        if ($product->images->isNotEmpty()) {
            foreach ($product->images as $image) {
                Storage::delete('public/' . $image->image_url);
                $image->delete();
            }
        }

        // Delete the product itself
        $product->delete();

        // Redirect back to the product index page with a success message
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }
    // Method untuk mengekspor produk
    public function export()
    {
        return Excel::download(new ProductExport, 'products.xlsx');  // Ekspor data ke file Excel
    }

    // Method untuk mengimpor produk
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',  // Validasi file yang diupload
        ]);

        Excel::import(new ProductImport, $request->file('file'));  // Import data dari file Excel

        return redirect()->route('products.index')->with('success', 'Produk berhasil diimpor!');
    }

}
