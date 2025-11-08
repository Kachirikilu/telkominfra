<div wire:poll.2000ms="loadData" class="w-full mx-auto mt-6 mb-6 p-6 bg-white shadow-lg rounded-lg space-y-6">

    {{-- Notifikasi Gerakan --}}
    {{-- 
    @if ($motion)
        <div class="flex items-center gap-3 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
            </svg>
            <span>⚠️ Gerakan terdeteksi! Periksa segera.</span>

            <!-- Audio player -->
            <audio autoplay hidden>
                <source src="{{ asset('sounds/1000 Hz.mp3') }}" type="audio/mpeg">
            </audio>
        </div>
    @endif 
    --}}

    <div class="space-y-1">
        <h1 class="text-xl font-bold text-gray-800">ID: {{ $id }}</h1>
        <p class="text-gray-700">Message: {{ $message }}</p>
    </div>

    <div class="w-full flex justify-center">
        @if ($image)
            <img src="data:image/jpeg;base64,{{ $image }}" alt="Camera Image"
                 class="max-h-80 object-contain rounded-md" />
        @else
            <p class="text-gray-500 italic text-center">Tidak ada image</p>
        @endif
    </div>

    <div class="flex justify-center">
        <button type="button" wire:click="sendCapture"
                class="w-64 px-5 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 transition">
            Kirim Perintah Capture
        </button>
    </div>

    {{-- <script>
        window.addEventListener('notify', event => {
            alert(event.detail.message);
        });
    </script> --}}

    <div class="scroll-mt-20 mx-2 md:mx-0 mt-6 mb-4">
        <h2 class="text-xl font-semibold mb-4 border-b border-gray-200 pb-2">Tester</h2>
        @if($iotCamera->isEmpty())
            <p class="text-gray-600 text-xs sm:text-sm text-center py-20 mt-3">Tidak ada jadwal untuk wedbj.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2 md:gap-4">
                @foreach($iotCamera as $iot)
                    <a href="/iot/all-data/{{ $iot->id_device }}" class="block bg-white rounded-md shadow-md overflow-hidden hover:scale-105 hover:shadow-lg  transition duration-300">
                        @if($iot->image)
                            <img src="{{ asset($iot->image) }}" alt="{{ $iot->image }}" class="w-full aspect-square object-cover">
                        @else
                            <div class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-500">Tidak Ada image</div>
                        @endif
                        <div class="p-3">
                            <h3 class="font-semibold sm:text-lg mb-2">Device ID: {{ $iot->id_device }}</h3>
                            <p class="text-gray-600 text-xs sm:text-sm">Pesan: {{ $iot->message }}</p>

                            <p class="text-gray-500 text-xs sm:text-sm">
                                Diambil pada: {{ \Carbon\Carbon::parse($iot->created_at)->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm:ss') }} WIB
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</div>


