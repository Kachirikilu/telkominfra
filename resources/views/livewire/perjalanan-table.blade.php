<div>
    <div class="p-2 sm:p-4 mb-6 py-4 bg-white rounded-lg shadow-md border border-gray-100">
        {{-- 🔹 TAB FILTER --}}
        <div class="flex border-b mb-4">
            <button wire:click="switchMode('')"
                class="px-4 py-2 text-sm font-medium rounded-t-lg border-b-2 transition duration-150 
            {{ $mode === '' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                <i class="fas fa-list mr-2"></i>
                Semua Data ({{ number_format($totalPerjalanan) }})
            </button>

            <button wire:click="switchMode('0')"
                class="px-4 py-2 text-sm font-medium rounded-t-lg border-b-2 transition duration-150 
            {{ $mode === '0' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                <i class="fas fa-hourglass-half mr-2"></i>
                Belum Selesai ({{ number_format($perjalananBelumSelesai) }})
            </button>

            <button wire:click="switchMode('1')"
                class="px-4 py-2 text-sm font-medium rounded-t-lg border-b-2 transition duration-150 
            {{ $mode === '1' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }}">
                <i class="fas fa-check-double mr-2"></i>
                Sudah Selesai ({{ number_format($perjalananSelesai) }})
            </button>
        </div>

        {{-- 🔹 INPUT PENCARIAN --}}
        <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Cari ID, ID Perjalanan, Lokasi, atau Pengguna..."
                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">

            <button wire:click="$set('search', '')"
                class="flex items-center justify-center py-2 px-4 text-gray-600 hover:text-gray-900 bg-gray-100 rounded-lg transition duration-150 shadow-sm border border-gray-300">
                Reset
            </button>
        </div>
    </div>

    {{-- 🔹 TABEL DATA --}}
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        {{-- <div wire:loading.flex class="p-6 justify-center text-gray-500">
            <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
        </div> --}}

        <div class="overflow-x-auto">
            <table wire:loading.class="opacity-50" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Peta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Perjalanan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Selesai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Unggah</th>
                        @if ($isAdmin)
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hapus</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($perjalanans as $perjalanan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $perjalanan->id }}</td>
                            <td class="px-6 py-4 text-center">
                                <button wire:navigate href="{{ route('maintenance.show', $perjalanan->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300">
                                    Peta
                                </button>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $perjalanan->id_perjalanan }}</td>
                            <td class="px-6 py-4 text-center text-sm">
                                @if ($perjalanan->selesai)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ya</span>
                                @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Tidak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $perjalanan->nama_tempat }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $perjalanan->nama_pengguna }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $perjalanan->created_at->format('d M Y H:i') }}</td>

                            @if ($isAdmin)
                                <td class="px-6 py-4 text-center">
                                    {{-- TOMBOL HAPUS BARU MENGGUNAKAN LIVEWIRE --}}
                                    <button wire:click="confirmDelete({{ $perjalanan->id }})"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition">
                                        Hapus
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($perjalanans->hasPages())
                <div class="p-4">
                    {{ $perjalanans->links() }}
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
    </div>

    <div x-show="$wire.showDeleteConfirmation" x-transition.opacity.duration.200ms x-cloak
        class="fixed inset-0 bg-gray-900 bg-opacity-40 flex justify-center items-center z-50">
        <div @click.outside="$wire.cancelDelete()"
            class="bg-white rounded-lg p-6 w-full max-w-sm transform transition-all duration-200 ease-out scale-100">

            {{-- Header --}}
            <h3 class="text-xl font-bold mb-2 text-red-600">Konfirmasi Hapus Data Perjalanan</h3>

            {{-- Body Pesan (Menggunakan properti Livewire) --}}
            <p class="text-gray-700 mb-6">
                Apakah Anda yakin ingin menghapus data perjalanan **{{ $perjalananNamaToDelete }}** dengan ID **{{ $perjalananIdPerjalananToDelete }}**?
                Tindakan ini tidak dapat dibatalkan.
            </p>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end space-x-3">
                <button wire:click="cancelDelete"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-150">
                    Batal
                </button>
                {{-- Panggil deletePerjalanan saat konfirmasi --}}
                <button wire:click="deletePerjalanan"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
