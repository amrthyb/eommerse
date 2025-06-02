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
    public function __construct()
    {

        $this->middleware('permission:produk.buat')->only(['create', 'store']);
        $this->middleware('permission:produk.edit')->only(['edit', 'update']);
        $this->middleware('permission:produk.hapus')->only(['destroy']);
        $this->middleware('permission:produk')->only(['index']);
    }
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
        $request->validate([
            'name' => 'required|max:225',
            'description' => 'required|max:225',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('product_images', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $imagePath,
            ]);
        }
    }

       $users = User::where('role', 'user')->get();
       foreach ($users as $user) {
            sleep(2);
           $user->notify(new NewProduct($product));
       }

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
        $request->validate([
            'name' => 'required|max:225',
            'description' => 'required|max:225',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::findOrFail($id);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        if ($request->hasFile('images')) {
            if ($product->images->isNotEmpty()) {
                foreach ($product->images as $image) {
                    Storage::delete('public/' . $image->image_url);
                    $image->delete();
                }
            }

            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('product_images', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imagePath,
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->images->isNotEmpty()) {
            foreach ($product->images as $image) {
                Storage::delete('public/' . $image->image_url);
                $image->delete();
            }
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try{
            Excel::import(new ProductImport, $request->file('file'));
            return redirect()->route('products.index')->with('success', 'Produk berhasil diimpor!');
        }catch (\Exception $e){
            \Log::error('Gagal import produk: '. $e->getMessage());
            return redirect()->route('products.index')->with('error', 'Gagal mengimport produk. Pastikan file sesuai format.');
        }
    }

}
