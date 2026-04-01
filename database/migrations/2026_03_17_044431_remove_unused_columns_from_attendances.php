<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'lat_pulang',
                'long_pulang',
                'last_lat',
                'last_long',
                'last_selfie_at'
            ]);
        });
    }

    public function down()
    {
        // Rollback jika dibutuhkan
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('last_lat')->nullable();
            $table->string('last_long')->nullable();
            $table->string('lat_pulang')->nullable();
            $table->string('long_pulang')->nullable();
            $table->timestamp('last_selfie_at')->nullable();
        });
    }
};
