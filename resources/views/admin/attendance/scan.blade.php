@extends('layouts.app')
@section('content')
    <div class="p-6">
        <h2 class="text-3xl font-bold mb-2 text-center text-gray-800">Scanner Absensi (PC Admin)</h2>
        <p class="text-center text-gray-500 mb-6 text-sm">
            *Pastikan kecerahan layar HP maksimal dan beri jarak 10-15 cm dari kamera.
        </p>

        <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-lg border border-gray-200">

            <div id="reader" class="w-full rounded-lg overflow-hidden"></div>

            <div id="result" class="mt-6 p-4 rounded-lg text-center text-lg font-bold hidden transition-all duration-300">
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        // Flag agar scanner tidak memproses QR berulang kali dalam waktu bersamaan
        let isProcessing = false;

        function onScanSuccess(decodedText) {
            // Jika sedang diproses atau sedang cooldown, abaikan hasil scan
            if (isProcessing) return;

            isProcessing = true; // Kunci scanner
            const resDiv = document.getElementById('result');

            // 1. Tampilkan status "Memproses" agar Admin/Peserta tidak mengira scanner macet
            resDiv.classList.remove('hidden');
            resDiv.className = "mt-6 p-4 rounded-lg bg-blue-100 text-blue-700 text-lg font-bold";
            resDiv.innerText = "⏳ Memproses absensi...";

            // 2. Mainkan suara beep ringan (dengan penanganan error jika browser memblokir autoplay)
            var audio = new Audio('https://www.soundjay.com/buttons/beep-07a.mp3');
            audio.play().catch(error => console.log("Suara beep diblokir browser, butuh interaksi user."));

            // 3. Kirim data ke server
            fetch("{{ route('admin.attendance.process') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        qr_token: decodedText
                    })
                })
                .then(res => res.json())
                .then(data => {
                    // Update UI berdasarkan respons sukses atau gagal
                    resDiv.className = data.success ?
                        "mt-6 p-4 rounded-lg bg-green-100 text-green-700 text-lg font-bold" :
                        "mt-6 p-4 rounded-lg bg-red-100 text-red-700 text-lg font-bold";
                    resDiv.innerText = data.success ? "✅ " + data.message : "❌ " + data.message;

                    // --- FITUR TEXT TO SPEECH (SUARA) ---
                    if (data.success && data.nama_peserta) {
                        let teksSuara = "";

                        // Tentukan kalimat berdasarkan tipe absen
                        if (data.tipe === 'masuk') {
                            teksSuara = "Selamat datang, " + data.nama_peserta;
                        } else if (data.tipe === 'pulang') {
                            teksSuara = "Terima kasih, " + data.nama_peserta + ". Hati-hati di jalan.";
                        }

                        // Panggil Web Speech API
                        let speech = new SpeechSynthesisUtterance(teksSuara);
                        speech.lang = 'id-ID'; // Gunakan logat Bahasa Indonesia
                        speech.rate = 0.9; // Kecepatan bicara
                        window.speechSynthesis.speak(speech);
                    } else if (!data.success) {
                        // Suara jika gagal/error
                        let errorSpeech = new SpeechSynthesisUtterance(data.message);
                        errorSpeech.lang = 'id-ID';
                        window.speechSynthesis.speak(errorSpeech);
                    }

                    // --- FITUR JEDA (COOLDOWN) 4 DETIK ---
                    setTimeout(() => {
                        isProcessing = false;
                        resDiv.classList.add('hidden'); // Sembunyikan pesan untuk scan berikutnya
                    }, 4000);
                })
                .catch(err => {
                    console.error(err);
                    resDiv.className = "mt-6 p-4 rounded-lg bg-red-100 text-red-700 text-lg font-bold";
                    resDiv.innerText = "❌ Terjadi kesalahan jaringan. Coba lagi.";

                    setTimeout(() => {
                        isProcessing = false; // Buka kunci agar bisa dicoba lagi
                        resDiv.classList.add('hidden');
                    }, 4000);
                });
        }

        // Konfigurasi Scanner
        let scanner = new Html5QrcodeScanner("reader", {
            fps: 10, // DITURUNKAN KE 10: Mengurangi beban CPU dan motion blur di kamera PC

            // UKURAN DINAMIS: Mengambil 60% dari ukuran layar/kamera agar fokus QR lebih pas
            qrbox: function(vw, vh) {
                var minEdgeSize = Math.min(vw, vh);
                var qrboxSize = Math.floor(minEdgeSize * 0.6);

                // Beri batas minimal dan maksimal ukuran kotak
                if (qrboxSize < 250) qrboxSize = 250;
                if (qrboxSize > 400) qrboxSize = 400;

                return {
                    width: qrboxSize,
                    height: qrboxSize
                };
            }
        });

        scanner.render(onScanSuccess);
    </script>
@endsection
