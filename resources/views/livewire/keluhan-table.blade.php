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
                    {{ $data['label'] }} ({{ $data['count'] }})
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
    
        <div wire:loading.flex class="p-6 justify-center text-gray-500">
            <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
        </div>

        <div wire:loading.remove class="overflow-x-auto">
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
                                            @if ($isAdmin || $item->user_id == $idUser)
                                                <form method="POST"
                                                    action="{{ route('keluh_pengguna.destroy', $item->id) }}"
                                                    onsubmit="return confirm('Yakin ingin menghapus keluhan ini?');"
                                                    class="flex-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition duration-150">
                                                        Hapus
                                                    </button>
                                                </form>
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
    </div>
</div>
