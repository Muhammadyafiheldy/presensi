@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Daftar Pengajuan Izin Peserta</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Nama Peserta</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Tanggal Izin</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Alasan</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Bukti Surat</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Status</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permits as $permit)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="py-3 px-4 border-b">{{ $permit->intern->nama ?? 'Peserta Terhapus' }}</td>

                        <td class="py-3 px-4 border-b text-sm">
                            {{ \Carbon\Carbon::parse($permit->tanggal_mulai)->format('d M Y') }} - <br>
                            {{ \Carbon\Carbon::parse($permit->tanggal_selesai)->format('d M Y') }}
                        </td>

                        <td class="py-3 px-4 border-b text-sm text-gray-700">{{ $permit->alasan }}</td>

                        <td class="py-3 px-4 border-b text-center">
                            @if ($permit->bukti_file)
                                <a href="{{ $permit->bukti_file }}" target="_blank"
                                    class="text-blue-600 hover:text-blue-800 underline text-sm">
                                    Lihat Surat
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>

                        <td class="py-3 px-4 border-b text-center">
                            @if ($permit->status == 'pending')
                                <span
                                    class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">Pending</span>
                            @elseif($permit->status == 'disetujui')
                                <span
                                    class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Disetujui</span>
                            @else
                                <span
                                    class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">Ditolak</span>
                            @endif
                        </td>

                        <td class="py-3 px-4 border-b text-center space-x-1">

                            @if ($permit->status !== 'disetujui')
                                <form action="{{ route('admin.permits.update_status', $permit->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="disetujui">
                                    <button type="submit"
                                        class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 text-xs shadow-sm"
                                        onclick="return confirm('Yakin ingin menyetujui izin ini?')">Setujui</button>
                                </form>
                            @endif

                            @if ($permit->status !== 'ditolak')
                                <form action="{{ route('admin.permits.update_status', $permit->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="ditolak">
                                    <button type="submit"
                                        class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 text-xs shadow-sm"
                                        onclick="return confirm('Yakin ingin menolak izin ini?')">Tolak</button>
                                </form>
                            @endif

                            @if ($permit->status !== 'pending')
                                <form action="{{ route('admin.permits.update_status', $permit->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit"
                                        class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 text-xs shadow-sm"
                                        onclick="return confirm('Yakin ingin membatalkan keputusan ini?')">Batal</button>
                                </form>
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">Belum ada data pengajuan izin yang masuk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
