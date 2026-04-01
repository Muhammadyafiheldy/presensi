<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    // Menampilkan riwayat logbook khusus untuk 1 peserta
    public function logbooks($id)
    {
        $intern = \App\Models\Intern::with(['logbooks' => function ($query) {
            // Urutkan dari tanggal terbaru ke terlama
            $query->orderBy('tanggal', 'desc');
        }])->findOrFail($id);

        return view('admin.interns.logbooks', compact('intern'));
    }
}
