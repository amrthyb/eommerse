<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Menampilkan semua notifikasi
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(10);
        return view('admin.notifications.index', compact('notifications'));
    }

    // Menampilkan notifikasi dan arahkan ke detail sesuai tipe
    public function show($id)
{
    $notification = auth()->user()->notifications()->findOrFail($id);
    $notification->markAsRead();

    if ($notification->type === 'App\Notifications\NewOrder') {
        return redirect()->route('orders.show', $notification->data['order_id']);
    } elseif ($notification->type === 'App\Notifications\NewProduct') {
        return redirect()->route('products.show', $notification->data['product_id']);
    } elseif ($notification->type === 'App\Notifications\NewUserRegistered') {
        return redirect()->route('users.show', $notification->data['user_id']);
    } elseif ($notification->type === 'App\Notifications\OrderStatusChanged') {
        return redirect()->route('orders.show', $notification->data['order_id']);
    }

    return redirect()->route('notifications.index');
}


    // Menghapus notifikasi
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        return redirect()->route('notifications.index')->with('success', 'Notifikasi berhasil dihapus.');
    }

    // Ambil jumlah notifikasi belum dibaca
    public function unreadCount()
    {
        $unreadCount = auth()->user()->unreadNotifications->count();
        return response()->json(['unread_count' => $unreadCount]);
    }
    public function markAsRead($id)
{
    $notification = auth()->user()->notifications()->find($id);

    if ($notification && is_null($notification->read_at)) {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    return response()->json(['error' => 'Notifikasi tidak ditemukan atau sudah dibaca'], 404);
}

}
