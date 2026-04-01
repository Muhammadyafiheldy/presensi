@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Rekapitulasi Absensi Keseluruhan</h2>
            <p class="text-sm text-gray-500 mt-1">Laporan kehadiran berdasarkan masa magang aktif per peserta.</p>
        </div>

        <form action="{{ route('admin.attendance.recap') }}" method="GET" class="flex gap-2">
            @php
                $bulanIndo = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ];
            @endphp

            <select name="bulan" class="border-gray-300 rounded-md shadow-sm text-sm">
                @foreach ($bulanIndo as $angka => $nama)
                    <option value="{{ str_pad($angka, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == $angka ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>

            <select name="tahun" class="border-gray-300 rounded-md shadow-sm text-sm">
                @for ($i = date('Y'); $i >= date('Y') - 2; $i--)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>

            <button type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 transition">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Nama / Divisi</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-700">Hari Kerja</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-700">Hadir</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-700">Terlambat</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-700">Izin/Sakit</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-700">Alfa</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($interns as $intern)
                    <tr class="hover:bg-gray-50 transition border-b">
                        <td class="py-3 px-4 border-r">
                            <div class="text-sm font-bold text-gray-800">{{ $intern->nama }}</div>
                            <div class="text-[10px] uppercase tracking-wider text-gray-500">{{ $intern->divisi }}</div>
                        </td>

                        {{-- Kolom baru: Menampilkan berapa hari dia seharusnya masuk di bulan tersebut --}}
                        <td class="py-3 px-4 border-r text-center text-sm text-gray-600">
                            {{ $intern->hari_wajib }} Hari
                        </td>

                        <td class="py-3 px-4 border-r text-center font-bold text-green-600">
                            {{ $intern->total_hadir }}
                        </td>

                        <td class="py-3 px-4 border-r text-center font-bold text-red-500">
                            {{ $intern->total_terlambat }}
                        </td>

                        <td class="py-3 px-4 border-r text-center font-bold text-yellow-600">
                            {{ $intern->total_izin }}
                        </td>

                        <td class="py-3 px-4 border-r text-center">
                            <span
                                class="inline-block px-2 py-1 rounded text-xs font-bold {{ $intern->total_alfa > 0 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-400' }}">
                                {{ $intern->total_alfa }} Hari
                            </span>
                        </td>

                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('admin.attendance.history', $intern->id) }}"
                                class="text-xs font-bold text-blue-600 hover:text-blue-800 uppercase tracking-tighter">Detail
                                &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-gray-500 italic">
                            Tidak ada data peserta magang untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
