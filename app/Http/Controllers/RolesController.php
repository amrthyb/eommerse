<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use FontLib\Table\Type\name;

class RolesController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:peran.buat')->only(['create', 'store']);
        $this->middleware('permission:peran.edit')->only(['edit', 'update']);
        $this->middleware('permission:peran.hapus')->only(['destroy']);
        $this->middleware('permission:peran')->only(['index']);
    }
    public function index()
    {
        $roles = Role::where('id','!=',auth()->user()->role_id)->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:225|unique:roles,name',
            'permissions' =>'array',
        ]);

        Role::create([
            'name' => $request->name,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('roles.index')->with('succes, role berhasil ditambahkan');
    }

    public function edit($id)
    {
        $roles = Role::findOrFail($id);
        // dd($roles);

        return view ('admin.roles.edit', compact('roles'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:225|unique:roles,name,' . $id,
            'permissions' => 'array',
        ]);

        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->name,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('roles.index')->with('berhasil ditambahkan');
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')->with('berhasil dihapus');
    }
}
