<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    use HasFactory;

    protected $fillable = ['intern_id', 'tanggal_mulai', 'tanggal_selesai', 'alasan', 'bukti_file', 'status'];

    // Relasi balik ke Intern
    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }
}
