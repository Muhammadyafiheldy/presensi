<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function scanPage()
    {
        return view('admin.attendance.scan');
    }

    public function processScan(Request $request)
    {
        $intern = Intern::where('qr_token', $request->qr_token)->first();
        if (!$intern) return response()->json(['success' => false, 'message' => 'Peserta tidak terdaftar!'], 404);

        $today = Carbon::today()->toDateString();
        // Pastikan kita menggunakan zona waktu yang konsisten di seluruh variabel
        $now = Carbon::now('Asia/Jakarta');
        $jamSekarang = $now->format('H:i:s');

        $attendance = Attendance::where('intern_id', $intern->id)->where('tanggal', $today)->first();

        // 1. Pastikan user sudah melakukan verifikasi selfie
        if (!$attendance || ($attendance->last_selfie == null && $attendance->jam_masuk == null)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Peserta belum melakukan Verifikasi Kehadiran (Selfie) di Aplikasi HP.'
            ], 403);
        }

        // --- 2. LOGIKA SCAN ABSEN MASUK ---
        if ($attendance->jam_masuk == null) {
            $status = ($jamSekarang <= "08:01:00") ? 'hadir' : 'terlambat';

            if ($status == 'terlambat' && empty($attendance->keterangan_masuk)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta terlambat tapi belum mengisi alasan di aplikasi!'
                ], 403);
            }

            $attendance->update([
                'jam_masuk' => $jamSekarang,
                'status' => $status
            ]);

            return response()->json([
                'success' => true,
                'message' => "Masuk: $intern->nama (" . ($status == 'hadir' ? 'Tepat Waktu' : 'Terlambat') . ")",
                'tipe' => 'masuk',
                'nama_peserta' => $intern->nama
            ]);
        }

        // --- 3. LOGIKA SCAN ABSEN PULANG ---
        if ($attendance->jam_pulang) {
            return response()->json(['success' => false, 'message' => "Peserta atas nama $intern->nama sudah absen pulang hari ini!"]);
        }

        if ($attendance->selfie_pulang == null) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Peserta belum melakukan Verifikasi Kepulangan (Selfie) di Aplikasi HP.'
            ], 403);
        }

        if ($jamSekarang < "17:00:00" && empty($attendance->keterangan_pulang)) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta pulang lebih awal tapi belum mengisi alasan kompensasi di aplikasi!'
            ], 403);
        }

        // --- 4. PERHITUNGAN TOTAL JAM KERJA AKURAT (DENGAN ISTIRAHAT 12.00 - 13.30) ---

        // Parsing jam masuk dengan menggabungkan tanggal hari ini dan zona waktu yang tepat
        $jamMasuk = Carbon::parse($attendance->tanggal . ' ' . $attendance->jam_masuk, 'Asia/Jakarta');
        $jamPulang = $now;

        // Tentukan batas waktu istirahat
        $mulaiIstirahat = Carbon::parse($attendance->tanggal . ' 12:00:00', 'Asia/Jakarta');
        $selesaiIstirahat = Carbon::parse($attendance->tanggal . ' 13:30:00', 'Asia/Jakarta');

        // Cari irisan waktu (overlap) antara jam keberadaan peserta dengan jam istirahat
        $overlapStart = $jamMasuk->copy()->max($mulaiIstirahat);
        $overlapEnd = $jamPulang->copy()->min($selesaiIstirahat);

        // Jika ada irisan waktu (overlapStart lebih kecil dari overlapEnd), hitung durasinya dalam menit
        $durasiIstirahatMenit = 0;
        if ($overlapStart->lt($overlapEnd)) {
            $durasiIstirahatMenit = $overlapStart->diffInMinutes($overlapEnd);
        }

        // Hitung total menit mentah, lalu kurangi dengan durasi istirahat yang dihabiskan
        $totalMenitKotor = $jamMasuk->diffInMinutes($jamPulang);
        $totalMenitBersih = $totalMenitKotor - $durasiIstirahatMenit;

        // Konversi kembali menit bersih ke format Jam dan Menit
        $jamKerja = floor($totalMenitBersih / 60);
        $menitKerja = $totalMenitBersih % 60;

        $totalJam = "{$jamKerja} jam {$menitKerja} menit";

        $attendance->update([
            'jam_pulang' => $jamSekarang,
            'total_jam_kerja' => $totalJam
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pulang: $intern->nama. Jam Kerja: $totalJam",
            'tipe' => 'pulang',
            'nama_peserta' => $intern->nama
        ]);
    }

    public function report()
    {
        $interns = Intern::with(['attendances' => function ($query) {
            $query->where('tanggal', Carbon::today()->toDateString());
        }])->get();

        return view('admin.attendance.report', compact('interns'));
    }

    /**
     * Menampilkan Rekapitulasi Absensi Bulanan
     */
    public function recap(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $interns = Intern::withCount([
            'attendances as total_hadir' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                    ->whereIn('status', ['hadir', 'terlambat']);
            },
            'attendances as total_terlambat' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                    ->where('status', 'terlambat');
            },
            'attendances as total_izin' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                    ->whereIn('status', ['izin', 'sakit']);
            },
        ])
            ->get()
            ->map(function ($intern) use ($bulan, $tahun) {
                // 1. Total hari wajib dalam sebulan penuh (untuk informasi)
                $hariWajibUser = $this->countUserWorkingDays($bulan, $tahun, $intern->tanggal_mulai, $intern->tanggal_selesai);

                // 2. Hari kerja yang sudah terlewati (untuk hitung Alfa agar tidak otomatis penuh di awal)
                $hariSudahLewat = $this->countPassedWorkingDays($bulan, $tahun, $intern->tanggal_mulai, $intern->tanggal_selesai);

                // 3. Alfa = Hari yang sudah lewat - (Hadir + Izin)
                $intern->total_alfa = max(0, $hariSudahLewat - ($intern->total_hadir + $intern->total_izin));
                $intern->hari_wajib = $hariWajibUser;

                return $intern;
            });

        return view('admin.attendance.recap', compact('interns', 'bulan', 'tahun'));
    }

    /**
     * Menghitung total hari kerja (Senin-Jumat) dalam satu bulan penuh/masa magang
     */
    private function countUserWorkingDays($month, $year, $startDate, $endDate)
    {
        $startOfFilter = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfFilter = $startOfFilter->copy()->endOfMonth()->endOfDay();

        $userStart = $startDate ? Carbon::parse($startDate)->startOfDay() : $startOfFilter;
        $userEnd = $endDate ? Carbon::parse($endDate)->endOfDay() : $endOfFilter;

        $actualStart = $userStart->gt($startOfFilter) ? $userStart : $startOfFilter;
        $actualEnd = $userEnd->lt($endOfFilter) ? $userEnd : $endOfFilter;

        if ($actualStart->gt($actualEnd)) return 0;

        $workdays = 0;
        for ($date = $actualStart->copy(); $date->lte($actualEnd); $date->addDay()) {
            if ($date->isWeekday()) $workdays++;
        }
        return $workdays;
    }

    /**
     * Menghitung hari kerja yang SUDAH TERLEWATI (dari awal bulan/magang sampai HARI INI)
     */
    private function countPassedWorkingDays($month, $year, $startDate, $endDate)
    {
        $today = Carbon::today();
        $startOfFilter = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfFilter = $startOfFilter->copy()->endOfMonth()->endOfDay();

        // Jika filter di masa depan, hari yang lewat adalah 0
        if ($startOfFilter->isFuture()) return 0;

        $userStart = $startDate ? Carbon::parse($startDate)->startOfDay() : $startOfFilter;
        $userEnd = $endDate ? Carbon::parse($endDate)->endOfDay() : $endOfFilter;

        // Batas akhir hitungan adalah mana yang lebih dulu: Hari ini atau Akhir masa magang atau Akhir bulan filter
        $limitDate = $today->lt($endOfFilter) ? $today : $endOfFilter;
        if ($userEnd->lt($limitDate)) $limitDate = $userEnd;

        $actualStart = $userStart->gt($startOfFilter) ? $userStart : $startOfFilter;

        if ($actualStart->gt($limitDate)) return 0;

        $passedWorkdays = 0;
        for ($date = $actualStart->copy(); $date->lte($limitDate); $date->addDay()) {
            // Opsional: Jika Anda ingin hari ini BELUM dihitung Alfa (karena jam kerja belum habis), 
            // ganti .lte($limitDate) menjadi .lt($today)
            if ($date->isWeekday()) $passedWorkdays++;
        }

        return $passedWorkdays;
    }

    public function history($id)
    {
        $intern = Intern::findOrFail($id);
        $attendances = $intern->attendances()->orderBy('tanggal', 'desc')->paginate(10);
        return view('admin.attendance.history', compact('intern', 'attendances'));
    }
}
