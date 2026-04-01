<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $blueprint) {
            // Menambahkan field baru setelah kolom yang sudah ada (misal setelah jam_pulang)
            $blueprint->string('last_selfie')->nullable()->after('jam_pulang');
            $blueprint->string('last_lat')->nullable()->after('last_selfie');
            $blueprint->string('last_long')->nullable()->after('last_lat');
            $blueprint->dateTime('last_selfie_at')->nullable()->after('last_long');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $blueprint) {
            // Menghapus kolom jika migration di-rollback
            $blueprint->dropColumn(['last_selfie', 'last_lat', 'last_long', 'last_selfie_at']);
        });
    }
};
