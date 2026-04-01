@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Daftar Peserta Magang</h2>
        <a href="{{ route('admin.interns.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Peserta
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-2 px-4 border-b text-left">Nama</th>
                    <th class="py-2 px-4 border-b text-left">Divisi</th>
                    <th class="py-2 px-4 border-b text-left">Asal</th>
                    <th class="py-2 px-4 border-b text-left">Periode</th>
                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($interns as $intern)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b">{{ $intern->nama }}</td>
                        <td class="py-2 px-4 border-b">{{ $intern->divisi }}</td>
                        <td class="py-2 px-4 border-b">{{ $intern->asal }}</td>
                        <td class="py-2 px-4 border-b">
                            {{ \Carbon\Carbon::parse($intern->tanggal_mulai)->format('d/m/Y') }} -
                            {{ \Carbon\Carbon::parse($intern->tanggal_selesai)->format('d/m/Y') }}
                        </td>

                        <td class="py-3 px-4 border-b text-center space-x-2 text-sm whitespace-nowrap">
                            {{-- <a href="{{ route('admin.interns.id_card', $intern->id) }}" target="_blank"
                                class="text-green-600 hover:underline">ID Card</a> --}}

                            {{-- <span class="text-gray-300">|</span>

                            <a href="{{ route('admin.interns.logbooks', $intern->id) }}"
                                class="text-indigo-600 font-medium hover:text-indigo-800 hover:underline">Logbook</a>

                            <span class="text-gray-300">|</span> --}}

                            <a href="{{ route('admin.interns.edit', $intern->id) }}"
                                class="text-yellow-600 hover:underline">Edit</a>

                            <span class="text-gray-300">|</span>

                            <form action="{{ route('admin.interns.destroy', $intern->id) }}" method="POST"
                                class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Belum ada data peserta magang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $interns->links() }}
    </div>
@endsection
