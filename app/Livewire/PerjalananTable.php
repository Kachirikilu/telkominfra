<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Perjalanan;
use Illuminate\Support\Facades\Auth;

class PerjalananTable extends Component
{
    use WithPagination;

    public $search = '';
    public $mode = ''; // '', '0', '1' (semua, belum selesai, sudah selesai)
    public $isAdmin;

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
