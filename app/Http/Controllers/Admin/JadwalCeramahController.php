<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalCeramah;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str; // Import Str class untuk fungsi slug

class JadwalCeramahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwalCeramahs = JadwalCeramah::latest()->paginate(10); // Ambil data terbaru dengan paginasi
        return view('schedules', compact('jadwalCeramahs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('schedules');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_ceramah' => 'required|string|max:255',
            'nama_ustadz' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Contoh validasi gambar
            'tanggal_ceramah' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'tempat_ceramah' => 'required|string|max:255',
            'tentang_ceramah' => 'nullable|string',
            'kategori_ceramah' => 'nullable|string|max:255',
            'link_streaming' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.schedules.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->judul_ceramah) . '_' . Str::slug($request->nama_ustadz) . '_' . now()->format('YmdHisu');

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $extension = $gambar->getClientOriginalExtension();
            $namaRandom = Str::random(100) . '_' . now()->format('YmdHisu');
            $namaGambar = $namaRandom . '.' . $extension;
            $pathGambar = $gambar->move(public_path('images/jadwal'), $namaGambar);
            $data['gambar'] = 'images/jadwal/' . $namaGambar;
        } else {
            $data['gambar'] = 'images/No Image.png';
        }

        JadwalCeramah::create($data);

        return Redirect::route('admin.schedules.index')->with('success', 'Jadwal ceramah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $jadwalCeramah = JadwalCeramah::where('slug', $slug)->firstOrFail();
        return view('schedules', compact('jadwalCeramah'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $jadwalCeramah = JadwalCeramah::findOrFail($id);
        return view('schedules', compact('jadwalCeramah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jadwalCeramah = JadwalCeramah::findOrFail($id);

        // $request->validate([
        //     'judul_ceramah' => 'required|string|max:255',
        //     'nama_ustadz' => 'required|string|max:255',
        //     'tanggal_ceramah' => 'required|date',
        //     'jam_mulai' => 'required|date_format:H:i',
        //     'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
        //     'tempat_ceramah' => 'required|string|max:255',
        //     'tentang_ceramah' => 'nullable|string',
        //     'kategori_ceramah' => 'nullable|string|max:255',
        //     'link_streaming' => 'nullable|url|max:255',
        //     'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk gambar
        // ]);

        $validator = Validator::make($request->all(), [
            'judul_ceramah' => 'required|string|max:255',
            'nama_ustadz' => 'required|string|max:255',
            'tanggal_ceramah' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'tempat_ceramah' => 'required|string|max:255',
            'tentang_ceramah' => 'nullable|string',
            'kategori_ceramah' => 'nullable|string|max:255',
            'link_streaming' => 'nullable|url|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk gambar
        ]);
        if ($validator->fails()) {
            return redirect()->route('admin.schedules.edit', ['schedule' => $jadwalCeramah->id])
                ->withErrors($validator)
                ->withInput();
        }

        
        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $extension = $gambar->getClientOriginalExtension();
            $namaRandom = Str::random(100) . '_' . now()->format('YmdHisu');
            $namaGambar = $namaRandom . '.' . $extension;
            $pathGambar = $gambar->move(public_path('images/jadwal'), $namaGambar);
            $data['gambar'] = 'images/jadwal/' . $namaGambar;

            if ($jadwalCeramah->gambar && $jadwalCeramah->gambar !== 'images/No Image.png') {
                $pathGambarLama = public_path($jadwalCeramah->gambar);
                if (file_exists($pathGambarLama)) {
                    unlink($pathGambarLama);
                }
            }
        }

        $slug_20 = substr($jadwalCeramah->slug, -20);
        $data['slug'] = Str::slug($request->judul_ceramah) . '_' . Str::slug($request->nama_ustadz) . '_' . $slug_20;

        $jadwalCeramah->update($data);

        return Redirect::route('admin.schedules.index')->with('success', 'Jadwal ceramah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jadwalCeramah = JadwalCeramah::findOrFail($id);

        if ($jadwalCeramah->gambar !== 'images/No Image.png') {
            if (str_starts_with($jadwalCeramah->gambar, 'images/jadwal/')) {
                $filePath = public_path($jadwalCeramah->gambar);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
    
        $jadwalCeramah->delete();
    
        return Redirect::route('admin.schedules.index')->with('success', 'Jadwal ceramah berhasil dihapus.');
    }
}