<div class="p-2 sm:p-4">
    {{-- ============================== PENCARIAN & TAB FILTER ============================== --}}
    <div class="mb-6 p-2 sm:p-4 bg-white rounded-xl shadow-md border border-gray-100">
        <div class="flex flex-col sm:flex-row items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Filter Data Keluhan</h3>

            <a href="{{ route('keluh_pengguna.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition shadow-md mt-3 sm:mt-0">
                <i class="fas fa-plus mr-1"></i> Tambah Keluhan
            </a>
        </div>

        {{-- ===================== TABS FILTER MODE ===================== --}}
        <div class="flex border-b mb-4 overflow-x-auto">
            @foreach ([
        'myComment' => ['icon' => 'fa-hourglass-half', 'label' => 'Komentar Saya', 'count' => $keluhanSaya ?? 0],
        'pending' => ['icon' => 'fa-hourglass-half', 'label' => 'Belum', 'count' => $keluhanBelumSelesai ?? 0],
        'processing' => ['icon' => 'fa-tools', 'label' => 'Sedang Diproses', 'count' => $keluhanDiproses ?? 0],
        'complete' => ['icon' => 'fa-check-double', 'label' => 'Sudah Selesai', 'count' => $keluhanSelesai ?? 0],
    ] as $tabMode => $data)
                <button wire:click="switchMode('{{ $tabMode }}')"
                    class="tab-mode px-4 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap
                        {{ $mode === $tabMode
                            ? 'border-indigo-500 text-indigo-700'
                            : 'border-transparent text-gray-500 hover:text-indigo-700 hover:border-gray-300' }}">
                    <i class="fas {{ $data['icon'] }} mr-2"></i>
                    @auth
                        {{ $data['label'] }}
                    @else
                        @if ($data['label'] == 'Komentar Saya')
                            Semua Komentar
                        @else
                            {{ $data['label'] }}
                        @endif
                    @endauth
                    ({{ $data['count'] }})
                </button>
            @endforeach
        </div>

        {{-- ===================== SEARCH INPUT ===================== --}}
        <form id="search-form" class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3">
            <input wire:model.live.debounce.300ms="search" type="text" id="search-input"
                placeholder="Cari Nama Pengguna, Tempat, atau Komentar dalam mode {{ $modeName }}..."
                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
        </form>
    </div>

    <hr class="mb-5" />

    {{-- ============================== DAFTAR KELUHAN ============================== --}}
    <div id="default-results" class="bg-white shadow-lg rounded-lg overflow-hidden">

        <div wire:loading.class="opacity-50" class="overflow-x-auto">
            <div class="overflow-x-auto">
                @if ($keluhans->count())
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                    Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Tempat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komentar
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($keluhans as $item)
                                @php
                                    $statusText = 'Belum';
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    if ($item->perjalanan_id !== null) {
                                        if ($item->perjalanan && $item->perjalanan->selesai) {
                                            $statusText = 'Selesai';
                                            $statusClass = 'bg-green-100 text-green-800';
                                        } else {
                                            $statusText = 'Diproses';
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                        }
                                    }
                                    $shortKomentar = $item->komentar
                                        ? (strlen($item->komentar) > 25
                                            ? substr($item->komentar, 0, 25) . '...'
                                            : $item->komentar)
                                        : '-';
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $item->user->name ?? ($item->nama_pengguna ?? '-') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $item->nama_tempat ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $shortKomentar }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMM Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('keluh_pengguna.show', $item->id) }}"
                                                class="flex-1 text-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition duration-150">
                                                Lihat
                                            </a>
                                            @if ($item->perjalanan_id)
                                                <a href="{{ route('maintenance.show', $item->perjalanan_id) }}"
                                                    class="flex-1 text-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition duration-150">
                                                    Peta
                                                </a>
                                            @endif
                                            @if ($isAdmin || (!is_null($idUser) && $item->user_id == $idUser))
                                                {{-- Tombol Hapus Livewire --}}
                                                <button wire:click="confirmDelete({{ $item->id }})"
                                                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition duration-150 flex-1">
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-6 text-center text-gray-500">
                        Tidak ada keluhan <strong>{{ $modeName }}</strong>
                        {{ $search ? "dengan pencarian: '{$search}'" : '' }}.
                    </div>
                @endif
            </div>
        </div>

        @if ($keluhans->hasPages())
            <div class="p-4">
                {{ $keluhans->links() }}
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

    <div x-show="$wire.showDeleteConfirmation" x-transition.opacity.duration.200ms x-cloak
        class="fixed inset-0 bg-gray-900 bg-opacity-40 flex justify-center items-center z-50">
        <div @click.outside="$wire.cancelDelete()"
            class="bg-white rounded-lg p-6 w-full max-w-sm transform transition-all duration-200 ease-out scale-100">

            {{-- Header --}}
            <h3 class="text-xl font-bold mb-2 text-red-600">Konfirmasi Hapus Keluhan</h3>

            {{-- Body Pesan (Menggunakan properti Livewire) --}}
            <p class="text-gray-700 mb-6">
                Apakah Anda yakin ingin menghapus keluhan **{{ $keluhanNamaToDelete }}**?
                Tindakan ini tidak dapat dibatalkan.
            </p>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end space-x-3">
                <button wire:click="cancelDelete"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-150">
                    Batal
                </button>
                {{-- Panggil deleteKeluhan saat konfirmasi --}}
                <button wire:click="deleteKeluhan"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
