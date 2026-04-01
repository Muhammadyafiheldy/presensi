@extends('layouts.app')

@section('content')
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-6 sm:p-8">

            <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-100">
                <h2 class="text-2xl font-bold text-gray-800">Tambah Peserta Magang</h2>
                <a href="{{ route('admin.interns.index') }}"
                    class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline text-sm font-medium transition">
                    &larr; Kembali ke Daftar
                </a>
            </div>

            <div class="max-w-4xl">
                <form action="{{ route('admin.interns.store') }}" method="POST">
                    @csrf
                    <div class="grid gap-6 sm:grid-cols-2">

                        <div class="sm:col-span-2">
                            <label for="nama" class="block mb-2 text-sm font-semibold text-gray-700">Nama
                                Lengkap</label>
                            <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                                placeholder="Masukkan nama lengkap peserta" required>
                            @error('nama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full">
                            <label for="email" class="block mb-2 text-sm font-semibold text-gray-700">Alamat
                                Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                                placeholder="masukkan email" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full">
                            <label for="divisi" class="block mb-2 text-sm font-semibold text-gray-700">Divisi /
                                Penempatan</label>
                            <input type="text" name="divisi" id="divisi" value="{{ old('divisi') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                                placeholder="masukkan nama divisi" required>
                            @error('divisi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="asal" class="block mb-2 text-sm font-semibold text-gray-700">Asal Instansi
                                (Sekolah/Universitas)</label>
                            <input type="text" name="asal" id="asal" value="{{ old('asal') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                                placeholder="Masukkan nama sekolah atau perguruan tinggi" required>
                            @error('asal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full">
                            <label for="tanggal_mulai" class="block mb-2 text-sm font-semibold text-gray-700">Tanggal
                                Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                value="{{ old('tanggal_mulai') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                                required>
                            @error('tanggal_mulai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full">
                            <label for="tanggal_selesai" class="block mb-2 text-sm font-semibold text-gray-700">Tanggal
                                Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                value="{{ old('tanggal_selesai') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                                required>
                            @error('tanggal_selesai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex mt-8 border-t border-gray-100 pt-6">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg focus:ring-4 focus:ring-blue-200 hover:bg-blue-700 transition shadow-sm">
                            Simpan Data Peserta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
