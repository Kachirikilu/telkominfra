<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perjalanan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     * Secara default Laravel menggunakan nama jamak dari nama model (perjalanans).
     *
     * @var string
     */
    protected $table = 'perjalanans';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     * Pastikan semua kolom yang di-CREATE di Controller ada di sini.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_perjalanan',
        'nama_pengguna',
        'perangkat',
        'file_nmf',
        'file_gpx',
        'timestamp_mulai',
        'timestamp_selesai',
    ];

    /**
     * Definisi relasi: Satu Perjalanan memiliki banyak Titik GPS.
     */
    public function titikGps(): HasMany
    {
        return $this->hasMany(TitikGPS::class, 'perjalanan_id');
    }

    /**
     * Definisi relasi: Satu Perjalanan memiliki banyak Pengukuran Sinyal.
     */
    public function pengukuranSinyal(): HasMany
    {
        return $this->hasMany(PengukuranSinyal::class, 'perjalanan_id');
    }
}