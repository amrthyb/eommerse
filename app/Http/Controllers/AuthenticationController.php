<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthenticationController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validasi input form registrasi
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|numeric|min:10',
        ]);

        // Membuat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        Auth::login($user);

        // Redirect ke halaman dashboard sesuai dengan role
        if ($user->role == 'admin') {
            return redirect('dashboard')->with('success', 'Registration successful. Welcome to the admin dashboard.');
        } elseif ($user->role == 'user') {
            return redirect('dashboard')->with('success', 'Registration successful. Welcome to your dashboard.');
        }

        // Jika tidak ada role yang sesuai, arahkan ke halaman default
        return redirect('login')->with('success', 'Registration successful. Welcome!');
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // hal yang terpelukan apa dulu ? username & password
        // gimana dapatin username sama password.
        // setelah dapat mau ngapain ? bener atau salah password / username,e ? redirect dashboard ? home ? profile ?
        // Validasi form login
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek false or true
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'The provided credentials are incorrect.'])->withInput();
        }

        // Jika validasi berhasil Login ke aplikasi
        Auth::login($user);

        // Cek role admin & user
        if ($user->role == 'admin') {
            return redirect('dashboard')->with('success', 'Login as admin successful.');
        } elseif ($user->role == 'user') {
            return redirect('dashboard')->with('success', 'Login as user successful.');
        }

        return redirect('/home');
    }

    public function logout(Request $request)
    {
        return to_route('login')->with('success', 'You have logged out successfully!');
    }

    public function settingView()
    {
        return view('admin.settings.index');
    }

    public function emailChange(Request $request)
    {
        // Mendapatkan data pengguna yang sedang login
        $user = Auth::user();
        return view('admin.settings.index', compact('user'));
    }

    public function updateAccount(Request $request)
    {
        // Validasi data yang telah diinputkan
        // dd(Auth::user()->id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email'],
            'password' => 'nullable|min:8',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|numeric|min:10',
        ]);

        // Ambil pengguna yang sedang login
        $form = $request->except('password', '_token', 'password_confirmation');
        User::where('id', Auth::user()->id)->update($form);
        // Redirect ke halaman dashboard dengan pesan sukses
        return redirect('/dashboard')->with('success', 'Your account details have been updated successfully.');
    }

}
