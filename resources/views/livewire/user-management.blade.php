<div class="mb-8 m-2 sm:m-4">
    <div class="mt-8">

        <div class="flex flex-wrap items-center gap-2 mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">Manajemen Pengguna</h2>
            <div class="ml-auto flex gap-2">
                <button wire:click="showAddModal('admin')" class="bg-red-500 text-white px-3 py-2 rounded-lg">+
                    Admin</button>
                <button wire:click="showAddModal('user')" class="bg-green-500 text-white px-3 py-2 rounded-lg">+
                    User</button>
            </div>
        </div>

        {{-- Pesan Livewire setelah Aksi (misalnya hapus) --}}
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- ========== PENCARIAN & TAB FILTER ========== --}}
        <div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">
            <div class="flex border-b mb-4">
                {{-- Tab Semua --}}
                <button wire:click="setMode('')"
                    class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 
                    {{ $searchMode == '' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                    <i class="fas fa-users mr-2"></i> Semua Pengguna (<span>{{ number_format($totalUsers) }}</span>)
                </button>
                {{-- Tab Admin --}}
                <button wire:click="setMode('admin')"
                    class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 
                    {{ $searchMode == 'admin' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                    <i class="fas fa-crown mr-2"></i> Admin (<span>{{ number_format($totalAdmins) }}</span>)
                </button>
                {{-- Tab User --}}
                <button wire:click="setMode('user')"
                    class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 
                    {{ $searchMode == 'user' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                    <i class="fas fa-user mr-2"></i> User (<span>{{ number_format($totalNormalUsers) }}</span>)
                </button>
            </div>

            {{-- Form Pencarian --}}
            {{-- Menggunakan wire:model.live.debounce.500ms untuk pencarian real-time/saat mengetik --}}
            <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3">
                <input type="text" wire:model.live.debounce.500ms="search"
                    placeholder="Cari Nama, Email, atau ID Pengguna..."
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">

                <div class="flex space-x-3 w-full sm:w-auto">
                    {{-- Tombol Cari (tidak terlalu diperlukan lagi dengan wire:model.live) --}}
                    <button wire:click.prevent="$set('search', $wire.search)"
                        class="w-1/2 sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-md">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    {{-- Tombol Reset --}}
                    <button wire:click="setMode('')" {{-- setMode('') mereset semua parameter, termasuk search --}}
                        class="w-1/2 sm:w-auto flex items-center justify-center py-2 px-4 text-gray-600 hover:text-gray-900 bg-gray-100 rounded-lg transition duration-150 shadow-sm border border-gray-300">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- ========== HASIL TABEL PENGGUNA ========== --}}
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                {{-- Livewire secara otomatis menangani loading state/spinner --}}
                {{-- <div wire:loading class="p-6 text-center text-indigo-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
                </div> --}}

                <table class="min-w-full divide-y divide-gray-200" wire:loading.class="opacity-50">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Pada</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-center text-sm">
                                    @if ($user->admin)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Admin</span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">User</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm">
                                    {{-- Cek apakah pengguna saat ini adalah Admin dan BUKAN dirinya sendiri --}}
                                    {{-- @if (Auth::user()?->admin && Auth::id() !== $user->id)
                                        <button wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="Apakah Anda yakin ingin menghapus pengguna {{ $user->name }} (ID: {{ $user->id }})?"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition">
                                            Hapus
                                        </button>
                                    @endif --}}
                                    <div class="flex items-center justify-center space-x-2">
                                        @if (Auth::user()?->admin)
                                            <button wire:click="editUser({{ $user->id }})"
                                                class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">
                                                Edit
                                            </button>

                                            @if (Auth::id() !== $user->id)
                                                {{-- TOMBOL DELETE BARU --}}
                                                <button wire:click="confirmDelete({{ $user->id }})"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                                    Hapus
                                                </button>
                                            @endif
                                        @endif
                                    </div
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada pengguna
                                    ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="p-4" id="pagination-links-container">
                    {{ $users->links() }}
                </div>
            @endif

            <div wire:loading.flex class="justify-center items-center py-4">
                <div class="flex items-center space-x-2 text-gray-500">
                    <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memuat data...</span>
                </div>
            </div>

        </div>



        <div x-data="{ show: false }" @open-user-modal.window="show = true" @close-user-modal.window="show = false"
            x-cloak>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-40 flex justify-center items-center z-50">

                <div @click.outside="$wire.closeModal()"
                    class="bg-white rounded-lg w-full max-w-lg lg:w-4/5 transform transition-all duration-200 ease-out scale-100 max-h-[90vh] flex flex-col">

                    {{-- 1. Header Modal (Tetap di Atas) --}}
                    <div class="p-6 pb-4 border-b">
                        <h3 class="text-xl font-semibold text-gray-800">
                            {{ $modalTitle }}
                        </h3>
                    </div>

                    {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
                    <div class="p-6 flex-1 overflow-y-auto space-y-6">
                        <form wire:submit.prevent="{{ $isEditing ? 'updateUser' : 'saveUser' }}" id="userForm">

                            {{-- ****************************************************** --}}
                            {{-- 1. ACCOUNT INFORMATION & PERSONAL INFO --}}
                            {{-- ****************************************************** --}}
                            <div class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
                                <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Informasi Akun & Personal
                                </h4>

                                {{-- 👤 Nama Input --}}
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name
                                        <span class="text-red-500">*</span></label>
                                    <input wire:model.lazy="name" type="text" id="name"
                                        placeholder="Masukkan Nama Lengkap"
                                        class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('name')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- 📧 Email Input --}}
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email <span
                                            class="text-red-500">*</span></label>
                                    <input wire:model.lazy="email" type="email" id="email"
                                        placeholder="contoh@domain.com"
                                        class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('email')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- 🔒 Password Input --}}
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password
                                        @if (!$isEditing)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input wire:model.lazy="password" type="password" id="password"
                                        placeholder="{{ $isEditing ? 'Kosongkan jika tidak ingin diubah' : 'Masukkan Password' }}"
                                        class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('password')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- 🔑 Input Hidden untuk is_admin --}}
                                <input wire:model.defer="is_admin" type="hidden" value="{{ $is_admin }}">

                                {{-- Tampilkan Role di Modal --}}
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Role Pengguna:</p>
                                    <p class="text-base font-semibold text-indigo-600">
                                        {{ $is_admin ? 'Admin' : 'User' }}
                                    </p>
                                </div>

                            </div>


                            {{-- 3. Footer/Tombol --}}
                            <div class="p-4 mt-4 border-t bg-gray-50 rounded-b-lg gap-4">

                                {{-- 💡 Bagian Kiri (Error & Tips) --}}
                                <div class="flex-1 text-xs text-gray-600 space-y-3">

                                    {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
                                    @if ($errors->any())
                                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                            <h4 class="font-semibold text-red-700 mb-2">⚠️ Ada beberapa kesalahan:</h4>
                                            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- 💡 2. Tips (Di bawah Error) --}}
                                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
                                        <span class="font-semibold text-gray-700 block mb-1">💡 Tips:</span>
                                        <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                            <li>Kosongkan kolom **password** untuk mempertahankan password
                                                lama (saat edit).</li>
                                            <li>Pastikan semua kolom **wajib diisi** dengan benar.</li>
                                            <li>Perubahan akan tersimpan segera setelah formulir dikirim.</li>
                                        </ul>
                                    </div>

                                </div>

                                {{-- 💾 3. Tombol Aksi (Di sebelah Kanan) --}}
                                <div
                                    class="flex flex-col-reverse sm:flex-row sm:justify-end sm:items-start gap-2 w-full sm:w-auto mt-4">

                                    {{-- Tombol submit --}}
                                    <button type="submit"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition w-full sm:w-auto disabled:opacity-50"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove
                                            wire:target="{{ $isEditing ? 'updateUser' : 'saveUser' }}">
                                            {{ $isEditing ? 'Perbarui' : 'Simpan' }}
                                        </span>
                                        <span wire:loading wire:target="{{ $isEditing ? 'updateUser' : 'saveUser' }}">
                                            Memproses...
                                        </span>
                                    </button>

                                    {{-- Tombol Batal --}}
                                    <button wire:click.prevent="closeModal" type="button"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition w-full sm:w-auto">
                                        Batal
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>


                </div>
            </div>
        </div>

        {{-- @if ($showDeleteConfirmation) --}}
        <div x-show="$wire.showDeleteConfirmation" x-transition.opacity.duration.200ms x-cloak
            class="fixed inset-0 bg-gray-900 bg-opacity-40 flex justify-center items-center z-50">
            <div @click.outside="$wire.cancelDelete()"
                class="bg-white rounded-lg p-6 w-full max-w-sm transform transition-all duration-200 ease-out scale-100">

                {{-- Header --}}
                <h3 class="text-xl font-bold mb-2 text-red-600">Konfirmasi Hapus</h3>

                {{-- Body Pesan --}}
                <p class="text-gray-700 mb-6">
                    Apakah Anda yakin ingin menghapus **{{ $userEmailToDelete }}**?
                    Tindakan ini tidak dapat dibatalkan.
                </p>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end space-x-3">
                    <button wire:click="cancelDelete"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-150">
                        Batal
                    </button>
                    <button wire:click="deleteUser"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
        {{-- @endif --}}

    </div>
</div>
