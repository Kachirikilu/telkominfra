<div wire:poll.1500ms="loadData" class="w-full mx-auto mt-6 mb-6 p-6 bg-white shadow-lg rounded-lg space-y-6">

    <div class="space-y-1">
        <h1 class="text-xl font-bold text-gray-800">ID: {{ $id_device }}</h1>
        <p class="text-gray-700">Message: {{ $message }}</p>
    </div>

    <div class="w-full flex justify-center rounded-md">
        @if ($image)
            <img src="{{ $image }}" alt="Camera Image"
                 class="max-h-80 object-contain rounded-md" />
        @else
            <p class="text-gray-500 italic text-center">Tidak Ada Gambar!</p>
        @endif
    </div>

    <div class="flex justify-center">
        <button type="button" wire:click="sendCapture"
                class="w-64 px-5 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 active:bg-red-700 transition">
            Kirim Perintah Capture
        </button>
    </div>

    <div class="scroll-mt-20 mx-2 md:mx-0 mt-6 mb-4">
        <h2 class="text-xl font-semibold mb-4 border-b border-gray-200 pb-2">ESP32 Camera</h2>
        @if($iotCamera->isEmpty())
            <p class="text-gray-600 text-xs sm:text-sm text-center py-20 mt-3">Tidak ada foto dari ESP32 Camera</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2 md:gap-4">
                @foreach($iotCamera as $iot)
                    <a href="/iot/all-data/{{ $iot->id_device }}" class="block bg-white rounded-md shadow-md overflow-hidden hover:scale-105 hover:shadow-lg  transition duration-300">
                        @if($iot->image)
                            <img src="{{ asset($iot->image) }}" alt="{{ $iot->image }}" class="w-full aspect-square object-cover">
                        @else
                            <div class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-500">Tidak Ada Gambar!</div>
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


