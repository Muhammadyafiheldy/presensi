<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class InternController extends Controller
{
    // 1. Mengambil semua data peserta magang
    public function index()
    {
        $interns = Intern::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar data peserta magang',
            'data'    => $interns
        ], 200);
    }

    // 2. Mengambil detail data 1 peserta berdasarkan ID
    public function show($id)
    {
        $intern = Intern::find($id);

        if ($intern) {
            return response()->json([
                'success' => true,
                'message' => 'Detail peserta magang',
                'data'    => $intern
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data peserta tidak ditemukan',
        ], 404);
    }

    public function updateProfile(Request $request)
    {
        $intern = $request->user();

        $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'foto' => 'nullable|string',
        ]);

        // 1. Update nama
        if ($request->has('nama')) {
            $intern->nama = $request->nama;
        }

        // 2. Update foto dari Cloudinary (URL)
        if ($request->filled('foto')) {
            $intern->foto = $request->foto;
        }

        $intern->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $intern
        ]);
    }

    // 3. Mengambil data peserta berdasarkan hasil SCAN QR CODE (qr_token)
    public function getByQr($token)
    {
        $intern = Intern::where('qr_token', $token)->first();

        if ($intern) {
            return response()->json([
                'success' => true,
                'message' => 'Data peserta valid',
                'data'    => $intern
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token QR tidak valid atau tidak ditemukan',
        ], 404);
    }

    // 4. FUNGSI BARU: Untuk Login dari aplikasi Flutter
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Cari data peserta berdasarkan email
        $intern = Intern::where('email', $request->email)->first();

        // Cek apakah email terdaftar dan passwordnya cocok
        if (!$intern || !Hash::check($request->password, $intern->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // Buat Token API (Sanctum) agar user bisa mengakses rute yang dilindungi
        $token = $intern->createToken('intern_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data'    => $intern,
            'token'   => $token // Token ini akan disimpan di Flutter
        ], 200);
    }

    public function changePassword(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
            // 'confirmed' mewajibkan adanya field 'new_password_confirmation' yang isinya sama persis
        ], [
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        $intern = $request->user();

        // 2. Cek apakah password lama yang dimasukkan sesuai dengan di database
        if (!Hash::check($request->old_password, $intern->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.'
            ], 400);
        }

        // 3. Jika cocok, ganti dengan password baru yang sudah di-hash
        $intern->password = Hash::make($request->new_password);
        $intern->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.'
        ], 200);
    }
}
