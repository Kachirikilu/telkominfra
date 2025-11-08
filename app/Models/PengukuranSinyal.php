<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengukuranSinyal extends Model
{
    use HasFactory;
    
    /**
     * Menonaktifkan timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $table = 'pengukuran_sinyals';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'data_perjalanan_id',
        'timestamp_waktu',
        'cell_id',
        'pci',
        'earfcn',
        'band',
        'frekuensi',
        'bandwidth',
        'n_value',
        'rsrp',
        'rssi',
        'rsrq',
        'sinr',
        'latitude',
        'longitude'
    ];

    /**
     * Definisi relasi: Banyak Pengukuran Sinyal dimiliki oleh satu Perjalanan.
     */
    public function perjalanan_data(): BelongsTo
    {
        return $this->belongsTo(DataPerjalanan::class, 'data_perjalanan_id');
    }
}