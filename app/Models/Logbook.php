<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'tanggal',
        'kegiatan',
        'dokumentasi',
    ];

    // Relasi balik ke tabel Intern
    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }
}
