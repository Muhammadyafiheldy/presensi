<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Intern extends Model
{
    // 2. Tambahkan HasApiTokens di dalam class
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'divisi',
        'asal',
        'tanggal_mulai',
        'tanggal_selesai',
        'qr_token',
        'foto'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    // 3. Tambahkan hidden agar password tidak bocor ke Flutter saat API diakses
    protected $hidden = [
        'password',
    ];

    // Relasi dari Intern ke banyak Permit
    public function permits()
    {
        return $this->hasMany(Permit::class);
    }
}
