<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalCeramah extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'judul_ceramah',
        'nama_ustadz',
        'gambar',
        'tanggal_ceramah',
        'jam_mulai',
        'jam_selesai',
        'tempat_ceramah',
        'tentang_ceramah',
        'kategori_ceramah',
        'link_streaming',
    ];

    protected $casts = [
        'tanggal_ceramah' => 'date',
        'jam_mulai' => 'datetime:H:i:s',
        'jam_selesai' => 'datetime:H:i:s',
    ];
}