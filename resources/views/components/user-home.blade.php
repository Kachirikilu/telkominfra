<div class="flex-1 p-4 md:p-8 overflow-y-auto">

    <div class="header-with-backdrop-blur text-white shadow-md mb-6 rounded-md">
        <div class="w-full h-full py-20 backdrop-blur-sm hover:backdrop-brightness-50 duration-500 ease-in-out backdrop-brightness-75 flex flex-col lg:flex-row justify-between items-center rounded-md">
            <a href="map" id="scroll-ke-map" class="text-3xl font-semibold mb-1 lg:ml-10 sm:mb-2 lg:mb-0">Al-Aqobah 1</a>
            <div class="flex items-center">
                <span class="lg:mr-10">Selamat datang, ....</span>
            </div>
        </div> 
    </div>

    <style>
        .header-with-backdrop-blur {
            background-image: url('/images/masjid/Pic 5_Al-Aqobah 1.jpg');
            background-size: cover;
            background-position-y: 50%;
        }
    </style>

    

          
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-3">
         <a href="#jadwal-hari-ini" id="scroll-ke-hari-ini" class="bg-white shadow-md rounded-md p-6 hover:bg-green-200 hover:shadow-lg transition duration-300">
            <h3 class="text-lg font-semibold mb-2">Jadwal Hari Ini</h3>
                @if($jadwalHariIni->isEmpty())
                    <p class="text-gray-600 text-sm">Tidak ada jadwal hari ini.</p>
                @else
                    <div class="text-2xl font-bold text-red-500">{{ $jadwalHariIni->count() }}</div>
                    @if($jadwalHariIni->count() > 0)
                        @php
                            $jadwalTerdekat = $jadwalHariIni->sortBy('jam_mulai')->first();
                            $jadwalMingguSelanjutnyaTerdekat = $jadwalHariIni->sortBy('jam_mulai')->skip(1)->first();
                        @endphp
                        <p class="text-gray-600 text-sm mt-1">
                            Terdekat: {{ \Carbon\Carbon::parse($jadwalTerdekat->jam_mulai)->format('H:i') }} WIB
                            @if($jadwalMingguSelanjutnyaTerdekat)
                                <br>Selanjutnya: {{ \Carbon\Carbon::parse($jadwalMingguSelanjutnyaTerdekat->jam_mulai)->format('H:i') }} WIB
                            @endif
                        </p>
                    @endif
                @endif
        </a>

        <a href="#jadwal-minggu-depan" id="scroll-ke-minggu-ini" class="bg-white shadow-md rounded-md p-6 hover:bg-orange-200 hover:shadow-lg transition duration-300">
            <h3 class="text-lg font-semibold mb-2">Jadwal Belum Terlaksana</h3>
            <div class="text-2xl font-bold text-blue-500">{{ $jadwalBelumTerlaksanaCount }}</div>
        </a>
        <a href="#jadwal-sudah-terlaksana" id="scroll-ke-sudah-terlaksana" class="bg-white shadow-md rounded-md p-6 hover:bg-blue-200 hover:shadow-lg transition duration-300">
            <h3 class="text-lg font-semibold mb-2">Jadwal Sudah Terlaksana</h3>
            <div class="text-2xl font-bold text-green-500">{{ $jadwalSudahTerlaksanaCount }}</div>
        </a>
        <a href="#jadwal-sudah-terlaksana" id="scroll-ke-sudah-terlaksana-2" class="bg-white shadow-md rounded-md p-6 hover:bg-gray-300 hover:shadow-lg transition duration-300">
            <h3 class="text-lg font-semibold mb-2">Total Jadwal</h3>
            <div class="text-2xl font-bold text-gray-700">{{ $totalJadwalCount }}</div>
        </a>
        <div></div>
    </div>







    
    
      
    






    <section class="bg-white shadow-md rounded-md">
        <div class="py-4 px-2 mx-auto max-w-screen-xl sm:px-4 lg:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 h-full">
                <div class="col-span-2 sm:col-span-2 bg-gray-50 h-[20rem] lg:h-full flex flex-col">
                    <a href="" class="group relative flex flex-col overflow-hidden rounded-lg px-4 pb-4 pt-40 flex-grow">
                        <img src="/images/masjid/Pic 1_Al-Aqobah 1.jpg" alt="" class="absolute inset-0 h-full w-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out">
                        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/25 to-gray-900/5"></div>
                        <h3 class="z-10 text-2xl font-medium text-white absolute top-0 left-0 p-4 xs:text-xl md:text-3xl">Al-Aqobah 1</h3>
                    </a>
                </div>
                <div class="col-span-2 md:col-span-1 lg:col-span-2 bg-stone-50">
                    <a href="" class="group relative flex flex-col overflow-hidden rounded-lg px-4 pb-4 pt-40 mb-4 h-[15rem] md:h-auto">
                        <img src="/images/masjid/Pic 4_Al-Aqobah 1.jpg" alt="" class="absolute inset-0 h-full w-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out">
                        <div class="absolute inset-0 6g-gradient-to-b from-gray-900/25 to-gray-900/5"></div>
                        {{-- <h3 class="z-10 text-2xl font-medium text-white absolute top-0 left-0 p-4 xs:text-xl md:text-3xl">PT. PUSRI</h3> --}}
                    </a>
                    <div class="grid gap-4 grid-cols-2 md:grid-cols-2 lg:grid-cols-2 h-[20rem] md:h-auto">
                        <a href="" class="group relative flex flex-col overflow-hidden rounded-lg px-4 pb-4 pt-40">
                            <img src="/images/masjid/Pic 2_Al-Aqobah 1.jpg" alt="" class="absolute inset-0 h-full w-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out">
                            <div class="absolute inset-0 bg-gradient-to-b from-gray-900/25 to-gray-900/5"></div>
                            {{-- <h3 class="z-10 text-2xl font-medium text-white absolute top-0 left-0 p-4 xs:text-xl md:text-3xl"></h3> --}}
                        </a>
                        <a href="" class="group relative flex flex-col overflow-hidden rounded-lg px-4 pb-4 pt-40">
                            <img src="/images/masjid/Pic 8_Al-Aqobah 1.jpg" alt="" class="absolute inset-0 h-full w-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out">
                            <div class="absolute inset-0 bg-gradient-to-b from-gray-900/25 to-gray-900/5"></div>
                            {{-- <h3 class="z-10 text-2xl font-medium text-white absolute top-0 left-0 p-4 xs:text-xl md:text-3xl"></h3> --}}
                        </a>
                    </div>
                </div>
                <div class="col-span-2 sm:col-span-2 md:col-span-1 bg-sky-50 h-[15rem] md:h-full flex flex-col">
                    <a href="" class="group relative flex flex-col overflow-hidden rounded-lg px-4 pb-4 pt-40 flex-grow">
                        <img src="/images/masjid/Pic 11_Al-Aqobah 1.jpg" alt="" class="absolute inset-0 h-full w-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out">
                        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/25 to-gray-900/5"></div>
                        {{-- <h3 class="z-10 text-2xl font-medium text-white absolute top-0 left-0 p-4 xs:text-xl md:text-3xl"></h3> --}}
                    </a>
                </div>
            </div>
        </div>
    </section>




    <div id="jadwal-hari-ini" class="scroll-mt-20 my-6 bg-white shadow-md rounded-md p-6">
        <h3 class="text-lg font-semibold mb-2">Jadwal Hari Ini</h3>
        @if($jadwalHariIni->isEmpty())
            <p class="text-gray-600 text-sm">Tidak ada jadwal hari ini.</p>
        @else
            <div class="grid grid-cols-1 gap-4 pt-4">
                @foreach($jadwalHariIni as $jadwal)
                    <div class="rounded-md overflow-hidden hover:bg-gray-100 hover:shadow-lg transition duration-300 group">
                        <a href="/schedules/show/{{ $jadwal->slug }}" class="block">
                            <div class="flex sm:flex-row flex-col items-start">
                                @if($jadwal->gambar)
                                    <img src="{{ asset($jadwal->gambar) }}" alt="{{ $jadwal->judul_ceramah }}" class="w-full sm:w-32 h-32 object-cover rounded-md sm:mr-4 mb-2 sm:mb-0">
                                @else
                                    <div class="w-full sm:w-32 h-32 bg-gray-100 flex items-center justify-center text-gray-500 rounded-md sm:mr-4 mb-2 sm:mb-0">Tidak Ada Gambar</div>
                                @endif
                                <div class="pt-0 pb-5 pl-4 sm:pl-0 sm:pt-5">
                                    <h3 class="font-semibold text-lg group-focus:underline">{{ $jadwal->judul_ceramah }}</h3>
                                    <p class="text-gray-600 text-sm group-focus:underline">{{ \Carbon\Carbon::parse($jadwal->tanggal_ceramah)->locale('id')->isoFormat('D MMMM Y') }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} WIB</p>
                                    <p class="text-gray-500 text-sm group-focus:underline">{{ $jadwal->nama_ustadz }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div id="jadwal-minggu-ini" class="scroll-mt-20 mt-8 mb-4">
        <h2 class="text-xl font-semibold mb-4 border-b border-gray-200 pb-2">Jadwal Minggu Ini</h2>
        @if($jadwalMingguIni->isEmpty())
            <p>Tidak ada jadwal untuk minggu ini.</p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($jadwalMingguIni as $jadwal)
                    <a href="/schedules/show/{{ $jadwal->slug }}" class="block bg-white rounded-md shadow-md overflow-hidden hover:scale-105 hover:shadow-lg  transition duration-300">
                        @if($jadwal->gambar)
                            <img src="{{ asset($jadwal->gambar) }}" alt="{{ $jadwal->judul_ceramah }}" class="w-full h-32 object-cover">
                        @else
                            <div class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-500">Tidak Ada Gambar</div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-lg">{{ $jadwal->judul_ceramah }}</h3>
                            <p class="text-gray-600 text-sm">{{ \Carbon\Carbon::parse($jadwal->tanggal_ceramah)->locale('id')->isoFormat('D MMMM Y') }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} WIB</p>
                            <p class="text-gray-500 text-sm">{{ $jadwal->nama_ustadz }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mt-8 mb-4 border-b border-gray-200 pb-2">Jadwal Minggu Depan</h2>
        @if($jadwalMingguDepan->isEmpty())
            <p>Tidak ada jadwal untuk minggu depan.</p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($jadwalMingguDepan as $jadwal)
                    <a href="/schedules/show/{{ $jadwal->slug }}" class="block bg-white rounded-md shadow-md overflow-hidden hover:scale-105 hover:shadow-lg  transition duration-300">
                        @if($jadwal->gambar)
                            <img src="{{ asset($jadwal->gambar) }}" alt="{{ $jadwal->judul_ceramah }}" class="w-full h-32 object-cover">
                        @else
                            <div class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-500">Tidak Ada Gambar</div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-lg">{{ $jadwal->judul_ceramah }}</h3>
                            <p class="text-gray-600 text-sm">{{ \Carbon\Carbon::parse($jadwal->tanggal_ceramah)->locale('id')->isoFormat('D MMMM Y') }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} WIB</p>
                            <p class="text-gray-500 text-sm">{{ $jadwal->nama_ustadz }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div>
        <h2 class="text-xl font-semibold mt-8 mb-4 border-b border-gray-200 pb-2">Jadwal Minggu Selanjutnya</h2>
        @if($jadwalMingguSelanjutnya->isEmpty())
            <p>Tidak ada jadwal minggu selanjutnya.</p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($jadwalMingguSelanjutnya as $jadwal)
                    <a href="/schedules/show/{{ $jadwal->slug }}" class="block bg-white rounded-md shadow-md overflow-hidden hover:scale-105 hover:shadow-lg  transition duration-300">
                        @if($jadwal->gambar)
                            <img src="{{ asset($jadwal->gambar) }}" alt="{{ $jadwal->judul_ceramah }}" class="w-full h-32 object-cover">
                        @else
                            <div class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-500">Tidak Ada Gambar</div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-lg">{{ $jadwal->judul_ceramah }}</h3>
                            <p class="text-gray-600 text-sm">{{ \Carbon\Carbon::parse($jadwal->tanggal_ceramah)->locale('id')->isoFormat('D MMMM Y') }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} WIB</p>
                            <p class="text-gray-500 text-sm">{{ $jadwal->nama_ustadz }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
    
            {{ $jadwalMingguSelanjutnya->links() }}
        @endif
    </div>

    <div id="jadwal-sudah-terlaksana" class="scroll-mt-20 my-8">
        <h2 class="text-xl font-semibold mb-4 border-b border-gray-200 pb-2">Jadwal yang Sudah Terlaksana</h2>
        @if($jadwalSudahTerlaksana->isEmpty())
            <p>Tidak ada jadwal untuk minggu lalu.</p>
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
                                        <span class="text-gray-500 text-sm">Tidak Ada</span>
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

    <div id="map" class="scroll-mt-20 mt-8 mb-40">
        <h2 class="text-xl font-semibold mb-4 border-b border-gray-200 pb-2">Lokasi Masjid</h2>
        <div class="overflow-hidden rounded-md shadow-md">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.4424348373436!2d104.79979469999999!3d-2.974643!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e3b77b54752dde9%3A0xa476856998a2a3b2!2sMasjid%20Al%20-%20Aqobah%201!5e0!3m2!1sid!2sid!4v1746677602550!5m2!1sid!2sid"
                width="100%"
                height="450"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollLinks = [
            { id: 'scroll-ke-hari-ini', target: '#jadwal-hari-ini' },
            { id: 'scroll-ke-minggu-ini', target: '#jadwal-minggu-ini' },
            { id: 'scroll-ke-sudah-terlaksana', target: '#jadwal-sudah-terlaksana' },
            { id: 'scroll-ke-sudah-terlaksana-2', target: '#jadwal-sudah-terlaksana' },
            { id: 'scroll-ke-map', target: '#map' }
        ];

        scrollLinks.forEach(linkInfo => {
            const scrollLink = document.getElementById(linkInfo.id);
            const targetId = linkInfo.target;
            const targetElement = document.querySelector(targetId);

            if (scrollLink && targetElement) {
                scrollLink.addEventListener('click', function(event) {
                    event.preventDefault();

                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            }
        });
    });
</script>