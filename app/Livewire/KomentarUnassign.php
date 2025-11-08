<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KeluhPengguna;

class KomentarUnassign extends Component
{
    use WithPagination;

    public $perjalananId;

    // agar pagination tetap di halaman yg benar saat ganti page
    protected $paginationTheme = 'tailwind'; 

    public function mount($perjalananId)
    {
        $this->perjalananId = $perjalananId;
    }

    public function updatingPerjalananId()
    {
        $this->resetPage();
    }

    public function getKomentarTerhubungProperty()
    {
        return KeluhPengguna::where('perjalanan_id', $this->perjalananId)
            ->orderByDesc('created_at')
            ->paginate(5); // 🔹 hanya tampil 5 komentar per halaman
    }

    public function unassignKomentar($keluhanId)
    {
        $komentar = KeluhPengguna::find($keluhanId);

        if (!$komentar || $komentar->perjalanan_id != $this->perjalananId) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Komentar tidak ditemukan atau sudah dilepaskan.']);
            return;
        }

        $komentar->update(['perjalanan_id' => null]);

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Komentar berhasil dilepaskan dari perjalanan.']);
        $this->resetPage(); // reset ke halaman 1 setelah unassign
    }

    public function render()
    {
        return view('livewire.komentar-unassign', [
            'komentarTerhubung' => $this->komentarTerhubung,
        ]);
    }
}
