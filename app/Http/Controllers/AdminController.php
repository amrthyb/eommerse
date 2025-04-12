<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class AdminController extends Controller
{
    // Menampilkan daftar admin
    public function index()
    {
        // Mengambil data pengguna dengan role 'admin'
        $admins = DB::table('users')->where('role', 'admin')->orderBy('id', 'desc')->get();
        return view('admin.admins.index', compact('admins'));
    }

    // Menampilkan halaman edit admin
    public function edit($id)
    {
        // Ambil data admin berdasarkan ID dari tabel 'users'
        $admin = User::findOrFail($id);
        return view('admin.admins.edit', compact('admin'));
    }

    // Menangani pembaruan data admin
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        $admin->name = $request->input('name');
        $admin->save();

        return redirect()->route('admins.index')->with('success', 'Admin updated successfully.');
    }

    // Menampilkan halaman create admin
    public function create()
    {
        return view('admin.admins.create');
    }

    // Menangani penyimpanan data admin baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Menyimpan admin baru ke tabel 'users'
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role' => 'admin',
        ]);

        return redirect()->route('admins.index')->with('success', 'Admin created successfully.');
    }
        // Method untuk menghapus admin
    public function destroy($id)
    {
        // Cari admin berdasarkan ID
        $admin = User::findOrFail($id);
        $admin->delete();

        // Redirect ke halaman daftar admin dengan pesan sukses
        return redirect()->route('admins.index')->with('success', 'Admin deleted successfully.');
    }

}
