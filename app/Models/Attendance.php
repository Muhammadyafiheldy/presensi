<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'last_selfie',        // Foto Masuk
        'selfie_pulang',      // Foto Pulang
        'status',
        'total_jam_kerja',
        'keterangan_masuk',   // Alasan Terlambat
        'keterangan_pulang',  // Alasan Pulang Cepat
    ];

    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }
}
