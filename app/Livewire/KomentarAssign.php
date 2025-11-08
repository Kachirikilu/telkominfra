<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KeluhPengguna;

class KomentarAssign extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedKeluhan = [];
    public $perjalananId;

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search' => ['except' => '']];

    public function mount($perjalananId)
    {
        $this->perjalananId = $perjalananId;
    }

    // Reset pagination setiap kali input berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function assignKomentar()
    {
        if (empty($this->selectedKeluhan)) {
            $this->dispatch('alert', ['type' => 'warning', 'message' => 'Pilih setidaknya satu komentar!']);
            return;
        }

        KeluhPengguna::whereIn('id', $this->selectedKeluhan)
            ->update(['perjalanan_id' => $this->perjalananId]);

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Komentar berhasil dihubungkan!']);

        // 🔹 Reload halaman penuh setelah sukses
        $this->dispatch('reloadPage');
    }

    public function getKomentarsProperty()
    {
        $baseQuery = KeluhPengguna::whereNull('perjalanan_id');

        if (strlen(trim($this->search)) < 2) {
            return $baseQuery->whereRaw('0=1')->paginate(10);
        }

        $baseQuery->where(function ($q) {
            $q->where('nama_tempat', 'like', '%' . $this->search . '%')
              ->orWhere('komentar', 'like', '%' . $this->search . '%')
              ->orWhere('nama_pengguna', 'like', '%' . $this->search . '%');
        });

        return $baseQuery->orderByDesc('created_at')->paginate(10);
    }

    public function render()
    {
        return view('livewire.komentar-assign', [
            'komentars' => $this->komentars
        ]);
    }
}
