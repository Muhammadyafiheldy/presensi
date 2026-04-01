<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ID Card - {{ $intern->nama }}</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; padding: 50px; background: #f3f4f6; }
        .id-card { width: 300px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden; text-align: center; }
        .header { background: #3b82f6; color: white; padding: 20px 10px; font-weight: bold; font-size: 1.2rem; }
        .content { padding: 20px; }
        .photo-placeholder { width: 100px; height: 100px; background: #e5e7eb; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 0.8rem; border: 2px dashed #9ca3af; }
        .photo { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0 auto 15px; }
        .detail { margin: 5px 0; font-size: 0.9rem; color: #374151; }
        .qr-code { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: center;}
    </style>
</head>
<body>

    <div class="id-card">
        <div class="header">
            ID CARD MAGANG
        </div>
        <div class="content">
            @if($intern->foto)
                <img src="{{ asset('storage/' . $intern->foto) }}" class="photo" alt="Foto {{ $intern->nama }}">
            @else
                <div class="photo-placeholder">
                    Upload via Flutter
                </div>
            @endif

            <h2 style="margin: 0 0 10px; font-size: 1.2rem; color: #111827;">{{ $intern->nama }}</h2>
            <div class="detail"><strong>Divisi:</strong> {{ $intern->divisi }}</div>
            <div class="detail"><strong>Asal:</strong> {{ $intern->asal }}</div>
            <div class="detail"><strong>Periode:</strong> <br> {{ \Carbon\Carbon::parse($intern->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($intern->tanggal_selesai)->format('d M Y') }}</div>

            <div class="qr-code">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate($intern->qr_token) !!}
            </div>
            <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 10px;">Gunakan QR Code ini untuk absensi</p>
        </div>
    </div>

</body>
</html>