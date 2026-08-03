<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Services\NotifikasiService;

class NotifikasiController extends Controller
{   ////menginisialisasi notifikasiservice
    public function __construct(
        protected NotifikasiService $service
    ) {}

    //mengambil 10 notif yang belum dibaca 
    public function unread(Request $request)
    {
        $notifs = Notifikasi::where('id_user', auth()->id())
            ->where('is_read', false) //notif yg blm dibaca
            ->latest()
            ->limit(10)
            ->get(['id_notif', 'judul', 'pesan', 'tipe', 'url', 'created_at']);

        return response()->json([
            'count' => $notifs->count(),
            'data'  => $notifs,
        ]);
    }

    //menandai seluruh notif yang belum dibaca menjadi dibuaca
    public function markAllRead()
    {
        Notifikasi::where('id_user', auth()->id())
            ->where('is_read', false)
            ->update([ //ubah is read
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    //menandai satu notif tertentu sebagai dibaca
    public function markRead($id)
    {
        $notif = Notifikasi::where('id_notif', $id)
            ->where('id_user', auth()->id())
            ->first();

        if ($notif) {
            $notif->markAsRead();
        }

        return response()->json(['success' => true]);
    }
    //menghapus notifikasi
    public function destroy($id)
    {
        $notif = Notifikasi::where('id_notif', $id)
            ->where('id_user', auth()->id())
            ->first();

        if (!$notif) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $notif->delete();

        return response()->json(['success' => true]);
    }
}