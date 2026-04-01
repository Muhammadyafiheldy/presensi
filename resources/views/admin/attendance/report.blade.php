@extends('layouts.app')

@section('content')
    {{-- Library Alpine.js untuk fitur Modal/Pop-up --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div x-data="{ showModal: false, imgSource: '' }">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Monitoring Kehadiran Harian</h2>
                <p class="text-sm text-gray-500">Seluruh data masuk, pulang, dan alasan keterangan peserta magang hari ini.
                </p>
            </div>
            <a href="{{ route('admin.attendance.recap') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition font-semibold text-sm shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Rekap Bulanan
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-100 uppercase text-[10px] tracking-wider text-gray-700 font-bold">
                    <tr>
                        <th rowspan="2" class="py-3 px-4 border align-middle">Nama / Divisi</th>
                        <th rowspan="2" class="py-3 px-4 border align-middle text-center">Status</th>
                        <th colspan="3" class="py-2 px-4 border text-center text-blue-700 bg-blue-50">Data Absen Masuk
                        </th>
                        {{-- Colspan Data Absen Pulang disesuaikan menjadi 3 --}}
                        <th colspan="3" class="py-2 px-4 border text-center text-orange-700 bg-orange-50">Data Absen
                            Pulang</th>
                        <th rowspan="2" class="py-3 px-4 border align-middle text-center">Total Jam Kerja</th>
                    </tr>
                    <tr class="bg-gray-50">
                        {{-- Sub-Header Masuk --}}
                        <th class="py-2 px-2 border">Jam</th>
                        <th class="py-2 px-2 border">Foto</th>
                        <th class="py-2 px-2 border bg-blue-100 text-blue-800">Alasan Terlambat</th>

                        {{-- Sub-Header Pulang --}}
                        <th class="py-2 px-2 border">Jam</th>
                        <th class="py-2 px-2 border">Foto</th>
                        <th class="py-2 px-2 border bg-orange-100 text-orange-800">Keterangan Pulang</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($interns as $intern)
                        @php
                            $absen = $intern->attendances->first();
                        @endphp
                        <tr class="hover:bg-gray-50 transition border-b group">
                            <td class="py-3 px-4 border-r">
                                <div class="font-bold text-gray-800">{{ $intern->nama }}</div>
                                <div class="text-[9px] text-gray-400 uppercase leading-none">{{ $intern->divisi }}</div>
                            </td>

                            <td class="py-3 px-4 border-r text-center">
                                @if ($absen)
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ strtolower($absen->status) == 'terlambat' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $absen->status }}
                                    </span>
                                @else
                                    <span class="text-gray-300 italic text-[10px]">Belum Absen</span>
                                @endif
                            </td>

                            {{-- DETAIL MASUK --}}
                            <td
                                class="py-3 px-2 border-r text-center font-bold {{ $absen?->jam_masuk ? 'text-blue-600' : 'text-gray-300' }}">
                                {{ $absen?->jam_masuk ?? '--:--' }}
                            </td>
                            <td class="py-3 px-2 border-r text-center">
                                @if ($absen?->last_selfie)
                                    <button @click="showModal = true; imgSource = '{{ $absen->last_selfie }}'"
                                        class="focus:outline-none">
                                        <img src="{{ $absen->last_selfie }}"
                                            class="w-10 h-10 rounded shadow-sm mx-auto object-cover border hover:scale-110 transition-transform">
                                    </button>
                                @else
                                    <span class="text-gray-200">-</span>
                                @endif
                            </td>
                            <td
                                class="py-3 px-2 border-r text-center text-[10px] bg-blue-50/30 min-w-[130px] italic text-gray-600">
                                {{ $absen?->keterangan_masuk ?? '-' }}
                            </td>

                            {{-- DETAIL PULANG --}}
                            <td
                                class="py-3 px-2 border-r text-center font-bold {{ $absen?->jam_pulang ? 'text-orange-600' : 'text-gray-300' }}">
                                {{ $absen?->jam_pulang ?? '--:--' }}
                            </td>
                            <td class="py-3 px-2 border-r text-center">
                                @if ($absen?->selfie_pulang)
                                    <button @click="showModal = true; imgSource = '{{ $absen->selfie_pulang }}'"
                                        class="focus:outline-none">
                                        <img src="{{ $absen->selfie_pulang }}"
                                            class="w-10 h-10 rounded shadow-sm mx-auto object-cover border hover:scale-110 transition-transform">
                                    </button>
                                @else
                                    <span class="text-gray-200">-</span>
                                @endif
                            </td>
                            <td
                                class="py-3 px-2 border-r text-center text-[10px] bg-orange-50/30 min-w-[130px] italic text-gray-600">
                                {{ $absen?->keterangan_pulang ?? '-' }}
                            </td>

                            {{-- KOLOM TOTAL JAM AKHIR --}}
                            <td class="py-3 px-4 text-center font-bold text-gray-700 bg-gray-50">
                                {{ $absen?->total_jam_kerja ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            {{-- Colspan disesuaikan menjadi 9 --}}
                            <td colspan="9" class="py-10 text-center text-gray-400 italic">Data peserta tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL PREVIEW FOTO --}}
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85" @click="showModal = false"
            style="display: none;">

            <div class="relative inline-block" @click.stop>
                <button @click="showModal = false"
                    class="absolute -top-12 right-0 text-white text-4xl font-bold drop-shadow-md hover:text-red-500 transition-colors">&times;</button>
                <img :src="imgSource" class="max-h-[85vh] max-w-full rounded-md shadow-2xl object-contain">
            </div>
        </div>

    </div>
@endsection
