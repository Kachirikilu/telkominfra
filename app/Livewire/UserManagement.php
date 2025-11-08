<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    // Properti yang diikat dengan elemen form (search, mode)
    public $search = '';
    public $searchMode = ''; // '' untuk 'all', 'admin', atau 'user'
    
    // Properti untuk menampilkan total di tab
    public $totalUsers = 0;
    public $totalAdmins = 0;
    public $totalNormalUsers = 0;

    // Properti untuk memuat data awal dan mengelola perubahan
    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => ''],
    ];
    
    // Dipanggil setiap kali properti $search atau $searchMode berubah
    public function updated($propertyName)
    {
        // Reset paginasi ke halaman 1 ketika filter atau pencarian berubah
        if (in_array($propertyName, ['search', 'searchMode'])) {
            $this->resetPage();
        }
    }

    // Method untuk mengganti mode (dipanggil dari tab button)
    public function setMode($mode)
    {
        $this->searchMode = $mode;
        // Panggilan updated('searchMode') secara otomatis, yang mereset page.
    }

    // Method untuk menghapus pengguna
    public function deleteUser($userId)
    {
        // Guard: Pastikan pengguna terautentikasi dan admin
        if (!auth()->check() || !auth()->user()->admin) {
            session()->flash('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus.');
            return;
        }

        // Guard: Tidak boleh menghapus diri sendiri
        if (auth()->id() == $userId) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
            return;
        }
        
        try {
            $userToDelete = User::findOrFail($userId);
            $userToDelete->delete();

            session()->flash('success', 'Pengguna berhasil dihapus.');
            // Setelah menghapus, perbarui data (termasuk total hitungan)
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus pengguna.');
        }
    }

    // Method utama untuk mendapatkan data pengguna
    public function getUsers()
    {
        $query = User::query();

        // 1. Filter berdasarkan Mode (Role)
        if ($this->searchMode === 'admin') {
            $query->where('admin', true);
        } elseif ($this->searchMode === 'user') {
            $query->where('admin', false);
        }

        // 2. Filter berdasarkan Keyword Pencarian
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
                if (is_numeric($this->search)) {
                    $q->orWhere('id', $this->search);
                }
            });
        }
        
        // 3. Ambil data dengan paginasi
        return $query->latest()->paginate(10);
    }
    
    // Method render Livewire
    public function render()
    {
        $users = $this->getUsers();

        // Hitung total untuk Badge Tab (perlu dihitung ulang setiap render)
        $this->totalUsers = User::count();
        $this->totalAdmins = User::where('admin', true)->count();
        $this->totalNormalUsers = User::where('admin', false)->count();

        return view('livewire.user-management', [
            'users' => $users,
        ]);
    }
}