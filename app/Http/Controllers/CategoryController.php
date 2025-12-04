<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoryExport;
use App\Imports\CategoryImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:kategori.buat')->only(['create', 'store']);
        $this->middleware('permission:kategori.edit')->only(['edit', 'update']);
        $this->middleware('permission:kategori.hapus')->only(['destroy']);
        $this->middleware('permission:kategori')->only(['index']);
    }
    
    public function index()
    {
        $categories = Category::orderBy('id', 'asc')->get();
        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:225',
            'description' => 'required|max:225',
        ]);
        // dd($request->all());
        Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function edit($id)
    {
        $category = Category::find($id);
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:225',
            'description' => 'required|max:225',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
        }

        return redirect()->route('categories.index')->with('error', 'Category not found.');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx',
        ]);

        try {
            Excel::import(new CategoryImport(), $request->file('file'));
            return redirect()->route('categories.index')->with(['success' => 'Data Berhasil Diimport!']);
        } catch (\Exception $e) {
            \Log::error('Gagal import produk: '. $e->getMessage());
            return redirect()->route('categories.index')->with('error', 'Gagal import produk. Pastikan data sesuai format');
        }
    }

    public function export()
    {
        return Excel::download(new CategoryExport(), 'categories.xlsx');
    }
}
