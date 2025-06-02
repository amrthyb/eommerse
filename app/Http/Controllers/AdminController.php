<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class AdminController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:admin.buat')->only(['create', 'store']);
        $this->middleware('permission:admin.edit')->only(['edit', 'update']);
        $this->middleware('permission:admin')->only(['index']);
    }

    public function index()
    {
        $admins = DB::table('users')
        ->select('users.id', 'users.name', 'users.email', 'roles.name as role_name')
        ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
        ->where('users.role', 'admin')
        ->orderBy('users.id', 'desc')
        ->get();

        return view('admin.admins.index', compact('admins'));
    }

    public function edit($id)
    {
        $admin = User::findOrFail($id);
        $roles = Role::get();
        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        $admin->name = $request->input('name');
        $admin->role_id = $request->input('role_id');
        $admin->save();

        return redirect()->route('admins.index')->with('success', 'Admin updated successfully.');
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role' => 'admin',
        ]);

        return redirect()->route('admins.index')->with('success', 'Admin created successfully.');
    }

    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'Admin deleted successfully.');
    }

}
