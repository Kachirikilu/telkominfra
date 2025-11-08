<div id="jadwal-sudah-terlaksana" class="scroll-mt-20 my-8 mb-40">
    <h2 class="text-xl font-semibold mb-4 border-b border-gray-200 pb-2">Jadwal yang Sudah Terlaksana</h2>
    @if($jadwalSudahTerlaksana->isEmpty())
        <p>Tidak ada jadwal yang telah dilaksanakan.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-md shadow-md">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ustadz</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tempat</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($jadwalSudahTerlaksana as $jadwal)
                    <tr onclick="window.location='/schedules/show/{{ $jadwal->slug }}'" class="cursor-pointer hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($jadwal->gambar)
                                    <img src="{{ asset($jadwal->gambar) }}" alt="{{ $jadwal->judul_ceramah }}" class="max-w-40 rounded">
                                @else
                                    <span class="text-gray-500 text-xs sm:text-sm">Tidak Ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $jadwal->judul_ceramah }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $jadwal->nama_ustadz }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->tanggal_ceramah)->locale('id')->isoFormat('D MMMM Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $jadwal->tempat_ceramah }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $jadwalSudahTerlaksana->links() }}
        </div>
    @endif
</div>