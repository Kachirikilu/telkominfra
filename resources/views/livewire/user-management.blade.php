<div class="mb-8 m-2 sm:m-4">
    <div class="mt-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Manajemen Pengguna</h2>

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
                {{-- Tab User Biasa --}}
                <button wire:click="setMode('user')"
                    class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 
                    {{ $searchMode == 'user' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                    <i class="fas fa-user mr-2"></i> User Biasa (<span>{{ number_format($totalNormalUsers) }}</span>)
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
                <div wire:loading class="p-6 text-center text-indigo-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
                </div>

                <table class="min-w-full divide-y divide-gray-200" wire:loading.remove>
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
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">User
                                            Biasa</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm">
                                    {{-- Cek apakah pengguna saat ini adalah Admin dan BUKAN dirinya sendiri --}}
                                    @if (Auth::user()?->admin && Auth::id() !== $user->id)
                                        <button wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="Apakah Anda yakin ingin menghapus pengguna {{ $user->name }} (ID: {{ $user->id }})?"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition">
                                            Hapus
                                        </button>
                                    @endif
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
        </div>
    </div>
</div>
