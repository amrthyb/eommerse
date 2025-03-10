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

class CategoryController extends Controller
{
    public function index()
    {
        // $query dari db
        $categories = Category::orderBy('id','asc')->get();
        return view('admin.categories.index',['categories'=>$categories]);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
         //validate form
         $this->validate($request, [
            'name'     => 'required|max:225',
            'description'=> 'required|max:225',
            'status'     => 'required|max:225',
        ]);
        // dd($request->all());
        //create post
        Category::create([
            'name'     => $request->name,
            'slug'=> str_replace(' ','-',$request->name),
            'description'   => $request->description,
            'status'   => 'active',
        ]);

        //redirect to index
        return redirect()->route('categories.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }


    public function edit($id)
    {
        $category = Category::find($id);
        return view('admin.categories.edit',['category' => $category]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'     => 'required|max:225',
            'description'=> 'required|max:225',
            'status'     => 'required|max:225',
        ]);
        $categories = Category::findOrFail($id);
        // dd($request->all());
        //create post
        Category::update([
            'name'     => $request->name,
            'slug'=> str_replace(' ','-',$request->name),
            'description'   => $request->description,
            'status'   => 'active',
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy()
    {
}
}

