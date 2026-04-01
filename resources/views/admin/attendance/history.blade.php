@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Absensi</h2>
            <p class="text-sm text-gray-500 mt-1">
                Menampilkan data kehadiran untuk: <span class="font-bold text-blue-600">{{ $intern->nama }}</span>
                ({{ $intern->divisi }})
            </p>
        </div>
        <a href="{{ url()->previous() }}"
            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
            &larr; Kembali
        </a>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th rowspan="2" class="py-3 px-4 border text-center text-sm font-semibold text-gray-700 align-middle">
                        Tanggal</th>
                    <th rowspan="2"
                        class="py-3 px-4 border text-center text-sm font-semibold text-gray-700 align-middle">Status</th>
                    <th colspan="3"
                        class="py-2 px-4 border-b text-center text-sm font-semibold text-blue-700 bg-blue-50">DATA MASUK
                    </th>
                    <th colspan="3"
                        class="py-2 px-4 border-b text-center text-sm font-semibold text-orange-700 bg-orange-50">DATA
                        PULANG</th>
                    <th rowspan="2"
                        class="py-3 px-4 border text-center text-sm font-semibold text-gray-700 align-middle">Total Jam</th>
                </tr>
                <tr>
                    <th class="py-2 px-2 border-b border-r text-center text-xs font-semibold text-gray-600 bg-blue-50">Jam
                    </th>
                    <th class="py-2 px-2 border-b border-r text-center text-xs font-semibold text-gray-600 bg-blue-50">
                        Selfie</th>
                    <th class="py-2 px-2 border-b border-r text-center text-xs font-semibold text-gray-600 bg-blue-50">
                        Lokasi</th>
                    <th class="py-2 px-2 border-b border-r text-center text-xs font-semibold text-gray-600 bg-orange-50">Jam
                    </th>
                    <th class="py-2 px-2 border-b border-r text-center text-xs font-semibold text-gray-600 bg-orange-50">
                        Selfie</th>
                    <th class="py-2 px-2 border-b text-center text-xs font-semibold text-gray-600 bg-orange-50">Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $absen)
                    <tr class="hover:bg-gray-50 transition border-b">
                        <td class="py-3 px-4 border-r text-center text-sm font-medium text-gray-800">
                            {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l, d M Y') }}
                        </td>

                        <td class="py-3 px-4 border-r text-center text-sm">
                            @if (str_contains(strtolower($absen->status_masuk), 'izin'))
                                <span
                                    class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">{{ $absen->status_masuk }}</span>
                            @else
                                <span
                                    class="bg-{{ strtolower($absen->status_masuk) == 'terlambat' ? 'red' : 'green' }}-100 text-{{ strtolower($absen->status_masuk) == 'terlambat' ? 'red' : 'green' }}-700 px-2 py-1 rounded text-xs font-semibold capitalize">
                                    {{ $absen->status_masuk ?? 'Hadir' }}
                                </span>
                            @endif
                        </td>

                        <td
                            class="py-3 px-2 border-r text-center text-sm font-bold {{ $absen->jam_masuk ? 'text-blue-600' : 'text-gray-400' }}">
                            {{ $absen->jam_masuk ?? '--:--' }}
                        </td>
                        <td class="py-3 px-2 border-r text-center">
                            @if ($absen->last_selfie)
                                <a href="{{ asset('storage/' . $absen->last_selfie) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $absen->last_selfie) }}"
                                        class="w-10 h-10 rounded-full object-cover mx-auto border hover:scale-110 transition-transform">
                                </a>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 border-r text-center">
                            @if ($absen->last_lat)
                                <a href="https://www.google.com/maps?q={{ $absen->last_lat }},{{ $absen->last_long }}"
                                    target="_blank" class="text-blue-500 hover:text-blue-700">
                                    <span class="text-[10px] font-mono bg-blue-50 px-1 rounded">Cek Lokasi</span>
                                </a>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>

                        <td
                            class="py-3 px-2 border-r text-center text-sm font-bold {{ $absen->jam_pulang ? 'text-orange-600' : 'text-gray-400' }}">
                            {{ $absen->jam_pulang ?? '--:--' }}
                        </td>
                        <td class="py-3 px-2 border-r text-center">
                            @if ($absen->selfie_pulang)
                                <a href="{{ asset('storage/' . $absen->selfie_pulang) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $absen->selfie_pulang) }}"
                                        class="w-10 h-10 rounded-full object-cover mx-auto border hover:scale-110 transition-transform">
                                </a>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 border-r text-center">
                            @if ($absen->lat_pulang)
                                <a href="https://www.google.com/maps?q={{ $absen->lat_pulang }},{{ $absen->long_pulang }}"
                                    target="_blank" class="text-orange-500 hover:text-orange-700">
                                    <span class="text-[10px] font-mono bg-orange-50 px-1 rounded">Cek Lokasi</span>
                                </a>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>

                        <td class="py-3 px-4 text-center text-sm font-bold text-gray-700">
                            {{ $absen->total_jam_kerja ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-gray-500 italic">Belum ada riwayat absensi untuk
                            peserta ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $attendances->links() }}
    </div>
@endsection
