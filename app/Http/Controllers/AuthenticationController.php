<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|numeric|min:10',
        ]);
        $role = Role::where('name','admin')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'role_id' => $role->id
        ]);
        // event(new Registered($user));

        Auth::login($user);

        Auth::user()->sendEmailVerificationNotification();

        if ($user->role == 'admin') {
            return redirect('dashboard')->with('success', 'Registration successful. Welcome to the admin dashboard.');
        }

        return redirect('dashboard')->with('success', 'Registration successful. Welcome!');
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['email' => 'The provided credentials are incorrect.'])
                ->withInput();
        }

        if ($user->role !== 'admin') {
            Auth::logout();
            return back()
                ->withErrors(['email' => 'You do not have admin privileges.'])
                ->withInput();
        }

        Auth::login($user);

        return redirect('dashboard')->with('success', 'Login as admin successful.');
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
        $user = Auth::user();
        return view('admin.settings.index', compact('user'));
    }

    public function updateAccount(Request $request)
    {
        // dd(Auth::user()->id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email'],
            'password' => 'nullable|min:8',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|numeric|min:10',
        ]);

        $form = $request->except('password', '_token', 'password_confirmation');
        $form['email_verified_at'] = null;
        $user = User::where('id', Auth::user()->id)->first();
        $user->update($form);
        $user->sendEmailVerificationNotification();

        return redirect('/dashboard')->with('success', 'Your account details have been updated successfully.');
    }

    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::ResetLinkSent ? back()->with(['status' => __($status)]) : back()->withErrors(['email' => __($status)]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                    'password' => Hash::make($password),
                ]);

            $user->save();

            event(new PasswordReset($user));
        });

        return $status === Password::PasswordReset ? redirect()->route('login')->with('status', __($status)) : back()->withErrors(['email' => [__($status)]]);
    }
}
