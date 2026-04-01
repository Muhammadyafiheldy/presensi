<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            $table->text('bukti_file')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            $table->string('bukti_file')->nullable()->change();
        });
    }
};
