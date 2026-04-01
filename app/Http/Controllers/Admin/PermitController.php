<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use Illuminate\Http\Request;

class PermitController extends Controller
{
    // 1. Fungsi untuk menampilkan halaman tabel izin
    public function index()
    {
        // Mengambil data izin terbaru beserta data pesertanya
        $permits = Permit::with('intern')->get();
        return view('admin.permits.index', compact('permits'));
    }

    // 2. FUNGSI UNTUK MENGKONFIRMASI PENGAJUAN (SETUJUI / TOLAK)
    public function updateStatus(Request $request, Permit $permit)
    {
        // 1. Tambahkan 'pending' ke dalam aturan validasi
        $request->validate([
            'status' => 'required|in:pending,disetujui,ditolak'
        ]);

        $permit->update([
            'status' => $request->status
        ]);

        // 2. Sesuaikan pesan suksesnya
        if ($request->status == 'pending') {
            $pesan = 'dibatalkan dan dikembalikan ke status Pending';
        } else {
            $pesan = $request->status == 'disetujui' ? 'disetujui' : 'ditolak';
        }

        return redirect()->route('admin.permits.index')
            ->with('success', "Pengajuan izin berhasil $pesan.");
    }
}
