<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalCeramah;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class JsonController extends Controller
{
    protected $today;
    protected $jadwalHariIni;
    /**
     * Menampilkan semua data jadwal ceramah dalam format JSON.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $jadwalCeramahs = JadwalCeramah::all();

        $data = $jadwalCeramahs->map(function ($jadwal) {
            return [
                'today' => $this->today,
                'slug' => $jadwal->slug,
                'judul_ceramah' => $jadwal->judul_ceramah,
                'nama_ustadz' => $jadwal->nama_ustadz,
                'gambar' => $jadwal->gambar,
                'tanggal_ceramah' => $jadwal->tanggal_ceramah,
                'jam_mulai' => $jadwal->jam_mulai,
                'jam_selesai' => $jadwal->jam_selesai,
                'tempat_ceramah' => $jadwal->tempat_ceramah,
                'tentang_ceramah' => $jadwal->tentang_ceramah,
                'kategori_ceramah' => $jadwal->kategori_ceramah,
                'link_streaming' => $jadwal->link_streaming,
            ];
        });

        return response()->json(['data' => $data], 200);
    }

    /**
     * Menampilkan data jadwal ceramah berdasarkan slug dalam format JSON.
     *
     * @param  string  $slug
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $jadwalCeramah = JadwalCeramah::where('id', $id)->first();

        if (!$jadwalCeramah) {
            return response()->json(['message' => 'Jadwal Ceramah tidak ditemukan'], 404);
        }

        $data = [
            'slug' => $jadwalCeramah->slug,
            'judul_ceramah' => $jadwalCeramah->judul_ceramah,
            'nama_ustadz' => $jadwalCeramah->nama_ustadz,
            'gambar' => $jadwalCeramah->gambar,
            'tanggal_ceramah' => $jadwalCeramah->tanggal_ceramah,
            'jam_mulai' => $jadwalCeramah->jam_mulai,
            'jam_selesai' => $jadwalCeramah->jam_selesai,
            'tempat_ceramah' => $jadwalCeramah->tempat_ceramah,
            'tentang_ceramah' => $jadwalCeramah->tentang_ceramah,
            'kategori_ceramah' => $jadwalCeramah->kategori_ceramah,
            'link_streaming' => $jadwalCeramah->link_streaming,
        ];

        return response()->json(['data' => $data], 200);
    }
}