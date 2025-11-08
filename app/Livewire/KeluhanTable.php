<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KeluhPengguna; // Sesuaikan dengan model Anda
use Illuminate\Support\Facades\Auth;

class KeluhanTable extends Component
{
    use WithPagination;

    // State properties
    public $search = '';
    public $mode = 'pending'; // Default mode: 'pending', 'processing', 'complete'

    // Properti yang akan digunakan di view
    public $isAdmin;
    public $idUser;

    protected $queryString = ['search' => ['except' => ''], 'mode'];

    // Update Livewire pagination theme (opsional, jika Anda menggunakan Tailwind)
    protected $paginationTheme = 'tailwind'; 

    public function mount()
    {
        // Ambil data user yang login saat komponen dimuat
        $this->isAdmin = Auth::check() ? (Auth::user()->admin ?? false) : false;
        $this->idUser = Auth::check() ? (Auth::user()->id ?? null) : null;
    }
    
    // Method ini dipanggil saat salah satu tab diklik
    public function switchMode($newMode)
    {
        $validModes = ['pending', 'processing', 'complete'];
        if (!in_array($newMode, $validModes)) {
            return;
        }

        $this->mode = $newMode;
        $this->search = ''; // Kosongkan pencarian saat berganti mode
        $this->resetPage(); // Reset halaman pagination
    }

    // Method ini dipanggil saat properti 'search' di-update
    public function updatingSearch()
    {
        $this->resetPage(); // Reset halaman pagination saat mulai mengetik
    }
    
    // Logic untuk mengambil data
    private function getKeluhan()
    {
        $query = KeluhPengguna::query()
            ->with(['user', 'perjalanan']); // Muat relasi

        // Filtering berdasarkan mode
        if ($this->mode === 'pending') {
            $query->whereNull('perjalanan_id');
        } elseif ($this->mode === 'processing') {
            // Perjalanan sedang berlangsung (perjalanan_id ada dan belum selesai)
            $query->whereNotNull('perjalanan_id')
                  ->whereHas('perjalanan', function ($q) {
                      $q->where('selesai', false);
                  });
        } elseif ($this->mode === 'complete') {
            // Sudah selesai (perjalanan_id ada dan sudah selesai)
            $query->whereNotNull('perjalanan_id')
                  ->whereHas('perjalanan', function ($q) {
                      $q->where('selesai', true);
                  });
        }

        // Filtering berdasarkan pencarian (search)
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                // Contoh: Cari di kolom nama_tempat atau komentar
                $q->where('nama_tempat', 'like', $searchTerm)
                  ->orWhere('komentar', 'like', $searchTerm)
                  ->orWhereHas('user', function ($qUser) use ($searchTerm) {
                      $qUser->where('name', 'like', $searchTerm);
                  });
            });
        }
        
        // Sorting
        $query->latest();

        // Menggunakan paginate() untuk hasil
        return $query->paginate(10); // Sesuaikan jumlah item per halaman
    }

public function render()
{
    // Hitung total keluhan per kategori (pending, processing, complete)
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

    
    // Utility method untuk mendapatkan nama mode
    private function getModeName()
    {
        switch ($this->mode) {
            case 'pending':
                return 'Belum Diproses';
            case 'processing':
                return 'Sedang Diproses';
            case 'complete':
                return 'Sudah Selesai';
            default:
                return 'Semua';
        }
    }
}