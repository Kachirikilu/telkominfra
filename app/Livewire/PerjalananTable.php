<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Perjalanan;
use App\Models\DataPerjalanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PerjalananTable extends Component
{
    use WithPagination;

    public $search = '';
    public $mode = ''; 
    public $isAdmin;

    // --- PROPERTI BARU UNTUK DELETE POP-UP ---
    public $showDeleteConfirmation = false;
    public $perjalananIdToDelete = null;
    public $perjalananIdPerjalananToDelete = '';
    public $perjalananNamaToDelete = '';
    // ------------------------------------------

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search' => ['except' => ''], 'mode'];

    public function mount()
    {
        $this->isAdmin = Auth::check() ? (Auth::user()->admin ?? false) : false;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function switchMode($newMode)
    {
        $this->mode = $newMode;
        $this->resetPage();
    }

    // --- METHOD BARU: CONFIRM DELETE ---
    public function confirmDelete($perjalananId)
    {
        $perjalanan = Perjalanan::findOrFail($perjalananId);

        $this->perjalananIdToDelete = $perjalananId;
        // Asumsi 'nama_tempat' adalah nama yang ingin ditampilkan
        $this->perjalananIdPerjalananToDelete = $perjalanan->id_perjalanan;
        $this->perjalananNamaToDelete = $perjalanan->nama_tempat; 
        $this->showDeleteConfirmation = true;
    }

    // --- METHOD BARU: BATALKAN DELETE ---
    public function cancelDelete()
    {
        $this->perjalananIdToDelete = null;
        $this->perjalananIdPerjalananToDelete = '';
        $this->perjalananIdPerjalananToDelete = '';
        $this->showDeleteConfirmation = false;
    }

    // --- METHOD BARU: EKSEKUSI DELETE ---
    public function deletePerjalanan()
    {
        // Guard: Cek admin
        if (!Auth::check() || !$this->isAdmin) {
            session()->flash('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus.');
            $this->cancelDelete();
            return;
        }

        try {
            $perjalanan = Perjalanan::findOrFail($this->perjalananIdToDelete);

            DB::transaction(function () use ($perjalanan) {

                $basePath = public_path('uploads/perjalanan/');
                $dataPerjalanans = DataPerjalanan::where('perjalanan_id', $perjalanan->id)->get();

                if ($dataPerjalanans->isNotEmpty()) {
                    foreach ($dataPerjalanans as $dataItem) {
                        // Hapus file NMF
                        if ($dataItem->file_nmf && File::exists($basePath . $dataItem->file_nmf)) {
                            File::delete($basePath . $dataItem->file_nmf);
                        }

                        // Hapus file GPX
                        if ($dataItem->file_gpx && File::exists($basePath . $dataItem->file_gpx)) {
                            File::delete($basePath . $dataItem->file_gpx);
                        }
                    }
                }

                // Hapus data DataPerjalanan di database
                DataPerjalanan::where('perjalanan_id', $perjalanan->id)->delete();

                // Hapus data Perjalanan utama
                $perjalanan->delete();
            });

            session()->flash('success', 'Data perjalanan "' . $this->perjalananIdPerjalananToDelete . '" beserta semua file log berhasil dihapus.');

            $this->cancelDelete(); 
            $this->resetPage();  // optional jika pakai pagination

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            session()->flash('error', 'Data perjalanan tidak ditemukan.');
            $this->cancelDelete();

        } catch (\Exception $e) {

            session()->flash('error', 'Terjadi kesalahan server saat menghapus data.');
            $this->cancelDelete();
        }
    }
    // ------------------------------------------

    private function getPerjalananQuery()
    {
        $query = Perjalanan::query();

        // Filter mode
        if ($this->mode === '0') {
            $query->where('selesai', false);
        } elseif ($this->mode === '1') {
            $query->where('selesai', true);
        }

        // Pencarian
        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('id', 'like', $term)
                    ->orWhere('id_perjalanan', 'like', $term)
                    ->orWhere('nama_tempat', 'like', $term)
                    ->orWhere('nama_pengguna', 'like', $term);
            });
        }

        return $query->latest();
    }

    public function render()
    {
        $baseQuery = Perjalanan::query();

        $totalPerjalanan = $baseQuery->count();
        $perjalananBelumSelesai = (clone $baseQuery)->where('selesai', false)->count();
        $perjalananSelesai = (clone $baseQuery)->where('selesai', true)->count();

        return view('livewire.perjalanan-table', [
            'perjalanans' => $this->getPerjalananQuery()->paginate(10),
            'totalPerjalanan' => $totalPerjalanan,
            'perjalananBelumSelesai' => $perjalananBelumSelesai,
            'perjalananSelesai' => $perjalananSelesai,
        ]);
    }
}