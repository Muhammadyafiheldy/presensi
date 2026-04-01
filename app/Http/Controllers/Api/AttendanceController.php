<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Permit;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    // 1. Cek Absensi Hari Ini
    public function today(Request $request)
    {
        $intern = $request->user();
        $today = Carbon::today()->toDateString();
        $attendance = $intern->attendances()->where('tanggal', $today)->first();

        if ($attendance) {
            return response()->json(['success' => true, 'message' => 'Data absensi hari ini', 'data' => $attendance], 200);
        }
        return response()->json(['success' => true, 'message' => 'Belum absen hari ini', 'data' => null], 200);
    }

    // 2. Ambil Riwayat Absensi
    public function history(Request $request)
    {
        $intern = $request->user();

        // Gabung Keterangan untuk Flutter
        $riwayatAbsen = $intern->attendances()->orderBy('tanggal', 'desc')->get()->map(function ($absen) {
            $ket = [];
            if ($absen->keterangan_masuk) $ket[] = $absen->keterangan_masuk;
            if ($absen->keterangan_pulang) $ket[] = $absen->keterangan_pulang;

            return [
                'id'         => $absen->id,
                'intern_id'  => $absen->intern_id,
                'tanggal'    => $absen->tanggal,
                'jam_masuk'  => $absen->jam_masuk,
                'jam_pulang' => $absen->jam_pulang,
                'status'     => $absen->status ?? 'Hadir',
                'keterangan' => empty($ket) ? '-' : implode(' | ', $ket),
            ];
        })->toArray();

        $tanggalAbsen = array_column($riwayatAbsen, 'tanggal');

        // Data Izin
        $izins = \App\Models\Permit::where('intern_id', $intern->id)
            ->whereIn('status', ['approved', 'Approved', 'disetujui', 'Disetujui'])
            ->get();

        $riwayatIzin = [];
        $tanggalIzin = [];

        foreach ($izins as $izin) {
            $period = \Carbon\CarbonPeriod::create($izin->tanggal_mulai, $izin->tanggal_selesai);
            foreach ($period as $date) {
                if (!$date->isWeekend()) {
                    $tglString = $date->format('Y-m-d');
                    $riwayatIzin[] = [
                        'id'         => rand(90000, 99999),
                        'intern_id'  => $intern->id,
                        'tanggal'    => $tglString,
                        'jam_masuk'  => '--:--',
                        'jam_pulang' => '--:--',
                        'status'     => 'Izin',
                        'keterangan' => $izin->alasan ?? 'Izin Disetujui',
                    ];
                    $tanggalIzin[] = $tglString;
                }
            }
        }

        // Data Alfa
        $riwayatAlfa = [];
        $startDate = clone $intern->created_at;
        $endDate = \Carbon\Carbon::yesterday();

        if ($startDate->lte($endDate)) {
            $periodAlfa = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($periodAlfa as $date) {
                if (!$date->isWeekend()) {
                    $tglCek = $date->format('Y-m-d');
                    if (!in_array($tglCek, $tanggalAbsen) && !in_array($tglCek, $tanggalIzin)) {
                        $riwayatAlfa[] = [
                            'id'         => rand(100000, 199999),
                            'intern_id'  => $intern->id,
                            'tanggal'    => $tglCek,
                            'jam_masuk'  => '--:--',
                            'jam_pulang' => '--:--',
                            'status'     => 'Alfa',
                            'keterangan' => 'Tanpa Keterangan',
                        ];
                    }
                }
            }
        }

        $allRiwayat = collect($riwayatAbsen)->merge($riwayatIzin)->merge($riwayatAlfa)->sortByDesc('tanggal')->values()->all();

        return response()->json([
            'success' => true,
            'message' => empty($allRiwayat) ? 'Belum ada riwayat absensi' : 'Riwayat absensi berhasil diambil',
            'data' => $allRiwayat
        ], 200);
    }

    // 5. Mengambil data absensi hari ini (Widget Info)
    public function todayAttendance(Request $request)
    {
        $intern = $request->user();
        $today = \Carbon\Carbon::today()->toDateString();
        $attendance = $intern->attendances()->where('tanggal', $today)->first();

        if ($attendance) {
            return response()->json([
                'success' => true,
                'message' => 'Data absensi hari ini ditemukan',
                'data' => [
                    'tanggal'    => $attendance->tanggal,
                    'jam_masuk'  => $attendance->jam_masuk,
                    'jam_pulang' => $attendance->jam_pulang,
                    'status'     => $attendance->status
                ]
            ], 200);
        }

        return response()->json(['success' => true, 'message' => 'Belum absen hari ini', 'data' => null], 200);
    }

    // 3. Update Selfie & Alasan (DARI FLUTTER) - OTOMATIS MASUK/PULANG
    // 3. Update Selfie & Alasan (DARI FLUTTER) - OTOMATIS MASUK/PULANG
    public function preScan(Request $request)
    {
        $request->validate([
            'last_selfie' => 'required|string',
            'keterangan'  => 'nullable|string'
        ]);

        $intern = $request->user();
        $today  = Carbon::today()->toDateString();
        $now    = Carbon::now('Asia/Jakarta');
        $jamSekarang = $now->format('H:i:s');

        $attendance = $intern->attendances()->where('tanggal', $today)->first();

        $isPulang = ($attendance && $attendance->last_selfie != null);

        if (!$isPulang) {
            // MASUK
            if ($jamSekarang > "14:01:00" && empty($request->keterangan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda terlambat! Wajib mengisi alasan.'
                ], 400);
            }

            if (!$attendance) {
                $attendance = $intern->attendances()->create([
                    'intern_id' => $intern->id,
                    'tanggal' => $today
                ]);
            }

            $attendance->last_selfie = $request->last_selfie;

            if ($request->filled('keterangan')) {
                $attendance->keterangan_masuk = $request->keterangan;
            }
        } else {
            // PULANG
            if ($jamSekarang < "12:00:00" && empty($request->keterangan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pulang lebih awal wajib mengisi alasan!'
                ], 400);
            }

            $attendance->selfie_pulang = $request->last_selfie;

            if ($request->filled('keterangan')) {
                $attendance->keterangan_pulang = $request->keterangan;
            }
        }

        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => $isPulang
                ? 'Selfie Pulang berhasil dikirim.'
                : 'Selfie Masuk berhasil dikirim.',
            'data' => $attendance
        ]);
    }
}
