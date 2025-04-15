<?php

namespace App\Http\Controllers;

use App\Notifications\NewOrder;
use App\Notifications\NewProduct;
use App\Notifications\NewUserRegistered;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Menampilkan semua notifikasi
    public function index()
    {
        $notifications = auth()
            ->user()
            ->notifications()
            ->whereIn('type', [NewUserRegistered::class, OrderStatusChanged::class, NewOrder::class])
            ->latest()
            ->paginate(10);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        // Mencari notifikasi berdasarkan ID untuk user yang sedang login
        $notification = auth()->user()->notifications()->findOrFail($id);
        // Tandai notifikasi sebagai telah dibaca
        $notification->markAsRead();

        // Jika tipe tidak dikenali, kembali ke halaman notifikasi
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
        $unreadCount = auth()->user()->unreadNotifications()
        ->whereIn('type', [NewUserRegistered::class, OrderStatusChanged::class, NewOrder::class])
            ->count();
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
