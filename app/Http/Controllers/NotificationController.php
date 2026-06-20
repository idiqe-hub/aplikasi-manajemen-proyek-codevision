<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tampilkan daftar semua notifikasi milik user yang login.
     * Developer hanya melihat miliknya sendiri (sudah otomatis karena
     * notifikasi dikirim ke user yang tepat).
     * Admin melihat notifikasi overdue yang dikirim khusus ke admin.
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()                    // eager order by latest
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Tandai semua sebagai dibaca saat halaman dibuka
        // (Opsional — dimatikan agar user bisa manually tandai sendiri)

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi sudah dibaca.');
    }

    /**
     * Hapus satu notifikasi yang sudah dibaca milik user sendiri.
     * Notifikasi yang belum dibaca tidak bisa dihapus.
     */
    public function destroy(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        // Pastikan notifikasi sudah dibaca sebelum boleh dihapus
        if (is_null($notification->read_at)) {
            return back()->with('error', 'Notifikasi yang belum dibaca tidak dapat dihapus. Tandai sebagai dibaca terlebih dahulu.');
        }

        $notification->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Hapus semua notifikasi yang sudah dibaca milik user sendiri.
     * Notifikasi yang belum dibaca (read_at IS NULL) tidak ikut terhapus.
     */
    public function destroyAllRead()
    {
        $deleted = auth()->user()
            ->notifications()
            ->whereNotNull('read_at')
            ->delete();

        if ($deleted === 0) {
            return back()->with('info', 'Tidak ada notifikasi yang sudah dibaca untuk dihapus.');
        }

        return back()->with('success', "{$deleted} notifikasi yang sudah dibaca berhasil dihapus.");
    }
}
