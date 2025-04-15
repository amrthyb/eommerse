<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\NewOrder;
use App\Notifications\NewProduct;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
class NotificationController extends Controller
    {
        // Menampilkan semua notifikasi milik user yang login
        public function index(Request $request)
        {
            $user = $request->user();
            // Ambil hanya notifikasi produk baru & status order
            $notifications = $user->notifications()
                ->whereIn('type', [
                    NewProduct::class,
                    OrderStatusChanged::class

                ])
                ->latest()
                ->get();

            // Format responsenya supaya rapi
            $data = $notifications->map(function ($notification) {
                $type = class_basename($notification->type); // "NewProduct" atau "OrderStatusChanged"

                return [
                    'id' => $notification->id,
                    'type' => $type,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'data' => $notification->data,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi produk baru & perubahan status order',
                'data' => $data
            ]);
        }



    public function markAsRead($id, Request $request)
    {
        $user = $request->user();

        $notification = DatabaseNotification::where('id', $id)
            ->where('notifiable_id', $user->id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read']);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }


    }
