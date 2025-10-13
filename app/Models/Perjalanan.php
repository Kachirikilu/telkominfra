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
        'nama_tempat',
    ];

    public function perjalananData(): HasMany
    {
        return $this->hasMany(PerjalananData::class, 'perjalanan_id');
    }
    public function pengukuranSinyal()
    {
        return $this->hasMany(PengukuranSinyal::class, 'perjalanan_id');
    }
}