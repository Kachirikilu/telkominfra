<div id="jadwal-hari-ini" class="scroll-mt-20 my-6 bg-white shadow-md rounded-md p-2 md:pd-6">
    <h3 class="text-xl font-semibold my-1 ml-2">Jadwal Hari Ini</h3>
    @if($jadwalHariIni->isEmpty())
        <p class="bg-gray-100 rounded-md text-gray-600 text-xs sm:text-sm text-center py-20 mt-3">Tidak ada jadwal hari ini.</p>
    @else
        <div class="grid grid-cols-1 gap-4 pt-3">
            @foreach($jadwalHariIni as $jadwal)
                <div class="rounded-md overflow-hidden hover:bg-gray-100 hover:shadow-lg transition duration-300 group">
                    <a href="/schedules/show/{{ $jadwal->slug }}" class="block">
                        <div class="flex sm:flex-row flex-col items-start">
                            @if($jadwal->gambar)
                                <img src="{{ asset($jadwal->gambar) }}" alt="{{ $jadwal->judul_ceramah }}" class="w-full sm:w-48 aspect-[3/2] sm:aspect-square object-cover rounded-md sm:mr-4 mb-2 sm:mb-0">
                            @else
                                <div class="w-full sm:w-32 aspect-[3/2] sm:aspect-square text-center bg-gray-100 flex items-center justify-center text-gray-500 rounded-md sm:mr-4 mb-2 sm:mb-0">Tidak Ada Gambar</div>
                            @endif
                            <div class="pt-0 px-4 mb-3 sm:mb-5 md:mb-0 sm:px-0 sm:pt-5">
                                <h3 class="font-semibold sm:text-lg group-focus:underline mb-2">{{ $jadwal->judul_ceramah }}</h3>
                                <p class="text-gray-600 text-xs sm:text-sm group-focus:underline">{{ \Carbon\Carbon::parse($jadwal->tanggal_ceramah)->locale('id')->isoFormat('D MMMM Y') }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} WIB</p>
                                <p class="text-gray-500 text-xs sm:text-sm group-focus:underline">{{ $jadwal->nama_ustadz }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>