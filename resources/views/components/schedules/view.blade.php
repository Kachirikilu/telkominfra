<div class="flex-1 overflow-y-auto">
    <div class="max-w-7xl mx-auto px-1 sm:px-2">
        <div class="bg-white shadow-xl sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Daftar Jadwal Ceramah</h3>
                <button onclick="window.location='{{ route('admin.schedules.create') }}'" class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 mb-4">
                    Tambah Jadwal Baru
                </button>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ustadz</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($jadwalCeramahs as $jadwal)
                            <tr 
                             class="cursor-pointer hover:bg-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap"><img src="{{ asset($jadwal->gambar) }}" alt="{{ $jadwal->judul_ceramah }}" class="max-w-40 rounded"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $jadwal->judul_ceramah }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $jadwal->nama_ustadz }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->tanggal_ceramah)->locale('id')->isoFormat('D MMMM Y') }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '-' }} WIB</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button onclick="window.location='/schedules/show/{{ $jadwal->slug }}'" class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                            Lihat Detail
                                        </button>
                                        <button onclick="window.location='{{ route('admin.schedules.edit', $jadwal->id) }}'" class="inline-flex items-center px-4 py-2 bg-orange-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 active:bg-orange-500 focus:outline-none focus:border-orange-900 focus:ring focus:ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.schedules.destroy', $jadwal->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-xs" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="px-6 py-4 whitespace-nowrap text-center" colspan="5">Tidak ada jadwal ceramah.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $jadwalCeramahs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>