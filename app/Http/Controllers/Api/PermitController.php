<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use App\Models\Attendance;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermitController extends Controller
{
    // 1. Riwayat izin user
    public function index(Request $request)
    {
        $permits = Permit::where('intern_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pengajuan izin',
            'data'    => $permits
        ], 200);
    }

    // 2. Kirim pengajuan izin
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string',
            'bukti_file'      => ['nullable', 'url', 'regex:/cloudinary\.com/']
        ]);

        $permit = Permit::create([
            'intern_id'        => $request->user()->id,
            'tanggal_mulai'    => $request->tanggal_mulai,
            'tanggal_selesai'  => $request->tanggal_selesai,
            'alasan'           => $request->alasan,
            'bukti_file'       => $request->bukti_file,
            'status'           => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan izin berhasil dikirim',
            'data'    => $permit
        ], 201);
    }

    // 3. Update status izin oleh admin
    public function updateStatus(Request $request, Permit $permit)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        // Update status
        $permit->update([
            'status' => $request->status
        ]);

        // Jika disetujui → generate absensi
        if ($permit->status === 'approved') {

            $period = CarbonPeriod::create(
                $permit->tanggal_mulai,
                $permit->tanggal_selesai
            );

            foreach ($period as $date) {

                if ($date->isWeekend()) {
                    continue;
                }

                Attendance::updateOrCreate(
                    [
                        'intern_id' => $permit->intern_id,
                        'tanggal'   => $date->format('Y-m-d'),
                    ],
                    [
                        'status_masuk' => 'Izin: ' . Str::limit($permit->alasan, 50),
                        'jam_masuk'    => null,
                        'jam_pulang'   => null,
                    ]
                );
            }
        }

        // Jika ditolak → hapus absensi izin
        elseif ($permit->status === 'rejected') {

            Attendance::where('intern_id', $permit->intern_id)
                ->whereBetween('tanggal', [
                    $permit->tanggal_mulai,
                    $permit->tanggal_selesai
                ])
                ->where('status_masuk', 'LIKE', 'Izin%')
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Status izin berhasil diperbarui',
            'data'    => $permit
        ], 200);
    }
}
