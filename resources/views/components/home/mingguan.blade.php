<div id="{{ $name == "Sudah Terlaksana" ? 'jadwal-sudah-terlaksana' : 'jadwal-minggu-ini' }}" class="scroll-mt-20 mx-2 md:mx-0 mt-6 mb-4">
    <h2 class="text-xl font-semibold mb-4 border-b border-gray-200 pb-2">Jadwal {{ $name }}</h2>
    @if($jadwalMingguan->isEmpty())
        <p class="text-gray-600 text-xs sm:text-sm text-center py-20 mt-3">Tidak ada jadwal untuk {{strtolower($name) }}.</p>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2 md:gap-4">
            @foreach($jadwalMingguan as $jadwal)
                <a @if($jadwal->slug) href="{{ url('/schedules/show/' . $jadwal->slug) }}" @endif class="block bg-white rounded-md shadow-md overflow-hidden hover:scale-105 hover:shadow-lg  transition duration-300">
                    @if($jadwal->gambar)
                        <img src="{{ asset($jadwal->gambar) }}" alt="{{ $jadwal->judul_ceramah }}" class="w-full aspect-square object-cover">
                    @elseif($jadwal->image)
                        <img src="{{ asset($jadwal->image) }}" alt="{{ $jadwal->judul_ceramah }}" class="w-full aspect-square object-cover">
                    @else
                        <div class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-500">Tidak Ada Gambar!</div>
                    @endif
                    <div class="p-3">
                        <h3 class="font-semibold sm:text-lg mb-2">
                            {{ $jadwal->judul_ceramah ?? 'Device ID: ' . $jadwal->id_device }}
                        </h3>
                        <p class="text-gray-600 text-xs sm:text-sm">
                            @if($jadwal->tanggal_ceramah)
                                {{ \Carbon\Carbon::parse($jadwal->tanggal_ceramah)->locale('id')->isoFormat('D MMMM Y') }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} WIB
                            @else
                                Pesan: {{ $jadwal->message }}
                            @endif
                        </p>
                        <p class="text-gray-500 text-xs sm:text-sm">
                            @if($jadwal->nama_ustadz)
                                {{ $jadwal->nama_ustadz }}
                            @else
                                Diambil pada: {{ \Carbon\Carbon::parse($jadwal->created_at)->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm:ss') }} WIB
                            @endif
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
        @if($name == "Sudah Terlaksana")
            <div class="mt-4">
                {{ $jadwalMingguan->links() }}
            </div>
        @endif
    @endif
</div>