<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('selfie_pulang')->nullable()->after('jam_pulang');
            $table->string('lat_pulang')->nullable()->after('selfie_pulang');
            $table->string('long_pulang')->nullable()->after('lat_pulang');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['selfie_pulang', 'lat_pulang', 'long_pulang']);
        });
    }
};