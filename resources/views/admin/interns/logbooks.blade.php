@extends('layouts.app')

@section('content')
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Catatan Harian (Logbook)</h2>
                <p class="text-gray-600 mt-1">Peserta: <span class="font-semibold text-blue-600">{{ $intern->nama }}</span> -
                    {{ $intern->divisi }}</p>
            </div>
            <a href="{{ route('admin.interns.index') }}"
                class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline text-sm font-medium transition">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700 w-32">Tanggal</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Aktivitas / Kegiatan</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold text-gray-700 w-48">Dokumentasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($intern->logbooks as $logbook)
                    <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                        <td class="py-4 px-4 text-sm text-gray-800 align-top font-medium">
                            {{ \Carbon\Carbon::parse($logbook->tanggal)->format('d M Y') }}
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-600 align-top whitespace-pre-line">
                            {{ $logbook->kegiatan }}
                        </td>
                        <td class="py-4 px-4 text-center align-top">
                            @if ($logbook->dokumentasi)
                                <a href="{{ asset('storage/' . $logbook->dokumentasi) }}" target="_blank"
                                    class="inline-block group">
                                    <img src="{{ asset('storage/' . $logbook->dokumentasi) }}"
                                        class="w-24 h-24 object-cover rounded-lg border border-gray-200 shadow-sm group-hover:scale-105 transition-transform"
                                        alt="Dokumentasi">
                                </a>
                            @else
                                <span
                                    class="inline-block bg-gray-100 text-gray-500 text-xs px-3 py-1 rounded-full italic">Tanpa
                                    Foto</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-gray-500 italic">Peserta ini belum mengisi catatan
                            harian apa pun.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
