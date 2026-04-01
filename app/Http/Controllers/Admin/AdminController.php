<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login'); // Pastikan file view ini sudah Anda buat sebelumnya
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.interns.index');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function index()
    {
        $interns = Intern::paginate(10); // Menambahkan pagination, 10 data per halaman
        return view('admin.interns.index', compact('interns'));
    }

    public function create()
    {
        return view('admin.interns.create');
    }

    public function store(Request $request)
    {
        // 1. Hapus aturan validasi password, karena tidak dikirim dari form
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:interns,email',
            'divisi' => 'required|string|max:255',
            'asal' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        // 2. Set password default secara otomatis di belakang layar
        $validated['password'] = Hash::make('123456');

        // 3. Generate Token QR Code
        $validated['qr_token'] = Str::uuid()->toString();

        Intern::create($validated);

        return redirect()->route('admin.interns.index');
    }

    public function edit(Intern $intern)
    {
        return view('admin.interns.edit', compact('intern'));
    }

    public function update(Request $request, Intern $intern)
    {
        // 1. Validasi diperbarui (abaikan pengecekan email kembar untuk user ini sendiri)
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:interns,email,' . $intern->id,
            'password' => 'nullable|string|min:6',
            'divisi' => 'required|string|max:255',
            'asal' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        // 2. Cek apakah admin mengisi password baru di form edit
        if ($request->filled('password')) {
            // Jika diisi, enkripsi password barunya
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // Jika kosong, hapus dari data agar password lama tidak tertimpa menjadi kosong
            unset($validated['password']);
        }

        $intern->update($validated);

        return redirect()->route('admin.interns.index');
    }

    public function destroy(Intern $intern)
    {
        $intern->delete();
        return redirect()->route('admin.interns.index');
    }

    // Fungsi show untuk menangani error rute resource default
    public function show(Intern $intern)
    {
        return redirect()->route('admin.interns.index');
    }

    // Fungsi khusus untuk menampilkan ID Card beserta QR Code
    public function idCard(Intern $intern)
    {
        // Pastikan nama view ini sesuai dengan lokasi file id_card.blade.php Anda
        // Jika filenya ada di resources/views/admin/id_card.blade.php, gunakan 'admin.id_card'
        // Jika ada di resources/views/admin/interns/id_card.blade.php, gunakan 'admin.interns.id_card'
        return view('admin.id_card', compact('intern'));
    }

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
