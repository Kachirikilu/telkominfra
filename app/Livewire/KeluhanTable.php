<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KeluhPengguna; // Sesuaikan dengan model Anda
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Tambahkan Log untuk debugging/logging
use Illuminate\Support\Facades\File;


class KeluhanTable extends Component
{
    use WithPagination;

    // State properties
    public $search = '';
    public $mode = 'pending'; // Default mode: 'pending', 'processing', 'complete'

    // Properti yang akan digunakan di view
    public $isAdmin;
    public $idUser;

    // --- PROPERTI BARU UNTUK DELETE POP-UP ---
    public $showDeleteConfirmation = false;
    public $keluhanIdToDelete = null;
    public $keluhanNamaToDelete = ''; // Digunakan untuk menampilkan konteks keluhan
    // ------------------------------------------

    protected $queryString = ['search' => ['except' => ''], 'mode'];
    protected $paginationTheme = 'tailwind'; 

    public function mount()
    {
        // Ambil data user yang login saat komponen dimuat
        $this->isAdmin = Auth::check() ? (Auth::user()->admin ?? false) : false;
        $this->idUser = Auth::check() ? (Auth::user()->id ?? null) : null;
    }
    
    // ... (Metode switchMode dan updatingSearch tetap sama)

    // Method ini dipanggil saat salah satu tab diklik
    public function switchMode($newMode)
    {
        $validModes = ['pending', 'processing', 'complete'];
        if (!in_array($newMode, $validModes)) {
            return;
        }

        $this->mode = $newMode;
        $this->search = ''; 
        $this->resetPage(); 
    }

    // Method ini dipanggil saat properti 'search' di-update
    public function updatingSearch()
    {
        $this->resetPage(); 
    }
    
    // --- METHOD BARU: CONFIRM DELETE ---
    public function confirmDelete($keluhanId)
    {
        $keluhan = KeluhPengguna::findOrFail($keluhanId);

        // Otorisasi: Hanya admin atau pemilik keluhan yang bisa menghapus
        if (!$this->isAdmin && $keluhan->user_id != $this->idUser) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus keluhan ini.');
            return;
        }
        
        $this->keluhanIdToDelete = $keluhanId;
        // Gunakan komentar atau nama tempat sebagai konteks di modal
        $this->keluhanNamaToDelete = substr($keluhan->komentar, 0, 50) . '...'; 
        $this->showDeleteConfirmation = true;
    }

    // --- METHOD BARU: BATALKAN DELETE ---
    public function cancelDelete()
    {
        $this->keluhanIdToDelete = null;
        $this->keluhanNamaToDelete = '';
        $this->showDeleteConfirmation = false;
    }

    // --- METHOD BARU: EKSEKUSI DELETE ---
    public function deleteKeluhan()
    {
        $id = $this->keluhanIdToDelete;

        // Guard: Pastikan ID ada
        if (is_null($id)) {
            $this->cancelDelete();
            return;
        }
        
        try {
            $keluhan = KeluhPengguna::findOrFail($id);

            // Otorisasi Final: Cek lagi di sini sebelum eksekusi
            if (!$this->isAdmin && $keluhan->user_id != $this->idUser) {
                session()->flash('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus keluhan ini.');
                $this->cancelDelete();
                return;
            }

            // --- LOGIKA HAPUS GAMBAR BARU ---
            $fileName = $keluhan->foto;
            $filePath = public_path('images/keluh/' . $fileName); // Sesuaikan path

            if ($fileName && File::exists($filePath)) {
                File::delete($filePath);
                Log::info('Livewire: File gambar keluhan dihapus: ' . $fileName);
            }
            // ------------------------------------
            
            $keluhan->delete();

            session()->flash('success', 'Keluhan berhasil dihapus.');
            
            $this->cancelDelete(); // Tutup modal konfirmasi
            $this->resetPage(); // Muat ulang data tabel
        } catch (\Exception $e) {
            Log::error('Gagal menghapus keluhan ID: ' . $id . '. Error: ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus data. Terjadi kesalahan server.');
            $this->cancelDelete();
        }
    }
    // ------------------------------------------

    // ... (Metode getKeluhan, render, dan getModeName tetap sama)
    private function getKeluhan()
    {
        // ... (kode tetap sama)
        $query = KeluhPengguna::query()
            ->with(['user', 'perjalanan']); 

        // Filtering berdasarkan mode
        if ($this->mode === 'pending') {
            $query->whereNull('perjalanan_id');
        } elseif ($this->mode === 'processing') {
             $query->whereNotNull('perjalanan_id')
                   ->whereHas('perjalanan', function ($q) {
                       $q->where('selesai', false);
                   });
        } elseif ($this->mode === 'complete') {
             $query->whereNotNull('perjalanan_id')
                   ->whereHas('perjalanan', function ($q) {
                       $q->where('selesai', true);
                   });
        }

        // Filtering berdasarkan pencarian (search)
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                 $q->where('nama_tempat', 'like', $searchTerm)
                   ->orWhere('komentar', 'like', $searchTerm)
                   ->orWhereHas('user', function ($qUser) use ($searchTerm) {
                       $qUser->where('name', 'like', $searchTerm);
                   });
            });
        }
        
        $query->latest();
        return $query->paginate(10);
    }
    
    // ... (metode render dan getModeName tetap sama)
    public function render()
    {
        // ... (kode tetap sama)
        $keluhanBelumSelesai = KeluhPengguna::whereNull('perjalanan_id')->count();
        $keluhanDiproses = KeluhPengguna::whereNotNull('perjalanan_id')
            ->whereHas('perjalanan', function ($q) {
                $q->where('selesai', false);
            })
            ->count();
        $keluhanSelesai = KeluhPengguna::whereNotNull('perjalanan_id')
            ->whereHas('perjalanan', function ($q) {
                $q->where('selesai', true);
            })
            ->count();

        return view('livewire.keluhan-table', [
            'keluhans' => $this->getKeluhan(),
            'modeName' => $this->getModeName(),
            'keluhanBelumSelesai' => $keluhanBelumSelesai,
            'keluhanDiproses' => $keluhanDiproses,
            'keluhanSelesai' => $keluhanSelesai,
        ]);
    }
    
    private function getModeName()
    {
        switch ($this->mode) {
            case 'pending': return 'Belum Diproses';
            case 'processing': return 'Sedang Diproses';
            case 'complete': return 'Sudah Selesai';
            default: return 'Semua';
        }
    }
}