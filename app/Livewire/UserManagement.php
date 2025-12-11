<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagement extends Component
{
    use WithPagination;

    // public $showModal = false;
    
    public $isEditing = false;
    public $userIdToEdit = null;
    public $modalTitle = '';
    
    // Properti Form Input
    public $name = '';
    public $email = '';
    public $password = '';
    public $is_admin = 0; // 1 untuk Admin, 0 untuk User

    // Properti Delete Confirmation
    public $showDeleteConfirmation = false;
    public $userIdToDelete = null;
    public $userEmailToDelete = '';

    // Properti yang diikat dengan elemen form (search, mode)
    public $search = '';
    public $searchMode = ''; // '' untuk 'all', 'admin', atau 'user'
    
    // Properti untuk menampilkan total di tab
    public $totalUsers = 0;
    public $totalAdmins = 0;
    public $totalNormalUsers = 0;

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
    
    // Reset properti form
    private function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->is_admin = 0;
        $this->userIdToEdit = null;
        $this->isEditing = false;
    }

    public function showAddModal($role)
    {
        $this->resetInputFields();
        
        $this->isEditing = false;
        $this->is_admin = ($role === 'admin') ? 1 : 0;
        $this->modalTitle = ($role === 'admin') ? 'Tambah Admin' : 'Tambah User';
        
        $this->dispatch('open-user-modal'); 
    }

    // Method untuk menampilkan Modal Edit
    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        
        $this->resetInputFields();

        $this->isEditing = true;
        $this->userIdToEdit = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_admin = $user->admin ? 1 : 0;
        $this->modalTitle = $user->admin ? 'Edit Admin' : 'Edit User';
        
        $this->dispatch('open-user-modal');
    }

    // Validation Rules
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                // Pastikan email unik, kecuali untuk user yang sedang diedit
                Rule::unique('users')->ignore($this->userIdToEdit),
            ],
            // Password hanya wajib saat mode 'tambah' atau jika diisi saat 'edit'
            'password' => $this->isEditing ? 'nullable|min:8' : 'required|min:8',
            'is_admin' => 'required|in:0,1',
        ];
    }
    
    // Method untuk menyimpan pengguna baru
    public function saveUser()
    {
        $this->validate();
        
        try {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'admin' => $this->is_admin,
            ]);
            
            session()->flash('success', 'Pengguna berhasil ditambahkan.');
            $this->closeModal();
            $this->dispatch('refresh-table'); 
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan pengguna.');
        }
    }

    
    // Method untuk memperbarui pengguna
    public function updateUser()
    {
        $rules = $this->rules();
        
        // Hapus validasi password jika tidak diisi saat update
        if (empty($this->password)) {
            unset($rules['password']);
        }
        
        $this->validate($rules);
        
        try {
            $user = User::findOrFail($this->userIdToEdit);
            
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'admin' => $this->is_admin,
            ];

            // Hanya update password jika diisi
            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            
            $user->update($data);
            
            session()->flash('success', 'Pengguna berhasil diperbarui.');
            $this->closeModal();
            $this->dispatch('refresh-table');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui pengguna.');
        }
    }

    // Method untuk menutup modal
    public function closeModal()
    {
        // $this->showModal = false;
        $this->resetInputFields();
        $this->resetValidation();
        // $this->resetPage();
        $this->dispatch('close-user-modal');
    }
    
    // Method untuk membuka konfirmasi hapus
    public function confirmDelete($userId)
    {
        $user = User::findOrFail($userId);
        
        // Guard: Tidak boleh menghapus diri sendiri (diulang di sini untuk keamanan)
        if (auth()->id() == $userId) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
            return;
        }

        $this->userIdToDelete = $userId;
        $this->userEmailToDelete = $user->email;
        $this->showDeleteConfirmation = true;
    }

    // Method untuk membatalkan hapus
    public function cancelDelete()
    {
        $this->userIdToDelete = null;
        $this->userEmailToDelete = '';
        $this->showDeleteConfirmation = false;
    }

    // Method untuk menghapus pengguna
    public function deleteUser()
    {
        // Guard: Pastikan pengguna terautentikasi dan admin
        if (!auth()->check() || !auth()->user()->admin) {
             // Sesuaikan pesan error
            session()->flash('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus.');
            $this->cancelDelete();
            return;
        }

        // Guard: Tidak boleh menghapus diri sendiri (diulang di sini untuk keamanan)
        if (auth()->id() == $this->userIdToDelete) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
            $this->cancelDelete();
            return;
        }
        
        try {
            $userToDelete = User::findOrFail($this->userIdToDelete);
            $userToDelete->delete();

            session()->flash('success', 'Pengguna ' . $this->userEmailToDelete . ' berhasil dihapus.');
            $this->cancelDelete(); // Tutup modal konfirmasi
            // $this->resetPage(); 
            $this->dispatch('refresh-table');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus pengguna.');
            $this->cancelDelete();
        }
    }

    public function refreshUsersList()
    {
        $this->resetPage();
    }

    // Method untuk mengganti mode (dipanggil dari tab button)
    public function setMode($mode)
    {
        $this->searchMode = $mode;
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