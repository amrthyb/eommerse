<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pengguna.lihat')->only(['show']);
        $this->middleware('permission:pengguna')->only(['index']);
    }
    public function index()
    {
        $users = User::where('role', 'user')->get();

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $orders = Order::where('user_id', $id)->orderBy('id')->get();
        // dd($orders);

        return view('admin.users.show', compact('user', 'orders'));
    }

    public function export()
    {
        return Excel::download(new UserExport(), 'users.xlsx');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}
