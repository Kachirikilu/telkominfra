<div>
    <div class="mb-6 py-4 bg-white rounded-lg shadow-md border border-gray-100">
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
        <div wire:loading.flex class="p-6 justify-center text-gray-500">
            <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
        </div>

        <div wire:loading.remove class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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
                                    <form action="{{ route('perjalanan.destroy', $perjalanan->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition">
                                            Hapus
                                        </button>
                                    </form>
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

            <div class="p-4">
                {{ $perjalanans->links() }}
            </div>
        </div>
    </div>
</div>
