<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data dari database
        $logbooks = Logbook::where('intern_id', $request->user()->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // 2. Format ulang datanya untuk menambahkan URL lengkap
        $logbooks->map(function ($logbook) {
            $logbook->dokumentasi_url = $logbook->dokumentasi;
            return $logbook;
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar Logbook',
            'data'    => $logbooks
        ], 200);
    }

    // 2. Buat Logbook Baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'kegiatan'    => 'required|string',
            'dokumentasi' => 'nullable|string',
        ]);

        $logbook = Logbook::create([
            'intern_id'   => $request->user()->id,
            'tanggal'     => $request->tanggal,
            'kegiatan'    => $request->kegiatan,
            'dokumentasi' => $request->dokumentasi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Logbook berhasil ditambahkan',
            'data'    => $logbook
        ], 201);
    }

    // 3. Edit Logbook
    public function update(Request $request, $id)
    {
        $logbook = Logbook::where('id', $id)
            ->where('intern_id', $request->user()->id)
            ->first();

        if (!$logbook) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'tanggal'     => 'required|date',
            'kegiatan'    => 'required|string',
            'dokumentasi' => 'nullable|string',
        ]);

        $logbook->tanggal  = $request->tanggal;
        $logbook->kegiatan = $request->kegiatan;

        if ($request->dokumentasi) {
            $logbook->dokumentasi = $request->dokumentasi;
        }

        $logbook->save();

        return response()->json([
            'success' => true,
            'message' => 'Logbook berhasil diupdate',
            'data'    => $logbook
        ], 200);
    }

    private function getPublicIdFromUrl($url)
    {
        $parts = explode('/', $url);

        // Ambil setelah 'upload/'
        $uploadIndex = array_search('upload', $parts);

        if ($uploadIndex === false) return null;

        // Ambil path setelah upload/
        $publicPath = array_slice($parts, $uploadIndex + 1);

        // Hapus versi (v123456)
        if (isset($publicPath[0]) && str_starts_with($publicPath[0], 'v')) {
            array_shift($publicPath);
        }

        // Gabungkan kembali
        $publicIdWithExt = implode('/', $publicPath);

        // Hapus ekstensi file (.jpg, .png)
        return pathinfo($publicIdWithExt, PATHINFO_DIRNAME) . '/' . pathinfo($publicIdWithExt, PATHINFO_FILENAME);
    }

    private function deleteFromCloudinary($publicId)
    {
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $cloudName = env('CLOUDINARY_CLOUD_NAME');

        $timestamp = time();

        $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$apiSecret}");

        $response = \Illuminate\Support\Facades\Http::asForm()->post(
            "https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy",
            [
                'public_id' => $publicId,
                'api_key' => $apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]
        );

        return $response->json();
    }

    // 4. Hapus Logbook
    public function destroy(Request $request, $id)
    {
        $logbook = Logbook::where('id', $id)
            ->where('intern_id', $request->user()->id)
            ->first();

        if (!$logbook) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        if ($logbook->dokumentasi) {
            $publicId = $this->getPublicIdFromUrl($logbook->dokumentasi);

            if ($publicId) {
                $this->deleteFromCloudinary($publicId);
            }
        }

        $logbook->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logbook berhasil dihapus'
        ], 200);
    }
}
